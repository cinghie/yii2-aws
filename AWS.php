<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.1.0
 */

namespace cinghie\aws;

use yii\base\Module;

/**
 * Class AWS
 *
 * @package cinghie\aws
 */
class AWS extends Module
{
	/**
	 * @var string Amazon API Access Key
	 */
	public $awsAccessKey = '';

	/**
	 * @var string Amazon API Region
	 */
	public $awsRegion = 'eu-central-1';

	/**
	 * @var string Amazon API Secret Key
	 */
	public $awsSecretKey = '';

	/**
	 * @var string Amazon API Version
	 */
	public $awsVersion = 'latest';

	/**
	 * @inheritdoc
	 *
	 * @throws InvalidParamException
	 */
	public function init()
	{
		parent::init();
		$this->registerTranslations();
	}

	/**
	 * Translating module message
	 */
	public function registerTranslations()
	{
		if (!isset(Yii::$app->i18n->translations['aws*']))
		{
			Yii::$app->i18n->translations['aws*'] = [
				'class' => PhpMessageSource::class,
				'basePath' => __DIR__ . '/messages',
			];
		}
	}
}
