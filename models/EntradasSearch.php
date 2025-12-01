<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Entradas;

/**
 * EntradasSearch represents the model behind the search form of `app\models\Entradas`.
 */
class EntradasSearch extends Entradas
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
            [['id', 'id_producto', 'cantidad', 'id_proveedor', 'id_lugar'], 'integer'],
            [['ruta_documento_respaldo', 'created_at', 'nombre_producto', 'fecha_desde', 'fecha_hasta'], 'safe'],
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
        $query = Entradas::find();

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
            'entradas.id' => $this->id,
            'entradas.id_producto' => $this->id_producto,
            'entradas.cantidad' => $this->cantidad,
            'entradas.id_proveedor' => $this->id_proveedor,
            'entradas.id_lugar' => $this->id_lugar,
        ]);

        $query->andFilterWhere(['like', 'entradas.ruta_documento_respaldo', $this->ruta_documento_respaldo]);

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
            $query->andFilterWhere(['>=', 'DATE(entradas.created_at)', $this->fecha_desde]);
        }

        if (!empty($this->fecha_hasta)) {
            $query->andFilterWhere(['<=', 'DATE(entradas.created_at)', $this->fecha_hasta]);
        }

        return $dataProvider;
    }
}
