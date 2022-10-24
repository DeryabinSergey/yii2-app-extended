<?php

namespace backend\tests\models;

/**
 * For tests only
 */
class TestUserResetForm extends \backend\models\UserResetForm
{
	public static $password;

	/**
	 * @inheritdoc
	 */
	public static function sendEmail($user, $password): bool
	{
		self::$password = $password;

		return parent::sendEmail($user, $password);
	}
}