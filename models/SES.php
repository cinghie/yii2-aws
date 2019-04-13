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
 * @see [SES Developer Guide](https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-examples.html)
 */
class SES extends Model
{
	/** @var SesClient $sesClient */
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
	 * To create a template to send personalized email messages, use the CreateTemplate operation.
	 * The template can be used by any account authorized to send messages in the AWS Region to which the template is added.
	 *
	 * @param string $template_name
	 * @param string $subject
	 * @param string $html_body
	 * @param string $plaintext_body
	 *
	 * @return bool
	 */
	public function createTemplate($template_name, $subject, $html_body, $plaintext_body)
	{
		try {

			$this->_sesClient->createTemplate([
				'Template' => [
					'HtmlPart' => $html_body,
					'SubjectPart' => $subject,
					'TemplateName' => $template_name,
					'TextPart' => $plaintext_body,
				],
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Template {0} correctly created',$template_name));

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * To view the content for an existing email template including the subject line, HTML body, and plain text, use the GetTemplate operation.
	 * Only TemplateName is required.
	 *
	 * @param $template_name
	 *
	 * @return array|Result
	 */
	public function getTemplate($template_name)
	{
		try {

			return $this->_sesClient->getTemplate(['TemplateName' => $template_name]);

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return [];
		}
	}

	/**
	 * To remove a specific email template, use the DeleteTemplate operation.
	 * All you need is the TemplateName.
	 *
	 * @param $template_name
	 *
	 * @return bool
	 */
	public function deleteTemplate($template_name)
	{
		try {

			$this->_sesClient->deleteTemplate(['TemplateName' => $template_name,]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Email Template deleted correctly'));

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * To retrieve a list of all email templates that are associated with your AWS account in the current AWS Region,
	 * use the ListTemplates operation.
	 *
	 * @param $itemsNumber
	 *
	 * @return array|Result
	 */
	public function listTemplate($itemsNumber)
	{
		try {

			return $this->_sesClient->listTemplates(['MaxItems' => $itemsNumber]);

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return [];
		}
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
	 * @return bool
	 */
	public function updateTemplate($template_name, $subject, $html_body, $plaintext_body)
	{
		try {

			$this->_sesClient->updateTemplate([
				'Template' => [
					'HtmlPart' => $html_body,
					'SubjectPart' => $subject,
					'TemplateName' => $template_name,
					'TextPart' => $plaintext_body,
				],
			]);

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * To use a template to send an email to recipients, use the SendTemplatedEmail operation.
	 *
	 * @param string $template_name
	 * @param string $sender_email
	 * @param string $recipeint_email
	 * @param string $reply_email
	 *
	 * @return bool
	 */
	public function sendTemplatedEmail($template_name,$sender_email,$recipeint_email,$reply_email = null)
	{
		if($reply_email === null) {
			$reply_email = $sender_email;
		}

		try {

			$this->_sesClient->sendTemplatedEmail([
				'Destination' => [
					'ToAddresses' => $recipeint_email,
				],
				'ReplyToAddresses' => [$reply_email],
				'Source' => $sender_email,
				'Template' => $template_name,
				'TemplateData' => '{ }'
			]);

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * To delete a verified email domain from the list of verified identities,
	 * use the DeleteIdentity operation.
	 *
	 * @param string $domain
	 *
	 * @return bool
	 */
	public function deleteDomain($domain)
	{
		try {

			$this->_sesClient->deleteIdentity(['Identity' => $domain]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Domain {0} deleted from the list of identities',$domain));

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * To delete a verified email address from the list of identities,
	 * use the DeleteIdentity operation.
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function deleteEmail($email)
	{
		try {

			$this->_sesClient->deleteIdentity(['Identity' => $email]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Email {0} deleted from the list of identities',$email));

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * To retrieve a list of email domains submitted in the current AWS Region,
	 * regardless of verification status use the ListIdentities operation.
	 *
	 * @return array
	 */
	public function listDomains()
	{
		try {

			$result = $this->_sesClient->listIdentities(['IdentityType' => 'Domain',]);

			return $result['Identities'];

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return [];
		}
	}

	/**
	 * To retrieve a list of email addresses submitted in the current AWS Region,
	 * regardless of verification status, use the ListIdentities operation.
	 *
	 * @return array
	 */
	public function listIdentities()
	{
		try {

			$result = $this->_sesClient->listIdentities(['IdentityType' => 'EmailAddress',]);

			return $result['Identities'];

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return [];
		}
	}

	/**
	 * Test CreateTemplate operation.
	 *
	 * @return void
	 */
	public function testCreateTemplate()
	{
		$template_name = 'Template_Name';
		$html_body = '<h1>AWS Amazon Simple Email Service Test Email</h1>' .
			'<p>This email was sent with <a href="https://aws.amazon.com/ses/">' .
			'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">' .
			'AWS SDK for PHP</a>.</p>';
		$subject = 'Amazon SES test (AWS SDK for PHP)';
		$plaintext_body = 'This email was send with Amazon SES using the AWS SDK for PHP.';

		$this->createTemplate($template_name, $subject, $html_body, $plaintext_body);
	}

	/**
	 * Amazon SES can send email only from verified email addresses or domains.
	 * By verifying a domain, you demonstrate that you're the owner of that domain.
	 * When you verify a domain, you allow Amazon SES to send email from any address on that domain.
	 *
	 * @param string $domain
	 *
	 * @return bool
	 */
	public function verifyDomainIdentity($domain)
	{
		try {

			$this->_sesClient->verifyDomainIdentity(['Domain' => $domain,]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Domain Identity {0} added correctly',$domain));

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * Amazon SES can send email only from verified email addresses or domains.
	 * By verifying an email address, you demonstrate that you're the owner of that address
	 * and want to allow Amazon SES to send email from that address.
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function verifyEmailIdentity($email)
	{
		try {
			
			$this->_sesClient->verifyEmailIdentity(['EmailAddress' => $email]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Verification Identity Email was sent to email {0}',$email));

			return true;

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());

			return false;
		}
	}
}
