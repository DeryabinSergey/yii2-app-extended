<?php
namespace backend\tests\unit\models;

use backend\models\UserUpdateForm;
use backend\models\User;

class UserUpdateFormTest extends \Codeception\Test\Unit
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
				'modelClass' => User::class,
				'dataFile' => codecept_data_dir() . 'user.php'
			],
			'user_role' => [
				'class' => \common\fixtures\UserRoleFixture::class,
				'dataFile' => codecept_data_dir() . 'user_role.php'
			]
		];
	}

    public function testUpdateBlankForm()
    {
        $model = UserUpdateForm::findOne(['id' => 1]);
        verify($model)->instanceOf(User::class);

        $model->load([
            'username' => '',
            'email' => '',
            'status' => ''
        ], '');

        verify($model->save())->false();
        verify($model->getErrors('username'))->notEmpty();
        verify($model->getFirstError('username'))->equals('Username cannot be blank.');
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('Email cannot be blank.');
        verify($model->getErrors('status'))->notEmpty();
        verify($model->getFirstError('status'))->equals('Status cannot be blank.');
    }

    public function testUpdateNotValidData()
    {
	    $model = UserUpdateForm::findOne(['id' => 1]);
        verify($model)->instanceOf(User::class);

        $model->load([
			'username' => '',
            'email' => 'mail#example.com',
            'status' => 999,
	        'role' => ['not existed role']
        ], '');

        verify($model->save())->false();
	    verify($model->getErrors('username'))->notEmpty();
	    verify($model->getFirstError('username'))->equals('Username cannot be blank.');
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('Email is not a valid email address.');
        verify($model->getErrors('status'));
        verify($model->getFirstError('status'))->equals('Status is invalid.');
        verify($model->getErrors('role'));
        verify($model->getFirstError('role'))->equals('Role is invalid.');
    }

    public function testUpdateDuplicateEmail()
    {
        $model = UserUpdateForm::findOne(['id' => 1]);
        $user = $this->tester->grabFixture('user', 1);
        verify($model)->instanceOf(User::class);

        $model->load([
            'email' => $user->email
        ], '');
        verify($model->save())->false();
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('This email address has already been taken.');
    }

    public function testUpdateUndeleteWithExistedUserDuplicateEmail()
    {
        $model = UserUpdateForm::findOne(['id' => 5]);
        verify($model)->instanceOf(User::class);

        $model->load([
            'status' => User::STATUS_ACTIVE,
        ], '');

        verify($model->save())->false();
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('Another user already registered with this email.');
    }

    public function testUpdateCorrectNotAdmin()
    {
        $this->testUpdateCorrect(
			2,
			'dziemann',
			'jenkins.karlee@walsh.com',
			User::STATUS_INACTIVE
        );
    }

    public function testUpdateCorrectSetAllowedPermissions()
    {
	    $this->testUpdateCorrect(
		    2,
		    'danderson',
		    'mozell43@gmail.com',
		    User::STATUS_ACTIVE,
		    [ROLE_ADMIN, ROLE_MANAGER, ROLE_VIEWER],
		    [ROLE_MANAGER, ROLE_VIEWER]
	    );
    }

    public function testUpdateSetNotAvailablePermissions()
    {
	    $model = $this->updateModel(
		    2,
		    'nhudson',
		    'sboehm@gmail.com',
		    User::STATUS_ACTIVE,
		    [ROLE_MANAGER, ROLE_VIEWER],
		    [ROLE_ADMIN, ROLE_MANAGER, ROLE_VIEWER]
	    );

	    verify($model->save())->false();
	    verify($model->getErrors())->arrayHasKey('role');
    }

    public function testUpdateCorrectRemoveAvailablePermissions()
    {
	    $this->testUpdateCorrect(
		    1,
		    'ike.graham',
		    'crooks.kiarra@prosacco.com',
		    User::STATUS_ACTIVE,
		    [ROLE_ADMIN, ROLE_MANAGER, ROLE_VIEWER],
		    [ROLE_VIEWER]
	    );
    }

	public function testUpdateRemoveNotAvailablePermissions()
	{
		$model = $this->updateModel(
			1,
			'mariana53',
			'connelly.everette@gmail.com',
			User::STATUS_ACTIVE,
			[ROLE_MANAGER, ROLE_VIEWER],
			[ROLE_MANAGER, ROLE_VIEWER]
		);

		verify($model->save())->false();
		verify($model->getErrors())->arrayHasKey('role');
	}

    protected function testUpdateCorrect(
	    int $id,
	    string $username,
	    string $email,
	    int $status,
	    array $availableRole = [],
	    array $role = []
    ): void
    {
        $model = $this->updateModel($id, $username, $email, $status, $availableRole, $role);

        verify($model->save())->true();
        verify($model->getErrors())->empty();

        /** @var User $user */
        $user = $this->tester->grabRecord(User::class, ['id' => $id]);
	    $userRoles = array_keys(\Yii::$app->authManager->getRolesByUser($id));

        verify($user)->instanceOf(User::class);
        verify($user->auth_key)->equals($user->auth_key);
        verify($user->password_hash)->equals($user->password_hash);
        verify($user->password_reset_token)->equals($user->password_reset_token);
        verify($user->verification_token)->equals($user->verification_token);
		verify($user->username)->equals($username);
		verify($user->email)->equals($email);
		verify($user->status)->equals($status);
		verify($userRoles)->arraySameSize($role);
		foreach($userRoles as $userRole) {
			verify($role)->arrayContains($userRole);
		}
    }

	protected function updateModel(
		int $id,
		string $username,
		string $email,
		int $status,
		array $availableRole = [],
		array $role = []
	): UserUpdateForm
	{
		$model = UserUpdateForm::findOne(['id' => $id]);
		verify($model)->instanceOf(User::class);
		if (!empty($availableRole)) {
			$model->setAvailableRole($availableRole);
		}

		$model->load([
			'username' => $username,
			'email' => $email,
			'status' => $status,
			'role' => $role
		], '');

		return $model;
	}
}