<?php

use yii\bootstrap5\Html;

/* @var yii\web\View $this */
/* @var common\models\User $user */
/* @var string $password */

$link = Yii::$app->urlManagerFrontend->createUrl(['/']);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Your credentials for site: <?= Html::a(Html::encode($link), $link) ?></p>

    <p>Login: <?=Html::encode($user->email)?></p>
    <p>Password: <?=$password?></p>
</div>
