<?php

/* @var yii\web\View $this */
/* @var common\models\User $user */
/* @var string $password */

$link = Yii::$app->urlManagerFrontend->createUrl(['/']);
?>
Hello <?= $user->username ?>,

Your credentials for site: <?= $link ?>


Login: <?=$user->email?>

Password: <?=$password?>