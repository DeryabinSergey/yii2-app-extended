<?php

/* @var yii\web\View $this */
/* @var string $name */
/* @var string $message */
/* @var Exception $exception */

use yii\bootstrap5\Html;

$this->title = $name;
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <?php
        if ($exception instanceof yii\web\ForbiddenHttpException && !\Yii::$app->user->isGuest) {
            echo
                Html::beginForm(['/site/logout'], 'post', ['class' => 'mt-5 text-center'])
                . Html::submitButton('Logout as ' . Yii::$app->user->identity->username, ['class' => 'btn btn-lg btn-info logout'])
                . Html::endForm();
        }
    ?>

</div>