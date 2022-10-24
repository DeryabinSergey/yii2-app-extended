<?php

namespace backend\models;

use Yii;

/**
 * Login form for backend
 */
class LoginForm extends \common\models\LoginForm
{
	public string $errorMessageNotAdmin = 'Login for admin only, sorry bro';
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
			$this->addError($attribute, $this->errorMessageNotAdmin);
		}
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	protected function getUser(): ?User
	{
		if ($this->_user === null) {
			$this->_user = User::findByEmail($this->email);
		}

		return $this->_user;
	}
}