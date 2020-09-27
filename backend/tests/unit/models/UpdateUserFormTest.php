<?php
namespace backend\tests\unit\models;

use common\fixtures\UserFixture;
use backend\models\UserUpdateForm;
use common\models\User;

class UpdateUserFormTest extends \Codeception\Test\Unit
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

    public function testUpdateNotExistedUser()
    {
    	$model = UserUpdateForm::findOne(3);

    	expect($model)->null();
    }

    public function testUpdateBlankForm()
    {
	    $model = UserUpdateForm::findOne(
		    $this->tester->grabFixture('user', 0)->id
	    );

    	expect($model)->isInstanceOf(User::class);

    	$model->attributes = [
    		'username' => '',
		    'email' => '',
		    'status' => ''
	    ];

	    expect($model->save())->false();
	    expect_that($model->getErrors('username'));
	    expect($model->getFirstError('username'))->equals('Username cannot be blank.');
	    expect_that($model->getErrors('email'));
	    expect($model->getFirstError('email'))->equals('Email cannot be blank.');
        expect_that($model->getErrors('status'));
	    expect($model->getFirstError('status'))->equals('Status cannot be blank.');
    }

	public function testUpdateNotValidData()
	{
		$model = UserUpdateForm::findOne(
			$this->tester->grabFixture('user', 0)->id
		);

		expect($model)->isInstanceOf(User::class);

		$model->attributes = [
			'email' => 'mail#example.com',
			'status' => 999
		];

		expect($model->save())->false();
		expect_that($model->getErrors('email'));
		expect($model->getFirstError('email'))->equals('Email is not a valid email address.');
		expect_that($model->getErrors('status'));
		expect($model->getFirstError('status'))->equals('Status is invalid.');
	}

	public function testUpdateDuplicateEmail()
	{
		$userOneFixture = $this->tester->grabFixture('user', 0);
		$userTwoFixture = $this->tester->grabFixture('user', 1);
		$model = UserUpdateForm::findOne($userOneFixture->id);

		expect($model)->isInstanceOf(User::class);

		$model->attributes = [
			'email' => $userTwoFixture->email
		];

		expect($model->save())->false();
		expect_that($model->getErrors('email'));
		expect($model->getFirstError('email'))->equals('This email address has already been taken.');
	}

    public function testUpdateCorrectNotAdmin()
    {
        $this->testUpdateCorrect('test.user','some_email@example.com', User::STATUS_INACTIVE, false);
    }

	public function testUpdateCorrectAdmin()
	{
		$this->testUpdateCorrect('test.user','some_email@example.com', User::STATUS_INACTIVE, true);
	}

	protected function testUpdateCorrect($username, $email, $status, $admin = false)
	{
		$userFixture = $this->tester->grabFixture('user', 0);
		$model = UserUpdateForm::findOne($userFixture->id);

		expect($model)->isInstanceOf(User::class);

		$model->attributes = [
			'username' => $username,
			'email' => $email,
			'status' => $status,
			'admin' => $admin
		];

		expect($model->save())->true();
		expect($model->getErrors())->isEmpty();

		/** @var \common\models\User $user */
		$user = $this->tester->grabRecord(User::class, [
			'username' => $username,
			'email' => $email,
			'status' => $status,
			'admin' => $admin
		]);

		expect($user)->isInstanceOf(User::class);
		expect($user->auth_key)->equals($userFixture->auth_key);
		expect($user->password_hash)->equals($userFixture->password_hash);
		expect($user->password_reset_token)->equals($userFixture->password_reset_token);
		expect($user->verification_token)->equals($userFixture->verification_token);
	}
}