<?php

use yii\helpers\Url;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'filter' => false
            ],
            'username',
            'email',
            [
                'attribute' => 'status',
                'filter' => User::statusList(),
                'value' => function ($model) { return User::statusList()[$model->status]; },
            ],
            [
                'attribute' => 'created_at',
                'filter' => false,
	            'format' => ['date', \Yii::$app->formatter->dateFormat]
            ],
            'admin:boolean',
            [
                'class' => 'common\flow\grid\ActionColumn',
                'buttons'=>[
                    'reset' => function ($url, $model) {
                        return
                            Html::a(
                                '<span class="fas fa-key"></span>',
                                Url::to(['reset', 'id' => $model->id]),
                                [
                                    'title' => Yii::t('yii', 'Reset password'),
                                    'data-pjax' => '0',
	                                'data-method' => 'post',
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to reset password?')
                                ]
                            );
                    }
                ],
                'template' => '{view} {update} {reset} {delete}'
            ]
        ]
    ]); ?>

    <?php Pjax::end(); ?>

</div>