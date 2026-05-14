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

namespace cinghie\aws\models;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sdk;
use Aws\S3\S3Client;
use InvalidArgumentException;
use Yii;
use yii\base\Model;

/**
 * Class AWS
 *
 * @property S3Client $_s3Client
 * @property Result $buckets
 * @property array $accessControlListPolicy
 *
 * @property-read S3Client $s3Client
 * @property-write array $corsConfiguration
 * @property-write array $cORSConfiguration
 *
 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples.html
 */
class S3 extends Model
{
	/**
	 * @var S3Client
	 */
	private $_s3Client;

	/**
	 * S3 constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
	{
		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		if ($this->_s3Client === null) {
			/** @var Sdk $sdk  */
			$sdk = Yii::$app->aws->sdk;
			$this->_s3Client = $sdk->createS3();
		}
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

	/**
	 * Set S3 Client
	 *
	 * @param S3Client $s3Client
	 */
	public function setS3Client(S3Client $s3Client)
	{
		$this->_s3Client = $s3Client;
	}

	/**
	 * Get Buckets
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html#list-buckets
	 */
	public function getBuckets()
	{
		$buckets = $this->_s3Client->listBuckets();

		return $buckets;
	}

	/**
	 * Create S3 Bucket
	 *
	 * @param string $bucketName
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html#create-a-bucket
	 */
	public function createBucket($bucketName)
	{
		$this->validateBucketName($bucketName);

		$result = $this->_s3Client->createBucket([
			'Bucket' => $bucketName,
		]);

		return $result;
	}

	/**
	 * Put Object in Bucket
	 *
	 * @param string $bucketName
	 * @param string $key
	 * @param string $filePath
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html#put-an-object-in-a-bucket
	 */
	public function putObjectInBucket($bucketName, $key, $filePath)
	{
		$this->validateBucketName($bucketName);

		if (!is_string($key) || trim($key) === '') {
			throw new InvalidArgumentException('S3 object key must be a non-empty string.');
		}

		if (!is_string($filePath) || trim($filePath) === '') {
			throw new InvalidArgumentException('S3 source file path must be a non-empty string.');
		}

		$result = $this->_s3Client->putObject([
			'Bucket' => $bucketName,
			'Key' => $key,
			'SourceFile' => $filePath,
		]);

		return $result;
	}

	/**
	 * Get an Access Control List Policy
	 *
	 * @param string $bucketName
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-access-permissions.html
	 */
	public function getAccessControlListPolicy($bucketName)
	{
		$this->validateBucketName($bucketName);

		$result = $this->_s3Client->getBucketAcl([
			'Bucket' => $bucketName
		]);

		return $result;
	}

	/**
	 * Set an Access Control List Policy
	 *
	 * @param array $params
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-access-permissions.html
	 */
	public function setAccessControlListPolicy($params)
	{
		$result = $this->_s3Client->putBucketAcl($params);

		return $result;
	}

	/**
	 * Create a PHP file with the following code. First create an AWS.S3 client service,
	 * then call the getBucketCors method and specify the bucket whose CORS configuration you want.
	 *
	 * The only parameter required is the name of the selected bucket. If the bucket currently has a CORS configuration,
	 * that configuration is returned by Amazon S3 as a CORSRules object.
	 *
	 * @param string $bucketName
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-configuring-a-bucket.html#get-the-cors-configuration
	 */
	public function getCorsConfiguration($bucketName)
	{
		$this->validateBucketName($bucketName);

		$result = $this->_s3Client->getBucketCors([
			'Bucket' => $bucketName
		]);

		return $result;
	}

	/**
	 * Create a PHP file with the following code. First create an AWS.S3 client service.
	 * Then call the putBucketCors method and specify the bucket whose CORS configuration to set,
	 * and the CORSConfiguration as a CORSRules JSON object.
	 *
	 * @param array $array
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-configuring-a-bucket.html#set-the-cors-configuration
	 */
	public function setCORSConfiguration($array)
	{
		$result = $this->_s3Client->putBucketCors($array);

		return $result;
	}

	/**
	 * Validate S3 bucket name before calling AWS.
	 *
	 * @param string $bucketName
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateBucketName($bucketName)
	{
		if (!is_string($bucketName)) {
			throw new InvalidArgumentException('S3 bucket name must be a string.');
		}

		if (!preg_match('/^(?!\\d+\\.\\d+\\.\\d+\\.\\d+$)(?!.*\\.\\.)(?!.*\\.-)(?!.*-\\.)[a-z0-9][a-z0-9.-]{1,61}[a-z0-9]$/', $bucketName)) {
			throw new InvalidArgumentException('Invalid S3 bucket name.');
		}
	}
}
