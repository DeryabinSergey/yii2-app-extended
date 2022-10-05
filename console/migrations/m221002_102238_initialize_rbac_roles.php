<?php

use yii\db\Migration;

/**
 * Class m221002_102238_initialize_rbac_roles
 */
class m221002_102238_initialize_rbac_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		/** @var \yii\rbac\DbManager $auth */
	    $auth = Yii::$app->authManager;

		$controlPanelPermission = $auth->createPermission(PERMISSION_BACKEND);
		$controlPanelPermission->description = 'Access to backend';
		$auth->add($controlPanelPermission);

		$userCreatePermission = $auth->createPermission(PERMISSION_USER_CREATE);
	    $userCreatePermission->description = 'Create new users';
	    $auth->add($userCreatePermission);

		$userReadPermission = $auth->createPermission(PERMISSION_USER_READ);
		$userReadPermission->description = 'Read user information';
		$auth->add($userReadPermission);

		$userUpdatePermission = $auth->createPermission(PERMISSION_USER_UPDATE);
		$userUpdatePermission->description = 'Update user information';
		$auth->add($userUpdatePermission);

		$userDeletePermission = $auth->createPermission(PERMISSION_USER_DELETE);
		$userDeletePermission->description = 'Delete existed user';
		$auth->add($userDeletePermission);

		$admin = $auth->createRole(ROLE_ADMIN);
		$admin->description = 'Backend full control';
		$auth->add($admin);

		$viewer = $auth->createRole(ROLE_VIEWER);
		$viewer->description = 'Backend read only access';
		$auth->add($viewer);

		$manager = $auth->createRole(ROLE_MANAGER);
		$manager->description = 'Backend create/read/update access';
		$auth->add($manager);

		$auth->addChild($viewer, $controlPanelPermission);
		$auth->addChild($viewer, $userReadPermission);

		$auth->addChild($manager, $viewer);
		$auth->addChild($manager, $userCreatePermission);
		$auth->addChild($manager, $userUpdatePermission);

		$auth->addChild($admin, $manager);
		$auth->addChild($admin, $userDeletePermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
	    $auth = Yii::$app->authManager;

	    $auth->removeAll();
    }
}
