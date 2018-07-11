<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.0.1
 */

namespace cinghie\aws\models;

use Yii;
use Aws\Ses\Exception\SesException;
use Aws\Ses\SesClient;

class SES
{

	/**
	 * @@inheritdoc
	 */
	public function init()
	{
		if(!Yii::$app->controller->module->accessKey)
		{
			Yii::$app->session->setFlash('error', Yii::t('aws', 'Access Key missing!'));
		}
	}

}
