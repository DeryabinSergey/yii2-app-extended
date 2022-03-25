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

		verify($model->create())->false();
		verify($model->getErrors('username'))->notEmpty();
		verify($model->getErrors('email'))->notEmpty();
	}

	public function testCreateNotValidEmail()
	{
		$model = new UserCreateForm([
			'username' => 'test.login',
			'email' => 'mail#example.com'
		]);

        verify($model->create())->false();
        verify($model->getErrors('username'))->empty();
		verify($model->getErrors('email'))->notEmpty();
	}

	public function testCreateDuplicateEmail()
	{
		$model = new UserCreateForm([
			'username' => 'bayer.hudson',
			'email' => 'nicole.paucek@schultz.info'
		]);

        verify($model->create())->false();
        verify($model->getErrors('username'))->empty();
        verify($model->getErrors('email'))->notEmpty();
		verify($model->getFirstError('email'))->equals('This email address has already been taken.');
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

		verify($model->create())->true();

		/** @var \common\models\User $user */
		$user = $this->tester->grabRecord(\common\models\User::class, [
			'username' => $username,
			'email' => $email,
			'status' => \common\models\User::STATUS_ACTIVE,
			'admin' => $admin
		]);

		verify($user)->instanceOf(User::class);

		$this->tester->seeEmailIsSent();
		$mail = $this->tester->grabLastSentEmail();
        verify($mail)->instanceOf(\yii\mail\MessageInterface::class);
        verify($mail->getTo())->arrayHasKey($email);
        verify($mail->getFrom())->arrayHasKey(\Yii::$app->params['supportEmail']);
        verify($mail->getSubject())->equals('Account credentials at ' . \Yii::$app->name);
        verify($mail->toString())->stringContainsString($model::$password);
	}
}