<?php


namespace backend\models;


use common\helper\Helper;
use common\models\BaseModel;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class UploadForm extends BaseModel
{
    public $file;
    public $type;
    public $url;
    public $alt;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @return Media|bool
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function upload()
    {
        $media = new Media();
        $basePath = "/";
        $uploadFolder = UPLOAD_PATH . $basePath;

        $this->file = UploadedFile::getInstanceByName('file');
        if (!$this->validate()) {
            throw new BadRequestHttpException('Invalid validate: ' . Helper::firstError($this));
        }
        $fileName = $this->file->getBaseName() . "_" . \Yii::$app->security->generateRandomString(10);
        $this->url = $basePath . $fileName . "." . $this->file->getExtension();

        if (!$this->file->saveAs($uploadFolder . $fileName . "." . $this->file->getExtension())) {
            throw new BadRequestHttpException("Upload error!!");
        }
        $media->getData($this);
        if (!$media->save()) {
            throw new BadRequestHttpException('Invalid save: ' . Helper::firstError($media));
        }
        return $media;
    }
}