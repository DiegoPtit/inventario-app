<?php

namespace app\controllers;

use app\models\Productos;
use app\models\Categorias;
use app\models\Stock;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use Yii;

/**
 * ApiController - Controlador central de API REST para la aplicación de inventario
 * 
 * Proporciona endpoints RESTful para consumo externo de datos.
 * Todos los endpoints retornan respuestas en formato JSON.
 * 
 * @author Sistema Inventario
 * @version 1.0
 */
class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'productos' => ['GET'],
                    'producto' => ['GET'],
                    'categorias' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Prepara la respuesta en formato JSON
     * @return void
     */
    protected function prepareJsonResponse()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * Genera una respuesta de éxito estandarizada
     * @param mixed $data Datos a retornar
     * @param string $message Mensaje opcional
     * @return array
     */
    protected function successResponse($data, $message = 'OK')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Genera una respuesta de error estandarizada
     * @param string $message Mensaje de error
     * @param int $code Código de error HTTP
     * @return array
     */
    protected function errorResponse($message, $code = 400)
    {
        Yii::$app->response->statusCode = $code;
        return [
            'success' => false,
            'message' => $message,
            'error_code' => $code,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * GET /api/categorias
     * 
     * Retorna la lista de todas las categorías disponibles.
     * 
     * @return array JSON response con lista de categorías
     */
    public function actionCategorias()
    {
        $this->prepareJsonResponse();

        try {
            $categorias = Categorias::find()->orderBy(['titulo' => SORT_ASC])->all();
            
            $data = [];
            foreach ($categorias as $categoria) {
                $data[] = [
                    'id' => $categoria->id,
                    'titulo' => $categoria->titulo,
                    'descripcion' => $categoria->descripcion,
                ];
            }

            return $this->successResponse($data);

        } catch (\Exception $e) {
            Yii::error("Error en API categorias: " . $e->getMessage());
            return $this->errorResponse('Error al obtener categorías: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/productos
     * 
     * Retorna todos los productos con información completa.
     * Incluye: datos del producto, categoría, stock total y primera imagen.
     * 
     * Parámetros opcionales (query string):
     * - categoria: int - Filtra por ID de categoría
     * - buscar: string - Búsqueda por marca, modelo o descripción
     * - limit: int - Limitar cantidad de resultados
     * - offset: int - Saltar N primeros resultados (paginación)
     * 
     * @return array JSON response con lista de productos
     */
    public function actionProductos()
    {
        $this->prepareJsonResponse();

        try {
            $request = Yii::$app->request;
            
            // Parámetros de filtrado
            $categoriaId = $request->get('categoria');
            $buscar = $request->get('buscar');
            $limit = $request->get('limit');
            $offset = $request->get('offset', 0);

            // Construir query
            $query = Productos::find()->with(['categoria', 'stocks']);

            // Filtrar por categoría
            if ($categoriaId !== null) {
                $query->andWhere(['id_categoria' => (int)$categoriaId]);
            }

            // Búsqueda por texto
            if ($buscar) {
                $query->andWhere([
                    'or',
                    ['like', 'marca', $buscar],
                    ['like', 'modelo', $buscar],
                    ['like', 'descripcion', $buscar],
                    ['like', 'codigo_barra', $buscar],
                ]);
            }

            // Aplicar paginación
            if ($limit !== null) {
                $query->limit((int)$limit);
            }
            $query->offset((int)$offset);

            // Ordenar por ID descendente (más recientes primero)
            $query->orderBy(['id' => SORT_DESC]);

            $productos = $query->all();
            $result = [];

            foreach ($productos as $producto) {
                $result[] = $this->formatProducto($producto);
            }

            // Obtener total para paginación
            $totalQuery = Productos::find();
            if ($categoriaId !== null) {
                $totalQuery->andWhere(['id_categoria' => (int)$categoriaId]);
            }
            if ($buscar) {
                $totalQuery->andWhere([
                    'or',
                    ['like', 'marca', $buscar],
                    ['like', 'modelo', $buscar],
                    ['like', 'descripcion', $buscar],
                    ['like', 'codigo_barra', $buscar],
                ]);
            }
            $total = $totalQuery->count();

            return $this->successResponse([
                'productos' => $result,
                'total' => (int)$total,
                'count' => count($result),
                'limit' => $limit ? (int)$limit : null,
                'offset' => (int)$offset,
            ]);

        } catch (\Exception $e) {
            Yii::error("Error en API productos: " . $e->getMessage());
            return $this->errorResponse('Error al obtener productos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/producto/{id}
     * 
     * Retorna información detallada de un producto específico.
     * 
     * @param int $id ID del producto
     * @return array JSON response con datos del producto
     * @throws NotFoundHttpException si el producto no existe
     */
    public function actionProducto($id)
    {
        $this->prepareJsonResponse();

        try {
            $producto = Productos::find()
                ->with(['categoria', 'stocks', 'stocks.lugar'])
                ->where(['id' => $id])
                ->one();

            if ($producto === null) {
                return $this->errorResponse('Producto no encontrado', 404);
            }

            $data = $this->formatProducto($producto, true);

            return $this->successResponse($data);

        } catch (\Exception $e) {
            Yii::error("Error en API producto/{$id}: " . $e->getMessage());
            return $this->errorResponse('Error al obtener producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Formatea un producto para la respuesta JSON
     * 
     * @param Productos $producto Modelo del producto
     * @param bool $detallado Si es true, incluye información adicional (stock por ubicación, todas las fotos)
     * @return array Datos formateados del producto
     */
    protected function formatProducto($producto, $detallado = false)
    {
        // Calcular stock total sumando todas las ubicaciones
        $stockTotal = 0;
        $stockPorUbicacion = [];
        
        foreach ($producto->stocks as $stock) {
            $stockTotal += $stock->cantidad;
            if ($detallado && $stock->lugar) {
                $stockPorUbicacion[] = [
                    'id_lugar' => $stock->id_lugar,
                    'lugar' => $stock->lugar->nombre ?? 'Sin nombre',
                    'cantidad' => $stock->cantidad,
                ];
            }
        }

        // Redondear contenido_neto a 2 decimales
        $contenidoNetoRedondeado = $producto->contenido_neto 
            ? number_format((float)$producto->contenido_neto, 2, '.', '') 
            : null;

        // Concatenar contenido_neto con unidad_medida
        $contenidoNetoConUnidad = $contenidoNetoRedondeado && $producto->unidad_medida
            ? $contenidoNetoRedondeado . ' ' . $producto->unidad_medida
            : ($contenidoNetoRedondeado ?: ($producto->unidad_medida ?: null));

        // Obtener título de categoría
        $tituloCategoria = $producto->categoria ? $producto->categoria->titulo : null;

        // Procesar fotos
        $fotos = [];
        $primeraFoto = null;
        if (!empty($producto->fotos)) {
            $fotosArray = json_decode($producto->fotos, true);
            if (is_array($fotosArray) && count($fotosArray) > 0) {
                $primeraFoto = Yii::$app->request->hostInfo . '/' . $fotosArray[0];
                if ($detallado) {
                    foreach ($fotosArray as $foto) {
                        $fotos[] = Yii::$app->request->hostInfo . '/' . $foto;
                    }
                }
            }
        }

        $data = [
            'id' => $producto->id,
            'marca' => $producto->marca,
            'modelo' => $producto->modelo,
            'color' => $producto->color,
            'descripcion' => $producto->descripcion,
            'contenido_neto' => $contenidoNetoConUnidad,
            'contenido_neto_valor' => $contenidoNetoRedondeado ? (float)$contenidoNetoRedondeado : null,
            'unidad_medida' => $producto->unidad_medida,
            'costo' => (float)$producto->costo,
            'precio_venta' => (float)$producto->precio_venta,
            'codigo_barra' => $producto->codigo_barra,
            'sku' => $producto->sku,
            'id_categoria' => $producto->id_categoria,
            'categoria' => $tituloCategoria,
            'stock' => $stockTotal,
            'foto' => $primeraFoto,
        ];

        // Agregar información adicional si es detallado
        if ($detallado) {
            $data['fotos'] = $fotos;
            $data['stock_por_ubicacion'] = $stockPorUbicacion;
            $data['created_at'] = $producto->created_at;
            $data['updated_at'] = $producto->updated_at;
        }

        return $data;
    }
}
