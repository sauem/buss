<?php

namespace backend\modules;

use mdm\admin\components\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
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
        // custom initialization code goes here
    }

}
