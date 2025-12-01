<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Clientes;

/**
 * ClientesSearch represents the model behind the search form of `app\models\Clientes`.
 */
class ClientesSearch extends Clientes
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
            [['id', 'edad'], 'integer'],
            [['documento_identidad', 'nombre', 'ubicacion', 'telefono', 'status', 'created_at', 'updated_at', 'search', 'fecha_inicio', 'fecha_fin'], 'safe'],
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
        $query = Clientes::find();

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
            'edad' => $this->edad,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        // Búsqueda general en nombre, documento, ubicación y teléfono
        if (!empty($this->search)) {
            $query->andFilterWhere([
                'or',
                ['like', 'nombre', $this->search],
                ['like', 'documento_identidad', $this->search],
                ['like', 'ubicacion', $this->search],
                ['like', 'telefono', $this->search],
            ]);
        }

        $query->andFilterWhere(['like', 'documento_identidad', $this->documento_identidad])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['ubicacion' => $this->ubicacion])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['status' => $this->status]);
        
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
