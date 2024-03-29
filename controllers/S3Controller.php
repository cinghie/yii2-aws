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
use cinghie\aws\models\S3;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class S3Controller
 */
class S3Controller extends Controller
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
	 * @return string
	 */
    public function actionIndex()
    {
	    $s3 = new S3();
	    $s3Client = $s3->getS3Client();
	    $buckets  = $s3->getBuckets();

        return $this->render('index',[
        	's3Client' => $s3Client,
	        'buckets' => $buckets
        ]);
    }
}
