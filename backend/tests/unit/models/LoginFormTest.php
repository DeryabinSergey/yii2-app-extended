<?php

namespace backend\tests\unit\models;

use Yii;
use backend\models\LoginForm;
use common\fixtures\UserFixture;

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
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
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
			'email' => 'sfriesen2@jenkins.info',
			'password' => 'password_0',
		]);

        verify($model->login())->false('model should not login user');
        verify($model->errors)->arrayHasKey('password', 'error message should be set');
        verify(Yii::$app->user->isGuest)->true('user should not be logged in');
	}

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'email' => 'nicole.paucek@schultz.info',
            'password' => 'password_0',
        ]);

        verify($model->login())->true('model should login user');
        verify($model->errors)->arrayHasNotKey('password', 'error message should not be set');
        verify(Yii::$app->user->isGuest)->false('user should be logged in');
    }
}
