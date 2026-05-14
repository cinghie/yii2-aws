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

namespace cinghie\aws\components;

use Aws\Sdk;
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
	/**
	 * @var string|null $accessKey
	 */
	public $accessKey;

	/**
	 * @var string|null $region
	 */
	public $region;

	/**
	 * @var string|null $secretKey
	 */
	public $secretKey;

	/**
	 * @var string $version
	 */
	public $version = 'latest';

	/**
	 * @var array|callable|null $credentials
	 */
	public $credentials;

	/**
	 * @var string|null $profile
	 */
	public $profile;

	/**
	 * @var string|null $endpoint
	 */
	public $endpoint;

	/**
	 * @var bool|null $usePathStyleEndpoint
	 */
	public $usePathStyleEndpoint;

	/**
	 * @var array $http
	 */
	public $http = [];

	/**
	 * @var int|array|null $retries
	 */
	public $retries;

	/**
	 * @var bool|resource|null $debug
	 */
	public $debug;

	/**
	 * Additional options passed directly to Aws\Sdk.
	 *
	 * @var array
	 */
	public $sdkOptions = [];

	/**
	 * @var Sdk $_aws
	 */
	private $_aws;

	/**
	 * AWS constructor
	 *
	 * @param array $config
	 *
	 */
	public function __construct(array $config = [])
	{
		parent::__construct($config);
	}

	/**
	 * AWS init
	 *
	 * @throws InvalidConfigException
	 */
	public function init()
	{
		parent::init();

		$this->_aws = new Sdk($this->buildSdkConfig());
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

	/**
	 * Build AWS SDK configuration.
	 *
	 * @return array
	 * @throws InvalidConfigException
	 */
	protected function buildSdkConfig()
	{
		$config = $this->sdkOptions;
		$config['version'] = $this->version;

		if ($this->region !== null && $this->region !== '') {
			$config['region'] = $this->region;
		}

		if ($this->credentials !== null) {
			$config['credentials'] = $this->credentials;
		} elseif ($this->accessKey || $this->secretKey) {
			if (!$this->accessKey || !$this->secretKey) {
				throw new InvalidConfigException('Both AWS accessKey and secretKey must be configured, or neither of them.');
			}

			$config['credentials'] = [
				'key' => $this->accessKey,
				'secret' => $this->secretKey,
			];
		}

		if ($this->profile !== null && $this->profile !== '') {
			$config['profile'] = $this->profile;
		}

		if ($this->endpoint !== null && $this->endpoint !== '') {
			$config['endpoint'] = $this->endpoint;
		}

		if ($this->usePathStyleEndpoint !== null) {
			$config['use_path_style_endpoint'] = $this->usePathStyleEndpoint;
		}

		if ($this->http !== []) {
			$config['http'] = $this->http;
		}

		if ($this->retries !== null) {
			$config['retries'] = $this->retries;
		}

		if ($this->debug !== null) {
			$config['debug'] = $this->debug;
		}

		return $config;
	}
}
