<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use Codeception\Util\HttpCode;
use common\fixtures\UserFixture;
use common\fixtures\UserRoleFixture;
use yii\helpers\Url;

class UserListCest
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
		$I->amOnRoute('user/index');
		$I->seeResponseCodeIsRedirection();
		$I->followRedirect();
		$I->seeInCurrentUrl(Url::to(['site/login']));
	}

	public function checkUserNotAdmin(FunctionalTester $I): void
	{
		$I->amLoggedInAs($I->grabFixture('user', 1));
		$I->amOnRoute('user/index');
		$I->seeResponseCodeIs(HttpCode::FORBIDDEN);
	}

	public function checkUserAdmin(FunctionalTester $I): void
	{
		$I->amLoggedInAs($I->grabFixture('user', 0));
		$I->amOnRoute('user/index');
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->see('Users', 'a.active');
		$I->seeLink('Dashboard', Url::to(['site/index']));
		$I->seeLink('Users', Url::to(['user/index']));

		$I->seeLink('Create User', Url::to(['user/create']));
		$I->seeLink('', Url::to(['user/view', 'id' => 1]));
		$I->seeLink('', Url::to(['user/update', 'id' => 1]));
		$I->seeLink('', Url::to(['user/reset', 'id' => 1]));
		$I->seeLink('', Url::to(['user/delete', 'id' => 1]));
	}

	public function checkUserManager(FunctionalTester $I): void
	{
		$I->amLoggedInAs($I->grabFixture('user', 6));
		$I->amOnRoute('user/index');
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->see('Users', 'a.active');
		$I->seeLink('Dashboard', Url::to(['site/index']));
		$I->seeLink('Users', Url::to(['user/index']));

		$I->seeLink('Create User', Url::to(['user/create']));
		$I->seeLink('', Url::to(['user/view', 'id' => 1]));
		$I->seeLink('', Url::to(['user/update', 'id' => 1]));
		$I->seeLink('', Url::to(['user/reset', 'id' => 1]));
		$I->dontSeeLink('', Url::to(['user/delete', 'id' => 1]));
	}

	public function checkUserViewer(FunctionalTester $I): void
	{
		$I->amLoggedInAs($I->grabFixture('user', 7));
		$I->amOnRoute('user/index');
		$I->seeResponseCodeIs(HttpCode::OK);

		$I->see('Users', 'a.active');
		$I->seeLink('Dashboard', Url::to(['site/index']));
		$I->seeLink('Users', Url::to(['user/index']));

		$I->dontSeeLink('Create User', Url::to(['user/create']));
		$I->seeLink('', Url::to(['user/view', 'id' => 1]));
		$I->dontSeeLink('', Url::to(['user/update', 'id' => 1]));
		$I->dontSeeLink('', Url::to(['user/reset', 'id' => 1]));
		$I->dontSeeLink('', Url::to(['user/delete', 'id' => 1]));
	}
}
