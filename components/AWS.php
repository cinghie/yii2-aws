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

namespace cinghie\aws\components;

use Aws\Sdk;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class AWS
 *
 * @property Sdk $sdk
 *
 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/welcome.html
 */
class AWS extends Component
{
	/** @var string $accessKey */
	public $accessKey;

	/** @var string $region */
	public $region;

	/** @var string $secretKey */
	public $secretKey;

	/** @var string $secretKey */
	public $version;

	/** @var Sdk $_aws */
	private $_aws;

	/**
	 * AWS constructor
	 *
	 * @param array $config
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct(array $config = [])
	{
		if(!isset($config['accessKey']) || !$config['accessKey']) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Access Key missing!'));
		}

		if(!isset($config['secretKey']) || !$config['secretKey']) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Secret Key missing!'));
		}

		if(!isset($config['region']) || !$config['region']) {
			throw new InvalidConfigException(Yii::t('aws', 'AWS Region missing!'));
		}

		$this->accessKey = $config['accessKey'];
		$this->secretKey = $config['secretKey'];
		$this->region    = $config['region'];
		$this->version   = $config['version'] ?: 'latest';

		parent::__construct($config);
	}

	/**
	 * AWS init
	 */
	public function init()
	{
		$this->_aws = new Sdk([
			'credentials' => array(
				'key' => $this->accessKey,
				'secret' => $this->secretKey,
			),
			'region' => $this->region,
			'version' => $this->version,
		]);

		parent::init();
	}

	/**
	 * Get SDK
	 *
	 * @return Sdk
	 */
	public function getSdk()
	{
		return $this->_aws;
	}
}
