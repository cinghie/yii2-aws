# Yii2 AWS

![License](https://img.shields.io/packagist/l/cinghie/yii2-aws.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-aws.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-aws.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-aws.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-aws.svg)](https://packagist.org/packages/cinghie/yii2-aws)

Yii2 AWS (Amazon Web Services) implementing Amazon AWS SDK for PHP

Features
-----------------

 - Amazon S3:  
 https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/s3-examples.html  
 - Amazon SES:  
 https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/ses-examples.html  
 - Amazon SNS:  
 https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/sns-examples.html  
 - Amazon SES Deliverability Dashboard:  
 https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/bouncecomplaintdashboard.html  
 
 
Installation
-----------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require cinghie/yii2-aws "*"
```

or add

```
"cinghie/yii2-aws": "*"
```

## Configuration

Add in your common configuration file:

```
use cinghie\aws\components\AWS;
use cinghie\aws\AWS as AWSModule;

'components' => [

    'aws' => [
    	'class' => AWS::class,
    	'accessKey' => 'AMAZON_ACCESS_KEY',
    	'secretKey' => 'AMAZON_SECRET_KEY',
    	'region' => 'eu-west-1',
    	'version' => 'latest'
    ],
    
],

'modules' => [

	'aws' => [
		'class' => AWSModule::class,
		'awsRoles' => ['admin'],
	],

],

```

## Overrides

Override controller example, on modules config

```
'modules' => [ 
	
	'aws' => [ 
		'class' => AWS::class,
		'controllerMap' => [
			's3' => 'app\controllers\S3Controller',
			'ses' => 'app\controllers\SesController',
		]
	]
	
],
```

Override models example, on modules config

```
'modules' => [ 
	
	'aws' => [ 
		'class' => AWS::class,
		'modelMap' => [
			'S3' => 'app\models\S3',
			'SES' => 'app\models\SES',
		]
	]
	
],
```

Override view example, on components config

```
'components' => [ 

	'view' => [
		'theme' => [
			'pathMap' => [
				'@cinghie/aws/views/s3' => '@app/views/aws/s3',
				'@cinghie/aws/views/ses' => '@app/views/aws/ses',
			],
		],
	],
	
],
```

## Filters

If you have a Yii2 App Advanced (frontend/backend) you can exclude frontend actions 

```
use cinghie\articles\filters\FrontendFilter as AwsFrontendFilter;

'modules' => [ 
	
	'aws' => [ 
		'class' => AWS::class,
		'as frontend' => AwsFrontendFilter::class,
	]
	
],
```
