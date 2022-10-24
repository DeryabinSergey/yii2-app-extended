<?php

namespace backend\tests\acceptance;

use backend\tests\AcceptanceTester;
use common\fixtures\UserFixture;
use common\fixtures\UserRoleFixture;

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
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
	        'user_role' => [
				'class' => UserRoleFixture::class,
		        'dataFile' => codecept_data_dir() . 'user_role.php'
	        ]
        ];
    }

	/**
	 * @param AcceptanceTester $I
	 * @return void
	 */
	public function _before(AcceptanceTester $I): void
	{
		$I->amOnPage('/');
	}

	public function checkUser(AcceptanceTester $I): void
	{
		$this->fillForm($I, 'dovie.gulgowski@swift.biz', 'Xk4_zCzs');
		$I->wait(2);
		$I->waitForText('Login for admin only, sorry bro');
		$I->dontSee('Logout (natalia.renner)');
	}

	public function checkAdmin(AcceptanceTester $I): void
	{
		$this->fillForm($I, 'nicole.paucek@schultz.info', 'password_0');
		$I->wait(2);
		$I->waitForText('Logout (bayer.hudson)');
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string $email
	 * @param string $password
	 * @return void
	 */
	protected function fillForm(AcceptanceTester $I, string $email, string $password): void
	{
		$I->fillField("//input[@type='email']", $email);
		$I->fillField("//input[@type='password']", $password);
		$I->click("//button[@type='submit']");
	}
}
