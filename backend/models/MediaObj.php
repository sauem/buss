<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "media_obj".
 *
 * @property int $id
 * @property int|null $obj_id
 * @property int|null $media_id
 * @property string|null $obj_type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Media $media
 */
class MediaObj extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    const OBJECT_BANNER = 'banner';


    public static function tableName()
    {
        return 'media_obj';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obj_id', 'media_id', 'created_at', 'updated_at'], 'integer'],
            [['obj_id', 'media_id', 'obj_type'], 'required'],
            [['obj_type'], 'string', 'max' => 255],
            [['media_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['media_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obj_id' => 'Obj ID',
            'media_id' => 'Media ID',
            'obj_type' => 'Obj Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Media]].
     *
     * @param $media_id
     * @param $obj_id
     * @param $obj_type
     * @return MediaObj
     * @throws BadRequestHttpException
     */
    public static function saveObject($media_id, $obj_id, $obj_type)
    {
        $model = MediaObj::findOne(['obj_id' => $obj_id]);
        if (!$model) {
            $model = new MediaObj();
        }
        $model->obj_id = $obj_id;
        $model->media_id = $media_id;
        $model->obj_type = $obj_type;
        if (!$model->save()) {
            throw new BadRequestHttpException(Helper::firstError($model));
        }
        return $model;
    }

    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }
}
