<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.1.2
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
	 * @return SnsClient
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
	 * @param string $topicName
	 *
	 * @return Result
	 */
	public function createTopic($topicName)
	{
		try {
			$result = $this->_snsClient->createTopic([
				'Name' => $topicName,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'SNS Topic {0} added correctly', $topicName));
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
				'TopicArn' => $topicArn,
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

	/**
	 * Subscribe an Email Address to a Topic
	 *
	 * To initiate a subscription to an email address, use the Subscribe operation.
	 * You can use the subscribe method to subscribe several different endpoints to an Amazon SNS topic,
	 * depending on the values used for parameters passed.
	 * This is shown in other examples in this topic.
	 *
	 * @param string $protocol
	 * @param string $endpoint
	 * @param string $topic
	 *
	 * @example $protocol = 'email';
	 * @example $endpoint = 'sample@example.com';
	 * @example $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
	 */
	public function subscribeEmailToTopic($protocol = 'email', $endpoint = 'sample@example.com', $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic')
	{
		try {
			$result = $this->_snsClient->subscribe([
				'Protocol' => $protocol,
				'Endpoint' => $endpoint,
				'ReturnSubscriptionArn' => true,
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Email {0} subscribed to Topic correctly', $endpoint));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Subscribe an Application Endpoint to a Topic
	 *
	 * To initiate a subscription to a web app, use the Subscribe operation.
	 * You can use the subscribe method to subscribe several different endpoints to an Amazon SNS topic,
	 * depending on the values used for parameters passed. This is shown in other examples in this topic.
	 *
	 * @param string $protocol
	 * @param string $endpoint
	 * @param string $topic
	 *
	 * @example $protocol = 'https';
	 * @example $endpoint = 'https://';
	 * @example $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
	 */
	public function subscribeAppEndPointToTopic($protocol = 'https', $endpoint = 'https://', $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic')
	{
		try {
			$result = $this->_snsClient->subscribe([
				'Protocol' => $protocol,
				'Endpoint' => $endpoint,
				'ReturnSubscriptionArn' => true,
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Email {0} subscribed to Topic correctly', $endpoint));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Subscribe a Lambda Function to a Topic
	 *
	 * To initiate a subscription to a Lambda function, use the Subscribe operation.
	 * You can use the subscribe method to subscribe several different endpoints to an Amazon SNS topic,
	 * depending on the values used for parameters passed. This is shown in other examples in this topic.
	 *
	 * @param string $protocol
	 * @param string $endpoint
	 * @param string $topic
	 *
	 * @example $protocol = 'lambda';
	 * @example $endpoint = 'arn:aws:lambda:us-east-1:123456789023:function:messageStore';
	 * @example $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
	 */
	public function subscribeLambdaFunctionToTopic($protocol = 'lambda', $endpoint = 'arn:aws:lambda:us-east-1:123456789023:function:messageStore', $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic')
	{
		try {
			$result = $this->_snsClient->subscribe([
				'Protocol' => $protocol,
				'Endpoint' => $endpoint,
				'ReturnSubscriptionArn' => true,
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Email {0} subscribed to Topic correctly', $endpoint));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Subscribe a Text SMS to a Topic
	 *
	 * To send SMS messages to multiple phone numbers at the same time, subscribe each number to a topic.
	 * To initiate a subscription to a phone number, use the Subscribe operation.
	 * You can use the subscribe method to subscribe several different endpoints to an Amazon SNS topic,
	 * depending on the values used for parameters passed. This is shown in other examples in this topic.
	 *
	 * @param string $protocol
	 * @param string $endpoint
	 * @param string $topic
	 *
	 * @example $protocol = 'sms';
	 * @example $endpoint = '+1XXX5550100';
	 * @example $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
	 */
	public function subscribeTextSMSToTopic($protocol = 'sms', $endpoint = '+1XXX5550100', $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic')
	{
		try {
			$result = $this->_snsClient->subscribe([
				'Protocol' => $protocol,
				'Endpoint' => $endpoint,
				'ReturnSubscriptionArn' => true,
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Email {0} subscribed to Topic correctly', $endpoint));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Confirm Subscription to a Topic
	 *
	 * To actually create a subscription, the endpoint owner must acknowledge intent to receive messages
	 * from the topic using a token sent when a subscription is established initially, as described earlier.
	 * Confirmation tokens are valid for three days. After three days, you can resend a token by creating a new subscription.
	 *
	 * @param string $subscription_token
	 * @param string $topic
	 *
	 * @example $subscription_token = 'arn:aws:sns:us-east-1:111122223333:MyTopic:123456-abcd-12ab-1234-12ba3dc1234a';
	 * @example $topic = 'arn:aws:sns:us-east-1:111122223333:MyTopic';
	 */
	public function confirmSubscriptionToTopic($subscription_token, $topic)
	{
		try {
			$result = $this->_snsClient->subscribe([
				'Token' => $subscription_token,
				'TopicArn' => $topic,
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Email {0} subscribed to Topic correctly', $endpoint));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}
}
