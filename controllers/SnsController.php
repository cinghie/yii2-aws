<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.1.2
 */

namespace cinghie\aws\controllers;

use RuntimeException;
use Yii;
use cinghie\aws\models\SNS;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class SnsController
 */
class SnsController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
						'roles' => $this->module->awsRoles
					],
				],
				'denyCallback' => static function () {
					throw new RuntimeException(Yii::t('traits','You are not allowed to access this page'));
				}
			],
		];
	}

	/**
	 * Dashboard
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$sns = new SNS();
		$snsClient = $sns->getSNSClient();

		return $this->render('index',[
			'snsClient' => $snsClient
		]);
	}
}
