<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "statistic_report".
 *
 * @property int $id
 * @property int|null $banner_id
 * @property int|null $click
 * @property int|null $shown
 * @property string|null $ip
 * @property string|null $geolocation
 * @property string|null $ref_url
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Banners $banner
 */
class StatisticReport extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    const TYPE_CLICK = 'click';
    const TYPE_SHOWN = 'shown';

    public static function tableName()
    {
        return 'statistic_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['banner_id', 'click', 'shown', 'created_at', 'updated_at'], 'integer'],
            [['ref_url'], 'string'],
            [['created_at', 'updated_at'], 'required'],
            [['ip', 'geolocation'], 'string', 'max' => 255],
            [['banner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Banners::className(), 'targetAttribute' => ['banner_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'banner_id' => 'Banner ID',
            'click' => 'Click',
            'shown' => 'Shown',
            'ip' => 'Ip',
            'geolocation' => 'Geolocation',
            'ref_url' => 'Ref Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Banner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBanner()
    {
        return $this->hasOne(Banners::className(), ['id' => 'banner_id']);
    }
}
