# Yii2 AWS

![License](https://img.shields.io/packagist/l/cinghie/yii2-aws.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-aws.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-aws.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-aws.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-aws.svg)](https://packagist.org/packages/cinghie/yii2-aws)

Yii2 AWS integrates AWS SDK for PHP v3 into Yii2 applications and provides wrappers for:

- Amazon S3
- Amazon SES
- Amazon SNS
- Yii2 admin dashboards for the included services

## Installation

```bash
composer require cinghie/yii2-aws
```

Or add the package to `composer.json`:

```json
{
    "require": {
        "cinghie/yii2-aws": "*"
    }
}
```

## Basic Configuration

Configure the `aws` component and, if you need the included dashboards, the `aws` module.

```php
use cinghie\aws\components\AWS;
use cinghie\aws\AWS as AWSModule;

return [
    'components' => [
        'aws' => [
            'class' => AWS::class,
            'region' => 'eu-south-1',
            'version' => 'latest',
        ],
    ],
    'modules' => [
        'aws' => [
            'class' => AWSModule::class,
            'awsRoles' => ['admin'],
        ],
    ],
];
```

With this configuration credentials are not stored in code: AWS SDK uses its native credential provider chain, including environment variables, IAM roles, container credentials, local profiles, and other supported providers.

## Credentials

### Recommended Option: IAM, Env Or Provider Chain

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'region' => 'eu-south-1',
    'version' => 'latest',
],
```

Environment variables supported by AWS SDK:

```bash
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=eu-south-1
```

### Local AWS Profile

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'profile' => 'default',
    'region' => 'eu-south-1',
    'version' => 'latest',
],
```

### Static Credentials

Use static credentials only when an IAM, env, or profile based setup is not available.

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'accessKey' => getenv('AWS_ACCESS_KEY_ID'),
    'secretKey' => getenv('AWS_SECRET_ACCESS_KEY'),
    'region' => 'eu-south-1',
    'version' => 'latest',
],
```

If only one of `accessKey` or `secretKey` is configured, the component throws `InvalidConfigException`.

## AWS SDK Options

The component exposes the main `Aws\Sdk` options:

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'region' => 'eu-south-1',
    'version' => 'latest',
    'profile' => 'default',
    'endpoint' => 'http://minio:9000',
    'usePathStyleEndpoint' => true,
    'retries' => 3,
    'debug' => false,
    'http' => [
        'connect_timeout' => 5,
        'timeout' => 30,
    ],
    'sdkOptions' => [
        // Any other option supported by Aws\Sdk.
    ],
],
```

`sdkOptions` is passed directly to the `Aws\Sdk` constructor, so it can be used for advanced options that are not exposed as dedicated component properties.

## Error Handling

The services do not write flash messages and do not convert AWS errors into empty arrays.

The contract is:

- success: returns `Aws\Result`
- AWS error: throws `Aws\Exception\AwsException`
- invalid local input: throws `InvalidArgumentException`

Example:

```php
use Aws\Exception\AwsException;
use cinghie\aws\models\S3;

try {
    $s3 = Yii::createObject(S3::class);
    $buckets = $s3->getBuckets();
} catch (AwsException $e) {
    Yii::error($e->getMessage(), __METHOD__);
    Yii::$app->session->setFlash('error', Yii::t('aws', 'Unable to load AWS data.'));
} catch (InvalidArgumentException $e) {
    Yii::$app->session->setFlash('error', $e->getMessage());
}
```

The included dashboards already catch `AwsException` in their controllers, log the detailed error, and display a generic message in the UI.

## Input Validation

The wrappers validate common input mistakes before calling AWS. This keeps application errors deterministic and avoids sending obviously invalid requests to AWS.

Validated inputs include:

- S3 bucket names
- SNS topic names and topic ARNs
- SNS subscription protocols and endpoints
- SES email addresses
- SES domain names
- SES template names
- SES recipient lists
- SES receipt filter policies, limited to `Allow` or `Block`
- SES policy JSON strings

These checks are intentionally strict for common application use. AWS remains the final authority for service-specific edge cases.

## Client Injection And Tests

Models can receive specific AWS clients through Yii configuration. This simplifies testing, mocking, and application-level overrides.

```php
use Aws\S3\S3Client;
use cinghie\aws\models\S3;

$client = new S3Client([
    'region' => 'eu-south-1',
    'version' => 'latest',
]);

$s3 = Yii::createObject([
    'class' => S3::class,
    's3Client' => $client,
]);
```

Available setters:

- `S3::setS3Client(S3Client $client)`
- `SES::setSesClient(SesClient $client)`
- `SNS::setSnsClient(SnsClient $client)`

If no client is injected, the model creates it from the `Yii::$app->aws->sdk` component.

## Amazon S3

### Basic Usage

```php
use Aws\Exception\AwsException;
use cinghie\aws\models\S3;

try {
    $s3 = Yii::createObject(S3::class);
    $result = $s3->getBuckets();

    foreach ($result['Buckets'] as $bucket) {
        echo $bucket['Name'] . PHP_EOL;
    }
} catch (AwsException $e) {
    Yii::error($e->getMessage(), __METHOD__);
}
```

### Create A Bucket

```php
$s3 = Yii::createObject(\cinghie\aws\models\S3::class);
$result = $s3->createBucket('my-application-bucket');
```

