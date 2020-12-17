<?php


namespace backend\controllers;


class ImportController extends BaseController
{

    function actionIndex($module)
    {
        return $this->render('index.blade', [
            'module' => $module
        ]);
    }
}