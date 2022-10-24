<?php

namespace backend\models;

use Yii;

class UserResetForm extends BaseUserForm
{
	public function __construct(private User $user, array $config = [])
	{
		parent::__construct($config);
	}

	/**
	 * @return bool
	 * @throws \yii\base\Exception
	 */
	public function resetPassword(): bool
	{
		$password = Yii::$app->security->generateRandomString(Yii::$app->params['user.passwordMinLength']);
		$this->user->setPassword($password);
		$this->user->removePasswordResetToken();

		return $this->user->save() && static::sendEmail($this->user, $password);
	}
}