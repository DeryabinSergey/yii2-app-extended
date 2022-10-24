<?php

namespace common\fixtures;

class UserRoleFixture extends \yii\test\ActiveFixture
{
	public $tableName = '{{%auth_assignment}}';
	public $depends = [UserFixture::class];
}