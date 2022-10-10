<?php

namespace backend\models;

use Yii;

abstract class BaseUserForm extends \yii\base\Model
{
	/**
	 * Sends email to user with credentials
	 * @param User $user user model to with email should be send
	 * @param string $password generated user password
	 * @return bool whether the email was sent
	 */
	public static function sendEmail(User $user, string $password): bool
	{
		return Yii::$app
			->mailer
			->compose(
				['html' => 'emailNotify-html', 'text' => 'emailNotify-text'],
				['user' => $user, 'password' => $password]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
			->setTo($user->email)
			->setSubject('Account credentials at ' . Yii::$app->name)
			->send();
	}
}