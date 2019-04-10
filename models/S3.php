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

use Aws\Sdk;
use Aws\S3\S3Client;
use Yii;
use yii\base\Component;

/**
 * Class AWS
 *
 * @property S3Client $s3Client
 *
 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples.html
 */
class S3 extends Component
{
	/** @var SesClient $sesClient */
	private $_s3Client;

	/**
	 * S3 constructor
	 */
	public function __construct()
	{
		/** @var Sdk $sdk  */
		$sdk = Yii::$app->aws;
		$this->_s3Client = $sdk->createS3();

		parent::__construct();
	}

	/**
	 * Get S3 Client
	 *
	 * @return S3Client
	 */
	public function getS3Client()
	{
		return $this->_s3Client;
	}
}
