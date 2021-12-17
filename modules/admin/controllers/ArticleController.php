<?php

namespace app\modules\admin\controllers;

use app\models\Article;
use app\models\ArticleSearch;
use app\models\Category;
use app\models\ImageUpload;
use app\models\Tag;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile as WebUploadedFile;

class ArticleController extends Controller {
  
    public function behaviors() {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex() {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate() {
        $model = new Article();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->saveArticle()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->saveArticle()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSetImage($id) {
        $model = new ImageUpload();

        if($this->request->isPost) {
            $article = $this->findModel($id);
            $file = WebUploadedFile::getInstance($model, 'image');

            if($article->saveImage($model->uploadFile($file, $article->image))) {
            return $this->redirect(['view', 'id' =>$article->id]);
        }
        }

        return $this->render('image',['model'=>$model]);
    }

    public function actionSetCategory($id) {

        $article = $this->findModel($id);
        $selectedCategory = $article->category_id; 
        $categories =  ArrayHelper::map(Category::find()->all(), 'id', 'title');

        if($this->request->isPost) {

            $category = $this->request->post('category');

            if($article->saveCategory($category)) {

                return $this->redirect(['view', 'id'=>$article->id]); 

            }
        }
        
        return $this->render('category', [
            'article' => $article,
            'selectedCategory' => $selectedCategory,
            'categories' => $categories
        ]);
    }
}