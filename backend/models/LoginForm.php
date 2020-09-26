<?php
namespace backend\models;

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
	 * @param array $params the additional name-value pairs given in the rule
	 */
	public function validatePassword($attribute, $params)
	{
		parent::validatePassword($attribute, $params);

		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user || !$user->admin) {
				$this->addError($attribute, 'Login for admin only, sorry bro');
			}
		}
	}
}