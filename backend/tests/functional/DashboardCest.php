<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use Codeception\Util\HttpCode;
use common\fixtures\UserFixture;
use common\fixtures\UserRoleFixture;
use yii\helpers\Url;

class DashboardCest
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
	        [
				'class' => UserRoleFixture::class,
		        'dataFile' => codecept_data_dir() . 'user_role.php'
	        ]
        ];
    }

	public function checkGuest(FunctionalTester $I): void
	{
		$I->stopFollowingRedirects();
		$I->amOnRoute('site/index');
		$I->seeResponseCodeIsRedirection();
		$I->followRedirect();
		$I->seeInCurrentUrl(Url::to(['site/login']));
	}

	public function checkUserNotAdmin(FunctionalTester $I): void
	{
		$I->amLoggedInAs($I->grabFixture('user', 1));
		$I->amOnRoute('site/index');
		$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
	}

	public function checkAdmin(FunctionalTester $I): void
	{
		$I->amLoggedInAs($I->grabFixture('user', 0));
		$I->amOnRoute('site/index');
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->see('Dashboard', 'a.active');
		$I->seeLink('Dashboard', Url::to(['site/index']));
		$I->seeLink('Users', Url::to(['user/index']));
	}
}
