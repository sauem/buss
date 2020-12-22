<?php

namespace backend\modules;

use yii\web\Response;

/**
 * api module definition class
 */
class Api extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backend\modules\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;

        // custom initialization code goes here
    }
}
