<?php


namespace backend\controllers;


use backend\models\Banners;
use backend\models\BannersSearch;
use backend\models\ContactsAssignment;
use backend\models\Media;
use backend\models\Products;
use backend\models\ProductsSearch;
use backend\models\UploadForm;
use common\helper\Helper;
use Illuminate\Support\Arr;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AjaxController extends BaseController
{
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * @return \backend\models\Media|bool
     * @throws BadRequestHttpException
     */
    function actionUploadFile()
    {
        $model = new UploadForm();
        if (\Yii::$app->request->isPost) {

            try {
                $model->load(\Yii::$app->request->post(), "");
                return $model->upload();
            } catch (Exception $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        }
        throw new BadRequestHttpException("Post only");
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     */
    function actionRemoveFile()
    {
        $model = Media::findOne(['url' => \Yii::$app->request->post('url')]);
        try {
            if (!$model) {
                throw new NotFoundHttpException('Không tìm thấy ảnh!');
            }
            if (file_exists(UPLOAD_PATH . str_replace('static/', '', $model->url))) {
                unlink(UPLOAD_PATH . str_replace('static/', '', $model->url));
                $model->delete();
            }
        } catch (\Exception $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
        return true;
    }

    /**
     * @return array
     */
    function actionGetBanner()
    {
        $searchModel = new BannersSearch();
        $dataProvider = $searchModel->search(array_merge(\Yii::$app->request->queryParams, [
            'BannersSearch' => [
                'active' => Banners::STATUS_ACTIVE,
            ]
        ]));
        return $dataProvider->query->asArray()->all();
    }
}