<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/* @var yii\web\View $this */
/* @var backend\models\UserCreateForm $model */
/* @var yii\bootstrap5\ActiveForm $form */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'username')->textInput() ?>

		<?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

		<?= $form->field($model, 'admin')->checkbox() ?>

        <div class="form-group">
			<?= Html::submitButton('Create user', ['class' => 'btn btn-success']) ?>
        </div>

		<?php ActiveForm::end(); ?>

    </div>

</div>