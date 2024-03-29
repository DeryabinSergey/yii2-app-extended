<?php

use common\models\User;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var yii\rbac\Role[] $roleList */

$this->title = 'Update User: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="user-update">

    <h1>Update User Id: <?=$model->id?></h1>

    <div class="user-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'username')->textInput() ?>

		<?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

		<?= $form->field($model, 'status')->dropDownList(User::statusList()) ?>

	    <?= $form->field($model, 'role')->dropDownList(
		    array_map(
			    fn(\yii\rbac\Role $item): string => $item->name . ($item->description ? ' - ' . $item->description : ''),
			    $roleList
		    ),
		    ['multiple'=>'multiple']
        )->hint('<code>Ctrl + click</code> to remove selection') ?>

        <div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

		<?php ActiveForm::end(); ?>

    </div>

</div>