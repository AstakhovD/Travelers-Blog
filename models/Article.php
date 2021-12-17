<?php

namespace app\models;

use app\models\ImageUpload;
use yii\helpers\ArrayHelper;
use Yii;
use yii\data\Pagination;


/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $content
 * @property string|null $date
 * @property string|null $image
 * @property int|null $viewed
 * @property int|null $user_id
 * @property int|null $status
 * @property int|null $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        [['title'], 'required'],
        [['title', 'description', 'content'], 'string'],
        [['date'], 'date', 'format'=>'php:Y-m-d'],
        [['date'], 'default', 'value'=> date('Y-m-d')],
        [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'date' => 'Date',
            'image' => 'Image',
            'viewed' => 'Viewed',
            'user_id' => 'User ID',
            'status' => 'Status',
            'category_id' => 'Category ID',
        ];
    }

    public function saveImage($fileName) {
        $this->image = $fileName;
        return $this->save(false);
    }

    public function deleteImage() {
        $imageUpload = new ImageUpload();
        $imageUpload->deleteNotUsedImage($this->image);
    }

    public function beforeDelete()
    {
        $this->deleteImage();
        return parent::beforeDelete();
    }

    public function getImage() {
        if($this->image) {
            return '/uploads/' . $this->image;
        }
        return '/uploads/no-image.png';
    }

    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function saveCategory($category_id) {
        $category = Category::findOne($category_id);

        if($category != null) {
            $this->link('category', $category);
            return true;

        }
    }

    public function getDate() {
        return Yii::$app->formatter->asDate($this->date);
    }

    public static function getAll($pageSize = 5) {

        $query = Article::find();
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $articles = $query->offset($pagination->offset)->limit($pagination->limit)
        ->all();

        $data['articles'] = $articles;
        $data['pagination'] = $pagination;

        return $data;
    }

    public static function getPopular() {
        return Article::find()->orderBy('viewed desc')->limit(3)->all();
    }

    public static function getRecent() {
        return Article::find()->orderBy('date desc')->limit(4)->all();
    }

    public function getId($id) {
        return $id;
    }

    public function saveArticle() {
        $this->user_id = Yii::$app->user->id;
        return $this->save();
    }

    public function getAuthor() {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getComments() {
        return $this->hasMany(Comment::className(), ['article_id' => 'id']); 
    }

    public function viewedCounter() {
        $this->viewed += 1;
        return $this->save(false);
    }
} 
