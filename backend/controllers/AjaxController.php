<?php


namespace backend\controllers;


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
     * @throws BadRequestHttpException
     */
    function actionAssignPhone()
    {
        $phones = \Yii::$app->request->post('phones');
        $saleID = \Yii::$app->request->post('saleID');

        try {
            if (!$phones) {
                throw new BadRequestHttpException('không có số điện hoại nào!');
            }
            $data = array_map(function ($item) use ($saleID) {
                return array_merge($item, ['user_id' => $saleID, 'created_at' => time(), 'updated_at' => time()]);
            }, $phones);
            \Yii::$app->db->createCommand()->batchInsert(
                ContactsAssignment::tableName(),
                ['phone', 'country', 'user_id', 'created_at', 'updated_at'],
                $data)
                ->execute();
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return true;
    }

    /**
     * @return array|\yii\db\ActiveRecord
     * @throws BadRequestHttpException
     */
    function actionInfoProduct()
    {
        $sku = Products::find()->where([
            'sku' => \Yii::$app->request->post('sku')])
            ->orWhere(['id' => \Yii::$app->request->post('sku')])
            ->with('category')
            ->with('partner')
            ->with('media')
            ->asArray()->one();
        if (!$sku) {
            throw new BadRequestHttpException('Không tìm thấy sản phẩm!');
        }
        return [
            'id' => $sku['id'],
            'sku' => $sku['sku'],
            'media' => $sku['media']['media']['url'],
            'name' => $sku['category']['name'] . '-' . $sku['sku'],
            'size' => $sku['size'],
            'weight' => $sku['weight'],
            'prices' => []
        ];
    }

    function actionGetListProduct()
    {
        $searchModel = new ProductsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $dataProvider->query->with(['media', 'category', 'partner'])->asArray()->all();
    }

    /**
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionChangeStatus()
    {
        $model = \Yii::$app->request->post('model');
        $status = \Yii::$app->request->post('status');
        $key = \Yii::$app->request->post('ids');

        try {
            $model = new \ReflectionClass("backend\models\\$model");
            $model = $model->newInstanceWithoutConstructor();
            $model::updateAll(['status' => $status], ['id' => $key]);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}