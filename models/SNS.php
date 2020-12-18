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
use Aws\Sns\SnsClient;
use Yii;
use yii\base\Model;

/**
 * Class SNS
 *
 * @property SnsClient $_snsClient
 *
 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/sns-examples.html
 */
class SNS extends Model
{
	/**
	 * @var SnsClient
	 */
	private $_snsClient;

	/**
	 * SNS constructor
	 */
	public function __construct()
	{
		/** @var Sdk $sdk  */
		$sdk = Yii::$app->aws->sdk;
		$this->_snsClient = $sdk->createSns();

		parent::__construct();
	}

	/**
	 * Get SNS Client
	 *
	 * @return SesClient
	 */
	public function getSnsClient()
	{
		return $this->_snsClient;
	}

	/**
	 * Create a Topic
	 *
	 * To create a topic, use the CreateTopic operation.
	 * Each topic name in your AWS account must be unique.
	 *
	 * @param string $topicname
	 *
	 * @return Result
	 */
	public function createTopic($topicname)
	{
		try {
			$result = $this->_snsClient->createTopic([
				'Name' => $topicname,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'SNS Topic {0} added correctly', $topicname));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * List Your Topics
	 *
	 * To list up to 100 existing topics in the current AWS Region, use the ListTopics operation.
	 */
	public function listYourTopics()
	{
		try {
			$result = $this->_snsClient->listTopics([

			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Delete a Topic
	 *
	 * To create a topic, use the CreateTopic operation.
	 * Each topic name in your AWS account must be unique.
	 *
	 * @param string $topicArn
	 *
	 * @example $topicArn = 'arn:aws:sns:us-east-1:111122223333:MyTopic'
	 *
	 * @return Result
	 */
	public function deleteTopic($topicArn)
	{
		try {
			$result = $this->_snsClient->deleteTopic([
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'SNS Topic {0} deleted correctly', $topicArn));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Get Topic Attributes
	 *
	 * To retrieve properties of a single existing topic, use the GetTopicAttributes operation.
	 *
	 * @param string $topicArn
	 *
	 * @example $topicArn = 'arn:aws:sns:us-east-1:111122223333:MyTopic'
	 *
	 * @return Result
	 */
	public function getTopicAttributes($topicArn)
	{
		try {
			$result = $this->_snsClient->getTopicAttributes([
				'TopicArn' => $topicArn,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Set Topic Attributes
	 *
	 * To update properties of a single existing topic, use the SetTopicAttributes operation.
	 * You can set only the Policy, DisplayName, and DeliveryPolicy attributes.
	 *
	 * @param string $attribute
	 * @param string $value
	 * @param string $topic
	 *
	 * @example $attribute = 'Policy | DisplayName | DeliveryPolicy';
	 * @example $value = 'First Topic';
	 * @example $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
	 *
	 * @return Result
	 */
	public function setTopicAttributes($attribute, $value, $topic)
	{
		try {
			$result = $this->_snsClient->setTopicAttributes([
				'AttributeName' => $attribute,
				'AttributeValue' => $value,
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'SNS Topic Attribute setted correctly'));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}
}
