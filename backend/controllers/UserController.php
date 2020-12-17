<?php


namespace backend\controllers;


use backend\models\UserRole;
use backend\models\UserSearch;
use common\helper\Helper;
use common\models\User;
use backend\models\UserModel;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Transaction;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use function Symfony\Component\String\s;

class UserController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render('index.blade', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */

    public function actionCreate()
    {
        $model = new UserModel();
        $transaction = \Yii::$app->getDb()->beginTransaction(Transaction::SERIALIZABLE);
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    UserRole::assignRole($model);
                    $transaction->commit();
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return static::responseSuccess($exception->getMessage());
        }

        return static::responseRemote('create.blade', [
            'model' => $model,
        ], 'Tạo tài khoản', parent::footer('* các thông tin bắt buộc!'));
    }

    public function actionView($id)
    {
        $model = static::findModel($id);
        return static::responseRemote('view.blade', [
            'model' => $model,
        ], 'Chi tiết tài khoản');
    }

    public function actionUpdate($id)
    {
        $model = static::findModel($id);
        try {
            if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    return static::responseSuccess();
                }
            }
        } catch (\Exception $exception) {
            return static::responseSuccess($exception->getMessage());
        }

        return static::responseRemote('create.blade', [
            'model' => $model
        ], 'Cập nhật tài khoản', '<button class="btn btn-success" type="submit">Cập nhật</button>');
    }

    public function actionDelete($id)
    {
        $model = UserModel::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Không tìm thấy trang này!');
        }
        $model->delete();
        return self::responseSuccess();
    }

    public static function findModel($id)
    {
        $model = UserModel::findOne($id);
        if (!$model) {
            return static::responseRemote('view.blade', [
                'model' => $model,
                'title' => 'Chi tiết tài khoản'
            ]);
        }
        return $model;
    }
}