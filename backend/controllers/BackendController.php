<?php

namespace backend\controllers;

/**
 * Class BackendController.
 * Base class for backend controllers for change layout to error action
 *
 * @package backend\controllers
 */
abstract class BackendController extends \yii\web\Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function actions(): array
	{
		return [
			'error' => [
				'class' => \yii\web\ErrorAction::class,
				'layout' => 'blank'
			],
		];
	}
}