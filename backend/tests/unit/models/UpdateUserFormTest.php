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
        $model = UserUpdateForm::findOne(4);

        verify($model)->null();
    }

    public function testUpdateBlankForm()
    {
        $model = UserUpdateForm::findOne(
            $this->tester->grabFixture('user', 0)->id
        );

        verify($model)->instanceOf(User::class);

        $model->attributes = [
            'username' => '',
            'email' => '',
            'status' => ''
        ];

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
        $model = UserUpdateForm::findOne(
            $this->tester->grabFixture('user', 0)->id
        );

        verify($model)->instanceOf(User::class);

        $model->attributes = [
            'email' => 'mail#example.com',
            'status' => 999
        ];

        verify($model->save())->false();
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('Email is not a valid email address.');
        verify($model->getErrors('status'));
        verify($model->getFirstError('status'))->equals('Status is invalid.');
    }

    public function testUpdateDuplicateEmail()
    {
        $userOneFixture = $this->tester->grabFixture('user', 0);
        $userTwoFixture = $this->tester->grabFixture('user', 1);
        $model = UserUpdateForm::findOne($userOneFixture->id);

        verify($model)->instanceOf(User::class);

        $model->attributes = [
            'email' => $userTwoFixture->email
        ];

        verify($model->save())->false();
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('This email address has already been taken.');
    }

    public function testUpdateUndeleteWithExistedUserDuplicateEmail()
    {
        $model = UserUpdateForm::findOne(3);

        verify($model)->instanceOf(User::class);

        $model->attributes = [
            'status' => User::STATUS_ACTIVE,
        ];

        verify($model->save())->false();
        verify($model->getErrors('email'))->notEmpty();
        verify($model->getFirstError('email'))->equals('Another user already registered with this email.');
    }

    public function testUpdateCorrectNotAdmin()
    {
        $this->testUpdateCorrect('test.user','some_email@example.com', User::STATUS_INACTIVE, false);
    }

    public function testUpdateCorrectAdmin()
    {
        $this->testUpdateCorrect('test.user','some_email@example.com', User::STATUS_INACTIVE, true);
    }

    protected function testUpdateCorrect($username, $email, $status)
    {
        $userFixture = $this->tester->grabFixture('user', 0);
        $model = UserUpdateForm::findOne($userFixture->id);

        verify($model)->instanceOf(User::class);

        $model->attributes = [
            'username' => $username,
            'email' => $email,
            'status' => $status
        ];

        verify($model->save())->true();
        verify($model->getErrors())->empty();

        /** @var \common\models\User $user */
        $user = $this->tester->grabRecord(User::class, [
            'username' => $username,
            'email' => $email,
            'status' => $status
        ]);

        verify($user)->instanceOf(User::class);
        verify($user->auth_key)->equals($userFixture->auth_key);
        verify($user->password_hash)->equals($userFixture->password_hash);
        verify($user->password_reset_token)->equals($userFixture->password_reset_token);
        verify($user->verification_token)->equals($userFixture->verification_token);
    }
}