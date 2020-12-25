<?php


namespace backend\controllers;


use backend\models\Banners;
use backend\models\BannersSearch;
use backend\models\MediaObj;
use common\helper\Helper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class BannerController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new BannersSearch();
        $mobileProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            "BannersSearch" => [
                'device' => Banners::DEVICE_MOBILE
            ]
        ]));
        $desktopProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            "BannersSearch" => [
                'device' => Banners::DEVICE_DESKTOP
            ]
        ]));
        return $this->render('index.blade', [
            'desktopProvider' => $desktopProvider,
            'mobileProvider' => $mobileProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Banners();
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    if ($model->type == Banners::TYPE_IMAGE) {
                        MediaObj::saveObject($model->thumb, $model->id, MediaObj::OBJECT_BANNER);
                    }
                    return static::responseSuccess();
                }
            }
        } catch (BadRequestHttpException $e) {
            \Yii::$app->session->setFlash('danger', $e->getMessage());
        }

        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Thêm mới banner', $this->footer(), 'md');
    }

    /**
     * @param $id
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $model = Banners::findOne($id);
        if (!$model) {
            throw new BadRequestHttpException('Không tồn tại banner này!');
        }
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    if ($model->type == Banners::TYPE_IMAGE) {
                        MediaObj::saveObject($model->thumb, $model->id, MediaObj::OBJECT_BANNER);
                    }
                    return static::responseSuccess();
                }
            }
        } catch (BadRequestHttpException $e) {
            \Yii::$app->session->setFlash('danger', $e->getMessage());
        }
        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Cập nhật banner', $this->footer(), 'md');
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = Banners::findOne($id);
        if (!$model) {
            if (!$model) {
                throw new NotFoundHttpException('Không tìm thấy trang này!');
            }
        }
        $model->delete();
        return self::responseSuccess();
    }
}