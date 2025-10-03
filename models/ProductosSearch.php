<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Productos;

/**
 * ProductosSearch represents the model behind the search form of `app\models\Productos`.
 */
class ProductosSearch extends Productos
{
    // Campo para búsqueda general
    public $search;
    
    // Campos para filtros de fecha
    public $fecha_inicio;
    public $fecha_fin;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_lugar', 'id_categoria'], 'integer'],
            [['marca', 'modelo', 'color', 'descripcion', 'unidad_medida', 'codigo_barra', 'fotos', 'sku', 'created_at', 'updated_at', 'search', 'fecha_inicio', 'fecha_fin'], 'safe'],
            [['contenido_neto', 'costo', 'precio_venta'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Productos::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'contenido_neto' => $this->contenido_neto,
            'costo' => $this->costo,
            'precio_venta' => $this->precio_venta,
            'id_categoria' => $this->id_categoria,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        // Filtro por lugar usando la tabla Stock
        if (!empty($this->id_lugar)) {
            $query->innerJoin('stock', 'stock.id_producto = productos.id')
                  ->andWhere(['stock.id_lugar' => $this->id_lugar])
                  ->andWhere(['>', 'stock.cantidad', 0]);
        }

        // Búsqueda general en marca, modelo y descripción
        if (!empty($this->search)) {
            $query->andFilterWhere([
                'or',
                ['like', 'marca', $this->search],
                ['like', 'modelo', $this->search],
                ['like', 'descripcion', $this->search],
                ['like', 'color', $this->search],
                ['like', 'sku', $this->search],
                ['like', 'codigo_barra', $this->search],
            ]);
        }

        $query->andFilterWhere(['like', 'marca', $this->marca])
            ->andFilterWhere(['like', 'modelo', $this->modelo])
            ->andFilterWhere(['color' => $this->color])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['unidad_medida' => $this->unidad_medida])
            ->andFilterWhere(['like', 'codigo_barra', $this->codigo_barra])
            ->andFilterWhere(['like', 'fotos', $this->fotos])
            ->andFilterWhere(['like', 'sku', $this->sku]);
        
        // Filtro por rango de fechas
        if (!empty($this->fecha_inicio)) {
            $query->andFilterWhere(['>=', 'created_at', $this->fecha_inicio . ' 00:00:00']);
        }
        if (!empty($this->fecha_fin)) {
            $query->andFilterWhere(['<=', 'created_at', $this->fecha_fin . ' 23:59:59']);
        }

        return $dataProvider;
    }
}
