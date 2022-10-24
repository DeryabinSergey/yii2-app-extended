<?php

use common\models\User;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var common\models\User $model */

$user = \Yii::$app->user;

$this->title =  $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($user->can(PERMISSION_USER_UPDATE)): ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if ($user->can(PERMISSION_USER_DELETE)): ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => fn (User $model): string => User::statusList()[$model->status] ?? ' - ',
            ],
	        [
		        'label' => 'Roles',
		        'value' => fn (User $model): string
		            => implode(', ', array_keys(\Yii::$app->authManager->getRolesByUser($model->id)))
	        ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
