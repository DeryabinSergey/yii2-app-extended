<?php

namespace common\models;

class UserRole extends \yii\base\BaseObject
{
	public int $userId;
	public \yii\rbac\ManagerInterface $authManager;
	/** @var \yii\rbac\Role[]  */
	private array $roles;
	/**
	 * {@inheritdoc}
	 */
	public function init(): void
	{
		parent::init();

		if (($this->authManager ?? null) === null) {
			$this->authManager = \Yii::$app->authManager;
		}
	}

	/**
	 * @return \yii\rbac\Role[]
	 */
	public function getRoles(): array
	{
		if (($this->roles ?? null) !== null) {
			return $this->roles;
		}

		$this->roles = $this->authManager->getRolesByUser($this->userId);
		if (empty($this->roles)) {
			return $this->roles;
		}

		foreach(array_keys($this->roles) as $roleName) {
			$this->roles += $this->authManager->getChildRoles($roleName);
		}

		return $this->roles;
	}
}