<?php

namespace backend\modules\controllers;

use backend\models\Banners;
use backend\models\BannersSearch;
use backend\models\StatisticReport;
use common\helper\Helper;
use mdm\admin\components\AccessControl;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\web\BadRequestHttpException;
use yii\rest\Controller;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];
        return $behaviors;
    }

    function actionGetBanner()
    {
        $searchModel = new BannersSearch();
        $dataProvider = $searchModel->search([
            'BannersSearch' => array_merge([
                'active' => Banners::STATUS_ACTIVE,
            ], \Yii::$app->request->queryParams)
        ]);
        $result = $dataProvider->query->asArray()->all();
        return $result;
    }

    /**
     * @throws BadRequestHttpException
     */

    function actionCounter()
    {
        $bannerId = \Yii::$app->request->post('bannerKey'); // banner key
        $page = \Yii::$app->request->post('page'); // page shown
        $type = \Yii::$app->request->post('type'); //click or show

        $banner = Banners::findOne($bannerId);
        if (!$banner) {
            throw new BadRequestHttpException('Không tìm thấy link!');
        }
        try {
            $model = StatisticReport::findOne(['banner_id' => $bannerId]);
            if (!$model) {
                $model = new StatisticReport();
            }
            $model->banner_id = $bannerId;
            switch ($type) {
                case StatisticReport::TYPE_CLICK:
                    $model->click += $model->click;
                    break;
                case StatisticReport::TYPE_SHOWN:
                    $model->shown += $model->shown;
                    break;
            }
            $model->ip = Yii::$app->getRequest()->getUserIP();
            if (!$model->save()) {
                throw new BadRequestHttpException(Helper::firstError($model));
            }
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return [
            'success' => 1,
            'redirect' => $banner->href
        ];
    }
}
