<?php

namespace backend\models;

/**
 * Update User form from admin
 */
class UserUpdateForm extends \common\models\User
{
	/**
	 * Assigned roles to User
	 *
	 * @var string[]|string
	 */
	public array|string $role;

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
                'when' => function ($model) { return $model->isAttributeChanged('email'); },
                'message' => 'This email address has already been taken.'
            ],
            [
                'email', 'unique',
                'targetClass' => self::class,
		        'filter' => ['!=', 'status', self::STATUS_DELETED],
                'when' => function (self $model) {
                    return
                        $model->isAttributeChanged('status')
                        && $model->getAttribute('status') != self::STATUS_DELETED
                        && $model->getOldAttribute('status') == self::STATUS_DELETED;
                },
                'message' => 'Another user already registered with this email.'
            ],

	        ['status', 'in', 'range' => array_keys(self::statusList())],

	        ['role', 'each', 'rule' => ['in', 'range' => array_keys(\Yii::$app->authManager->getRoles())]],

	        [['username', 'email', 'status'], 'required'],
        ];
    }

	/**
	 * {@inheritdoc}
	 */
	public function afterFind(): void
	{
		parent::afterFind();

		$this->role = $this->id ? array_keys(\Yii::$app->authManager->getRolesByUser($this->id)) : [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterValidate(): void
	{
		parent::afterValidate();

		$user = \Yii::$app->user;
		list($rolesToAdd, $rolesToRemove) = $this->getRolesToAddAndRemove();

		$errorToAdd = array_filter(
			$rolesToAdd,
			fn(string $role): bool => !$user->can($role)
		);
		$errorToRemove = array_filter(
			$rolesToRemove,
			fn(string $role): bool => !$user->can($role)
		);
		if (!empty($errorToAdd)) {
			$this->addError('role', 'You can`t assign role: ' . implode(', ', $errorToAdd));
		}

		if (!empty($errorToRemove)) {
			$this->addError('role', 'You can`t revoke role: ' . implode(', ', $errorToRemove));
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

			$auth = \Yii::$app->authManager;
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
		$auth = \Yii::$app->authManager;
		$formRole = $this->role ?: [];
		$userRoles = $this->id ? array_keys($auth->getRolesByUser($this->id)) : [];

		return [
			array_diff($formRole, $userRoles),
			array_diff($userRoles, $formRole)
		];
	}
}