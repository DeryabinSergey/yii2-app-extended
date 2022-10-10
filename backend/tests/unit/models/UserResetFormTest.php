<?php
namespace backend\tests\unit\models;

use backend\models\User;
use backend\tests\models\TestUserResetForm;

class UserResetFormTest extends \Codeception\Test\Unit
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
			]
		];
	}

	public function testSuccessResetPassword()
	{
		/** @var User $user */
		$user = $this->tester->grabFixture('user', 0);
		$model = new TestUserResetForm($user);

		verify($model->resetPassword())->true();
		/** @var User $user */
		$user = $this->tester->grabRecord(User::class, ['id' => $user->id]);
		verify($user)->instanceOf(User::class);
		verify($user->validatePassword($model::$password))->true();

		$this->tester->seeEmailIsSent();
		$mail = $this->tester->grabLastSentEmail();
		verify($mail)->instanceOf(\yii\mail\MessageInterface::class);
		verify($mail->getTo())->arrayHasKey($user->email);
		verify($mail->getFrom())->arrayHasKey(\Yii::$app->params['supportEmail']);
		verify($mail->getSubject())->equals('Account credentials at ' . \Yii::$app->name);
		verify($mail->toString())->stringContainsString($model::$password);
	}
}