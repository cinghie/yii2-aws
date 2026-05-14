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
use Aws\Sns\SnsClient;
use InvalidArgumentException;
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

		if ($this->_snsClient === null) {
			/** @var Sdk $sdk  */
			$sdk = Yii::$app->aws->sdk;
			$this->_snsClient = $sdk->createSns();
		}
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
	 * Set SNS Client
	 *
	 * @param SnsClient $snsClient
	 */
	public function setSnsClient(SnsClient $snsClient)
	{
		$this->_snsClient = $snsClient;
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
	 * @throws AwsException
	 */
	public function createTopic($topicName)
	{
		$this->validateTopicName($topicName);

		$result = $this->_snsClient->createTopic([
			'Name' => $topicName,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * List Your Topics
	 *
	 * To list up to 100 existing topics in the current AWS Region, use the ListTopics operation.
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function listYourTopics()
	{
		$result = $this->_snsClient->listTopics([

		]);

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
	 * @throws AwsException
	 */
	public function deleteTopic($topicArn)
	{
		$this->validateSnsTopicArn($topicArn);

		$result = $this->_snsClient->deleteTopic([
			'TopicArn' => $topicArn,
		]);

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
	 * @throws AwsException
	 */
	public function getTopicAttributes($topicArn)
	{
		$this->validateSnsTopicArn($topicArn);

		$result = $this->_snsClient->getTopicAttributes([
			'TopicArn' => $topicArn,
		]);

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
	 * @throws AwsException
	 */
	public function setTopicAttributes($attribute, $value, $topic)
	{
		$this->validateSnsTopicArn($topic);

		if (!in_array($attribute, ['Policy', 'DisplayName', 'DeliveryPolicy'], true)) {
			throw new InvalidArgumentException('SNS topic attribute must be Policy, DisplayName, or DeliveryPolicy.');
		}

		if (!is_string($value)) {
			throw new InvalidArgumentException('SNS topic attribute value must be a string.');
		}

		$result = $this->_snsClient->setTopicAttributes([
			'AttributeName' => $attribute,
			'AttributeValue' => $value,
			'TopicArn' => $topic,
		]);

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
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function subscribeEmailToTopic($protocol, $endpoint, $topic)
	{
		$this->validateProtocol($protocol, ['email']);
		$this->validateEmail($endpoint);
		$this->validateSnsTopicArn($topic);

		$result = $this->_snsClient->subscribe([
			'Protocol' => $protocol,
			'Endpoint' => $endpoint,
			'ReturnSubscriptionArn' => true,
			'TopicArn' => $topic,
		]);

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
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function subscribeAppEndPointToTopic($protocol, $endpoint, $topic)
	{
		$this->validateProtocol($protocol, ['http', 'https']);

		if (!filter_var($endpoint, FILTER_VALIDATE_URL)) {
			throw new InvalidArgumentException('SNS application endpoint must be a valid URL.');
		}

		$this->validateSnsTopicArn($topic);

		$result = $this->_snsClient->subscribe([
			'Protocol' => $protocol,
			'Endpoint' => $endpoint,
			'ReturnSubscriptionArn' => true,
			'TopicArn' => $topic,
		]);

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
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function subscribeLambdaFunctionToTopic($protocol, $endpoint, $topic)
	{
		$this->validateProtocol($protocol, ['lambda']);

		if (!is_string($endpoint) || !preg_match('/^arn:aws[a-zA-Z-]*:lambda:[a-z0-9-]+:\\d{12}:function:.+$/', $endpoint)) {
			throw new InvalidArgumentException('SNS Lambda endpoint must be a valid Lambda ARN.');
		}

		$this->validateSnsTopicArn($topic);

		$result = $this->_snsClient->subscribe([
			'Protocol' => $protocol,
			'Endpoint' => $endpoint,
			'ReturnSubscriptionArn' => true,
			'TopicArn' => $topic,
		]);

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
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function subscribeTextSMSToTopic($protocol, $endpoint, $topic)
	{
		$this->validateProtocol($protocol, ['sms']);

		if (!is_string($endpoint) || !preg_match('/^\\+[1-9]\\d{6,14}$/', $endpoint)) {
			throw new InvalidArgumentException('SNS SMS endpoint must be a valid E.164 phone number.');
		}

		$this->validateSnsTopicArn($topic);

		$result = $this->_snsClient->subscribe([
			'Protocol' => $protocol,
			'Endpoint' => $endpoint,
			'ReturnSubscriptionArn' => true,
			'TopicArn' => $topic,
		]);

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
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function confirmSubscriptionToTopic($subscription_token, $topic)
	{
		if (!is_string($subscription_token) || trim($subscription_token) === '') {
			throw new InvalidArgumentException('SNS subscription token must be a non-empty string.');
		}

		$this->validateSnsTopicArn($topic);

		$result = $this->_snsClient->subscribe([
			'Token' => $subscription_token,
			'TopicArn' => $topic,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * @param string $topicName
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateTopicName($topicName)
	{
		if (!is_string($topicName) || !preg_match('/^[A-Za-z0-9_-]{1,256}(\\.fifo)?$/', $topicName)) {
			throw new InvalidArgumentException('Invalid SNS topic name.');
		}
	}

	/**
	 * @param string $topicArn
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateSnsTopicArn($topicArn)
	{
		if (!is_string($topicArn) || !preg_match('/^arn:aws[a-zA-Z-]*:sns:[a-z0-9-]+:\\d{12}:[A-Za-z0-9_-]{1,256}(\\.fifo)?$/', $topicArn)) {
			throw new InvalidArgumentException('Invalid SNS topic ARN.');
		}
	}

	/**
	 * @param string $email
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateEmail($email)
	{
		if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException('Invalid email address.');
		}
	}

	/**
	 * @param string $protocol
	 * @param array $allowedProtocols
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateProtocol($protocol, array $allowedProtocols)
	{
		if (!is_string($protocol) || !in_array($protocol, $allowedProtocols, true)) {
			throw new InvalidArgumentException('Invalid SNS subscription protocol.');
		}
	}
}
