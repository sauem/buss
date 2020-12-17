<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $path
 * @property int|null $type
 * @property int|null $status
 * @property string|null $alter
 * @property int $created_at
 * @property int $updated_at
 *
 * @property MediaObj[] $mediaObjs
 */
class Media extends \common\models\BaseModel
{
    const STT_TEMP = 0;
    const STT_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'path'], 'string'],
            [['type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['url'], 'required'],
            [['alter'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'path' => 'Path',
            'type' => 'Type',
            'status' => 'Status',
            'alter' => 'Alter',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[MediaObjs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMediaObjs()
    {
        return $this->hasMany(MediaObj::className(), ['media_id' => 'id']);
    }
    public function getData($uploadForm)
    {
        $this->url = "/static{$uploadForm->url}";
        $this->status = self::STT_TEMP;
        $this->type = $uploadForm->type;
        $this->alter = $uploadForm->alt;
    }
}
