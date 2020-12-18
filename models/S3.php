<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.1.1
 */

namespace cinghie\aws\models;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sdk;
use Aws\S3\S3Client;
use Yii;
use yii\base\Model;

/**
 * Class AWS
 *
 * @property S3Client $_s3Client
 * @property Result $buckets
 * @property array $accessControlListPolicy
 * @property-write array $corsConfiguration
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
	 */
	public function __construct()
	{
		/** @var Sdk $sdk  */
		$sdk = Yii::$app->aws->sdk;
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

	/**
	 * Get Buckets
	 *
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html#list-buckets
	 */
	public function getBuckets()
	{
		try {
			$buckets = $this->_s3Client->listBuckets();
		} catch (AwsException $e) {
			$buckets = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $buckets;
	}

	/**
	 * Create S3 Bucket
	 *
	 * @param string $bucketName
	 *
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html#create-a-bucket
	 */
	public function createBucket($bucketName)
	{
		try {
			$result = $this->_s3Client->createBucket([
				'Bucket' => $bucketName,
			]);
		} catch (AwsException $e) {
			$result = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $result;
	}

	/**
	 * Put Object in Bucket
	 *
	 * @param string $bucketName
	 * @param string $key
	 * @param string $filePath
	 *
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html#put-an-object-in-a-bucket
	 */
	public function putObjectInBucket($bucketName, $key, $filePath)
	{
		try {
			$result = $this->_s3Client->putObject([
				'Bucket' => $bucketName,
				'Key' => $key,
				'SourceFile' => $filePath,
			]);
		} catch (AwsException $e) {
			$result = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $result;
	}

	/**
	 * Get an Access Control List Policy
	 *
	 * @param string $bucketName
	 *
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-access-permissions.html
	 */
	public function getAccessControlListPolicy($bucketName)
	{
		try {
			$result = $this->_s3Client->getBucketAcl([
				'Bucket' => $bucketName
			]);
		} catch (AwsException $e) {
			$result = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $result;
	}

	/**
	 * Set an Access Control List Policy
	 *
	 * @param array $params
	 *
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-access-permissions.html
	 */
	public function setAccessControlListPolicy($params)
	{
		try {
			$result = $this->_s3Client->putBucketAcl($params);
		} catch (AwsException $e) {
			$result = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-configuring-a-bucket.html#get-the-cors-configuration
	 */
	public function getCorsConfiguration($bucketName)
	{
		try {
			$result = $this->_s3Client->getBucketCors([
				'Bucket' => $bucketName
			]);
		} catch (AwsException $e) {
			$result = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $result;
	}

	/**
	 * Create a PHP file with the following code. First create an AWS.S3 client service.
	 * Then call the putBucketCors method and specify the bucket whose CORS configuration to set,
	 * and the CORSConfiguration as a CORSRules JSON object.
	 *
	 * @param array $array
	 *
	 * @return Result || array
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples-configuring-a-bucket.html#set-the-cors-configuration
	 */
	public function setCORSConfiguration($array)
	{
		try {
			$result = $this->_s3Client->putBucketCors($array);
		} catch (AwsException $e) {
			$result = [];
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $result;
	}
}
