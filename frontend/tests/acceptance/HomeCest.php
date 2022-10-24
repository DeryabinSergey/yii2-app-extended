<?php

namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;

class HomeCest
{
	public function checkHome(AcceptanceTester $I): void
	{
		$I->amOnPage('/site/index');
		$I->see('My Application');

		$I->seeLink('About');
		$I->click('About');
		$I->wait(2); // wait for page to be opened

		$I->see('This is the About page.');
	}
}
