<?php

namespace app\widgets;

use yii\base\Widget;
use app\models\HistoricoPreciosDolar;

class DollarPriceWidget extends Widget
{
    public function run()
    {
        // Obtener el precio más reciente de cada tipo
        $precioOficial = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_OFICIAL])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        $precioParalelo = HistoricoPreciosDolar::find()
            ->where(['tipo' => HistoricoPreciosDolar::TIPO_PARALELO])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        return $this->render('dollar-price-widget', [
            'precioOficial' => $precioOficial,
            'precioParalelo' => $precioParalelo,
        ]);
    }
}
