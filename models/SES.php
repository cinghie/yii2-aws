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
 * @property SesClient $sesClient
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
}
