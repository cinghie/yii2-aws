<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.2.0
 */

namespace cinghie\aws\controllers;

use Aws\Exception\AwsException;
use Yii;
use cinghie\aws\models\SES;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Class SesController
 */
class SesController extends Controller
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
					throw new ForbiddenHttpException(Yii::t('aws','You are not allowed to access this page'));
				}
			],
		];
	}

	/**
	 * Dashboard
	 *
	 * @return string
	 */
    public function actionIndex()
    {
	    try {
		    Yii::createObject(SES::class);
	    } catch (AwsException $e) {
		    Yii::error($e->getMessage(), __METHOD__);
		    Yii::$app->session->setFlash('error', Yii::t('aws', 'Unable to load AWS data.'));
	    }

	    return $this->render('index');
    }
}
