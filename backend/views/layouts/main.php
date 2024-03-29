<?php

/* @var \yii\web\View $this */
/* @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;

AppAsset::register($this);

$user = \Yii::$app->user;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="<?=Yii::$app->homeUrl?>"><?=Yii::$app->name?></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
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
    <div class="row justify-content-end">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <?php
                    $menuItems = [];
                    if ($user->can(PERMISSION_BACKEND)) {
                        $menuItems[] = ['label' => FAS::i('home') . ' Dashboard', 'url' => ['/site/index'], 'encode' => false];
                    }
                    if ($user->can(PERMISSION_USER_READ)) {
	                    $menuItems[] = ['label' => FAS::i('users') . ' Users', 'url' => ['/user/index'], 'encode' => false];
                    }
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
<?php $this->endPage();