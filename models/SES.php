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

use Yii;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use yii\base\InvalidConfigException;

/**
 * Class SES
 *
 * @package cinghie\aws\models
 * @see [SES Developer Guide](https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-examples.html)
 */
class SES extends AWS
{
	/** @var SesClient $sesClient */
	public $sesClient;

	/**
	 * SES constructor
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct()
	{
		parent::__construct();
		$this->sesClient = $this->getSDK()->createSes();
	}

	/**
	 * To create a template to send personalized email messages, use the CreateTemplate operation.
	 * The template can be used by any account authorized to send messages in the AWS Region to which the template is added.
	 *
	 * @param string $name
	 * @param string $subject
	 * @param string $html_body
	 * @param string $plaintext_body
	 */
	public function createTemplate($name, $subject, $html_body, $plaintext_body)
	{
		try {

			$this->sesClient->createTemplate([
				'Template' => [
					'HtmlPart' => $html_body,
					'SubjectPart' => $subject,
					'TemplateName' => $name,
					'TextPart' => $plaintext_body,
				],
			]);
			Yii::$app->session->setFlash('success', Yii::t('aws', 'Template {0} correctly created',$name));

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}
	}

	/**
	 * Delete a verified email domain from the list of verified identities
	 *
	 * @param string $domain
	 */
	public function deleteDomain($domain)
	{
		try {

			$this->sesClient->deleteIdentity(['Identity' => $domain]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Domain {0} deleted from the list of identities',$domain));

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}
	}

	/**
	 * Delete a verified email address from the list of identities
	 *
	 * @param string $email
	 */
	public function deleteEmail($email)
	{
		try {

			$this->sesClient->deleteIdentity(['Identity' => $email]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Email {0} deleted from the list of identities',$email));

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}
	}

	/**
	 * List all domains Identities
	 *
	 * @return array
	 */
	public function listDomains()
	{
		try {

			$result = $this->sesClient->listIdentities(['IdentityType' => 'Domain',]);
			$identities = $result['Identities'];

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $identities;
	}

	/**
	 * List all email adress Identities
	 *
	 * @return array
	 */
	public function listIdentities()
	{
		try {

			$result = $this->sesClient->listIdentities(['IdentityType' => 'EmailAddress',]);
			$identities = $result['Identities'];

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $identities;
	}

	/**
	 * Test CreateTemplate operation.
	 *
	 * @return void
	 */
	public function testCreateTemplate()
	{
		$name = 'Template_Name';
		$html_body = '<h1>AWS Amazon Simple Email Service Test Email</h1>' .
			'<p>This email was sent with <a href="https://aws.amazon.com/ses/">' .
			'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">' .
			'AWS SDK for PHP</a>.</p>';
		$subject = 'Amazon SES test (AWS SDK for PHP)';
		$plaintext_body = 'This email was send with Amazon SES using the AWS SDK for PHP.';

		$this->createTemplate($name, $subject, $html_body, $plaintext_body);
	}

	/**
	 * Verify Domain Identity to send from Amazon SES
	 *
	 * @param string $domain
	 *
	 * @return void
	 */
	public function verifyDomainIdentity($domain)
	{
		try {

			$this->sesClient->verifyDomainIdentity(['Domain' => $domain,]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Domain Identity {0} added correctly',$domain));

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}
	}

	/**
	 * Verify Email Identity to send from Amazon SES
	 *
	 * @param string $email
	 *
	 * @return void
	 */
	public function verifyEmailIdentity($email)
	{
		try {
			
			$this->sesClient->verifyEmailIdentity(['EmailAddress' => $email]);
			Yii::$app->session->setFlash('info', Yii::t('aws', 'Verification Identity Email was sent to email {0}',$email));

		} catch (AwsException $e) {

			Yii::$app->session->setFlash('error', $e->getMessage());
		}
	}
}
