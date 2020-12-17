<?php


namespace backend\models;


use mdm\admin\models\Assignment;
use yii\helpers\ArrayHelper;

class UserRole extends UserModel
{
    const ROLE_ADMIN = 'Admin';
    const ROLE_SALE = 'Sale';
    const ROLE_MARKETER = 'Marketer';
    const ROLE_VENDOR = 'Vendor';
    const ROLE_PARTNER = 'Partner';

    const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_MARKETER => 'Marketer',
        self::ROLE_SALE => 'Sale',
        self::ROLE_VENDOR => 'Vận đơn',
        self::ROLE_PARTNER => 'Đối tác',
    ];

    public static function assignRole($model)
    {
        $auth = new Assignment($model->id, $model);
        return $auth->assign([
            $model->role
        ]);
    }

    public static function ROLES()
    {
        $roles = AuthItem::find()->where(['type' => '1'])->asArray()->all();
        return ArrayHelper::map($roles, 'name', 'name');
    }

    public function LISTS($role = UserRole::ROLE_PARTNER)
    {
        $users = UserModel::find()->joinWith([
            'position' => function ($query) use ($role) {
                $query->andWhere(['{{auth_assignment}}.item_name' => $role]);
            }
        ], false)->all();
        return ArrayHelper::map($users, 'id', 'username');
    }
}