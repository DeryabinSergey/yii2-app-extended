<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;

/**
 * Class BackendController.
 * Base class for backend controllers to check admin flag logged in user
 *
 * For adding behaviors in controller need merge it to parent, for example:
 *
 * ```php
 * public function behaviors()
 * {
 *  return
 *      ArrayHelper::merge(
 *          [
 *              'access' => [
 *                  'class' => AccessControl::class,
 *                  'rules' => [
 *                      [
 *                          'actions' => ['login', 'error'],
 *                          'allow' => true,
 *                      ],
 *                  ],
 *              ],
 *          ],
 *          parent::behaviors()
 *      );
 * }
 *
 * @package backend\controllers
 */
abstract class BackendController extends \yii\web\Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'matchCallback' => function ($rule, $action) {
							return !Yii::$app->user->isGuest && Yii::$app->user->identity->admin;
						}
					]
				],
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
				'layout' => 'blank'
			],
		];
	}
}