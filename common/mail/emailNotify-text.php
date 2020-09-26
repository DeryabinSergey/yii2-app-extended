<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $password string */

$link = Yii::$app->urlManagerFrontend->createUrl(['/']);
?>
Hello <?= $user->username ?>,

Your credentials for site: <?= $link ?>


Login: <?=$user->email?>

Password: <?=$password?>