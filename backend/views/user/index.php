<?php

use common\models\User;
use yii\helpers\Url;
use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var backend\models\UserSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;

$user = \Yii::$app->user;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($user->can(PERMISSION_USER_CREATE)): ?>
        <p><?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
            [
                'label' => 'Role',
                'attribute' => 'role',
                'filter' => array_map(
                    fn(\yii\rbac\Role $role): string => $role->name,
                    \Yii::$app->authManager->getRoles()
                ),
                'value' => fn ($model) => empty($userRoles = \Yii::$app->authManager->getRolesByUser($model->id))
                    ? null
                    : implode(', ', array_keys($userRoles)),
            ],
            [
                'class' => \yii\grid\ActionColumn::class,
                'buttons'=>[
                    'reset' => fn ($url, $model) => Html::a(
                        '<span class="fas fa-key"></span>',
                        Url::to(['reset', 'id' => $model->id]),
                        [
                            'title' => Yii::t('yii', 'Reset password'),
                            'data-pjax' => '0',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to reset password?')
                        ]
                    )
                ],
                'visibleButtons' => [
                    'view' => $user->can(PERMISSION_USER_READ),
                    'update' => $user->can(PERMISSION_USER_UPDATE),
                    'reset' => $user->can(PERMISSION_USER_UPDATE),
                    'delete' => $user->can(PERMISSION_USER_DELETE),
                ],
                'template' => '{view} {update} {reset} {delete}'
            ]
        ]
    ]); ?>

    <?php Pjax::end(); ?>

</div>