### Upload A File

```php
$s3 = Yii::createObject(\cinghie\aws\models\S3::class);

$result = $s3->putObjectInBucket(
    'my-application-bucket',
    'documents/report.pdf',
    Yii::getAlias('@runtime/report.pdf')
);
```

### MinIO Or S3-Compatible Configuration

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'region' => 'us-east-1',
    'version' => 'latest',
    'endpoint' => 'http://minio:9000',
    'usePathStyleEndpoint' => true,
    'accessKey' => getenv('MINIO_ACCESS_KEY'),
    'secretKey' => getenv('MINIO_SECRET_KEY'),
],
```

## Amazon SES

### Verify Identities

```php
use Aws\Exception\AwsException;
use cinghie\aws\models\SES;

try {
    $ses = Yii::createObject(SES::class);
    $result = $ses->verifyEmailIdentity('sender@example.com');
} catch (AwsException $e) {
    Yii::error($e->getMessage(), __METHOD__);
}
```

### List Email Identities

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);
$result = $ses->listIdentities();
```

### Create A Template

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);

$result = $ses->createTemplate(
    'OrderConfirmation',
    'Order confirmation',
    '<h1>Thank you for your order</h1>',
    'Thank you for your order'
);
```

### Send A Templated Email

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);

$result = $ses->sendTemplatedEmail(
    'OrderConfirmation',
    'sender@example.com',
    ['customer@example.com'],
    'reply@example.com'
);
```

### Create A Receipt Filter

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);

$result = $ses->createEmailFilter(
    'OfficeIpAllowList',
    '203.0.113.10/32',
    'Allow'
);
```

### Create A Receipt Rule With Recipients

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);

$result = $ses->createReceiptRule(
    'InboundToS3',
    'DefaultRuleSet',
    'my-application-bucket',
    ['inbound@example.com']
);
```

## Amazon SNS

SNS methods no longer have fake default values: protocol, endpoint, and topic must always be passed explicitly.

### Create A Topic

```php
use Aws\Exception\AwsException;
use cinghie\aws\models\SNS;

try {
    $sns = Yii::createObject(SNS::class);
    $result = $sns->createTopic('orders-events');
    $topicArn = $result['TopicArn'];
} catch (AwsException $e) {
    Yii::error($e->getMessage(), __METHOD__);
}
```

### Subscribe An Email Endpoint

```php
$sns = Yii::createObject(\cinghie\aws\models\SNS::class);

$result = $sns->subscribeEmailToTopic(
    'email',
    'operator@example.com',
    'arn:aws:sns:eu-south-1:123456789012:orders-events'
);
```

### Subscribe An HTTPS Endpoint

```php
$sns = Yii::createObject(\cinghie\aws\models\SNS::class);

$result = $sns->subscribeAppEndPointToTopic(
    'https',
    'https://example.com/sns/webhook',
    'arn:aws:sns:eu-south-1:123456789012:orders-events'
);
```

## Yii2 Dashboards

The module exposes basic dashboards for the services:

- `/aws/s3/index`
- `/aws/ses/index`
- `/aws/sns/index`

Access is controlled by `awsRoles`:

```php
'modules' => [
    'aws' => [
        'class' => cinghie\aws\AWS::class,
        'awsRoles' => ['admin'],
    ],
],
```

## Overrides

### Controllers

```php
'modules' => [
    'aws' => [
        'class' => cinghie\aws\AWS::class,
        'controllerMap' => [
            's3' => 'app\controllers\S3Controller',
            'ses' => 'app\controllers\SesController',
            'sns' => 'app\controllers\SnsController',
        ],
    ],
],
```

### Models

```php
'modules' => [
    'aws' => [
        'class' => cinghie\aws\AWS::class,
        'modelMap' => [
            'S3' => 'app\models\S3',
            'SES' => 'app\models\SES',
            'SNS' => 'app\models\SNS',
        ],
    ],
],
```

The bootstrap process registers model overrides in Yii's DI container. Query aliases such as `S3Query`, `SESQuery`, and `SNSQuery` are registered only when the mapped class extends `yii\db\ActiveRecord`; plain service models are not treated as ActiveRecord classes.

### Views

```php
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@cinghie/aws/views/s3' => '@app/views/aws/s3',
                '@cinghie/aws/views/ses' => '@app/views/aws/ses',
                '@cinghie/aws/views/sns' => '@app/views/aws/sns',
            ],
        ],
    ],
],
```

## Frontend Filter

In a Yii2 Advanced App, frontend module actions can be hidden with the included filter.

```php
use cinghie\aws\filters\FrontendFilter as AwsFrontendFilter;

'modules' => [
    'aws' => [
        'class' => cinghie\aws\AWS::class,
        'as frontend' => AwsFrontendFilter::class,
    ],
],
```

## Operational Suggestions

- Prefer IAM roles, environment variables, or AWS profiles over static credentials.
- Do not store access keys or secret keys in the repository.
- Always handle `AwsException` at the application layer.
- Inject specific clients in tests instead of calling real AWS services.
- Use `endpoint` and `usePathStyleEndpoint` for MinIO, LocalStack, and S3-compatible services.
- Avoid demo values or placeholders in production code: always pass explicit parameters.

## AWS Documentation

- S3: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-examples.html
- SES: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/ses-examples.html
- SNS: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/sns-examples.html
