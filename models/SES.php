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
use Aws\Ses\SesClient;
use InvalidArgumentException;
use Yii;
use yii\base\Model;

/**
 * Class SES
 *
 * @property SesClient $_sesClient
 * @property Result $sendingStatistics
 *
 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-examples.html
 */
class SES extends Model
{
	/**
	 * @var SesClient
	 */
	private $_sesClient;

	/**
	 * SES constructor
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

		if ($this->_sesClient === null) {
			/** @var Sdk $sdk  */
			$sdk = Yii::$app->aws->sdk;
			$this->_sesClient = $sdk->createSes();
		}
	}

	/**
	 * Get SES Client
	 *
	 * @return SesClient
	 */
	public function getSesClient()
	{
		return $this->_sesClient;
	}

	/**
	 * Set SES Client
	 *
	 * @param SesClient $sesClient
	 */
	public function setSesClient(SesClient $sesClient)
	{
		$this->_sesClient = $sesClient;
	}

	/**
	 * Amazon SES can send email only from verified email addresses or domains.
	 * By verifying an email address, you demonstrate that you're the owner of that address
	 * and want to allow Amazon SES to send email from that address.
	 *
	 * @param string $email
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verifying-email-addresses
	 */
	public function verifyEmailIdentity($email)
	{
		$this->validateEmail($email);

		$result = $this->_sesClient->verifyEmailIdentity([
			'EmailAddress' => $email
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Amazon SES can send email only from verified email addresses or domains.
	 * By verifying a domain, you demonstrate that you're the owner of that domain.
	 * When you verify a domain, you allow Amazon SES to send email from any address on that domain.
	 *
	 * @param string $domain
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verify-an-email-domain
	 */
	public function verifyDomainIdentity($domain)
	{
		$this->validateDomain($domain);

		$result = $this->_sesClient->verifyDomainIdentity([
			'Domain' => $domain
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve a list of email addresses submitted in the current AWS Region,
	 * regardless of verification status, use the ListIdentities operation.
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#list-email-addresses
	 */
	public function listIdentities()
	{
		$result = $this->_sesClient->listIdentities([
			'IdentityType' => 'EmailAddress'
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve a list of email domains submitted in the current AWS Region,
	 * regardless of verification status use the ListIdentities operation.
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#list-email-domains
	 */
	public function listDomains()
	{
		$result = $this->_sesClient->listIdentities([
			'IdentityType' => 'Domain'
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To delete a verified email address from the list of identities,
	 * use the DeleteIdentity operation.
	 *
	 * @param string $email
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#delete-an-email-address
	 */
	public function deleteEmail($email)
	{
		$this->validateEmail($email);

		$result = $this->_sesClient->deleteIdentity([
			'Identity' => $email
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To delete a verified email domain from the list of verified identities,
	 * use the DeleteIdentity operation.
	 *
	 * @param string $domain
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#delete-an-email-domain
	 */
	public function deleteDomain($domain)
	{
		$this->validateDomain($domain);

		$result = $this->_sesClient->deleteIdentity([
			'Identity' => $domain
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To create a template to send personalized email messages, use the CreateTemplate operation.
	 * The template can be used by any account authorized to send messages in the AWS Region to which the template is added.
	 *
	 * @param string $template_name
	 * @param string $subject
	 * @param string $html_body
	 * @param string $plaintext_body
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verifying-email-addresses
	 */
	public function createTemplate($template_name, $subject, $html_body, $plaintext_body)
	{
		$this->validateTemplateName($template_name);

		$result = $this->_sesClient->createTemplate([
			'Template' => [
				'HtmlPart' => $html_body,
				'SubjectPart' => $subject,
				'TemplateName' => $template_name,
				'TextPart' => $plaintext_body,
			],
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To view the content for an existing email template including the subject line, HTML body, and plain text, use the GetTemplate operation.
	 * Only TemplateName is required.
	 *
	 * @param string $template_name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verify-an-email-domain
	 */
	public function getTemplate($template_name)
	{
		$this->validateTemplateName($template_name);

		$result = $this->_sesClient->getTemplate([
			'TemplateName' => $template_name
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve a list of all email templates that are associated with your AWS account in the current AWS Region,
	 * use the ListTemplates operation.
	 *
	 * @param integer $itemsNumber
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function listTemplates($itemsNumber = 25)
	{
		$result = $this->_sesClient->listTemplates([
			'MaxItems' => $itemsNumber
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To change the content for a specific email template including the subject line,
	 * HTML body, and plain text, use the UpdateTemplate operation.
	 *
	 * @param string $template_name
	 * @param string $subject
	 * @param string $html_body
	 * @param string $plaintext_body
	 *
	 * @return Result
	 * @throws AwsException
	 */
	public function updateTemplate($template_name, $subject, $html_body, $plaintext_body)
	{
		$this->validateTemplateName($template_name);

		$result = $this->_sesClient->updateTemplate([
			'Template' => [
				'HtmlPart' => $html_body,
				'SubjectPart' => $subject,
				'TemplateName' => $template_name,
				'TextPart' => $plaintext_body,
			],
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To remove a specific email template, use the DeleteTemplate operation.
	 * All you need is the TemplateName.
	 *
	 * @param string $template_name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verify-an-email-domain
	 */
	public function deleteTemplate($template_name)
	{
		$this->validateTemplateName($template_name);

		$result = $this->_sesClient->deleteTemplate([
			'TemplateName' => $template_name
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To use a template to send an email to recipients, use the SendTemplatedEmail operation.
	 *
	 * @param string $template_name
	 * @param string $sender_email
	 * @param array|string $recipient_email
	 * @param string $reply_email
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-template.html#send-an-email-with-a-template
	 */
	public function sendTemplatedEmail($template_name, $sender_email, $recipient_email, $reply_email = null)
	{
		$this->validateTemplateName($template_name);
		$this->validateEmail($sender_email);
		$recipient_email = $this->normalizeRecipientList($recipient_email);

		if($reply_email === null) {
			$reply_email = $sender_email;
		} else {
			$this->validateEmail($reply_email);
		}

		$result = $this->_sesClient->sendTemplatedEmail([
			'Destination' => [
				'ToAddresses' => $recipient_email,
			],
			'ReplyToAddresses' => [$reply_email],
			'Source' => $sender_email,
			'Template' => $template_name,
			'TemplateData' => '{ }'
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To allow or block emails from a specific IP address, use the CreateReceiptFilter operation.
	 * Provide the IP address or range of addresses and a unique name to identify this filter.
	 *
	 * @param string $filter_name
	 * @param string $ip_address_range
	 * @param string $policy
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-filters.html#create-an-email-filter
	 */
	public function createEmailFilter($filter_name, $ip_address_range, $policy = 'Block')
	{
		$this->validateName($filter_name, 'SES receipt filter name');
		$this->validateCidr($ip_address_range);
		$this->validateReceiptFilterPolicy($policy);

		$result = $this->_sesClient->createReceiptFilter([
			'Filter' => [
				'IpFilter' => [
					'Cidr' => $ip_address_range,
					'Policy' => $policy,
				],
				'Name' => $filter_name,
			],
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To list the IP address filters associated with your AWS account in the current AWS Region,
	 * use the ListReceiptFilters operation.
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-filters.html#list-all-email-filters
	 */
	public function listEmailFilters()
	{
		$result = $this->_sesClient->listReceiptFilters();

		/** @var Result $result */
		return $result;
	}

	/**
	 * To remove an existing filter for a specific IP address use the DeleteReceiptFilter operation.
	 * Provide the unique filter name to identify the receipt filter to delete.
	 *
	 * If you need to change the range of addresses that are filtered,
	 * you can delete a receipt filter and create a new one.
	 *
	 * @param string $filter_name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-filters.html#delete-an-email-filter
	 */
	public function deleteEmailFilter($filter_name)
	{
		$this->validateName($filter_name, 'SES receipt filter name');

		$result = $this->_sesClient->deleteReceiptFilter([
			'FilterName' => $filter_name,

		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * A receipt rule set contains a collection of receipt rules.
	 * You must have at least one receipt rule set associated with your account before you can create a receipt rule.
	 *
	 * @param string $name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#create-a-receipt-rule-set
	 */
	public function createReceiptRuleSet($name)
	{
		$this->validateName($name, 'SES receipt rule set name');

		$result = $this->_sesClient->createReceiptRuleSet([
			'RuleSetName' => $name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Control your incoming email by adding a receipt rule to an existing receipt rule set.
	 * This example shows you how to create a receipt rule that sends incoming messages to an Amazon S3 bucket,
	 * but you can also send messages to Amazon SNS and AWS Lambda.
	 *
	 * @param string $rule_name
	 * @param string $rule_set_name
	 * @param string $s3_bucket
	 * @param array $recipients
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#create-a-receipt-rule
	 */
	public function createReceiptRule($rule_name, $rule_set_name, $s3_bucket, array $recipients = [])
	{
		$this->validateName($rule_name, 'SES receipt rule name');
		$this->validateName($rule_set_name, 'SES receipt rule set name');
		$this->validateBucketName($s3_bucket);

		$rule = [
			'Actions' => [
				[
					'S3Action' => [
						'BucketName' => $s3_bucket,
					],
				],
			],
			'Name' => $rule_name,
			'ScanEnabled' => true,
			'TlsPolicy' => 'Optional',
		];

		if ($recipients !== []) {
			$rule['Recipients'] = $this->normalizeRecipientList($recipients);
		}

		$result = $this->_sesClient->createReceiptRule([
			'Rule' => $rule,
			'RuleSetName' =>  $rule_set_name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Once per second, return the details of the specified receipt rule set.
	 * To use the DescribeReceiptRuleSet operation, provide the RuleSetName.
	 *
	 * @param string $name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#describe-a-receipt-rule-set
	 */
	public function describeReceiptRuleSet($name)
	{
		$this->validateName($name, 'SES receipt rule set name');

		$result = $this->_sesClient->describeReceiptRuleSet([
			'RuleSetName' => $name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Return the details of a specified receipt rule.
	 * To use the DescribeReceiptRule operation, provide the RuleName and RuleSetName.
	 *
	 * @param string $rule_name
	 * @param string $rule_set_name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#describe-a-receipt-rule
	 */
	public function describeReceiptRule($rule_name, $rule_set_name)
	{
		$this->validateName($rule_name, 'SES receipt rule name');
		$this->validateName($rule_set_name, 'SES receipt rule set name');

		$result = $this->_sesClient->describeReceiptRule([
			'RuleName' => $rule_name,
			'RuleSetName' => $rule_set_name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To list the receipt rule sets that exist under your AWS account in the current AWS Region,
	 * use the ListReceiptRuleSets operation.
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#list-all-receipt-rule-sets
	 */
	public function listReceiptRuleSets()
	{
		$result = $this->_sesClient->listReceiptRuleSets();

		/** @var Result $result */
		return $result;
	}

	/**
	 * This example shows you how to update a receipt rule that sends incoming messages to an AWS Lambda function,
	 * but you can also send messages to Amazon SNS and Amazon S3.
	 *
	 * @param string $rule_name
	 * @param string $rule_set_name
	 * @param string $lambda_arn
	 * @param string $sns_topic_arn
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#update-a-receipt-rule
	 */
	public function updateReceiptRule($rule_name, $rule_set_name, $lambda_arn, $sns_topic_arn)
	{
		$this->validateName($rule_name, 'SES receipt rule name');
		$this->validateName($rule_set_name, 'SES receipt rule set name');
		$this->validateLambdaArn($lambda_arn);
		$this->validateSnsTopicArn($sns_topic_arn);

		$result = $this->_sesClient->updateReceiptRule([
			'Rule' => [
				'Actions' => [
					[
						'LambdaAction' => [
							'FunctionArn' => $lambda_arn,
							'TopicArn' => $sns_topic_arn,
						],
					]
				],
				'Enabled' => true,
				'Name' => $rule_name,
				'ScanEnabled' => false,
				'TlsPolicy' => 'Require',
			],
			'RuleSetName' => $rule_set_name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Remove a specified receipt rule set that isn't currently disabled.
	 * This also deletes all of the receipt rules it contains.
	 * To delete a receipt rule set, provide the RuleSetName to the DeleteReceiptRuleSet operation.
	 *
	 * @param string $name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#delete-a-receipt-rule-set
	 */
	public function deleteReceiptRuleSet($name)
	{
		$this->validateName($name, 'SES receipt rule set name');

		$result = $this->_sesClient->deleteReceiptRuleSet([
			'RuleSetName' => $name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To delete a specified receipt rule, provide the RuleName and RuleSetName to the DeleteReceiptRule operation.
	 *
	 * @param string $rule_name
	 * @param string $rule_set_name
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#delete-a-receipt-rule
	 */
	public function deleteReceiptRule($rule_name, $rule_set_name)
	{
		$this->validateName($rule_name, 'SES receipt rule name');
		$this->validateName($rule_set_name, 'SES receipt rule set name');

		$result = $this->_sesClient->deleteReceiptRule([
			'RuleName' => $rule_name,
			'RuleSetName' => $rule_set_name,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * You are limited to sending only a certain amount of messages in a single 24-hour period.
	 * To check how many messages you are still allowed to send, use the GetSendQuota operation.
	 * For more information, see Managing Your Amazon SES Sending Limits.
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-send-email.html#check-your-sending-quota
	 */
	public function checkSendingQuota()
	{
		$result = $this->_sesClient->getSendQuota();
		$send_limit = $result['Max24HourSend'];
		$sent = $result['SentLast24Hours'];
		$available = $send_limit - $sent;

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve metrics for messages you've sent in the past two weeks, use the GetSendStatistics operation.
	 * This example returns the number of delivery attempts, bounces, complaints, and rejected messages in 15-minute increments.
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-send-email.html#monitor-your-sending-activity
	 */
	public function getSendingStatistics()
	{
		$result = $this->_sesClient->getSendStatistics();

		/** @var Result $result */
		return $result;
	}

	/**
	 * To authorize another AWS account to send emails on your behalf,
	 * use an identity policy to add or update authorization to send emails from your verified email addresses or domains.
	 * To create an identity policy, use the PutIdentityPolicy operation.
	 *
	 * @param string $identity
	 * @param string $policy
	 * @param string $policyName
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#create-an-authorized-sender
	 */
	public function createAuthorizedSender($identity, $policy, $policyName)
	{
		$this->validateIdentity($identity);
		$this->validateName($policyName, 'SES policy name');

		if (!is_string($policy) || trim($policy) === '') {
			throw new InvalidArgumentException('SES policy must be a non-empty JSON string.');
		}

		json_decode($policy);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new InvalidArgumentException('SES policy must be valid JSON.');
		}

		$result = $this->_sesClient->putIdentityPolicy([
			'Identity' => $identity,
			'Policy' => $policy,
			'PolicyName' => $policyName,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Return the sending authorization policies that are associated with a specific email identity or domain identity.
	 * To get the sending authorization for a given email address or domain, use the GetIdentityPolicy operation.
	 *
	 * @param string $identity
	 * @param string $policyNames
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#retrieve-polices-for-an-authorized-sender
	 */
	public function retrievePolicesForAuthorizedSender($identity, $policyNames)
	{
		$this->validateIdentity($identity);

		if (!is_array($policyNames) || $policyNames === []) {
			throw new InvalidArgumentException('SES policy names must be a non-empty array.');
		}

		foreach ($policyNames as $policyName) {
			$this->validateName($policyName, 'SES policy name');
		}

		$result = $this->_sesClient->getIdentityPolicies([
			'Identity' => $identity,
			'PolicyNames' => $policyNames,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * To list the sending authorization policies that are associated with a specific email identity or
	 * domain identity in the current AWS Region, use the ListIdentityPolicies operation.
	 *
	 * @param string $identity
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#list-authorized-senders
	 */
	public function listAuthorizedSenders($identity)
	{
		$this->validateIdentity($identity);

		$result = $this->_sesClient->listIdentityPolicies([
			'Identity' => $identity,
		]);

		/** @var Result $result */
		return $result;
	}

	/**
	 * Remove sending authorization for another AWS account to send emails with an email identity or
	 * domain identity by deleting the associated identity policy with the DeleteIdentityPolicy operation.
	 *
	 * @param string $identity
	 * @param string $policyName
	 *
	 * @return Result
	 * @throws AwsException
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#revoke-permission-for-an-authorized-sender
	 */
	public function revokePermissionForAuthorizedSender($identity, $policyName)
	{
		$this->validateIdentity($identity);
		$this->validateName($policyName, 'SES policy name');

		$result = $this->_sesClient->deleteIdentityPolicy([
			'Identity' => $identity,
			'PolicyName' => $policyName,
		]);

		/** @var Result $result */
		return $result;
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
	 * @param string $domain
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateDomain($domain)
	{
		if (!is_string($domain) || !preg_match('/^(?=.{1,253}$)(?!-)(?:[A-Za-z0-9-]{1,63}\\.)+[A-Za-z]{2,63}$/', $domain)) {
			throw new InvalidArgumentException('Invalid domain name.');
		}
	}

	/**
	 * @param string $identity
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateIdentity($identity)
	{
		if (is_string($identity) && filter_var($identity, FILTER_VALIDATE_EMAIL)) {
			return;
		}

		$this->validateDomain($identity);
	}

	/**
	 * @param string $templateName
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateTemplateName($templateName)
	{
		if (!is_string($templateName) || !preg_match('/^[A-Za-z0-9_-]{1,64}$/', $templateName)) {
			throw new InvalidArgumentException('Invalid SES template name.');
		}
	}

	/**
	 * @param array|string $recipients
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	protected function normalizeRecipientList($recipients)
	{
		if (is_string($recipients)) {
			$recipients = [$recipients];
		}

		if (!is_array($recipients) || $recipients === []) {
			throw new InvalidArgumentException('SES recipient list must be a non-empty array.');
		}

		foreach ($recipients as $recipient) {
			$this->validateEmail($recipient);
		}

		return array_values($recipients);
	}

	/**
	 * @param string $policy
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateReceiptFilterPolicy($policy)
	{
		if (!in_array($policy, ['Allow', 'Block'], true)) {
			throw new InvalidArgumentException('SES receipt filter policy must be Allow or Block.');
		}
	}

	/**
	 * @param string $name
	 * @param string $label
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateName($name, $label)
	{
		if (!is_string($name) || !preg_match('/^[A-Za-z0-9_-]{1,64}$/', $name)) {
			throw new InvalidArgumentException($label . ' is invalid.');
		}
	}

	/**
	 * @param string $cidr
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateCidr($cidr)
	{
		if (!is_string($cidr) || !preg_match('/^(.+)\\/(\\d{1,3})$/', $cidr, $matches)) {
			throw new InvalidArgumentException('Invalid CIDR range.');
		}

		$ip = $matches[1];
		$prefix = (int)$matches[2];
		$isIpv4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
		$isIpv6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;

		if ((!$isIpv4 && !$isIpv6) || ($isIpv4 && $prefix > 32) || ($isIpv6 && $prefix > 128)) {
			throw new InvalidArgumentException('Invalid CIDR range.');
		}
	}

	/**
	 * @param string $bucketName
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateBucketName($bucketName)
	{
		if (!is_string($bucketName) || !preg_match('/^(?!\\d+\\.\\d+\\.\\d+\\.\\d+$)(?!.*\\.\\.)(?!.*\\.-)(?!.*-\\.)[a-z0-9][a-z0-9.-]{1,61}[a-z0-9]$/', $bucketName)) {
			throw new InvalidArgumentException('Invalid S3 bucket name.');
		}
	}

	/**
	 * @param string $lambdaArn
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateLambdaArn($lambdaArn)
	{
		if (!is_string($lambdaArn) || !preg_match('/^arn:aws[a-zA-Z-]*:lambda:[a-z0-9-]+:\\d{12}:function:.+$/', $lambdaArn)) {
			throw new InvalidArgumentException('Invalid Lambda ARN.');
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
}
