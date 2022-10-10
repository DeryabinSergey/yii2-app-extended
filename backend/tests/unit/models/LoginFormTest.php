<?php

namespace backend\tests\unit\models;

use backend\models\LoginForm;
use Yii;

/**
 * Login form test
 */
class LoginFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            [
                'class' => \common\fixtures\UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
	        [
		        'class' => \common\fixtures\UserRoleFixture::class,
		        'dataFile' => codecept_data_dir() . 'user_role.php'
	        ]
        ];
    }

    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'email' => 'not_existing_username@example.com',
            'password' => 'not_existing_password',
        ]);

        verify($model->login())->false('model should not login user');
        verify(Yii::$app->user->isGuest)->true('user should not be logged in');
    }

    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'email' => 'nicole.paucek@schultz.info',
            'password' => 'wrong_password',
        ]);

        verify($model->login())->false('model should not login user');
        verify($model->errors)->arrayHasKey('password', 'error message should be set');
        verify(Yii::$app->user->isGuest)->true('user should not be logged in');
    }

	public function testLoginCorrectNotAdmin()
	{
		$model = new LoginForm([
			'email' => 'dovie.gulgowski@swift.biz',
			'password' => 'Xk4_zCzs',
		]);

        verify($model->login())->false('model should not login user');
		verify($model->getErrors())->notEmpty('error must be not empty');
		verify($model->getFirstError('password'))->equals($model->errorMessageNotAdmin, 'must be information about user not admin');
        verify(Yii::$app->user->isGuest)->true('user should not be logged in');
	}

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'email' => 'nicole.paucek@schultz.info',
            'password' => 'password_0',
        ]);

        verify($model->login())->true('model should login user');
	    verify($model->errors)->empty('error messages should be empty');
        verify(Yii::$app->user->isGuest)->false('user should be logged in');
    }

	public function testLoginInactiveUser()
	{
		$model = new LoginForm([
			'email' => 'kaden57@gmail.com',
			'password' => '3ZJoptEV',
		]);

		verify($model->login())->false('model should not login user');
		verify($model->errors)->arrayHasKey('password', 'error message should be set');
		verify(Yii::$app->user->isGuest)->true('user should not be logged in');
	}

	public function testLoginDeletedUser()
	{
		$model = new LoginForm([
			'email' => 'geraldine.hintz@hotmail.com',
			'password' => 'RbECfjkR',
		]);

		verify($model->login())->false('model should not login user');
		verify($model->errors)->arrayHasKey('password', 'error message should be set');
		verify(Yii::$app->user->isGuest)->true('user should not be logged in');
	}

	public function testLoginDeletedAndRecreatedUser()
	{
		$model = new LoginForm([
			'email' => 'emmerich.brionna@waelchi.com',
			'password' => 'LxXkWsZz',
		]);

		verify($model->login())->true('model should login user');
		verify($model->errors)->empty('error messages should be empty');
		verify(Yii::$app->user->isGuest)->false('user should be logged in');
		verify(Yii::$app->user->getIdentity()->username)->equals('eschroeder', 'username must be from new credentials');
	}
}
