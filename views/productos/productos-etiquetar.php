<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos a Etiquetar</title>
    <style>
        /* === CONFIGURACIÓN DE TAMAÑO DE ETIQUETAS === */
        /* PUEDES EDITAR ESTOS VALORES FÁCILMENTE */
        :root {
            --etiqueta-ancho: 80mm;
            --etiqueta-alto: 40mm;
            --codigo-barras-ancho: 250px;
            --codigo-barras-alto: 80px;
            --fuente-producto: 10px;
            --fuente-codigo: 12px;
        }
        /* ============================================= */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 10mm;
        }
        
        h1 {
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .etiquetas-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, var(--etiqueta-ancho));
            gap: 5mm;
            justify-content: center;
        }
        
        .etiqueta {
            width: var(--etiqueta-ancho);
            height: var(--etiqueta-alto);
            border: 1px solid #ccc;
            padding: 3mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            page-break-inside: avoid;
        }
        
        .producto-info {
            text-align: center;
            font-size: var(--fuente-producto);
            line-height: 1.2;
        }
        
        .marca {
            font-weight: bold;
        }
        
        .modelo {
            color: #555;
        }
        
        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .barcode-svg {
            width: var(--codigo-barras-ancho);
            height: var(--codigo-barras-alto);
        }
        
        .codigo-texto {
            font-size: var(--fuente-codigo);
            font-weight: bold;
            margin-top: 2px;
        }
        
        .btn-toolbar {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-imprimir {
            background: #007bff;
            color: white;
        }
        
        .btn-imprimir:hover {
            background: #0056b3;
        }
        
        .btn-guardar {
            background: #28a745;
            color: white;
        }
        
        .btn-guardar:hover {
            background: #218838;
        }
        
        .info-header {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        
        .alert {
            padding: 12px 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }
        
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            color: #0c5460;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .btn-toolbar,
            .info-header,
            .alert {
                display: none;
            }
            
            .etiqueta {
                border: 1px dashed #999;
            }
        }
    </style>
</head>
<body>
    <div class="btn-toolbar">
        <button class="btn btn-imprimir" onclick="window.print()">🖨️ Imprimir</button>
        <?php if ($hayProductosSinCodigo && !empty($codigosGeneradosMap)): ?>
            <button class="btn btn-guardar" onclick="guardarCodigos()">💾 Guardar en Base de Datos</button>
        <?php endif; ?>
    </div>
    
    <?php if ($hayProductosSinCodigo): ?>
        <div class="alert alert-warning">
            ⚠️ <strong>CÓDIGOS GENERADOS - NO GUARDADOS EN BASE DE DATOS</strong><br>
            Haz clic en "Guardar en Base de Datos" para registrar estos códigos.
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            ✓ <strong>TODOS LOS PRODUCTOS TIENEN CÓDIGO DE BARRAS</strong><br>
            Mostrando códigos existentes sin repetir.
        </div>
    <?php endif; ?>
    
    <div class="info-header">
        <h1><?= $hayProductosSinCodigo ? 'Productos para Etiquetar' : 'Códigos de Barras de Productos' ?></h1>
        <p>Total de etiquetas: <strong><?= count($productosParaEtiquetar) ?></strong></p>
        <?php if ($hayProductosSinCodigo): ?>
            <p>Productos únicos sin código: <strong><?= count($codigosGeneradosMap) ?></strong></p>
        <?php endif; ?>
        <p>Longitud del código de barras: <strong><?= $barcodeLength ?></strong> dígitos</p>
    </div>
    
    <div class="etiquetas-container">
        <?php foreach ($productosParaEtiquetar as $item): ?>
            <div class="etiqueta">
                <div class="producto-info">
                    <div class="marca"><?= htmlspecialchars($item['producto']->marca ?: 'Sin marca') ?></div>
                    <div class="modelo"><?= htmlspecialchars($item['producto']->modelo ?: 'Sin modelo') ?></div>
                </div>
                
                <div class="barcode-container">
                    <svg class="barcode-svg" id="barcode-<?= $item['codigo_barra_generado'] ?>-<?= $item['producto']->id ?>"></svg>
                    <div class="codigo-texto"><?= $item['codigo_barra_generado'] ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Formulario oculto para guardar códigos -->
    <?php if ($hayProductosSinCodigo && !empty($codigosGeneradosMap)): ?>
        <form id="formGuardarCodigos" method="POST" action="<?= \yii\helpers\Url::to(['productos/guardar-codigos-barras']) ?>" style="display: none;">
            <input type="hidden" name="<?= \Yii::$app->request->csrfParam ?>" value="<?= \Yii::$app->request->csrfToken ?>">
            <input type="hidden" name="codigos" value='<?= json_encode($codigosGeneradosMap) ?>'>
        </form>
    <?php endif; ?>
    
    <!-- Librería JsBarcode para generar códigos de barras -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    
    <script>
        // Generar todos los códigos de barras
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($productosParaEtiquetar as $item): ?>
                JsBarcode("#barcode-<?= $item['codigo_barra_generado'] ?>-<?= $item['producto']->id ?>", "<?= $item['codigo_barra_generado'] ?>", {
                    format: "CODE128",
                    width: 2,
                    height: 60,
                    displayValue: false,
                    margin: 0
                });
            <?php endforeach; ?>
        });
        
        // Función para guardar códigos en la base de datos
        function guardarCodigos() {
            if (confirm('¿Estás seguro de que deseas guardar estos códigos de barras en la base de datos?')) {
                document.getElementById('formGuardarCodigos').submit();
            }
        }
    </script>
</body>
</html>
