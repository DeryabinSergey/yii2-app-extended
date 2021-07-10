<?php

namespace backend\controllers;

use Yii;

use yii\helpers\ArrayHelper;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\Response;


/**
 * Site controller
 */
class SiteController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
	    return
		    ArrayHelper::merge(
			    [
		            'access' => [
		                'rules' => [
		                    [
		                        'actions' => ['login', 'error'],
		                        'allow' => true,
		                    ],
		                    [
		                        'actions' => ['logout'],
		                        'allow' => true,
		                        'roles' => ['@'],
		                    ]
		                ],
		            ],
		            'verbs' => [
		                'class' => VerbFilter::class,
		                'actions' => [
		                    'logout' => ['post'],
		                ],
		            ],
		        ],
			    parent::behaviors()
		    );
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
