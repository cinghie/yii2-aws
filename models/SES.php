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

/**
 * Class SES
 *
 * @package cinghie\aws\models
 * @see [SES Developer Guide](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/getting-started.html)
 */
class SES extends AWS
{
	/**
	 * @@inheritdoc
	 */
	public function getSES()
	{
		$sdk = $this->getSDK();

		return $sdk->createSes();
	}
}
