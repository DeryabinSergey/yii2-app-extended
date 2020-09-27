<?php
namespace backend\models;

/**
 * Update User form from admin
 */
class UserUpdateForm extends \common\models\User
{
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
            [
            	'email', 'unique',
	            'targetClass' => \common\models\User::class,
	            'when' => function ($model) { return $model->isAttributeChanged('email'); },
	            'message' => 'This email address has already been taken.'
            ],

	        ['status', 'in', 'range' => array_keys(self::statusList())],

	        [['username', 'email', 'status'], 'required'],

	        ['admin', 'boolean'],
        ];
    }
}