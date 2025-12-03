<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\HistoricoPreciosDolar;

/**
 * DollarStaticsController handles dollar exchange rate statistics display
 */
class DollarStaticsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays dollar statics page with BCV and Binance/Parallel rates
     *
     * @return string
     */
    public function actionIndex()
    {
        // Verificar si el usuario está logueado
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        return $this->render('index');
    }

    /**
     * AJAX: Get historical data for chart
     *
     * @return Response
     */
    public function actionHistoricalData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $period = Yii::$app->request->get('period', '1d');

            Yii::info("Historical data requested for period: {$period}", __METHOD__);

            // Determinar el rango de fechas según el período
            // Ajuste horario: trabajamos siempre 4 horas "atrás" para hablar el mismo idioma que la BD
            $now = $this->getShiftedNow();
            $dateFrom = $this->getDateFromPeriod($period, clone $now);
            
            Yii::info("Date from: {$dateFrom}, Date to: " . $now->format('Y-m-d H:i:s'), __METHOD__);
            
            // Obtener TODOS los datos históricos del rango
            $oficialData = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
                ->andWhere(['>=', 'created_at', $dateFrom])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
            
            $paraleloData = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
                ->andWhere(['>=', 'created_at', $dateFrom])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
            
            Yii::info("Oficial data count: " . count($oficialData), __METHOD__);
            Yii::info("Paralelo data count: " . count($paraleloData), __METHOD__);
            
            // Crear línea de tiempo completa con forward-fill
            $timelineData = $this->createTimeline($oficialData, $paraleloData, $period, new \DateTime($dateFrom), $now);
            
            $result = [
                'success' => true,
                'data' => [
                    'oficial' => $timelineData['oficial'],
                    'paralelo' => $timelineData['paralelo']
                ],
                'debug' => [
                    'period' => $period,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $now->format('Y-m-d H:i:s'),
                    'oficialCount' => count($oficialData),
                    'paraleloCount' => count($paraleloData),
                    'timelinePoints' => count($timelineData['oficial']['values'])
                ]
            ];
            
            Yii::info("Returning timeline data with " . count($timelineData['oficial']['values']) . " points", __METHOD__);
            
            return $result;
            
        } catch (\Exception $e) {
            Yii::error('Error al obtener datos históricos: ' . $e->getMessage(), __METHOD__);
            Yii::error('Stack trace: ' . $e->getTraceAsString(), __METHOD__);
            
            return [
                'success' => false,
                'message' => 'Error al obtener datos históricos: ' . $e->getMessage(),
                'trace' => YII_DEBUG ? $e->getTraceAsString() : null
            ];
        }
    }
    
    /**
     * Create complete timeline with forward-fill for both data series
     */
    private function createTimeline($oficialData, $paraleloData, $period, $dateFrom, $dateTo)
    {
        // Determinar el intervalo según el período
        $interval = $this->getIntervalByPeriod($period);
        
        // Crear mapas de datos por timestamp
        $oficialMap = [];
        foreach ($oficialData as $record) {
            $date = new \DateTime($record->created_at);
            $timestamp = $date->getTimestamp();
            $oficialMap[$timestamp] = floatval($record->precio_ves);
        }
        
        $paraleloMap = [];
        foreach ($paraleloData as $record) {
            $date = new \DateTime($record->created_at);
            $timestamp = $date->getTimestamp();
            $paraleloMap[$timestamp] = floatval($record->precio_ves);
        }
        
        // Ordenar timestamps
        ksort($oficialMap);
        ksort($paraleloMap);
        
        // Crear línea de tiempo completa
        $timeline = [];
        $currentDate = clone $dateFrom;
        
        $lastOficialValue = null;
        $lastParaleloValue = null;
        
        // Obtener primer valor de cada uno si existe
        if (!empty($oficialMap)) {
            $lastOficialValue = reset($oficialMap);
        }
        if (!empty($paraleloMap)) {
            $lastParaleloValue = reset($paraleloMap);
        }
        
        while ($currentDate <= $dateTo) {
            $currentTimestamp = $currentDate->getTimestamp();
            
            // Buscar valor oficial en este timestamp o usar el último conocido
            if (isset($oficialMap[$currentTimestamp])) {
                $lastOficialValue = $oficialMap[$currentTimestamp];
            } else {
                // Buscar el último valor antes de este timestamp
                foreach (array_reverse($oficialMap, true) as $ts => $val) {
                    if ($ts <= $currentTimestamp) {
                        $lastOficialValue = $val;
                        break;
                    }
                }
            }
            
            // Buscar valor paralelo en este timestamp o usar el último conocido
            if (isset($paraleloMap[$currentTimestamp])) {
                $lastParaleloValue = $paraleloMap[$currentTimestamp];
            } else {
                // Buscar el último valor antes de este timestamp
                foreach (array_reverse($paraleloMap, true) as $ts => $val) {
                    if ($ts <= $currentTimestamp) {
                        $lastParaleloValue = $val;
                        break;
                    }
                }
            }
            
            // Solo agregar si tenemos al menos un valor
            if ($lastOficialValue !== null || $lastParaleloValue !== null) {
                $timeline[] = [
                    'date' => clone $currentDate,
                    'oficial' => $lastOficialValue,
                    'paralelo' => $lastParaleloValue
                ];
            }
            
            // Avanzar al siguiente intervalo
            $currentDate->add($interval);
        }
        
        // Agrupar por período para el gráfico
        return $this->groupTimeline($timeline, $period);
    }
    
    /**
     * Get interval based on period
     */
    private function getIntervalByPeriod($period)
    {
        switch ($period) {
            case '15min':
                return new \DateInterval('PT15M'); // 15 minutos
            case '30min':
                return new \DateInterval('PT30M'); // 30 minutos
            case '1h':
                return new \DateInterval('PT1H'); // 1 hora
            case '5h':
                return new \DateInterval('PT5H'); // 5 horas
            case '1d':
            case '5d':
            case '10d':
            case '15d':
            case '1m':
                return new \DateInterval('PT1H'); // 1 hora para períodos de días
            case '1y':
                return new \DateInterval('P1D'); // 1 día para año
            default:
                return new \DateInterval('PT1H');
        }
    }
    
    /**
     * Group timeline data by period for display
     */
    private function groupTimeline($timeline, $period)
    {
        if (empty($timeline)) {
            return [
                'oficial' => ['labels' => [], 'values' => []],
                'paralelo' => ['labels' => [], 'values' => []]
            ];
        }
        
        $groupFormat = $this->getGroupFormatByPeriod($period);
        $grouped = [];
        
        foreach ($timeline as $point) {
            $key = $point['date']->format($groupFormat);
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'oficial_sum' => 0,
                    'oficial_count' => 0,
                    'paralelo_sum' => 0,
                    'paralelo_count' => 0,
                    'label' => $this->formatLabel($point['date'], $period)
                ];
            }
            
            if ($point['oficial'] !== null) {
                $grouped[$key]['oficial_sum'] += $point['oficial'];
                $grouped[$key]['oficial_count']++;
            }
            
            if ($point['paralelo'] !== null) {
                $grouped[$key]['paralelo_sum'] += $point['paralelo'];
                $grouped[$key]['paralelo_count']++;
            }
        }
        
        // Calcular promedios
        $oficialLabels = [];
        $oficialValues = [];
        $paraleloLabels = [];
        $paraleloValues = [];
        
        foreach ($grouped as $group) {
            $label = $group['label'];
            
            if ($group['oficial_count'] > 0) {
                $oficialLabels[] = $label;
                $oficialValues[] = round($group['oficial_sum'] / $group['oficial_count'], 2);
            }
            
            if ($group['paralelo_count'] > 0) {
                $paraleloLabels[] = $label;
                $paraleloValues[] = round($group['paralelo_sum'] / $group['paralelo_count'], 2);
            }
        }
        
        return [
            'oficial' => [
                'labels' => $oficialLabels,
                'values' => $oficialValues
            ],
            'paralelo' => [
                'labels' => $paraleloLabels,
                'values' => $paraleloValues
            ]
        ];
    }
    
    /**
     * Obtener el "ahora" ajustado 4 horas hacia atrás para que coincida con los registros de BD
     *
     * @return \DateTime
     */
    private function getShiftedNow()
    {
        $now = new \DateTime();
        // Posicionar 4 horas atrás respecto al servidor para que los rangos coincidan con la hora de la BD
        $now->modify('-4 hours');
        return $now;
    }
    
    /**
     * Get date from based on period, using a base "now" (ya ajustado)
     */
    private function getDateFromPeriod($period, \DateTime $now)
    {
        switch ($period) {
            case '15min':
                $now->modify('-15 minutes');
                break;
            case '30min':
                $now->modify('-30 minutes');
                break;
            case '1h':
                $now->modify('-1 hour');
                break;
            case '5h':
                $now->modify('-5 hours');
                break;
            case '1d':
                $now->modify('-1 day');
                break;
            case '5d':
                $now->modify('-5 days');
                break;
            case '10d':
                $now->modify('-10 days');
                break;
            case '15d':
                $now->modify('-15 days');
                break;
            case '1m':
                $now->modify('-1 month');
                break;
            case '1y':
                $now->modify('-1 year');
                break;
            default:
                $now->modify('-1 day');
        }
        return $now->format('Y-m-d H:i:s');
    }
    
    /**
     * Get group format based on period
     */
    private function getGroupFormatByPeriod($period)
    {
        // Para períodos cortos (minutos/horas), agrupar por hora o minuto
        if (in_array($period, ['15min', '30min'])) {
            return 'Y-m-d H:i'; // Por minuto
        } elseif (in_array($period, ['1h', '5h'])) {
            return 'Y-m-d H:00'; // Por hora
        } else {
            return 'Y-m-d'; // Por día
        }
    }
    
    /**
     * Process data by period with aggregation
     */
    private function processDataByPeriod($data, $period)
    {
        if (empty($data)) {
            return [
                'labels' => [],
                'values' => []
            ];
        }
        
        $groupFormat = $this->getGroupFormatByPeriod($period);
        
        // Agrupar por fecha/hora según el formato
        $grouped = [];
        
        foreach ($data as $record) {
            $date = new \DateTime($record->created_at);
            $key = $date->format($groupFormat);
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'sum' => 0,
                    'count' => 0,
                    'label' => $this->formatLabel($date, $period)
                ];
            }
            
            $grouped[$key]['sum'] += $record->precio_ves;
            $grouped[$key]['count']++;
        }
        
        // Calcular promedios
        $labels = [];
        $values = [];
        
        foreach ($grouped as $key => $group) {
            $labels[] = $group['label'];
            $values[] = round($group['sum'] / $group['count'], 2);
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
    
    /**
     * Format label based on period
     */
    private function formatLabel($date, $period)
    {
        if (in_array($period, ['15min', '30min'])) {
            // Formato: HH:MM
            return $date->format('H:i');
        } elseif (in_array($period, ['1h', '5h'])) {
            // Formato: HH:00
            return $date->format('H:00');
        } elseif (in_array($period, ['1d', '5d', '10d', '15d'])) {
            // Formato: DD/MM
            return $date->format('d/m');
        } elseif ($period === '1m') {
            // Formato: DD/MM
            return $date->format('d/m');
        } else {
            // Para 1y: Formato: MMM YYYY
            return $date->format('M Y');
        }
    }
}

