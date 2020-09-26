<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;

/**
 * Class LoginCest
 */
class LoginCest
{
    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

	public function _before(FunctionalTester $I)
	{
		$I->amOnRoute('site/login');
	}

	protected function formParams($email, $password)
	{
		return [
			'LoginForm[email]' => $email,
			'LoginForm[password]' => $password,
		];
	}

	public function checkEmpty(FunctionalTester $I)
	{
		$I->submitForm('#login-form', $this->formParams('', ''));
		$I->seeValidationError('Email cannot be blank.');
		$I->seeValidationError('Password cannot be blank.');
	}

	public function checkWrongPassword(FunctionalTester $I)
	{
		$I->submitForm('#login-form', $this->formParams('admin@example.com', 'wrong'));
		$I->seeValidationError('Incorrect username or password.');
	}

	public function checkInactiveAccount(FunctionalTester $I)
	{
		$I->submitForm('#login-form', $this->formParams('test@mail.com', 'Test1234'));
		$I->seeValidationError('Incorrect username or password');
	}
    
    /**
     * @param FunctionalTester $I
     */
    public function loginUser(FunctionalTester $I)
    {
	    $I->submitForm('#login-form', $this->formParams('sfriesen@jenkins.info', 'password_0'));

        $I->see('Logout (erau)', 'form button[type=submit]');
        $I->dontSeeLink('Login');
    }

	/**
	 * @param FunctionalTester $I
	 */
	public function denyNotAdminUser(FunctionalTester $I)
	{
		$I->submitForm('#login-form', $this->formParams('sfriesen2@jenkins.info', 'password_0'));

		$I->see('Login for admin only, sorry bro', 'form div.invalid-feedback');
		$I->see('Login', 'form button[type=submit]');
	}
}
