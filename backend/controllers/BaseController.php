<?php


namespace backend\controllers;


use common\helper\Helper;
use cyneek\yii2\blade\BladeBehavior;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function init()
    {
        parent::init();

    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => array_merge(['error'], $this->unRequiredAuthAction()),
                        'allow' => true,
                    ],
                    [
                        'actions' => $this->requiredAuthAction(),
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    []
                ],
            ],
            'blade' => [
                'class' => BladeBehavior::class
            ],
        ];
    }


    public function unRequiredAuthAction()
    {
        return [];
    }

    public function requiredAuthAction()
    {
        return [];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render(
                '@backend/views/site/error.blade',
                [
                    'exception' => $exception
                ]
            );
        }
    }

    static function responseClose($message = '')
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'forceReload' => 1,
            'message' => $message
        ];

    }

    static function responseSuccess($forceReload = 1, $forceClose = 1, $message = '')
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'forceReload' => $forceReload,
            'forceClose' => $forceClose,
            'message' => $message
        ];
    }

    static function responseRemote($view, $params, $title = null, $footer = null, $size = 'normal')
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->layout = 'blank.blade';
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'content' => \Yii::$app->view->renderAjax('@backend/views/' . \Yii::$app->controller->id . '/' . $view, $params),
                'footer' => $footer,
                'title' => $title,
                'size' => $size
            ];
        }
        return \Yii::$app->view->render('@backend/views/' . \Yii::$app->controller->id . '/' . $view, $params);
    }

    public function footer($note = '* các trường bắt buộc!')
    {
        return '<div class="d-flex w-100 justify-content-between"><button class="btn btn-secondary" data-dismiss="modal" >Đóng</button><div><span class="mr-3 text-warning">' . $note . '</span><button class="btn btn-success" type="submit">Lưu</button></div></div>';
    }
}