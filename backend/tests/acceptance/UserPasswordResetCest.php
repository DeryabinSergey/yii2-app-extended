<?php

namespace backend\tests\acceptance;

use backend\tests\AcceptanceTester;
use common\fixtures\UserFixture;
use common\fixtures\UserRoleFixture;
use yii\helpers\Url;

class UserPasswordResetCest
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

	public function checkAdmin(AcceptanceTester $I): void
	{
		$this->fillForm($I, 'nicole.paucek@schultz.info', 'password_0');

		$I->click("//tr[@data-key='2']/td/a[@title='Reset password']");
		$I->acceptPopup();
		$I->waitForText('Password was changed and email to user');
	}

	public function checkManager(AcceptanceTester $I): void
	{
		$this->fillForm($I, 'billy.cronin@wintheiser.org', 'a9yBknGE');

		$I->click("//tr[@data-key='2']/td/a[@title='Reset password']");
		$I->acceptPopup();
		$I->waitForText('Password was changed and email to user');
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

		$usersLink = "//a[@href='" . Url::to(['/user/index']) . "']";
		$I->waitForElement($usersLink);
		$I->click($usersLink);
		$I->waitForText('natalia.renner');
	}
}
