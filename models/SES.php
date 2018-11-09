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
	 * List email adress Identities
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
