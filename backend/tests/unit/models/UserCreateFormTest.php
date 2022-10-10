<?php
namespace backend\tests\unit\models;

use backend\models\User;
use backend\tests\models\TestUserCreateForm;

class UserCreateFormTest extends \Codeception\Test\Unit
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
				'class' => \common\fixtures\UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php'
			],
			'user_role' => [
				'class' => \common\fixtures\UserRoleFixture::class,
				'dataFile' => codecept_data_dir() . 'user_role.php'
			]
		];
	}

	public function testCreateBlankForm()
	{
		$model = $this->createFormModel('', '');

		verify($model->create())->false();
		verify($model->getErrors('username'))->notEmpty();
		verify($model->getErrors('email'))->notEmpty();
	}

	public function testCreateNotValidEmail()
	{
		$model = $this->createFormModel('test.login', 'mail#example.com');

        verify($model->create())->false();
        verify($model->getErrors('username'))->empty();
		verify($model->getErrors('email'))->notEmpty();
	}

	public function testCreateDuplicateEmail()
	{
		$model = $this->createFormModel('bayer.hudson', 'nicole.paucek@schultz.info');

        verify($model->create())->false();
        verify($model->getErrors('username'))->empty();
        verify($model->getErrors('email'))->notEmpty();
		verify($model->getFirstError('email'))->equals('This email address has already been taken.');
	}

	public function testCreateCorrectUser()
	{
		$this->testCreateCorrect('test.user','some_email@example.com');
	}

	public function testCreateCorrectAdmin()
	{
		$this->testCreateCorrect('test.user','some_email@example.com', [ROLE_ADMIN], [ROLE_ADMIN]);
	}

	public function testCreateWithRolesWithoutAvailableRoles()
	{
		$model = $this->createFormModel('test.user','some_email@example.com', [], [ROLE_ADMIN]);
		verify($model->create())->false();
		verify($model->getErrors('role'))->notEmpty('error for role must be filled');
	}

	public function testCreateWithOverRolesWithoutAvailableRoles()
	{
		$model = $this->createFormModel('test.user','some_email@example.com', [ROLE_MANAGER], [ROLE_ADMIN]);
		verify($model->create())->false();
		verify($model->getErrors('role'))->notEmpty('error for role must be filled');
	}

	/**
	 * @param string $username
	 * @param string $email
	 * @param string[] $availableRole
	 * @param string[] $role
	 * @return void
	 */
	protected function testCreateCorrect(string $username, string $email, array $availableRole = [], array $role = []): void
	{
		$model = $this->createFormModel($username, $email, $availableRole, $role);
		verify($model->create())->true();

		/** @var User $user */
		$user = $this->tester->grabRecord(User::class, [
			'username' => $username,
			'email' => $email,
			'status' => User::STATUS_ACTIVE
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

	protected function createFormModel(string $username, string $email, array $availableRole = [], array $role = []): TestUserCreateForm
	{
		$credentials = [
			'username' => $username,
			'email' => $email,
			'role' => $role
		];
		$model = new TestUserCreateForm();
		if (!empty($availableRole)) {
			$model->setAvailableRole($availableRole);
		}
		$model->load($credentials, '');

		return $model;
	}
}