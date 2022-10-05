<?php

namespace console\controllers;

use common\models\User;

use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manage User Roles.
 */
class UserRoleController extends \yii\console\Controller
{
	public $defaultAction = 'list';

	/**
	 * Show available Roles
	 * @return int
	 */
	public function actionList(): int
	{
		$rolesList = \Yii::$app->authManager->getRoles();
		if (empty($rolesList)) {
			$this->stdout(PHP_EOL . "There is no available Roles" . PHP_EOL . PHP_EOL);

			return ExitCode::OK;
		}

		$this->stdout(PHP_EOL . "List of available Roles:" . PHP_EOL . PHP_EOL);
		foreach($rolesList as $role) {
			$text = "- " . $this->ansiFormat($role->name, Console::FG_YELLOW);
			$text .= str_repeat(' ', 40 - strlen($text))
				. ($role->description ? ' ' . $role->description : '')
				. PHP_EOL;
			$this->stdout($text);
		}
		$this->stdout(PHP_EOL);

		return ExitCode::OK;
	}

	/**
	 * Get list Roles assigned to User
	 * @param string $userEmail user email
	 * @return int
	 */
	public function actionInfo(string $userEmail): int
	{
		$user = User::findByEmail($userEmail);
		if ($user === null) {
			$text = $this->ansiFormat(sprintf("User with email `%s` not found", $userEmail), Console::FG_RED);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		$userRoles = \Yii::$app->authManager->getRolesByUser($user->id);
		if (empty($userRoles)) {
			$this->stdout(PHP_EOL
				. sprintf("There is no Roles assigned to user `%s`", $userEmail)
				. PHP_EOL . PHP_EOL
			);

			return ExitCode::OK;
		}

		$this->stdout(PHP_EOL . sprintf("List Roles assigned to user `%s` :", $userEmail) . PHP_EOL . PHP_EOL);
		foreach($userRoles as $userRole) {
			$text = "- " . $this->ansiFormat($userRole->name, Console::FG_YELLOW);
			$text .= str_repeat(' ', 40 - strlen($text))
				. ($userRole->description ? ' ' . $userRole->description : '')
				. PHP_EOL;
			$this->stdout($text);
		}
		$this->stdout(PHP_EOL);

		return ExitCode::OK;
	}

	/**
	 * Assign Role to User
	 * @param string $userEmail
	 * @param string $roleName
	 * @return int
	 */
	public function actionAssign(string $userEmail, string $roleName): int
	{
		$user = User::findByEmail($userEmail);
		if ($user === null) {
			$text = $this->ansiFormat(sprintf("User with email `%s` not found", $userEmail), Console::FG_RED);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($roleName);
		if ($role === null) {
			$text = $this->ansiFormat(sprintf("Role `%s` not found", $roleName), Console::FG_RED);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		if ($auth->getAssignment($roleName, $user->id) !== null) {
			$text = $this->ansiFormat(
				sprintf("Role `%s` already assigned to user `%s`", $roleName, $userEmail),
				Console::FG_YELLOW
			);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		$auth->assign($role, $user->id);
		$text = $this->ansiFormat(
			sprintf("Role `%s` assigned to user `%s`", $roleName, $userEmail),
			Console::FG_GREEN
		);
		$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

		return ExitCode::OK;
	}

	/**
	 * Revoke Role from User
	 * @param string $userEmail
	 * @param string $roleName
	 * @return int
	 */
	public function actionRevoke(string $userEmail, string $roleName): int
	{
		$user = User::findByEmail($userEmail);
		if ($user === null) {
			$text = $this->ansiFormat(sprintf("User with email `%s` not found", $userEmail), Console::FG_RED);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($roleName);
		if ($role === null) {
			$text = $this->ansiFormat(sprintf("Role `%s` not found", $roleName), Console::FG_RED);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		if ($auth->getAssignment($roleName, $user->id) === null) {
			$text = $this->ansiFormat(
				sprintf("Role `%s` not assigned to user `%s`", $roleName, $userEmail),
				Console::FG_YELLOW
			);
			$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

			return ExitCode::UNSPECIFIED_ERROR;
		}

		$auth->revoke($role, $user->id);
		$text = $this->ansiFormat(
			sprintf("Role `%s` revoked from user `%s`", $roleName, $userEmail),
			Console::FG_GREEN
		);
		$this->stdout(PHP_EOL . $text . PHP_EOL . PHP_EOL);

		return ExitCode::OK;
	}
}