<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-aws
 * @license BSD-3-Clause
 * @package yii2-aws
 * @version 0.0.1
 */

namespace cinghie\aws;

use Yii;
use yii\base\Module;

class AWS extends Module
{

	/**
	 * @var string Amazon API Access Key
	 */
	public $accessKey = '';

	/**
	 * @var string Amazon Secret Key
	 */
	public $secretKey = '';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
	}

}
