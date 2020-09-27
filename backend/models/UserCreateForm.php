<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Create User form from admin
 */
class UserCreateForm extends Model
{
	public $id;
	public $username;
    public $email;
    public $admin = 0;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
	        ['username', 'trim'],
	        ['username', 'string', 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => \common\models\User::class, 'message' => 'This email address has already been taken.'],

	        [['username', 'email'], 'required'],

	        ['admin', 'boolean'],
        ];
    }

    /**
     * Create new user
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function create()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $password = Yii::$app->security->generateRandomString(Yii::$app->params['user.passwordMinLength']);
	    $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_ACTIVE;
        $user->admin = $this->admin;
        $user->setPassword($password);
        $user->generateAuthKey();
        if ($user->save()) {
        	$this->id = $user->id;
	        return static::sendEmail($user, $password);
        }

        return false;
    }

    /**
     * Sends email to user with credentials
     * @param User $user user model to with email should be send
     * @param string $password generated user password
     * @return bool whether the email was sent
     */
    public static function sendEmail($user, $password): bool
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