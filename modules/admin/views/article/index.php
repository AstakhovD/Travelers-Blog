<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Articles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Article', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'description:ntext',
            'content:ntext',
            'date',
            [
                'format' => 'html',
                'label' => 'image',
                'value' => function($data) {
                    return Html::img($data->getImage(), ['width' => 200]);
                }
            ],
            //'image',
            //'viewed',
            //'user_id',
            //'status',
            //'category_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
