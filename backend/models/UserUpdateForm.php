<?php

namespace backend\models;

use Yii;

/**
 * Update User form from admin
 */
class UserUpdateForm extends User
{
	/**
	 * Assigned roles to User
	 *
	 * @var string[]|string
	 */
	public array|string $role;

	/**
	 * List of roles, available for assign
	 * @var string[]
	 */
	protected array $availableRole = [];

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
	        ['username', 'trim'],
	        ['username', 'string', 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
            	'email', 'unique',
                'targetClass' => self::class,
                'when' => fn (self $model): bool => $model->isAttributeChanged('email'),
                'message' => 'This email address has already been taken.'
            ],
            [
                'email', 'unique',
                'targetClass' => self::class,
		        'filter' => ['!=', 'status', self::STATUS_DELETED],
                'when' => fn (self $model): bool =>
                        $model->isAttributeChanged('status')
                        && $model->getAttribute('status') != self::STATUS_DELETED
                        && $model->getOldAttribute('status') == self::STATUS_DELETED
	            ,
                'message' => 'Another user already registered with this email.'
            ],

	        ['status', 'in', 'range' => array_keys(self::statusList())],

	        ['role', 'each', 'rule' => ['in', 'range' => array_keys(Yii::$app->authManager->getRoles())]],

	        [['username', 'email', 'status'], 'required'],
        ];
    }

	/**
	 * @param string[] $role
	 * @return void
	 */
	public function setAvailableRole(array $role = []): void
	{
		$this->availableRole = $role;
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterFind(): void
	{
		parent::afterFind();

		$this->role = $this->id ? array_keys(Yii::$app->authManager->getRolesByUser($this->id)) : [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterValidate(): void
	{
		parent::afterValidate();

		list($rolesToAdd, $rolesToRemove) = $this->getRolesToAddAndRemove();
		foreach(array_diff($rolesToAdd, $this->availableRole) as $roleName) {
			$this->addError('role', 'You can`t assign role ' . $roleName);
		}
		foreach(array_diff($rolesToRemove, $this->availableRole) as $roleName) {
			$this->addError('role', 'You can`t revoke role ' . $roleName);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save($runValidation = true, $attributeNames = null): bool
	{
		list($rolesToAdd, $rolesToRemove) = $this->getRolesToAddAndRemove();
		if (empty($rolesToAdd) && empty($rolesToRemove)) {
			return parent::save($runValidation, $attributeNames);
		}

		$transaction = static::getDb()->beginTransaction();

		try {
			if (parent::save($runValidation, $attributeNames) === false) {
				$transaction->rollBack();

				return false;
			}

			$auth = Yii::$app->authManager;
			foreach($rolesToAdd as $role) {
				$auth->assign($auth->getRole($role), $this->id);
			}
			foreach($rolesToRemove as $role) {
				$auth->revoke($auth->getRole($role), $this->id);
			}

			$transaction->commit();
			return true;
		} catch (\Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}

	/**
	 * @return array{string[], string[]}
	 */
	protected function getRolesToAddAndRemove(): array
	{
		$auth = Yii::$app->authManager;
		$formRole = $this->role ?: [];
		$userRoles = $this->id ? array_keys($auth->getRolesByUser($this->id)) : [];

		return [
			array_diff($formRole, $userRoles),
			array_diff($userRoles, $formRole)
		];
	}
}