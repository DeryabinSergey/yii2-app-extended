<?php
namespace backend\tests\unit\models;

use common\fixtures\UserFixture;
use backend\models\UserCreateForm;
use backend\tests\models\TestUserCreateForm;
use common\models\User;

class CreateUserFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
    }

	public function testCreateBlankForm()
	{
		$model = new UserCreateForm([
			'username' => '',
			'email' => ''
		]);

		expect_not($model->create());
		expect_that($model->getErrors('username'));
		expect_that($model->getErrors('email'));
	}

	public function testCreateNotValidEmail()
	{
		$model = new UserCreateForm([
			'username' => 'test.login',
			'email' => 'mail#example.com'
		]);

		expect_not($model->create());
		expect_not($model->getErrors('username'));
		expect_that($model->getErrors('email'));
	}

	public function testCreateDuplicateEmail()
	{
		$model = new UserCreateForm([
			'username' => 'bayer.hudson',
			'email' => 'nicole.paucek@schultz.info'
		]);

		expect_not($model->create());
		expect_not($model->getErrors('username'));
		expect_that($model->getErrors('email'));
		expect($model->getFirstError('email'))->equals('This email address has already been taken.');
	}

    public function testCreateCorrectNotAdmin()
    {
        $this->testCreateCorrect('test.user','some_email@example.com');
    }

	public function testCreateCorrectAdmin()
	{
		$this->testCreateCorrect('test.user','some_email@example.com', true);
	}

	protected function testCreateCorrect($username, $email, $admin = false)
	{
		$credentials = [
			'username' => $username,
			'email' => $email
		];
		if ($admin) {
			$credentials['admin'] = $admin;
		}
		$model = new TestUserCreateForm($credentials);

		expect($model->create())->true();

		/** @var \common\models\User $user */
		$user = $this->tester->grabRecord('common\models\User', [
			'username' => $username,
			'email' => $email,
			'status' => \common\models\User::STATUS_ACTIVE,
			'admin' => $admin
		]);

		expect($user)->isInstanceOf(User::class);

		$this->tester->seeEmailIsSent();
		$mail = $this->tester->grabLastSentEmail();
		expect($mail)->isInstanceOf(\yii\mail\MessageInterface::class);
		expect($mail->getTo())->hasKey($email);
		expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account credentials at ' . \Yii::$app->name);
		expect($mail->toString())->stringContainsString($model::$password);
	}
}