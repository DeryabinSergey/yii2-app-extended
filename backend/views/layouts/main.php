<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\Breadcrumbs;
use common\widgets\Alert;
use rmrevin\yii\fontawesome\FAS;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="<?=Yii::$app->homeUrl?>"><?=Yii::$app->name?></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <ul class="navbar-nav d-none d-md-block px-3">
        <li class="nav-item text-nowrap">
            <?= Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton('Logout (' . Yii::$app->user->identity->username . ')', ['class' => 'btn btn-link py-0 nav-link'])
                . Html::endForm()
            ?>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <?php
                    $menuItems = [
	                    ['label' => FAS::i('home') . ' Dashboard', 'url' => ['/site/index'], 'encode' => false],
	                    ['label' => FAS::i('users') . ' Users', 'url' => ['/user/index'], 'encode' => false],
                    ];
	                $menuItems[] = '<li class="nav-item">'
		                . Html::beginForm(['/site/logout'], 'post')
		                . Html::submitButton(FAS::i('sign-out-alt') . ' Logout (' . Yii::$app->user->identity->username . ')', ['class' => 'btn btn-sm btn-link d-md-none nav-link'])
		                . Html::endForm()
		                . '</li>';
                    $menuItems[] = ['label' => FAS::i('share-square') . ' Go to main', 'url' => Yii::$app->urlManagerFrontend->createUrl(['/']), 'encode' => false, 'linkOptions' => ['target' => '_blank', 'class' => 'text-success border-top mt-4 pt-4']];
                    echo Nav::widget([
	                    'options' => ['class' => 'flex-column'],
	                    'items' => $menuItems,
                    ]);
                ?>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
	        <?= Breadcrumbs::widget([
		        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'options' => ['class' => 'my-3']
	        ]) ?>
	        <?= Alert::widget(['options' => ['class' => 'my-3']]) ?>

            <div class="mt-3">
	            <?= $content ?>
            </div>
        </main>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>