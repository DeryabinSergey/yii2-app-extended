<?php

namespace backend\controllers;

use backend\models\UserCreateForm;
use backend\models\UserSearch;
use backend\models\UserUpdateForm;
use common\models\User;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(
			[
				'access' => [
					'class' => AccessControl::class,
					'rules' => [
						[
							'actions' => ['index', 'view'],
							'allow' => true,
							'permissions' => [PERMISSION_USER_READ]
						],
						[
							'actions' => ['create'],
							'allow' => true,
							'permissions' => [PERMISSION_USER_CREATE],
						],
						[
							'actions' => ['update', 'reset'],
							'allow' => true,
							'permissions' => [PERMISSION_USER_UPDATE],
						],
						[
							'actions' => ['delete'],
							'allow' => true,
							'permissions' => [PERMISSION_USER_DELETE],
						]
					],
				],
	            'verbs' => [
	                'class' => VerbFilter::class,
	                'actions' => [
	                    'delete' => ['POST'],
		                'reset' => ['POST']
	                ],
	            ]
            ],
	        parent::behaviors()
        );
    }

	/**
	 * Lists all User models.
	 * @return Response|string
	 */
    public function actionIndex(): Response|string
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	/**
	 * Displays a single User model.
	 * @param integer $id
	 * @return Response|string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
    public function actionView(int $id): Response|string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	/**
	 * Creates a new User model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 * @throws \yii\base\Exception
	 */
    public function actionCreate(): Response|string
    {
        $model = new UserCreateForm();

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
	        'roleList' => Yii::$app->authManager->getRoles(),
        ]);
    }

	/**
	 * Updates an existing User model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return Response|string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id, UserUpdateForm::class);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
	        'roleList' => Yii::$app->authManager->getRoles(),
        ]);
    }

	/**
	 * Reset password for User and send to email
	 * @param integer $id
	 * @return Response|string
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws \yii\base\Exception
	 */
    public function actionReset(int $id): Response|string
    {
    	$user = $this->findModel($id);

	    $password = Yii::$app->security->generateRandomString(Yii::$app->params['user.passwordMinLength']);
	    $user->setPassword($password);
	    $user->password_reset_token = null;
	    if ($user->save() && UserCreateForm::sendEmail($user, $password)) {
		    Yii::$app->session->addFlash('success', 'Password was changed and email to user');
	    } else {
		    Yii::$app->session->addFlash('danger', 'Error while change password and email it to user');
	    }

	    return $this->redirect(['index']);
    }

	/**
	 * Deletes an existing User model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return Response|string
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete(int $id): Response|string
    {
        $this->findModel($id)->delete();

	    Yii::$app->session->addFlash('success', 'User successfully removed');

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $object
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id, string $object = User::class): User
    {
        if (($model = $object::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
