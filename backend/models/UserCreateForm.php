<?php
namespace backend\models;

use common\models\User;

use Yii;
use yii\base\Model;

/**
 * Create User form from admin
 */
class UserCreateForm extends Model
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

	        ['role', 'each', 'rule' => ['in', 'range' => array_keys(\Yii::$app->authManager->getRoles())]],
        ];
    }

	/**
	 * {@inheritdoc}
	 */
	public function afterValidate(): void
	{
		parent::afterValidate();

		$user = \Yii::$app->user;
		$errorToAdd = array_filter(
			$this->role ?: [],
			fn(string $role): bool => !$user->can($role)
		);

		if (!empty($errorToAdd)) {
			$this->addError('role', 'You can`t assign role: ' . implode(', ', $errorToAdd));
		}
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

	    $auth = \Yii::$app->authManager;
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
		    foreach(($this->role ?: []) as $roleName) {
			    $auth->assign($auth->getRole($roleName), $this->id);
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

    /**
     * Sends email to user with credentials
     * @param User $user user model to with email should be send
     * @param string $password generated user password
     * @return bool whether the email was sent
     */
    public static function sendEmail(User $user, string $password): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailNotify-html', 'text' => 'emailNotify-text'],
                ['user' => $user, 'password' => $password]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Account credentials at ' . Yii::$app->name)
            ->send();
    }
}