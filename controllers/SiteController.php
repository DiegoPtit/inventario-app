<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ContactForm;
use app\models\Productos;
use app\models\Clientes;
use app\models\Facturas;
use app\models\HistoricoCobros;
use app\models\Stock;
use app\models\HistoricoInventarios;
use app\models\HistoricoPreciosDolar;
use app\models\Usuarios;
use yii\authclient\ClientInterface;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // Verificar si el usuario está logueado
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        // Obtener productos con sus categorías para el carrusel
        $productos = Productos::find()
            ->with('categoria')
            ->orderBy('created_at DESC')
            ->all();
        
        // Obtener clientes para mostrar en la tabla
        $clientes = Clientes::find()
            ->orderBy('created_at DESC')
            ->all();
        
        // NUEVA LÓGICA: Obtener todas las facturas ordenadas por created_at DESC
        $facturas = Facturas::find()
            ->with(['historicoCobros', 'cliente'])
            ->orderBy('created_at DESC')
            ->all();

        //Obtener precios de dolar
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Función helper para convertir a USDT (para estadísticas)
        $convertToUSDT = function($amount, $fromCurrency) use ($precioParalelo, $precioOficial) {
            if (!$precioParalelo || !$precioOficial) {
                return $amount;
            }
            
            $value = floatval($amount);
            
            if ($fromCurrency === 'USDT') {
                return $value;
            }
            
            // Convertir a VES primero
            $amountInVES = 0;
            if ($fromCurrency === 'BCV') {
                $amountInVES = $value * $precioOficial->precio_ves;
            } elseif ($fromCurrency === 'VES') {
                $amountInVES = $value;
            }
            
            // De VES a USDT
            if ($precioParalelo->precio_ves > 0) {
                return $amountInVES / $precioParalelo->precio_ves;
            }
            
            return $value;
        };
        
        // Inicializar contadores para el gráfico (EN USDT)
        $cobrosCerradas = 0;  // Total cobrado de facturas cerradas (EN USDT)
        $cobrosAbiertas = 0;  // Saldo pendiente de facturas abiertas (EN USDT)
        
        // Procesar cada factura para determinar su estado y preparar datos para mostrar
        $cobrosParaMostrar = [];
        
        foreach ($facturas as $factura) {
            // Calcular total cobrado de esta factura (convertido a la moneda de la factura)
            $totalCobrado = 0;
            
            foreach ($factura->historicoCobros as $cobro) {
                $montoEnMonedaFactura = 0;
                
                // Si el cobro y la factura tienen la misma moneda, usar el monto directamente
                if ($cobro->currency === $factura->currency) {
                    $montoEnMonedaFactura = $cobro->monto;
                }
                // Si no, necesitamos convertir
                else {
                    // Primero, convertir el monto del cobro a VES (moneda base)
                    $montoEnVES = 0;
                    
                    if ($cobro->currency === 'VES') {
                        $montoEnVES = $cobro->monto;
                    } elseif ($cobro->currency === 'USDT' && $precioParalelo) {
                        $montoEnVES = $cobro->monto * $precioParalelo->precio_ves;
                    } elseif ($cobro->currency === 'BCV' && $precioOficial) {
                        $montoEnVES = $cobro->monto * $precioOficial->precio_ves;
                    }
                    
                    // Ahora convertir de VES a la moneda de la factura
                    if ($factura->currency === 'VES') {
                        $montoEnMonedaFactura = $montoEnVES;
                    } elseif ($factura->currency === 'USDT' && $precioParalelo && $precioParalelo->precio_ves > 0) {
                        $montoEnMonedaFactura = $montoEnVES / $precioParalelo->precio_ves;
                    } elseif ($factura->currency === 'BCV' && $precioOficial && $precioOficial->precio_ves > 0) {
                        $montoEnMonedaFactura = $montoEnVES / $precioOficial->precio_ves;
                    }
                }
                
                $totalCobrado += $montoEnMonedaFactura;
            }
            
            // Aplicar round para evitar problemas de precisión decimal
            $totalCobrado = round($totalCobrado, 2);
            
            // Determinar si está cerrada: los cobros cubren el monto_final
            $esCerrada = $totalCobrado >= $factura->monto_final;
            $montoRestante = round(max(0, $factura->monto_final - $totalCobrado), 2);
            
            // IMPORTANTE: Actualizar contadores EN USDT para estadísticas
            // Convertir el total cobrado de la factura a USDT
            $totalCobradoEnUSDT = $convertToUSDT($totalCobrado, $factura->currency);
            $montoRestanteEnUSDT = $convertToUSDT($montoRestante, $factura->currency);
            
            if ($esCerrada) {
                $cobrosCerradas += $totalCobradoEnUSDT;
            } else {
                $cobrosAbiertas += $montoRestanteEnUSDT;
            }
            
            // Crear UN SOLO ITEM por factura (no importa cuántos cobros tenga)
            $cobrosParaMostrar[] = [
                'tipo' => $esCerrada ? 'cerrada' : 'abierta',
                'factura' => $factura,
                'cliente' => $factura->cliente,
                'cobros' => $factura->historicoCobros,
                'totalCobrado' => $totalCobrado,
                'montoRestante' => $montoRestante,
                'esCerrada' => $esCerrada,
                'fechaCreacion' => $factura->created_at
            ];
        }
        
        // Aplicar round a los totales
        $cobrosCerradas = round($cobrosCerradas, 2);
        $cobrosAbiertas = round($cobrosAbiertas, 2);
        
        // Nota: cobrosParaMostrar ya está ordenado por created_at DESC gracias a la query inicial
        
        // Obtener valor de inventario del cierre más reciente
        $historicoReciente = HistoricoInventarios::find()
            ->orderBy(['fecha_cierre' => SORT_DESC])
            ->one();
        
        $valorInventario = $historicoReciente ? $historicoReciente->valor : 0;
        
        // Valor recaudado: Convertir TODOS los cobros a USDT
        $valorRecaudado = 0;
        $todosLosCobros = HistoricoCobros::find()->all();
        foreach ($todosLosCobros as $cobro) {
            $valorRecaudado += $convertToUSDT($cobro->monto, $cobro->currency);
        }
        $valorRecaudado = round($valorRecaudado, 2);
        
        // Calcular proporción de inventario vs recaudado (ambos en USDT)
        $proporcionDeuda = $valorInventario;
        $proporcionRecaudado = $valorRecaudado;
        
        // Contar clientes morosos vs solventes
        $clientesSolventes = 0;
        $clientesMorosos = 0;
        
        foreach ($clientes as $cliente) {
            if ($cliente->isStatusSolvente()) {
                $clientesSolventes++;
            } else {
                $clientesMorosos++;
            }
        }
        
        // Get latest dollar prices for conversions
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        return $this->render('index', [
            'productos' => $productos,
            'clientes' => $clientes,
            'cobrosCerradas' => $cobrosCerradas,
            'cobrosAbiertas' => $cobrosAbiertas,
            'cobrosParaMostrar' => $cobrosParaMostrar,
            'valorInventario' => $valorInventario,
            'valorRecaudado' => $valorRecaudado,
            'proporcionDeuda' => $proporcionDeuda,
            'proporcionRecaudado' => $proporcionRecaudado,
            'clientesSolventes' => $clientesSolventes,
            'clientesMorosos' => $clientesMorosos,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Register action.
     *
     * @return Response|string
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->session->setFlash('success', 'Registro exitoso. Has sido conectado automáticamente.');
            return $this->goHome();
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays menu page.
     *
     * @return string
     */
    public function actionMenu()
    {
        // Verificar si el usuario está logueado
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        return $this->render('menu');
    }

    /**
     * AJAX: Get current dollar prices for widget
     *
     * @return Response
     */
    public function actionDollarPrices()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Obtener el precio más reciente de cada tipo
            $precioOficial = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            $precioParalelo = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            $precios = [];

            if ($precioOficial) {
                $precios[] = [
                    'precio' => number_format($precioOficial->precio_ves, 2, ',', '.'),
                    'tipo' => $precioOficial->displayTipo(),
                    'class' => 'text-success'
                ];
            }

            if ($precioParalelo) {
                $precios[] = [
                    'precio' => number_format($precioParalelo->precio_ves, 2, ',', '.'),
                    'tipo' => $precioParalelo->displayTipo(),
                    'class' => 'text-warning'
                ];
            }

            return [
                'success' => true,
                'data' => $precios
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener los precios del dólar'
            ];
        }
    }

    /**
     * AJAX: Update official dollar rate from BCV API
     *
     * @return Response
     */
    public function actionUpdateDollarRate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Consumir API del BCV
            $apiUrl = 'https://bcv-api.rafnixg.dev/rates/';
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'GET',
                    'header' => 'User-Agent: InventarioApp/1.0'
                ]
            ]);

            $response = file_get_contents($apiUrl, false, $context);
            
            if ($response === false) {
                throw new \Exception('No se pudo conectar con la API del BCV');
            }

            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error al decodificar la respuesta de la API');
            }

            if (!isset($data['dollar']) || !is_numeric($data['dollar'])) {
                throw new \Exception('Datos inválidos recibidos de la API');
            }

            $nuevoPrecio = floatval($data['dollar']);

            // Verificar si el precio ha cambiado
            $ultimoPrecio = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            // Solo guardar si el precio es diferente o no existe registro previo
            if (!$ultimoPrecio || $ultimoPrecio->precio_ves != $nuevoPrecio) {
                $historicoPrecio = new HistoricoPreciosDolar();
                $historicoPrecio->precio_ves = $nuevoPrecio;
                $historicoPrecio->setTipoToOficial();
                // created_at will be set automatically by TimestampBehavior

                if (!$historicoPrecio->save()) {
                    throw new \Exception('Error al guardar el nuevo precio: ' . implode(', ', $historicoPrecio->getFirstErrors()));
                }
            }

            return [
                'success' => true,
                'message' => 'Precio oficial actualizado correctamente',
                'data' => [
                    'precio' => number_format($nuevoPrecio, 2, ',', '.'),
                    'fecha' => $data['date'] ?? date('Y-m-d'),
                    'actualizado' => $ultimoPrecio ? ($ultimoPrecio->precio_ves != $nuevoPrecio) : true
                ]
            ];

        } catch (\Exception $e) {
            Yii::error('Error al actualizar precio oficial del dólar: ' . $e->getMessage(), __METHOD__);
            
            return [
                'success' => false,
                'message' => 'Error al actualizar el precio oficial: ' . $e->getMessage()
            ];
        }
    }

    /**
     * AJAX: Update parallel dollar rate manually from user input
     *
     * @return Response
     */
    public function actionUpdateParallelDollarRate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $precioParalelo = Yii::$app->request->post('precio_paralelo');
            $observaciones = Yii::$app->request->post('observaciones', '');

            // Validar que se haya enviado el precio
            if (!$precioParalelo || !is_numeric($precioParalelo)) {
                throw new \Exception('El precio debe ser un número válido');
            }

            $nuevoPrecio = floatval($precioParalelo);

            // Validar que el precio sea mayor a 0
            if ($nuevoPrecio <= 0) {
                throw new \Exception('El precio debe ser mayor a 0');
            }

            // Verificar si el precio ha cambiado significativamente (más de 0.01 VES)
            $ultimoPrecio = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            $precioCambio = !$ultimoPrecio || abs($ultimoPrecio->precio_ves - $nuevoPrecio) > 0.01;

            // Guardar el nuevo precio
            $historicoPrecio = new HistoricoPreciosDolar();
            $historicoPrecio->precio_ves = $nuevoPrecio;
            $historicoPrecio->setTipoToParalelo();
            // created_at will be set automatically by TimestampBehavior

            if (!$historicoPrecio->save()) {
                throw new \Exception('Error al guardar el nuevo precio paralelo: ' . implode(', ', $historicoPrecio->getFirstErrors()));
            }

            // Log de la actualización manual
            Yii::info("Precio paralelo actualizado manualmente: {$nuevoPrecio} VES. Observaciones: {$observaciones}", __METHOD__);

            return [
                'success' => true,
                'message' => 'Precio paralelo actualizado correctamente',
                'data' => [
                    'precio' => number_format($nuevoPrecio, 2, ',', '.'),
                    'fecha' => date('Y-m-d H:i:s'),
                    'actualizado' => true,
                    'metodo' => 'Manual',
                    'observaciones' => $observaciones
                ]
            ];

        } catch (\Exception $e) {
            Yii::error('Error al actualizar precio paralelo manualmente: ' . $e->getMessage(), __METHOD__);
            
            return [
                'success' => false,
                'message' => 'Error al actualizar el precio paralelo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * AJAX: Get clients with pending invoices for payment reminder modal
     *
     * @return Response
     */
    public function actionGetClientesPendientes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Get latest dollar prices for conversions
            $precioOficial = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();
            
            $precioParalelo = HistoricoPreciosDolar::find()
                ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();
            
            // Obtener todos los clientes con facturas
            $clientes = Clientes::find()
                ->with(['facturas.historicoCobros'])
                ->all();

            $clientesPendientes = [];

            foreach ($clientes as $cliente) {
                $facturasPendientes = [];
                
                foreach ($cliente->facturas as $factura) {
                    // Calcular total cobrado de esta factura (convertido a la moneda de la factura)
                    $totalCobrado = 0;
                    
                    foreach ($factura->historicoCobros as $cobro) {
                        $montoEnMonedaFactura = 0;
                        
                        // Si el cobro y la factura tienen la misma moneda, usar el monto directamente
                        if ($cobro->currency === $factura->currency) {
                            $montoEnMonedaFactura = $cobro->monto;
                        }
                        // Si no, necesitamos convertir
                        else {
                            // Primero, convertir el monto del cobro a VES (moneda base)
                            $montoEnVES = 0;
                            
                            if ($cobro->currency === 'VES') {
                                $montoEnVES = $cobro->monto;
                            } elseif ($cobro->currency === 'USDT' && $precioParalelo) {
                                $montoEnVES = $cobro->monto * $precioParalelo->precio_ves;
                            } elseif ($cobro->currency === 'BCV' && $precioOficial) {
                                $montoEnVES = $cobro->monto * $precioOficial->precio_ves;
                            }
                            
                            // Ahora convertir de VES a la moneda de la factura
                            if ($factura->currency === 'VES') {
                                $montoEnMonedaFactura = $montoEnVES;
                            } elseif ($factura->currency === 'USDT' && $precioParalelo && $precioParalelo->precio_ves > 0) {
                                $montoEnMonedaFactura = $montoEnVES / $precioParalelo->precio_ves;
                            } elseif ($factura->currency === 'BCV' && $precioOficial && $precioOficial->precio_ves > 0) {
                                $montoEnMonedaFactura = $montoEnVES / $precioOficial->precio_ves;
                            }
                        }
                        
                        $totalCobrado += $montoEnMonedaFactura;
                    }
                    
                    // Si la factura tiene saldo pendiente, agregarla
                    if ($totalCobrado < $factura->monto_final) {
                        $facturasPendientes[] = [
                            'id' => $factura->id,
                            'codigo' => $factura->codigo,
                            'concepto' => $factura->concepto,
                            'monto_final' => $factura->monto_final,
                            'total_pagado' => $totalCobrado,
                            'saldo_pendiente' => $factura->monto_final - $totalCobrado,
                            'fecha' => $factura->fecha,
                            'currency' => $factura->currency, // Add currency field
                        ];
                    }
                }
                
                // Si el cliente tiene facturas pendientes, agregarlo a la lista
                if (!empty($facturasPendientes)) {
                    $clientesPendientes[] = [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'telefono' => $cliente->telefono,
                        'status' => $cliente->status,
                        'facturas' => $facturasPendientes,
                    ];
                }
            }

            return [
                'success' => true,
                'data' => $clientesPendientes
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener clientes pendientes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * AJAX: Close payment reminder modal and update user record
     *
     * @return Response
     */
    public function actionCerrarModalCobros()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (Yii::$app->user->isGuest) {
                throw new \Exception('Usuario no autenticado');
            }

            $usuario = Usuarios::findOne(Yii::$app->user->id);
            
            if (!$usuario) {
                throw new \Exception('Usuario no encontrado');
            }

            $usuario->modalClosed = '1';
            $usuario->dateModalClosed = date('Y-m-d H:i:s');

            if (!$usuario->save(false)) {
                throw new \Exception('Error al guardar la información del usuario');
            }

            return [
                'success' => true,
                'message' => 'Modal cerrado correctamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * AJAX: Reset modal closed status if date has changed
     *
     * @return Response
     */
    public function actionResetModalCobros()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (Yii::$app->user->isGuest) {
                throw new \Exception('Usuario no autenticado');
            }

            $usuario = Usuarios::findOne(Yii::$app->user->id);
            
            if (!$usuario) {
                throw new \Exception('Usuario no encontrado');
            }

            $usuario->modalClosed = '0';
            $usuario->dateModalClosed = null;

            if (!$usuario->save(false)) {
                throw new \Exception('Error al guardar la información del usuario');
            }

            return [
                'success' => true,
                'message' => 'Estado del modal reseteado correctamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Success callback for OAuth authentication
     *
     * @param ClientInterface $client
     * @return Response
     */
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        
        // Para Google, los atributos incluyen: id, email, name, given_name, family_name, picture
        $googleId = $attributes['id'] ?? null;
        $email = $attributes['email'] ?? null;
        $name = $attributes['name'] ?? null;
        $givenName = $attributes['given_name'] ?? null;
        $familyName = $attributes['family_name'] ?? null;
        
        if (!$googleId || !$email) {
            Yii::$app->session->setFlash('error', 'No se pudieron obtener los datos de Google.');
            return $this->redirect(['site/login']);
        }
        
        // Buscar usuario existente por google_id
        $user = Usuarios::findOne(['google_id' => $googleId]);
        
        if ($user) {
            // Usuario existe, hacer login
            Yii::$app->user->login($user);
            Yii::$app->session->setFlash('success', 'Inicio de sesión exitoso con Google.');
            return $this->goHome();
        }
        
        // Buscar usuario existente por email (en caso de que ya tenga cuenta con email/password)
        $user = Usuarios::findOne(['email' => $email]);
        
        if ($user) {
            // Usuario existe pero no tiene google_id, actualizar
            $user->google_id = $googleId;
            $user->google_access_token = $client->getAccessToken()->getToken();
            if ($user->save()) {
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Cuenta vinculada con Google exitosamente.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Error al vincular la cuenta con Google.');
                return $this->redirect(['site/login']);
            }
        }
        
        // Crear nuevo usuario
        // Generar username válido a partir del email (solo la parte antes del @)
        $baseUsername = explode('@', $email)[0];
        // Limpiar el username para que solo contenga caracteres permitidos
        $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '_', $baseUsername);
        // Asegurar que no esté vacío y tenga al menos 2 caracteres
        if (strlen($baseUsername) < 2) {
            $baseUsername = 'user_' . substr(md5($email), 0, 6);
        }
        
        // Verificar que el username sea único, si no, agregar un número
        $username = $baseUsername;
        $counter = 1;
        while (Usuarios::findOne(['username' => $username])) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }
        
        $nombre = $name ?: ($givenName . ' ' . $familyName) ?: $email;
        
        $user = new Usuarios();
        $user->username = $username;
        $user->email = $email;
        $user->nombre = trim($nombre);
        $user->google_id = $googleId;
        $user->google_access_token = $client->getAccessToken()->getToken();
        $user->admin = 0; // Por defecto no es admin
        
        // Generar una contraseña aleatoria (no se usará pero es requerida)
        $user->setPassword(Yii::$app->security->generateRandomString(12));
        
        if ($user->save()) {
            Yii::$app->user->login($user);
            Yii::$app->session->setFlash('success', 'Registro exitoso con Google. ¡Bienvenido!');
            return $this->goHome();
        } else {
            Yii::$app->session->setFlash('error', 'Error al crear la cuenta: ' . implode(', ', $user->getFirstErrors()));
            return $this->redirect(['site/login']);
        }
    }
}
