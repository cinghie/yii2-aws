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
use Aws\Ses\SesClient;
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
	 */
	public function __construct()
	{
		/** @var Sdk $sdk  */
		$sdk = Yii::$app->aws->sdk;
		$this->_sesClient = $sdk->createSes();

		parent::__construct();
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
	 * Amazon SES can send email only from verified email addresses or domains.
	 * By verifying an email address, you demonstrate that you're the owner of that address
	 * and want to allow Amazon SES to send email from that address.
	 *
	 * @param string $email
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verifying-email-addresses
	 */
	public function verifyEmailIdentity($email)
	{
		try {
			$result = $this->_sesClient->verifyEmailIdentity([
				'EmailAddress' => $email
			]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Verification Identity Email was sent to email {0}',$email));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verify-an-email-domain
	 */
	public function verifyDomainIdentity($domain)
	{
		try {
			$result = $this->_sesClient->verifyDomainIdentity([
				'Domain' => $domain
			]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Domain Identity {0} added correctly',$domain));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve a list of email addresses submitted in the current AWS Region,
	 * regardless of verification status, use the ListIdentities operation.
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#list-email-addresses
	 */
	public function listIdentities()
	{
		try {
			$result = $this->_sesClient->listIdentities([
				'IdentityType' => 'EmailAddress'
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve a list of email domains submitted in the current AWS Region,
	 * regardless of verification status use the ListIdentities operation.
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#list-email-domains
	 */
	public function listDomains()
	{
		try {
			$result = $this->_sesClient->listIdentities([
				'IdentityType' => 'Domain'
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#delete-an-email-address
	 */
	public function deleteEmail($email)
	{
		try {
			$result = $this->_sesClient->deleteIdentity([
				'Identity' => $email
			]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Email {0} deleted from the list of identities',$email));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#delete-an-email-domain
	 */
	public function deleteDomain($domain)
	{
		try {
			$result = $this->_sesClient->deleteIdentity([
				'Identity' => $domain
			]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Domain {0} deleted from the list of identities',$domain));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verifying-email-addresses
	 */
	public function createTemplate($template_name, $subject, $html_body, $plaintext_body)
	{
		try {
			$result = $this->_sesClient->createTemplate([
				'Template' => [
					'HtmlPart' => $html_body,
					'SubjectPart' => $subject,
					'TemplateName' => $template_name,
					'TextPart' => $plaintext_body,
				],
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Template {0} correctly created',$template_name));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Demo Create Template
	 *
	 * @return Result
	 */
	public function createDemoTemplate()
	{
		$template_name = 'Template_Name';
		$html_body = '<h1>AWS Amazon Simple Email Service Test Email</h1>' .
			'<p>This email was sent with <a href="https://aws.amazon.com/ses/">' .
			'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">' .
			'AWS SDK for PHP</a>.</p>';
		$subject = 'Amazon SES test (AWS SDK for PHP)';
		$plaintext_body = 'This email was send with Amazon SES using the AWS SDK for PHP.';

		return $this->createTemplate($template_name, $subject, $html_body, $plaintext_body);
	}

	/**
	 * To view the content for an existing email template including the subject line, HTML body, and plain text, use the GetTemplate operation.
	 * Only TemplateName is required.
	 *
	 * @param string $template_name
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verify-an-email-domain
	 */
	public function getTemplate($template_name)
	{
		try {
			$result = $this->_sesClient->getTemplate([
				'TemplateName' => $template_name
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 */
	public function listTemplates($itemsNumber = 25)
	{
		try {
			$result = $this->_sesClient->listTemplates([
				'MaxItems' => $itemsNumber
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 */
	public function updateTemplate($template_name, $subject, $html_body, $plaintext_body)
	{
		try {
			$result = $this->_sesClient->updateTemplate([
				'Template' => [
					'HtmlPart' => $html_body,
					'SubjectPart' => $subject,
					'TemplateName' => $template_name,
					'TextPart' => $plaintext_body,
				],
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Demo Update Template
	 *
	 * @return Result
	 */
	public function updateDemoTemplate()
	{
		$template_name = 'Template_Name';
		$html_body = '<h1>AWS Amazon Simple Email Service Test Email Updated!</h1>' .
			'<p>This email was sent with <a href="https://aws.amazon.com/ses/">' .
			'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">' .
			'AWS SDK for PHP</a>.</p>';
		$subject = 'Amazon SES test (AWS SDK for PHP)';
		$plaintext_body = 'This email was send with Amazon SES using the AWS SDK for PHP.';

		return $this->updateTemplate($template_name, $subject, $html_body, $plaintext_body);
	}

	/**
	 * To remove a specific email template, use the DeleteTemplate operation.
	 * All you need is the TemplateName.
	 *
	 * @param string $template_name
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-verify.html#verify-an-email-domain
	 */
	public function deleteTemplate($template_name)
	{
		try {
			$result = $this->_sesClient->deleteTemplate([
				'TemplateName' => $template_name
			]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Email Template deleted correctly'));
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * To use a template to send an email to recipients, use the SendTemplatedEmail operation.
	 *
	 * @param string $template_name
	 * @param string $sender_email
	 * @param string $recipeint_email
	 * @param string $reply_email
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-template.html#send-an-email-with-a-template
	 */
	public function sendTemplatedEmail($template_name, $sender_email, $recipeint_email, $reply_email = null)
	{
		if($reply_email === null) {
			$reply_email = $sender_email;
		}

		try {
			$result = $this->_sesClient->sendTemplatedEmail([
				'Destination' => [
					'ToAddresses' => $recipeint_email,
				],
				'ReplyToAddresses' => [$reply_email],
				'Source' => $sender_email,
				'Template' => $template_name,
				'TemplateData' => '{ }'
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * To allow or block emails from a specific IP address, use the CreateReceiptFilter operation.
	 * Provide the IP address or range of addresses and a unique name to identify this filter.
	 *
	 * @param string $filter_name
	 * @param string $ip_address_range
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-filters.html#create-an-email-filter
	 */
	public function createEmailFilter($filter_name, $ip_address_range)
	{
		try {
			$result = $this->_sesClient->createReceiptFilter([
				'Filter' => [
					'IpFilter' => [
						'Cidr' => $ip_address_range,
						'Policy' => 'Block|Allow',
					],
					'Name' => $filter_name,
				],
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Create Demo Filter
	 *
	 * @return Result
	 */
	public function createDemoEmailFilter()
	{
		$filter_name = 'FilterName';
		$ip_address_range = '10.0.0.1/24';

		return $this->createEmailFilter($filter_name, $ip_address_range);
	}

	/**
	 * To list the IP address filters associated with your AWS account in the current AWS Region,
	 * use the ListReceiptFilters operation.
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-filters.html#list-all-email-filters
	 */
	public function listEmailFilters()
	{
		try {
			$result = $this->_sesClient->listReceiptFilters();
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-filters.html#delete-an-email-filter
	 */
	public function deleteEmailFilter($filter_name)
	{
		try {
			$result = $this->_sesClient->deleteReceiptFilter([
				'FilterName' => $filter_name,

			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#create-a-receipt-rule-set
	 */
	public function createReceiptRuleSet($name)
	{
		try {
			$result = $this->_sesClient->createReceiptRuleSet([
				'RuleSetName' => $name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#create-a-receipt-rule
	 */
	public function createReceiptRule($rule_name, $rule_set_name, $s3_bucket)
	{
		try {
			$result = $this->_sesClient->createReceiptRule([
				'Rule' => [
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
					'Recipients' => ['<string>']
                ],
                'RuleSetName' =>  $rule_set_name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Create Demo Receipt Rule
	 *
	 * @return Result
	 */
	public function createDemoReceiptRule()
	{
		$rule_name = 'Rule_Name';
		$rule_set_name = 'Rule_Set_Name';
		$s3_bucket = 'Bucket_Name';

		$this->createReceiptRuleSet($rule_set_name);

		return $this->createReceiptRule($rule_name,$rule_set_name,$s3_bucket);
	}

	/**
	 * Once per second, return the details of the specified receipt rule set.
	 * To use the DescribeReceiptRuleSet operation, provide the RuleSetName.
	 *
	 * @param string $name
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#describe-a-receipt-rule-set
	 */
	public function describeReceiptRuleSet($name)
	{
		try {
			$result = $this->_sesClient->describeReceiptRuleSet([
				'RuleSetName' => $name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#describe-a-receipt-rule
	 */
	public function describeReceiptRule($rule_name, $rule_set_name)
	{
		try {
			$result = $this->_sesClient->describeReceiptRule([
				'RuleName' => $rule_name,
				'RuleSetName' => $rule_set_name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * To list the receipt rule sets that exist under your AWS account in the current AWS Region,
	 * use the ListReceiptRuleSets operation.
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#list-all-receipt-rule-sets
	 */
	public function listReceiptRuleSets()
	{
		try {
			$result = $this->_sesClient->listReceiptRuleSets();
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#update-a-receipt-rule
	 */
	public function updateReceiptRule($rule_name, $rule_set_name, $lambda_arn, $sns_topic_arn)
	{
		try {
			$result = $this->_sesClient->updateReceiptRule([
				'Rule' => [
					'Actions' => [
						'LambdaAction' => [
							'FunctionArn' => $lambda_arn,
							'TopicArn' => $sns_topic_arn,
						],
					],
					'Enabled' => true,
					'Name' => $rule_name,
					'ScanEnabled' => false,
					'TlsPolicy' => 'Require',
				],
				'RuleSetName' => $rule_set_name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * Update Demo Receipt Rule
	 *
	 * @return Result
	 */
	public function updateDemoReceiptRule()
	{
		$rule_name = 'Rule_Name';
		$rule_set_name = 'Rule_Set_Name';
		$lambda_arn = 'Amazon Resource Name (ARN) of the AWS Lambda function';
		$sns_topic_arn = 'Amazon Resource Name (ARN) of the Amazon SNS topic';

		return $this->updateReceiptRule($rule_name,$rule_set_name,$lambda_arn,$sns_topic_arn);
	}

	/**
	 * Remove a specified receipt rule set that isn't currently disabled.
	 * This also deletes all of the receipt rules it contains.
	 * To delete a receipt rule set, provide the RuleSetName to the DeleteReceiptRuleSet operation.
	 *
	 * @param string $name
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#delete-a-receipt-rule-set
	 */
	public function deleteReceiptRuleSet($name)
	{
		try {
			$result = $this->_sesClient->deleteReceiptRuleSet([
				'RuleSetName' => $name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-rules.html#delete-a-receipt-rule
	 */
	public function deleteReceiptRule($rule_name, $rule_set_name)
	{
		try {
			$result = $this->_sesClient->deleteReceiptRule([
				'RuleName' => $rule_name,
				'RuleSetName' => $rule_set_name,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * You are limited to sending only a certain amount of messages in a single 24-hour period.
	 * To check how many messages you are still allowed to send, use the GetSendQuota operation.
	 * For more information, see Managing Your Amazon SES Sending Limits.
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-send-email.html#check-your-sending-quota
	 */
	public function checkSendingQuota()
	{
		try {
			$result = $this->_sesClient->getSendQuota();
			$send_limit = $result['Max24HourSend'];
			$sent = $result['SentLast24Hours'];
			$available = $send_limit - $sent;
			//Yii::$app->session->setFlash('success', Yii::t('aws','You can send {available} more messages in the next 24 hours'),['available' => $available]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}

	/**
	 * To retrieve metrics for messages you've sent in the past two weeks, use the GetSendStatistics operation.
	 * This example returns the number of delivery attempts, bounces, complaints, and rejected messages in 15-minute increments.
	 *
	 * @return Result
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-send-email.html#monitor-your-sending-activity
	 */
	public function getSendingStatistics()
	{
		try {
			$result = $this->_sesClient->getSendStatistics();
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#create-an-authorized-sender
	 */
	public function createAuthorizedSender($identity, $policy, $policyName)
	{
		try {
			$result = $this->_sesClient->putIdentityPolicy([
				'Identity' => $identity,
				'Policy' => $policy,
				'PolicyName' => $policyName,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#retrieve-polices-for-an-authorized-sender
	 */
	public function retrievePolicesForAuthorizedSender($identity, $policyNames)
	{
		try {
			$result = $this->_sesClient->getIdentityPolicies([
				'Identity' => $identity,
				'PolicyNames' => $policyNames,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#list-authorized-senders
	 */
	public function listAuthorizedSenders($identity)
	{
		try {
			$result = $this->_sesClient->listIdentityPolicies([
				'Identity' => $identity,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

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
	 * @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-sender-policy.html#revoke-permission-for-an-authorized-sender
	 */
	public function revokePermissionForAuthorizedSender($identity, $policyName)
	{
		try {
			$result = $this->_sesClient->deleteIdentityPolicy([
				'Identity' => $identity,
				'PolicyName' => $policyName,
			]);
		} catch (AwsException $e) {
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		/** @var Result $result */
		return $result;
	}
}
