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

namespace cinghie\aws\models;

use Yii;
use Aws\Sdk;
use yii\base\InvalidConfigException;

/**
 * Class AWS
 *
 * @package cinghie\aws\models
 */
class AWS
{
	/** @var string $accessKey */
	private $accessKey;

	/** @var string $region */
	private $region;

	/** @var string $secretKey */
	private $secretKey;

	/**
	 * @@inheritdoc
	 */
	public function init()
	{
		if(!Yii::$app->getModule('aws')->accessKey) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Access Key missing!'));
		}

		if(!Yii::$app->getModule('aws')->secretKey) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Secret Key missing!'));
		}

		$this->accessKey = Yii::$app->getModule('aws')->awsAccessKey;
		$this->region    = Yii::$app->getModule('aws')->awsRegion;
		$this->secretKey = Yii::$app->getModule('aws')->awsSecretKey;
		$this->version   = Yii::$app->getModule('aws')->awsVersion;
	}

	/**
	 * @return Sdk
	 */
	public function getSDK()
	{
		$client = new Sdk([
			'credentials' => [
				'key' => $this->accessKey,
				'secret' => $this->secretKey,
			],
			'region' => $this->region
		]);

		return $client;
	}
}