<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Salidas;

/**
 * SalidasSearch represents the model behind the search form of `app\models\Salidas`.
 */
class SalidasSearch extends Salidas
{
    // Atributos virtuales para los filtros
    public $nombre_producto;
    public $fecha_desde;
    public $fecha_hasta;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_producto', 'cantidad', 'is_movimiento', 'id_lugar_origen', 'id_lugar_destino', 'id_cliente'], 'integer'],
            [['created_at', 'nombre_producto', 'fecha_desde', 'fecha_hasta'], 'safe'],
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
        $query = Salidas::find();

        // Agregar join con productos para poder filtrar por nombre de producto
        $query->joinWith(['producto']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'salidas.id' => $this->id,
            'salidas.id_producto' => $this->id_producto,
            'salidas.cantidad' => $this->cantidad,
            'salidas.is_movimiento' => $this->is_movimiento,
            'salidas.id_lugar_origen' => $this->id_lugar_origen,
            'salidas.id_lugar_destino' => $this->id_lugar_destino,
            'salidas.id_cliente' => $this->id_cliente,
        ]);

        // Filtro por nombre de producto (marca + descripción)
        if (!empty($this->nombre_producto)) {
            $query->andWhere([
                'or',
                ['like', 'productos.marca', $this->nombre_producto],
                ['like', 'productos.descripcion', $this->nombre_producto],
                ['like', "CONCAT(productos.marca, ' ', productos.descripcion)", $this->nombre_producto],
            ]);
        }

        // Filtro por rango de fechas
        if (!empty($this->fecha_desde)) {
            $query->andFilterWhere(['>=', 'DATE(salidas.created_at)', $this->fecha_desde]);
        }

        if (!empty($this->fecha_hasta)) {
            $query->andFilterWhere(['<=', 'DATE(salidas.created_at)', $this->fecha_hasta]);
        }

        return $dataProvider;
    }
}
