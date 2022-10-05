<?php

namespace backend\models;

use Yii;

/**
 * Login form for backend
 */
class LoginForm extends \common\models\LoginForm
{
	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array|null $params the additional name-value pairs given in the rule
	 */
	public function validatePassword(string $attribute, array|null $params = null): void
	{
		parent::validatePassword($attribute, $params);

		if (
			$this->hasErrors() === false
			&& $this->getUser() !== null
			&& Yii::$app->authManager->checkAccess($this->getUser()->id, PERMISSION_BACKEND) === false
		) {
			$this->addError($attribute, 'Login for admin only, sorry bro');
		}
	}
}