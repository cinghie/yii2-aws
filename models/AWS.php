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
	public $accessKey;

	/** @var string $profile */
	public $profile;

	/** @var string $region */
	public $region;

	/** @var string $secretKey */
	public $secretKey;

	/** @var string $secretKey */
	public $version;

	/**
	 * AWS constructor
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct()
	{
		if(!Yii::$app->getModule('aws')->accessKey) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Access Key missing!'));
		}

		if(!Yii::$app->getModule('aws')->secretKey) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Secret Key missing!'));
		}

		$this->accessKey = Yii::$app->getModule('aws')->accessKey;
		$this->profile   = Yii::$app->getModule('aws')->profile;
		$this->region    = Yii::$app->getModule('aws')->region;
		$this->secretKey = Yii::$app->getModule('aws')->secretKey;
		$this->version   = Yii::$app->getModule('aws')->version;
	}

	/**
	 * @return Sdk
	 */
	public function getSDK()
	{
		$sdkOptions = [
			'credentials' => [
				'key' => $this->accessKey,
				'secret' => $this->secretKey,
			],
			'region' => $this->region,
			'version' => $this->version
		];

		if($this->profile) {
			$sdkOptions['profile'] = Yii::$app->getModule('aws')->profile;
		}

		return new Sdk($sdkOptions);
	}
}