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

use Yii;
use yii\i18n\PhpMessageSource;
use yii\base\Module;

/**
 * Class AWS
 */
class AWS extends Module
{
	/**
	 * @inheritdoc
	 *
	 * @throws InvalidParamException
	 */
	public function init()
	{
		$this->registerTranslations();

		parent::init();
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
