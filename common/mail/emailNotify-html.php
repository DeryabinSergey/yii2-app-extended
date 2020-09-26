<?php
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $password string */

$link = Yii::$app->urlManagerFrontend->createUrl(['/']);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Your credentials for site: <?= Html::a(Html::encode($link), $link) ?></p>

    <p>Login: <?=Html::encode($user->email)?></p>
    <p>Password: <?=$password?></p>
</div>
