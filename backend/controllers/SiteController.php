<?php

namespace backend\controllers;

use backend\models\LoginForm;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
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
    public function behaviors(): array
    {
	    return
		    ArrayHelper::merge(
			    [
				    'access' => [
					    'class' => AccessControl::class,
					    'rules' => [
						    [
							    'actions' => ['login', 'error'],
							    'allow' => true,
						    ],
						    [
							    'actions' => ['logout'],
							    'allow' => true,
							    'roles' => ['@'],
						    ],
						    [
								'actions' => ['index'],
							    'allow' => true,
							    'permissions' => [PERMISSION_BACKEND],
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
	 * @return Response|string
	 */
    public function actionIndex(): Response|string
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
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
	 * @return Response|string
	 */
    public function actionLogout(): Response|string
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}