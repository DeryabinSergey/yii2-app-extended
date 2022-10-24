<?php
namespace backend\models;

use Yii;

/**
 * Create User form from admin
 */
class UserCreateForm extends BaseUserForm
{
	public $id;
	public $username;
    public $email;
	/**
	 * Assigned roles to User
	 *
	 * @var string[]|string
	 */
	public array|string $role = [];

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
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.', 'filter' => ['!=', 'status', User::STATUS_DELETED]],

	        [['username', 'email'], 'required'],

	        ['role', 'each', 'rule' => ['in', 'range' => $this->availableRole, 'message' => 'You can`t assign role {value}']],
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
	 * Create new user
	 *
	 * @return bool whether the creating new account was successful and email was sent
	 * @throws \yii\base\Exception
	 */
    public function create(): bool
    {
        if (!$this->validate()) {
            return false;
        }

	    $auth = Yii::$app->authManager;
		$roleList = array_map(
			fn(string $roleName): \yii\rbac\Role => $auth->getRole($roleName),
			$this->role ?: []
		);

	    $user = new User();
	    $password = Yii::$app->security->generateRandomString(Yii::$app->params['user.passwordMinLength']);
	    $user->username = $this->username;
	    $user->email = $this->email;
	    $user->status = User::STATUS_ACTIVE;
	    $user->setPassword($password);
	    $user->generateAuthKey();

	    $transaction = $user::getDb()->beginTransaction();
	    try {
		    if ($user->save() === false) {
			    $transaction->rollBack();

			    return false;
		    }

		    $this->id = $user->id;
		    foreach($roleList as $role) {
			    $auth->assign($role, $this->id);
		    }
			$transaction->commit();

		    return static::sendEmail($user, $password);
	    } catch (\Throwable $e) {
			if ($transaction->getIsActive()) {
				$transaction->rollBack();
			}

		    throw $e;
	    }
    }
}