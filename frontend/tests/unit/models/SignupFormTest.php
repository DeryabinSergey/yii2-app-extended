<?php

namespace frontend\tests\unit\models;

use common\fixtures\UserFixture;
use frontend\models\SignupForm;

class SignupFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
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

    public function testCorrectSignup()
    {
        $model = new SignupForm([
            'email' => 'some_email@example.com',
            'password' => 'some_password',
        ]);

        $user = $model->signup();
        verify($user)->notEmpty();

        $this->checkSignupEmail(
            /** @var \common\models\User $user */
            $this->tester->grabRecord('common\models\User', [
                'username' => 'some_email',
                'email' => 'some_email@example.com',
                'status' => \common\models\User::STATUS_INACTIVE
            ])
        );

        $this->tester->seeEmailIsSent();

        $mail = $this->tester->grabLastSentEmail();

        verify($mail)->instanceOf('yii\mail\MessageInterface');
        verify($mail->getTo())->arrayHasKey('some_email@example.com');
        verify($mail->getFrom())->arrayHasKey(\Yii::$app->params['supportEmail']);
        verify($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
        verify($mail->toString())->stringContainsString($user->verification_token);
    }

    public function testCorrectSignupWithDeletedBeforeEmail()
    {
        $model = new SignupForm([
            'email' => 'nicolas.dianna@hotmail.com',
            'password' => 'some_password',
        ]);

        $user = $model->signup();
        expect($user)->true();

        $this->checkSignupEmail(
            /** @var \common\models\User $user */
            $this->tester->grabRecord('common\models\User', [
                'username' => 'nicolas.dianna',
                'email' => 'nicolas.dianna@hotmail.com',
                'status' => \common\models\User::STATUS_INACTIVE
            ])
        );
    }

    public function testNotCorrectSignup()
    {
        $model = new SignupForm([
            'username' => 'troy.becker',
            'email' => 'nicolas.dianna@hotmail.com',
            'password' => 'some_password',
        ]);

        verify($model->signup())->empty();
        verify($model->getErrors('username'))->notEmpty();
        verify($model->getErrors('email'))->notEmpty();

        verify($model->getFirstError('username'))
            ->equals('This username has already been taken.');
        verify($model->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }

    protected function checkSignupEmail(\common\models\User $user)
    {
        $this->tester->seeEmailIsSent();

        $mail = $this->tester->grabLastSentEmail();

        expect($mail)->isInstanceOf('yii\mail\MessageInterface');
        expect($mail->getTo())->hasKey($user->email);
        expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
        expect($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
        expect($mail->toString())->stringContainsString($user->verification_token);
    }
}
