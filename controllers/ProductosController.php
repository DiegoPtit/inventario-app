<?php

namespace app\controllers;

use app\models\Productos;
use app\models\ProductosSearch;
use app\models\Entradas;
use app\models\Lugares;
use app\models\Stock;
use app\models\HistoricoPreciosDolar;
use app\models\Categorias;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * ProductosController implements the CRUD actions for Productos model.
 */
class ProductosController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Productos models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductosSearch();
        
        // Manejar búsqueda general
        $queryParams = $this->request->queryParams;
        if (isset($queryParams['search']) && !empty($queryParams['search'])) {
            $queryParams['ProductosSearch']['search'] = $queryParams['search'];
        }
        
        $dataProvider = $searchModel->search($queryParams);

        // Get latest dollar prices for conversions
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Obtener valores únicos para los filtros
        $colores = Productos::find()
            ->select('color')
            ->distinct()
            ->where(['IS NOT', 'color', null])
            ->andWhere(['<>', 'color', ''])
            ->orderBy(['color' => SORT_ASC])
            ->column();
        
        $unidadesMedida = Productos::find()
            ->select('unidad_medida')
            ->distinct()
            ->where(['IS NOT', 'unidad_medida', null])
            ->andWhere(['<>', 'unidad_medida', ''])
            ->orderBy(['unidad_medida' => SORT_ASC])
            ->column();
        
        // Obtener categorías
        $categorias = ArrayHelper::map(
            Categorias::find()->orderBy(['titulo' => SORT_ASC])->all(),
            'id',
            'titulo'
        );
        
        // Obtener lugares únicos que tienen stock
        $lugares = ArrayHelper::map(
            Lugares::find()
                ->innerJoin('stock', 'stock.id_lugar = lugares.id')
                ->where(['>', 'stock.cantidad', 0])
                ->groupBy(['lugares.id', 'lugares.nombre'])
                ->orderBy(['lugares.nombre' => SORT_ASC])
                ->all(),
            'id',
            'nombre'
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
            'colores' => $colores,
            'unidadesMedida' => $unidadesMedida,
            'categorias' => $categorias,
            'lugares' => $lugares,
        ]);
    }

    /**
     * Displays a single Productos model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // Get latest dollar prices for conversions
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Creates a new Productos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Productos();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Capturar archivos de imagen solo si se subieron
                $uploadedFiles = UploadedFile::getInstances($model, 'imageFiles');
                if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                    $model->imageFiles = $uploadedFiles;
                } else {
                    // Si no hay imágenes subidas, asignar null para evitar errores
                    $model->imageFiles = null;
                }
                
                // Iniciar una transacción
                $transaction = Yii::$app->db->beginTransaction();
                
                try {
                    // Subir imágenes solo si hay archivos válidos
                    if (!empty($model->imageFiles) && is_array($model->imageFiles)) {
                        $fotosArray = $model->uploadImages();
                        if ($fotosArray !== false && !empty($fotosArray)) {
                            $model->fotos = json_encode($fotosArray);
                        }
                    }
                    
                    // Guardar el producto
                    if (!$model->save()) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Error al guardar el producto.');
                        return $this->render('create', ['model' => $model]);
                    }
                    
                    // Si hay cantidad inicial, crear entrada y actualizar stock
                    if (!empty($model->cantidad) && $model->cantidad > 0 && !empty($model->id_lugar)) {
                        // Crear entrada
                        $entrada = new Entradas();
                        $entrada->id_producto = $model->id;
                        $entrada->cantidad = $model->cantidad;
                        $entrada->id_lugar = $model->id_lugar;
                        $entrada->tipo_entrada = 'Inventario Inicial';
                        $entrada->created_at = date('Y-m-d H:i:s');
                        
                        if (!$entrada->save()) {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Error al crear la entrada de inventario.');
                            return $this->render('create', ['model' => $model]);
                        }
                        
                        // Actualizar o crear stock
                        $stock = Stock::findOne(['id_producto' => $model->id, 'id_lugar' => $model->id_lugar]);
                        if (!$stock) {
                            $stock = new Stock();
                            $stock->id_producto = $model->id;
                            $stock->id_lugar = $model->id_lugar;
                            $stock->cantidad = 0;
                        }
                        $stock->cantidad += $model->cantidad;
                        $stock->updated_at = date('Y-m-d H:i:s');
                        
                        if (!$stock->save()) {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Error al actualizar el stock.');
                            return $this->render('create', ['model' => $model]);
                        }
                    }
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Producto creado exitosamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                    
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Productos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Capturar archivos de imagen solo si se subieron
                $uploadedFiles = UploadedFile::getInstances($model, 'imageFiles');
                if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                    $model->imageFiles = $uploadedFiles;
                    
                    // Subir nuevas imágenes si existen
                    $fotosArray = $model->uploadImages();
                    if ($fotosArray !== false && !empty($fotosArray)) {
                        $model->fotos = json_encode($fotosArray);
                    }
                } else {
                    // Si no hay imágenes subidas, asignar null
                    $model->imageFiles = null;
                }
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Producto actualizado exitosamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing Productos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Procesa carga masiva de productos desde archivo CSV
     * @return array JSON response
     */
    public function actionProcess()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $tempFile = null; // Para rastrear el archivo temporal creado
        
        try {
            // Verificar que se haya enviado un archivo
            $csvFile = UploadedFile::getInstanceByName('csvFile');
            
            if (!$csvFile) {
                return [
                    'success' => false,
                    'message' => 'No se ha seleccionado ningún archivo.'
                ];
            }
            
            // Validar extensión del archivo
            $extension = strtolower($csvFile->getExtension());
            if (!in_array($extension, ['csv', 'txt'])) {
                return [
                    'success' => false,
                    'message' => 'El archivo debe ser un CSV o TXT.'
                ];
            }
            
            // Obtener IDs de lugares (una sola vez al inicio)
            $lugarMilagro = Lugares::findOne(['nombre' => 'EL MILAGRO']);
            $lugarMijagua = Lugares::findOne(['nombre' => 'MIJAGUA']);
            $lugarDSamuel = Lugares::findOne(['nombre' => 'DON SAMUEL']);
            
            if (!$lugarMilagro || !$lugarMijagua || !$lugarDSamuel) {
                return [
                    'success' => false,
                    'message' => 'No se encontraron los lugares requeridos en la base de datos. Verifica que existan: EL MILAGRO, MIJAGUA, DON SAMUEL'
                ];
            }
            
            // Leer archivo CSV
            $filePath = $csvFile->tempName;
            
            // Leer contenido del archivo y detectar codificación
            $fileContent = file_get_contents($filePath);
            
            // Detectar la codificación del archivo
            $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
            
            if ($encoding && $encoding !== 'UTF-8') {
                // Convertir a UTF-8 si no lo está
                $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
                // Guardar el contenido convertido en un archivo temporal
                $tempFile = tempnam(sys_get_temp_dir(), 'csv_utf8_');
                file_put_contents($tempFile, $fileContent);
                $filePath = $tempFile;
            }
            
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                // Limpiar archivo temporal si existe
                if ($tempFile && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
                
                return [
                    'success' => false,
                    'message' => 'No se pudo leer el archivo CSV.'
                ];
            }
            
            // Saltar la primera fila (cabeceras)
            fgetcsv($handle);
            
            $productosCreados = 0;
            $productosActualizados = 0;
            $errores = [];
            
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                $lineNumber = 1; // Para tracking de errores
                
                while (($data = fgetcsv($handle)) !== false) {
                    $lineNumber++;
                    
                    // Validar que la fila tenga 9 columnas
                    if (count($data) < 9) {
                        $errores[] = "Línea $lineNumber: no tiene suficientes columnas.";
                        continue;
                    }
                    
                    // Extraer datos del CSV (ya están en UTF-8)
                    $marca = trim($data[0]);
                    $modelo = trim($data[1]);
                    $descripcion = trim($data[2]);
                    $cont_neto = floatval($data[3]);
                    $mg = trim($data[4]);
                    $mijagua_qty = intval($data[5]);
                    $milagro_qty = intval($data[6]);
                    $dsamuel_qty = intval($data[7]);
                    $costo = floatval($data[8]);
                    
                    // Validaciones básicas
                    if (empty($marca) || empty($modelo)) {
                        $errores[] = "Línea $lineNumber: marca y modelo son requeridos.";
                        continue;
                    }
                    
                    // Verificar si el producto ya existe
                    $producto = Productos::findOne([
                        'marca' => $marca,
                        'modelo' => $modelo
                    ]);
                    
                    if (!$producto) {
                        // Crear nuevo producto
                        $producto = new Productos();
                        $producto->marca = $marca;
                        $producto->modelo = $modelo;
                        $producto->descripcion = $descripcion;
                        $producto->contenido_neto = $cont_neto;
                        $producto->unidad_medida = $mg;
                        $producto->costo = $costo;
                        $producto->precio_venta = $costo * 2;
                        $producto->id_lugar = 1;
                        $producto->id_categoria = null;
                        
                        if (!$producto->save()) {
                            $errores[] = "Línea $lineNumber: error al crear producto - " . print_r($producto->getErrors(), true);
                            continue;
                        }
                        
                        $productosCreados++;
                    } else {
                        $productosActualizados++;
                    }
                    
                    $id_producto = $producto->id;
                    
                    // Gestionar stock en las 3 ubicaciones
                    $ubicaciones = [
                        ['id_lugar' => $lugarMilagro->id, 'cantidad' => $milagro_qty, 'nombre' => 'EL MILAGRO'],
                        ['id_lugar' => $lugarMijagua->id, 'cantidad' => $mijagua_qty, 'nombre' => 'MIJAGUA'],
                        ['id_lugar' => $lugarDSamuel->id, 'cantidad' => $dsamuel_qty, 'nombre' => 'DON SAMUEL'],
                    ];
                    
                    foreach ($ubicaciones as $ubicacion) {
                        if ($ubicacion['cantidad'] > 0) {
                            // Buscar o crear registro de stock
                            $stock = Stock::findOne([
                                'id_producto' => $id_producto,
                                'id_lugar' => $ubicacion['id_lugar']
                            ]);
                            
                            if (!$stock) {
                                $stock = new Stock();
                                $stock->id_producto = $id_producto;
                                $stock->id_lugar = $ubicacion['id_lugar'];
                                $stock->cantidad = 0;
                            }
                            
                            $stock->cantidad += $ubicacion['cantidad'];
                            $stock->updated_at = date('Y-m-d H:i:s');
                            
                            if (!$stock->save()) {
                                $errores[] = "Línea $lineNumber: error al actualizar stock en {$ubicacion['nombre']} - " . print_r($stock->getErrors(), true);
                                continue 2; // Saltar al siguiente producto
                            }
                            
                            // Crear registro de entrada
                            $entrada = new Entradas();
                            $entrada->id_producto = $id_producto;
                            $entrada->cantidad = $ubicacion['cantidad'];
                            $entrada->id_lugar = $ubicacion['id_lugar'];
                            $entrada->tipo_entrada = 'Inventario Inicial'; // Valor por defecto para carga masiva
                            $entrada->nro_documento = null;
                            $entrada->id_proveedor = null;
                            $entrada->ruta_documento_respaldo = null;
                            $entrada->created_at = date('Y-m-d H:i:s');
                            
                            if (!$entrada->save()) {
                                $errores[] = "Línea $lineNumber: error al crear entrada en {$ubicacion['nombre']} - " . print_r($entrada->getErrors(), true);
                                continue 2;
                            }
                        }
                    }
                }
                
                fclose($handle);
                
                // Limpiar archivo temporal si existe
                if ($tempFile && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
                
                // Si hay errores, hacer rollback
                if (!empty($errores)) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => 'Se encontraron errores durante el proceso.',
                        'errores' => $errores,
                        'productosCreados' => 0,
                        'productosActualizados' => 0
                    ];
                }
                
                $transaction->commit();
                
                return [
                    'success' => true,
                    'message' => 'Carga masiva completada exitosamente.',
                    'productosCreados' => $productosCreados,
                    'productosActualizados' => $productosActualizados
                ];
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                fclose($handle);
                
                // Limpiar archivo temporal si existe
                if ($tempFile && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
                
                return [
                    'success' => false,
                    'message' => 'Error durante el procesamiento: ' . $e->getMessage()
                ];
            }
            
        } catch (\Exception $e) {
            // Limpiar archivo temporal si existe
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            return [
                'success' => false,
                'message' => 'Error general: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Genera un reporte de inventario
     * @return string
     */
    public function actionReporte()
    {
        $tipo = Yii::$app->request->get('tipo', 'general');
        $idLugar = Yii::$app->request->get('id_lugar');
        
        // Configurar el layout para la vista de impresión
        $this->layout = false;
        
        // Obtener precios del dólar para conversiones
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        // Preparar datos del reporte según el tipo
        $datosReporte = [];
        
        if ($tipo === 'pasivos') {
            // Reporte de Pasivos - Facturas con pagos pendientes
            
            // Función helper para convertir entre monedas
            $convertCurrency = function($amount, $fromCurrency, $toCurrency) use ($precioParalelo, $precioOficial) {
                if (!$precioParalelo || !$precioOficial) {
                    return $amount;
                }
                
                $value = floatval($amount);
                
                if ($fromCurrency === $toCurrency) {
                    return $value;
                }
                
                // Convertir a VES primero
                $amountInVES = 0;
                if ($fromCurrency === 'USDT') {
                    $amountInVES = $value * $precioParalelo->precio_ves;
                } elseif ($fromCurrency === 'BCV') {
                    $amountInVES = $value * $precioOficial->precio_ves;
                } elseif ($fromCurrency === 'VES') {
                    $amountInVES = $value;
                }
                
                // Convertir de VES a moneda destino
                if ($toCurrency === 'USDT') {
                    return $amountInVES / $precioParalelo->precio_ves;
                } elseif ($toCurrency === 'BCV') {
                    return $amountInVES / $precioOficial->precio_ves;
                } elseif ($toCurrency === 'VES') {
                    return $amountInVES;
                }
                
                return $value;
            };
            
            // Obtener todas las facturas con sus relaciones
            $facturas = \app\models\Facturas::find()
                ->with(['cliente', 'itemsFacturas.producto', 'historicoCobros'])
                ->orderBy(['fecha' => SORT_DESC])
                ->all();
            
            // Filtrar solo facturas con pagos pendientes
            foreach ($facturas as $factura) {
                // Calcular total cobrado de esta factura - convertir cada cobro a la moneda de la factura
                $totalCobrado = 0;
                foreach ($factura->historicoCobros as $cobro) {
                    $totalCobrado += $convertCurrency($cobro->monto, $cobro->currency, $factura->currency);
                }
                
                // Aplicar round para evitar problemas de precisión
                $totalCobrado = round($totalCobrado, 2);
                
                // Si el total cobrado es menor al monto final, la factura está pendiente
                if ($totalCobrado < $factura->monto_final) {
                    $montoPendiente = $factura->monto_final - $totalCobrado;
                    
                    // Preparar lista de productos de esta factura
                    $productos = [];
                    foreach ($factura->itemsFacturas as $item) {
                        if ($item->producto) {
                            $productos[] = [
                                'producto' => $item->producto,
                                'cantidad' => $item->cantidad,
                                'precio_unitario' => $item->precio_unitario,
                                'subtotal' => $item->subtotal,
                            ];
                        }
                    }
                    
                    // Agregar factura al reporte
                    $datosReporte[] = [
                        'cliente' => $factura->cliente,
                        'factura' => $factura,
                        'productos' => $productos,
                        'monto_factura' => $factura->monto_final,
                        'monto_cobrado' => $totalCobrado,
                        'monto_pendiente' => $montoPendiente,
                        'currency' => $factura->currency,
                    ];
                }
            }
        } else {
            // Reporte de inventario (lógica existente)
            
            // Obtener los datos según el tipo de reporte
            if ($tipo === 'por-lugar' && !empty($idLugar)) {
                // Reporte por lugar específico
                $lugares = [\app\models\Lugares::findOne($idLugar)];
                if (!$lugares[0]) {
                    throw new NotFoundHttpException('El almacén no existe.');
                }
            } else {
                // Reporte general (todos los lugares)
                $lugares = \app\models\Lugares::find()->orderBy(['nombre' => SORT_ASC])->all();
            }
            
            // Preparar datos del reporte agrupados por lugar
            
            foreach ($lugares as $lugar) {
                // Obtener productos que tienen stock en este lugar
                $stocks = Stock::find()
                    ->where(['id_lugar' => $lugar->id])
                    ->andWhere(['>', 'cantidad', 0])
                    ->with('producto')
                    ->all();
                
                if (empty($stocks)) {
                    continue; // Saltar lugares sin stock
                }
                
                $productos = [];
                $totalCosto = 0;
                $totalPrecioVenta = 0;
                $totalCantidad = 0;
                
                foreach ($stocks as $stock) {
                    $producto = $stock->producto;
                    if (!$producto) {
                        continue;
                    }
                    
                    $productos[] = [
                        'marca' => $producto->marca,
                        'modelo' => $producto->modelo,
                        'color' => $producto->color,
                        'contenido_neto' => $producto->contenido_neto,
                        'unidad_medida' => $producto->unidad_medida,
                        'costo' => $producto->costo,
                        'precio_venta' => $producto->precio_venta,
                        'cantidad' => $stock->cantidad,
                        'subtotal_costo' => $producto->costo * $stock->cantidad,
                        'subtotal_venta' => $producto->precio_venta * $stock->cantidad,
                    ];
                    
                    $totalCosto += $producto->costo * $stock->cantidad;
                    $totalPrecioVenta += $producto->precio_venta * $stock->cantidad;
                    $totalCantidad += $stock->cantidad;
                }
                
                $datosReporte[] = [
                    'lugar' => $lugar,
                    'productos' => $productos,
                    'total_costo' => $totalCosto,
                    'total_precio_venta' => $totalPrecioVenta,
                    'total_cantidad' => $totalCantidad,
                ];
            }
        }
        
        return $this->render('reporte', [
            'datosReporte' => $datosReporte,
            'tipo' => $tipo,
            'fecha' => date('d/m/Y H:i:s'),
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Retorna lista de todos los productos con stock total (para modals)
     * @return array JSON response
     */
    public function actionListaTodos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $productos = Productos::find()->all();
        $result = [];
        
        foreach ($productos as $producto) {
            // Calcular stock total
            $stockTotal = Stock::find()
                ->where(['id_producto' => $producto->id])
                ->sum('cantidad');
            
            $result[] = [
                'id' => $producto->id,
                'marca' => $producto->marca,
                'modelo' => $producto->modelo,
                'descripcion' => $producto->descripcion,
                'precio_venta' => $producto->precio_venta,
                'stock_total' => $stockTotal ?: 0,
            ];
        }
        
        return $result;
    }

    /**
     * Retorna lista de productos con stock por ubicación (para modals de salidas)
     * @return array JSON response
     */
    public function actionListaConStock()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $stocks = Stock::find()
            ->where(['>', 'cantidad', 0])
            ->with(['producto', 'lugar'])
            ->all();
        
        // Agrupar por producto
        $productosMap = [];
        
        foreach ($stocks as $stock) {
            $producto = $stock->producto;
            $lugar = $stock->lugar;
            
            if (!$producto || !$lugar) {
                continue;
            }
            
            if (!isset($productosMap[$producto->id])) {
                $productosMap[$producto->id] = [
                    'id' => $producto->id,
                    'marca' => $producto->marca,
                    'modelo' => $producto->modelo,
                    'descripcion' => $producto->descripcion,
                    'costo' => $producto->costo,
                    'precio_venta' => $producto->precio_venta,
                    'stocks' => [],
                ];
            }
            
            $productosMap[$producto->id]['stocks'][] = [
                'id' => $stock->id,
                'id_lugar' => $stock->id_lugar,
                'lugar_nombre' => $lugar->nombre,
                'cantidad' => $stock->cantidad,
            ];
        }
        
        return array_values($productosMap);
    }

    /**
     * Lista productos sin código de barras para etiquetar
     * @return string
     */
    public function actionProductosEtiquetar()
    {
        // Desactivar layout (vista para impresión)
        $this->layout = false;
        
        // Encontrar el código de barras más largo y mayor numéricamente
        $maxBarcode = Productos::find()
            ->select('codigo_barra')
            ->where(['IS NOT', 'codigo_barra', null])
            ->andWhere(['<>', 'codigo_barra', ''])
            ->orderBy(['LENGTH(codigo_barra)' => SORT_DESC])
            ->limit(1)
            ->scalar();
        
        // Determinar el patrón base para generar códigos
        $barcodeLength = $maxBarcode ? strlen($maxBarcode) : 13; // EAN-13 por defecto
        $nextBarcodeNumber = $maxBarcode ? intval($maxBarcode) + 1 : 1000000000000;
        
        // Obtener todos los productos sin código de barras
        $productosSinCodigo = Productos::find()
            ->where(['OR',
                ['codigo_barra' => null],
                ['codigo_barra' => '']
            ])
            ->with('stocks')
            ->orderBy(['id' => SORT_ASC])
            ->all();
        
        // Verificar si hay productos sin código
        $hayProductosSinCodigo = !empty($productosSinCodigo);
        
        // Preparar lista de productos con códigos generados o existentes
        $productosParaEtiquetar = [];
        $codigosGeneradosMap = []; // Para guardar en BD luego
        
        if ($hayProductosSinCodigo) {
            // HAY PRODUCTOS SIN CÓDIGO - Generar códigos y repetir según stock
            foreach ($productosSinCodigo as $producto) {
                // Calcular stock total del producto
                $stockTotal = 0;
                foreach ($producto->stocks as $stock) {
                    $stockTotal += $stock->cantidad;
                }
                
                // Generar UN SOLO código para este producto (compartido por todas las unidades)
                $codigoGenerado = str_pad($nextBarcodeNumber, $barcodeLength, '0', STR_PAD_LEFT);
                
                // Guardar para luego insertar en BD
                $codigosGeneradosMap[$producto->id] = $codigoGenerado;
                
                // Repetir el producto según su stock (pero con el MISMO código)
                for ($i = 0; $i < $stockTotal; $i++) {
                    $productosParaEtiquetar[] = [
                        'producto' => $producto,
                        'codigo_barra_generado' => $codigoGenerado,
                        'stock_total' => $stockTotal,
                    ];
                }
                
                // Incrementar solo cuando cambiamos de producto
                $nextBarcodeNumber++;
            }
        } else {
            // TODOS LOS PRODUCTOS TIENEN CÓDIGO - Mostrar códigos existentes sin repetir
            $todosLosProductos = Productos::find()
                ->where(['IS NOT', 'codigo_barra', null])
                ->andWhere(['<>', 'codigo_barra', ''])
                ->with('stocks')
                ->orderBy(['codigo_barra' => SORT_ASC])
                ->all();
            
            foreach ($todosLosProductos as $producto) {
                // Calcular stock total
                $stockTotal = 0;
                foreach ($producto->stocks as $stock) {
                    $stockTotal += $stock->cantidad;
                }
                
                // Agregar UNA SOLA VEZ (sin repetir)
                $productosParaEtiquetar[] = [
                    'producto' => $producto,
                    'codigo_barra_generado' => $producto->codigo_barra,
                    'stock_total' => $stockTotal,
                ];
            }
        }
        
        return $this->render('productos-etiquetar', [
            'productosParaEtiquetar' => $productosParaEtiquetar,
            'barcodeLength' => $barcodeLength,
            'hayProductosSinCodigo' => $hayProductosSinCodigo,
            'codigosGeneradosMap' => $codigosGeneradosMap,
        ]);
    }
    
    /**
     * Guarda los códigos de barras generados en la base de datos
     * @return \yii\web\Response
     */
    public function actionGuardarCodigosBarras()
    {
        $codigosJson = Yii::$app->request->post('codigos');
        
        if (!$codigosJson) {
            Yii::$app->session->setFlash('error', 'No se recibieron códigos para guardar.');
            return $this->redirect(['productos-etiquetar']);
        }
        
        $codigos = json_decode($codigosJson, true);
        
        if (!is_array($codigos) || empty($codigos)) {
            Yii::$app->session->setFlash('error', 'Formato de códigos inválido.');
            return $this->redirect(['productos-etiquetar']);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $actualizados = 0;
            
            foreach ($codigos as $idProducto => $codigoBarra) {
                $producto = Productos::findOne($idProducto);
                
                if ($producto && (empty($producto->codigo_barra) || $producto->codigo_barra === null)) {
                    $producto->codigo_barra = $codigoBarra;
                    
                    if ($producto->save(false)) {
                        $actualizados++;
                    }
                }
            }
            
            $transaction->commit();
            
            Yii::$app->session->setFlash('success', "Se guardaron exitosamente $actualizados códigos de barras en la base de datos.");
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Error al guardar los códigos: ' . $e->getMessage());
        }
        
        return $this->redirect(['productos-etiquetar']);
    }

    /**

     * Finds the Productos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Productos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Productos::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
