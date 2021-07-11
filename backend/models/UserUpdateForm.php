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

	        [['username', 'email', 'status'], 'required'],

	        ['admin', 'boolean'],
        ];
    }
}