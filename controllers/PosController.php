<?php

namespace app\controllers;

use app\models\Clientes;
use app\models\Facturas;
use app\models\Productos;
use app\models\Stock;
use app\models\Lugares;
use app\models\ItemsFactura;
use app\models\HistoricoMovimientos;
use app\models\HistoricoCobros;
use app\models\Entradas;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * PosController implements the POS/Invoice actions.
 */
class PosController extends Controller
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
                        'process-invoice' => ['POST'],
                        'update-invoice' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Displays the main POS interface.
     *
     * @return string
     */
    public function actionIndex()
    {
        // Get latest dollar prices for conversions
        $precioOficial = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        return $this->render('index', [
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * Displays the POS interface in edit mode for an existing invoice.
     *
     * @param int $id Invoice ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEdit($id)
    {
        $factura = Facturas::findOne($id);
        
        if ($factura === null) {
            throw new NotFoundHttpException('La factura solicitada no existe.');
        }
        
        // Get latest dollar prices for conversions
        $precioOficial = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        $precioParalelo = \app\models\HistoricoPreciosDolar::find()
            ->where(['tipo' => \app\models\HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        return $this->render('index', [
            'editMode' => true,
            'facturaId' => $id,
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }

    /**
     * AJAX: Get client details by ID
     * 
     * @return array
     */
    public function actionGetClientDetails($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $cliente = Clientes::findOne($id);
        
        if ($cliente !== null) {
            return [
                'success' => true,
                'data' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'documento_identidad' => $cliente->documento_identidad,
                    'ubicacion' => $cliente->ubicacion,
                    'telefono' => $cliente->telefono,
                    'edad' => $cliente->edad,
                    'status' => $cliente->displayStatus(),
                ]
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Cliente no encontrado'
        ];
    }

    /**
     * AJAX: Generate next invoice code
     * 
     * @return array
     */
    public function actionGenerateInvoiceCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Get the last invoice with HVF- prefix
        $lastFactura = Facturas::find()
            ->where(['like', 'codigo', 'HVF-%', false])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        
        $nextNumber = 1;
        
        if ($lastFactura !== null) {
            // Extract the number from the last code
            $lastCode = $lastFactura->codigo;
            preg_match('/HVF-(\d+)/', $lastCode, $matches);
            
            if (isset($matches[1])) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }
        
        // Format the number with leading zeros (001, 002, etc.)
        $codigo = 'HVF-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return [
            'success' => true,
            'codigo' => $codigo
        ];
    }

    /**
     * AJAX: Get all clients for dropdown
     * 
     * @return array
     */
    public function actionGetClients()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $clientes = Clientes::find()
            ->select(['id', 'nombre', 'documento_identidad'])
            ->orderBy(['nombre' => SORT_ASC])
            ->asArray()
            ->all();
        
        return [
            'success' => true,
            'data' => $clientes
        ];
    }

    /**
     * AJAX: Get products with stock information grouped by location
     * 
     * @return array
     */
    public function actionGetProducts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Get all stock records with product and location info
        $stocks = Stock::find()
            ->with(['producto', 'lugar'])
            ->where(['>', 'cantidad', 0])
            ->all();
        
        $productos = [];
        
        foreach ($stocks as $stock) {
            $producto = $stock->producto;
            $lugar = $stock->lugar;
            
            if ($producto && $lugar) {
                $productos[] = [
                    'id_producto' => $producto->id,
                    'id_lugar' => $lugar->id,
                    'stock_id' => $stock->id,
                    'nombre' => $producto->marca . ' ' . $producto->modelo . ' - ' . $producto->descripcion,
                    'lugar_nombre' => $lugar->nombre,
                    'stock_disponible' => $stock->cantidad,
                    'costo' => $producto->costo,
                    'precio_venta' => $producto->precio_venta,
                    'display_name' => $producto->marca . ' ' . $producto->modelo . ' (' . $lugar->nombre . ') - Stock: ' . $stock->cantidad
                ];
            }
        }
        
        return [
            'success' => true,
            'data' => $productos
        ];
    }

    /**
     * AJAX: Get product by barcode with stock information
     * 
     * @param string $codigo Barcode to search for
     * @return array
     */
    public function actionGetProductoByBarcode($codigo)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Search for product by barcode
        $producto = Productos::findOne(['codigo_barra' => $codigo]);
        
        if (!$producto) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        }
        
        // Get all stocks for this product in different locations
        $stocks = Stock::find()
            ->where(['id_producto' => $producto->id])
            ->with(['lugar'])
            ->all();
        
        $stockTotal = 0;
        $stocksDisponibles = [];
        
        foreach ($stocks as $stock) {
            if ($stock->cantidad > 0) {
                $stockTotal += $stock->cantidad;
                $stocksDisponibles[] = [
                    'id' => $stock->id,
                    'id_lugar' => $stock->id_lugar,
                    'lugar_nombre' => $stock->lugar->nombre ?? 'N/A',
                    'cantidad' => $stock->cantidad
                ];
            }
        }
        
        // Get photo URL if exists
        $fotoUrl = null;
        if ($producto->fotos && file_exists(Yii::getAlias('@webroot') . '/' . $producto->fotos)) {
            $fotoUrl = Yii::getAlias('@web') . '/' . $producto->fotos;
        }
        
        return [
            'success' => true,
            'data' => [
                'id' => $producto->id,
                'marca' => $producto->marca,
                'modelo' => $producto->modelo,
                'descripcion' => $producto->descripcion,
                'costo' => $producto->costo,
                'precio_venta' => $producto->precio_venta,
                'codigo_barra' => $producto->codigo_barra,
                'foto_url' => $fotoUrl,
                'stock_total' => $stockTotal,
                'stocks_disponibles' => $stocksDisponibles
            ]
        ];
    }

    /**
     * AJAX: Get invoice data for editing
     * 
     * @return array
     */
    public function actionGetInvoiceData($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $factura = Facturas::findOne($id);
        
        if ($factura === null) {
            return [
                'success' => false,
                'message' => 'Factura no encontrada'
            ];
        }
        
        // Get invoice items with stock information
        $items = [];
        $itemsFactura = ItemsFactura::find()
            ->where(['id_factura' => $id])
            ->with(['producto'])
            ->all();
        
        foreach ($itemsFactura as $itemFactura) {
            $producto = $itemFactura->producto;
            
            if ($producto) {
                // Find all available stock for this product
                $stocks = Stock::find()
                    ->where(['id_producto' => $producto->id])
                    ->with(['lugar'])
                    ->all();
                
                // We need to find which stock location was used originally
                // For simplicity, we'll use the first available stock location
                $stockInfo = null;
                foreach ($stocks as $stock) {
                    if ($stock->cantidad > 0 || true) { // Include even empty stocks for context
                        $stockInfo = [
                            'id_lugar' => $stock->id_lugar,
                            'lugar_nombre' => $stock->lugar ? $stock->lugar->nombre : 'N/A',
                            'stock_disponible' => $stock->cantidad
                        ];
                        break;
                    }
                }
                
                $items[] = [
                    'item_id' => $itemFactura->id,
                    'id_producto' => $producto->id,
                    'id_lugar' => $stockInfo ? $stockInfo['id_lugar'] : null,
                    'cantidad' => $itemFactura->cantidad,
                    'precio_unitario' => $itemFactura->precio_unitario,
                    'subtotal' => $itemFactura->subtotal,
                    'producto_nombre' => $producto->marca . ' ' . $producto->modelo,
                    'stock_disponible' => $stockInfo ? $stockInfo['stock_disponible'] : 0,
                    'lugar_nombre' => $stockInfo ? $stockInfo['lugar_nombre'] : 'N/A'
                ];
            }
        }
        
        return [
            'success' => true,
            'data' => [
                'id' => $factura->id,
                'codigo' => $factura->codigo,
                'concepto' => $factura->concepto,
                'fecha' => $factura->fecha,
                'id_cliente' => $factura->id_cliente,
                'monto_calculado' => $factura->monto_calculado,
                'monto_final' => $factura->monto_final,
                'items' => $items
            ]
        ];
    }

    /**
     * AJAX: Process and save the invoice
     * 
     * @return array
     */
    public function actionProcessInvoice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $postData = Yii::$app->request->post();
            
            // Validate required data
            if (!isset($postData['codigo']) || !isset($postData['items']) || empty($postData['items'])) {
                throw new \Exception('Datos incompletos. Se requiere código e items.');
            }
            
            // Create the invoice
            $factura = new Facturas();
            $factura->id_cliente = $postData['id_cliente'] ?? null;
            $factura->codigo = $postData['codigo'];
            $factura->concepto = $postData['concepto'] ?? null;
            $factura->fecha = $postData['fecha'] ?? date('Y-m-d');
            $factura->monto_calculado = $postData['subtotal'] ?? 0;
            $factura->monto_final = $postData['monto_final'] ?? 0;
            
            // Set currency based on user selection
            $currency = $postData['currency'] ?? 'USDT'; // Default to USDT
            if ($currency === 'BCV') {
                $factura->setCurrencyToBCV();
            } elseif ($currency === 'VES') {
                $factura->setCurrencyToVES();
            } else {
                $factura->setCurrencyToUSDT();
            }
            
            if (!$factura->save()) {
                throw new \Exception('Error al crear la factura: ' . json_encode($factura->errors));
            }
            
            // Create invoice items and register movements
            foreach ($postData['items'] as $item) {
                $itemFactura = new ItemsFactura();
                $itemFactura->id_factura = $factura->id;
                $itemFactura->id_producto = $item['id_producto'];
                $itemFactura->cantidad = $item['cantidad'];
                $itemFactura->precio_unitario = $item['precio_unitario'];
                $itemFactura->subtotal = $item['cantidad'] * $item['precio_unitario'];
                
                if (!$itemFactura->save()) {
                    throw new \Exception('Error al crear item de factura: ' . json_encode($itemFactura->errors));
                }
                
                // Update stock
                $stock = Stock::findOne([
                    'id_producto' => $item['id_producto'],
                    'id_lugar' => $item['id_lugar']
                ]);
                
                if ($stock) {
                    $stock->cantidad -= $item['cantidad'];
                    if (!$stock->save()) {
                        throw new \Exception('Error al actualizar stock: ' . json_encode($stock->errors));
                    }
                }
                
                // Register in HistoricoMovimientos with action 'VENTA'
                $movimiento = new HistoricoMovimientos();
                $movimiento->id_producto = $item['id_producto'];
                $movimiento->accion = HistoricoMovimientos::ACCION_VENTA;
                $movimiento->id_lugar_origen = $item['id_lugar'];
                $movimiento->id_lugar_destino = null;
                $movimiento->cantidad = $item['cantidad'];
                $movimiento->referencia_id = $factura->id;
                
                if (!$movimiento->save()) {
                    throw new \Exception('Error al registrar movimiento: ' . json_encode($movimiento->errors));
                }
            }
            
            // Update client status to 'Moroso' if client exists
            if (!empty($postData['id_cliente'])) {
                $cliente = Clientes::findOne($postData['id_cliente']);
                if ($cliente) {
                    $cliente->setStatusToMoroso();
                    if (!$cliente->save()) {
                        throw new \Exception('Error al actualizar status del cliente: ' . json_encode($cliente->errors));
                    }
                }
                
                // Create HistoricoCobros record with pending payment
                $cobro = new HistoricoCobros();
                $cobro->id_cliente = $postData['id_cliente'];
                $cobro->id_factura = $factura->id;
                $cobro->fecha = date('Y-m-d');
                $cobro->monto = 0;
                $cobro->metodo_pago = 'POR PAGAR';
                $cobro->nota = 'USUARIO FALTA POR PAGAR';
                
                // Set currency to match the invoice currency
                $cobro->currency = $factura->currency;
                
                if (!$cobro->save()) {
                    throw new \Exception('Error al crear registro de cobro: ' . json_encode($cobro->errors));
                }
            }
            
            $transaction->commit();
            
            return [
                'success' => true,
                'message' => 'Factura procesada exitosamente',
                'factura_id' => $factura->id
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * AJAX: Update an existing invoice
     * 
     * @return array
     */
    public function actionUpdateInvoice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $postData = Yii::$app->request->post();
            
            // Validate required data
            if (!isset($postData['factura_id']) || !isset($postData['items'])) {
                throw new \Exception('Datos incompletos. Se requiere ID de factura e items.');
            }
            
            // Find the invoice
            $factura = Facturas::findOne($postData['factura_id']);
            
            if ($factura === null) {
                throw new \Exception('Factura no encontrada.');
            }
            
            // Get original items before modification
            $originalItems = ItemsFactura::find()
                ->where(['id_factura' => $factura->id])
                ->indexBy('id')
                ->all();
            
            // Store original items for stock restoration
            $originalItemsData = [];
            foreach ($originalItems as $originalItem) {
                // Buscar el lugar donde estaba almacenado este producto
                $stock = Stock::find()
                    ->where(['id_producto' => $originalItem->id_producto])
                    ->one();
                
                $originalItemsData[$originalItem->id] = [
                    'id_producto' => $originalItem->id_producto,
                    'cantidad' => $originalItem->cantidad,
                    'id_lugar' => $stock ? $stock->id_lugar : null,
                ];
            }
            
            // Process new items
            $newItemsIds = [];
            $newSubtotal = 0;
            
            foreach ($postData['items'] as $item) {
                $itemId = $item['item_id'] ?? null;
                
                if ($itemId && isset($originalItems[$itemId])) {
                    // Update existing item
                    $itemFactura = $originalItems[$itemId];
                    $oldCantidad = $itemFactura->cantidad;
                    $oldProducto = $itemFactura->id_producto;
                    $newCantidad = $item['cantidad'];
                    $newProducto = $item['id_producto'];
                    
                    // Find the stock location for the old product
                    $oldStock = Stock::find()
                        ->where(['id_producto' => $oldProducto])
                        ->one();
                    
                    $oldLugar = $oldStock ? $oldStock->id_lugar : null;
                    
                    // Restore old stock
                    if ($oldStock) {
                        $oldStock->cantidad += $oldCantidad;
                        if (!$oldStock->save()) {
                            throw new \Exception('Error al restaurar stock anterior: ' . json_encode($oldStock->errors));
                        }
                    }
                    
                    // Si cambió el producto o se redujo la cantidad, registrar devolución
                    if ($oldProducto != $newProducto && $oldLugar) {
                        // Producto completamente diferente - devolver toda la cantidad anterior
                        $this->registrarDevolucion($oldProducto, $oldLugar, $oldCantidad, $factura->id, 'Devolución por cambio de producto en factura');
                    } elseif ($oldProducto == $newProducto && $oldCantidad > $newCantidad && $oldLugar) {
                        // Mismo producto pero cantidad reducida - devolver la diferencia
                        $cantidadDevuelta = $oldCantidad - $newCantidad;
                        $this->registrarDevolucion($oldProducto, $oldLugar, $cantidadDevuelta, $factura->id, 'Devolución por reducción de cantidad en factura');
                    }
                    
                    // Update item
                    $itemFactura->id_producto = $item['id_producto'];
                    $itemFactura->cantidad = $item['cantidad'];
                    $itemFactura->precio_unitario = $item['precio_unitario'];
                    $itemFactura->subtotal = $item['cantidad'] * $item['precio_unitario'];
                    
                    if (!$itemFactura->save()) {
                        throw new \Exception('Error al actualizar item: ' . json_encode($itemFactura->errors));
                    }
                    
                    $newItemsIds[] = $itemId;
                    
                } else {
                    // Create new item
                    $itemFactura = new ItemsFactura();
                    $itemFactura->id_factura = $factura->id;
                    $itemFactura->id_producto = $item['id_producto'];
                    $itemFactura->cantidad = $item['cantidad'];
                    $itemFactura->precio_unitario = $item['precio_unitario'];
                    $itemFactura->subtotal = $item['cantidad'] * $item['precio_unitario'];
                    
                    if (!$itemFactura->save()) {
                        throw new \Exception('Error al crear nuevo item: ' . json_encode($itemFactura->errors));
                    }
                    
                    $newItemsIds[] = $itemFactura->id;
                }
                
                // Update stock for the new/updated item
                $stock = Stock::findOne([
                    'id_producto' => $item['id_producto'],
                    'id_lugar' => $item['id_lugar']
                ]);
                
                if ($stock) {
                    $stock->cantidad -= $item['cantidad'];
                    if (!$stock->save()) {
                        throw new \Exception('Error al actualizar stock: ' . json_encode($stock->errors));
                    }
                }
                
                $newSubtotal += $item['cantidad'] * $item['precio_unitario'];
            }
            
            // Delete removed items and restore their stock
            foreach ($originalItems as $itemId => $originalItem) {
                if (!in_array($itemId, $newItemsIds)) {
                    // Restore stock for deleted item
                    $stock = Stock::find()
                        ->where(['id_producto' => $originalItem->id_producto])
                        ->one();
                    
                    $idLugar = null;
                    if ($stock) {
                        $idLugar = $stock->id_lugar;
                        $stock->cantidad += $originalItem->cantidad;
                        if (!$stock->save()) {
                            throw new \Exception('Error al restaurar stock de item eliminado: ' . json_encode($stock->errors));
                        }
                    }
                    
                    // Registrar devolución del item eliminado
                    if ($idLugar) {
                        $this->registrarDevolucion(
                            $originalItem->id_producto, 
                            $idLugar, 
                            $originalItem->cantidad, 
                            $factura->id, 
                            'Devolución por eliminación de producto de factura'
                        );
                    }
                    
                    // Delete the item
                    if (!$originalItem->delete()) {
                        throw new \Exception('Error al eliminar item: ' . json_encode($originalItem->errors));
                    }
                }
            }
            
            // Update invoice totals
            $montoFinalAnterior = $factura->monto_final;
            $factura->monto_calculado = $postData['subtotal'] ?? $newSubtotal;
            $factura->monto_final = $postData['monto_final'] ?? 0;
            
            if (!$factura->save()) {
                throw new \Exception('Error al actualizar la factura: ' . json_encode($factura->errors));
            }
            
            // Ajustar HistoricoCobros si hay un cliente asociado
            if ($factura->id_cliente) {
                // Calcular el total de cobros actuales para esta factura
                $totalCobrado = HistoricoCobros::find()
                    ->where(['id_factura' => $factura->id])
                    ->sum('monto');
                
                $totalCobrado = $totalCobrado ?? 0;
                
                // CASO 1: El nuevo monto_final es MAYOR que los cobros actuales
                if ($factura->monto_final > $totalCobrado) {
                    // Cliente pasa a Moroso porque ahora debe más
                    $cliente = Clientes::findOne($factura->id_cliente);
                    if ($cliente) {
                        $cliente->setStatusToMoroso();
                        if (!$cliente->save()) {
                            throw new \Exception('Error al actualizar status del cliente: ' . json_encode($cliente->errors));
                        }
                    }
                    
                    // Verificar si ya existe un registro de cobro pendiente (monto = 0)
                    $cobroPendiente = HistoricoCobros::find()
                        ->where([
                            'id_factura' => $factura->id,
                            'id_cliente' => $factura->id_cliente,
                            'monto' => 0
                        ])
                        ->one();
                    
                    // Si no existe, crear uno nuevo
                    if (!$cobroPendiente) {
                        $nuevoCobro = new HistoricoCobros();
                        $nuevoCobro->id_cliente = $factura->id_cliente;
                        $nuevoCobro->id_factura = $factura->id;
                        $nuevoCobro->fecha = date('Y-m-d');
                        $nuevoCobro->monto = 0;
                        $nuevoCobro->metodo_pago = 'POR PAGAR';
                        $nuevoCobro->nota = 'Saldo pendiente por edición de factura';
                        
                        if (!$nuevoCobro->save()) {
                            throw new \Exception('Error al crear registro de cobro pendiente: ' . json_encode($nuevoCobro->errors));
                        }
                    }
                }
                // CASO 2: El nuevo monto_final es MENOR que los cobros actuales (pagó de más)
                elseif ($factura->monto_final < $totalCobrado) {
                    $excedente = $totalCobrado - $factura->monto_final;
                    
                    // Obtener todos los cobros de esta factura, ordenados del más reciente al más antiguo
                    $cobros = HistoricoCobros::find()
                        ->where(['id_factura' => $factura->id])
                        ->orderBy(['id' => SORT_DESC])
                        ->all();
                    
                    // Ajustar cobros desde el más reciente
                    $excedentePorAjustar = $excedente;
                    
                    foreach ($cobros as $cobro) {
                        if ($excedentePorAjustar <= 0) {
                            break;
                        }
                        
                        if ($cobro->monto == 0) {
                            // Eliminar registros con monto 0 (cobros pendientes)
                            $cobro->delete();
                            continue;
                        }
                        
                        if ($cobro->monto <= $excedentePorAjustar) {
                            // Este cobro completo es excedente, eliminarlo
                            $excedentePorAjustar -= $cobro->monto;
                            if (!$cobro->delete()) {
                                throw new \Exception('Error al eliminar cobro excedente: ' . json_encode($cobro->errors));
                            }
                        } else {
                            // Solo parte de este cobro es excedente, reducir su monto
                            $cobro->monto -= $excedentePorAjustar;
                            $cobro->nota = ($cobro->nota ? $cobro->nota . ' | ' : '') . 'Ajustado por edición de factura (reducción de $' . number_format($excedentePorAjustar, 2) . ')';
                            
                            if (!$cobro->save()) {
                                throw new \Exception('Error al ajustar cobro: ' . json_encode($cobro->errors));
                            }
                            
                            $excedentePorAjustar = 0;
                        }
                    }
                }
                
                // Reevaluar el status del cliente basado en TODAS sus facturas
                $cliente = Clientes::findOne($factura->id_cliente);
                if ($cliente) {
                    if (!$cliente->evaluarYActualizarStatus()) {
                        throw new \Exception('Error al evaluar status del cliente: ' . json_encode($cliente->errors));
                    }
                }
            }
            
            $transaction->commit();
            
            return [
                'success' => true,
                'message' => 'Factura actualizada exitosamente',
                'factura_id' => $factura->id
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Registra una devolución de producto en HistoricoMovimientos y Entradas
     * 
     * @param int $idProducto ID del producto devuelto
     * @param int $idLugar ID del lugar donde se devuelve
     * @param int $cantidad Cantidad devuelta
     * @param int $facturaId ID de la factura relacionada
     * @param string $motivo Motivo de la devolución
     * @throws \Exception
     */
    private function registrarDevolucion($idProducto, $idLugar, $cantidad, $facturaId, $motivo = 'Devolución por edición de factura')
    {
        // Registrar en HistoricoMovimientos
        $movimiento = new HistoricoMovimientos();
        $movimiento->id_producto = $idProducto;
        $movimiento->accion = HistoricoMovimientos::ACCION_ENTRADA;
        $movimiento->id_lugar_origen = null;
        $movimiento->id_lugar_destino = $idLugar;
        $movimiento->cantidad = $cantidad;
        $movimiento->referencia_id = $facturaId;
        
        if (!$movimiento->save()) {
            throw new \Exception('Error al registrar movimiento de devolución: ' . json_encode($movimiento->errors));
        }
        
        // Registrar en Entradas como evidencia de la devolución
        $entrada = new Entradas();
        $entrada->id_producto = $idProducto;
        $entrada->cantidad = $cantidad;
        $entrada->id_lugar = $idLugar;
        $entrada->id_proveedor = null; // No hay proveedor en devoluciones
        $entrada->nro_documento = 'DEV-' . $facturaId . '-' . time();
        $entrada->ruta_documento_respaldo = null;
        
        // Usar el campo virtual tipo_entrada para la validación, aunque no se guarda en la BD
        $entrada->tipo_entrada = 'Donación'; // Usamos 'Donación' ya que no requiere proveedor
        
        if (!$entrada->save()) {
            throw new \Exception('Error al registrar entrada de devolución: ' . json_encode($entrada->errors));
        }
    }
}

