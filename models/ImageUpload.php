<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile as WebUploadedFile;

class ImageUpload extends Model {

    public $image;

    public function rules()
    {
        return [
            [['image'], 'required'],
            [['image'], 'file', 'extensions' => 'jpg,png']
        ];
    }

    public function uploadFile(WebUploadedFile $file, $currentImage) {

        $this->image = $file;

        if($this->validate()) {

        $this->deleteNotUsedImage($currentImage);
        $fileName = $this->generator();
        $file->saveAs($this->getPath() . $fileName);
        return $fileName;

        }
    }

    private function getPath() {
        return Yii::getAlias('@web') . 'uploads/';
    }

    private function generator() {
        return strtolower(md5(uniqid($this->image->baseName)) . '.' . $this->image->extension);
    }

    public function deleteNotUsedImage($currentImage) {
        if($this->fileExists($currentImage)) {
            
            unlink($this->getPath() . $currentImage);

        }
    }

    public function fileExists($currentImage) {

        if(!empty($currentImage) && $currentImage != null) {
            return file_exists($this->getPath() . $currentImage); 
        }
    }
}