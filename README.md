# Yii2 AWS

![License](https://img.shields.io/packagist/l/cinghie/yii2-aws.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-aws.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-aws.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-aws.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-aws.svg)](https://packagist.org/packages/cinghie/yii2-aws)

Yii2 AWS integra AWS SDK for PHP v3 in applicazioni Yii2 e fornisce wrapper per:

- Amazon S3
- Amazon SES
- Amazon SNS
- dashboard amministrative Yii2 per i servizi inclusi

## Installazione

```bash
composer require cinghie/yii2-aws
```

Oppure aggiungere il pacchetto al `composer.json`:

```json
{
    "require": {
        "cinghie/yii2-aws": "*"
    }
}
```

## Configurazione Base

Configurare il componente `aws` e, se servono le dashboard incluse, il modulo `aws`.

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

Con questa configurazione le credenziali non sono salvate nel codice: AWS SDK usa la credential provider chain nativa, quindi variabili ambiente, IAM role, container credentials, profili locali, ecc.

## Credenziali

### Opzione Consigliata: IAM, Env O Provider Chain

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'region' => 'eu-south-1',
    'version' => 'latest',
],
```

Variabili ambiente supportate dall'AWS SDK:

```bash
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=eu-south-1
```

### Profilo AWS Locale

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'profile' => 'default',
    'region' => 'eu-south-1',
    'version' => 'latest',
],
```

### Credenziali Statiche

Usarle solo quando non e' disponibile una soluzione IAM/env/profile.

```php
'aws' => [
    'class' => cinghie\aws\components\AWS::class,
    'accessKey' => getenv('AWS_ACCESS_KEY_ID'),
    'secretKey' => getenv('AWS_SECRET_ACCESS_KEY'),
    'region' => 'eu-south-1',
    'version' => 'latest',
],
```

Se si configura solo una tra `accessKey` e `secretKey`, il componente genera `InvalidConfigException`.

## Opzioni AWS SDK

Il componente espone le principali opzioni di `Aws\Sdk`:

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
        // Qualsiasi altra opzione supportata da Aws\Sdk.
    ],
],
```

`sdkOptions` viene passato direttamente al costruttore `Aws\Sdk`, quindi puo' essere usato per opzioni avanzate non esposte come proprieta' dedicate.

## Gestione Errori

I service non scrivono flash message e non trasformano gli errori AWS in array vuoti.

Il contratto e':

- successo: ritorna `Aws\Result`
- errore AWS: lancia `Aws\Exception\AwsException`

Esempio:

```php
use Aws\Exception\AwsException;
use cinghie\aws\models\S3;

try {
    $s3 = Yii::createObject(S3::class);
    $buckets = $s3->getBuckets();
} catch (AwsException $e) {
    Yii::error($e->getMessage(), __METHOD__);
    Yii::$app->session->setFlash('error', $e->getAwsErrorMessage() ?: $e->getMessage());
}
```

Le dashboard incluse catturano gia' `AwsException` nei controller e mostrano il messaggio nella UI.

## Iniezione Client E Test

I model possono ricevere client AWS specifici via configurazione Yii. Questo semplifica test, mocking e override.

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

Sono disponibili i setter:

- `S3::setS3Client(S3Client $client)`
- `SES::setSesClient(SesClient $client)`
- `SNS::setSnsClient(SnsClient $client)`

Se il client non viene iniettato, il model lo crea dal componente `Yii::$app->aws->sdk`.

## Amazon S3

### Uso Base

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

### Creare Un Bucket

```php
$s3 = Yii::createObject(\cinghie\aws\models\S3::class);
$result = $s3->createBucket('my-application-bucket');
```

### Caricare Un File

```php
$s3 = Yii::createObject(\cinghie\aws\models\S3::class);

$result = $s3->putObjectInBucket(
    'my-application-bucket',
    'documents/report.pdf',
    Yii::getAlias('@runtime/report.pdf')
);
```

### Configurazione MinIO O S3 Compatibile

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

### Verificare Identita'

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

### Listare Identita' Email

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);
$result = $ses->listIdentities();
```

### Creare Un Template

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);

$result = $ses->createTemplate(
    'OrderConfirmation',
    'Conferma ordine',
    '<h1>Grazie per il tuo ordine</h1>',
    'Grazie per il tuo ordine'
);
```

### Inviare Email Con Template

```php
$ses = Yii::createObject(\cinghie\aws\models\SES::class);

$result = $ses->sendTemplatedEmail(
    'OrderConfirmation',
    'sender@example.com',
    ['customer@example.com'],
    'reply@example.com'
);
```

## Amazon SNS

I metodi SNS non hanno piu' valori fittizi di default: protocollo, endpoint e topic devono essere sempre espliciti.

### Creare Un Topic

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

### Sottoscrivere Un Endpoint Email

```php
$sns = Yii::createObject(\cinghie\aws\models\SNS::class);

$result = $sns->subscribeEmailToTopic(
    'email',
    'operator@example.com',
    'arn:aws:sns:eu-south-1:123456789012:orders-events'
);
```

### Sottoscrivere Un Endpoint HTTPS

```php
$sns = Yii::createObject(\cinghie\aws\models\SNS::class);

$result = $sns->subscribeAppEndPointToTopic(
    'https',
    'https://example.com/sns/webhook',
    'arn:aws:sns:eu-south-1:123456789012:orders-events'
);
```

## Dashboard Yii2

Il modulo espone dashboard base per i servizi:

- `/aws/s3/index`
- `/aws/ses/index`
- `/aws/sns/index`

L'accesso e' controllato da `awsRoles`:

```php
'modules' => [
    'aws' => [
        'class' => cinghie\aws\AWS::class,
        'awsRoles' => ['admin'],
    ],
],
```

## Override

### Controller

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

### Model

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

### View

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

## Filtro Frontend

In una Yii2 Advanced App e' possibile nascondere le azioni del modulo lato frontend.

```php
use cinghie\aws\filters\FrontendFilter as AwsFrontendFilter;

'modules' => [
    'aws' => [
        'class' => cinghie\aws\AWS::class,
        'as frontend' => AwsFrontendFilter::class,
    ],
],
```

## Suggerimenti Operativi

- Preferire IAM role, env vars o profili AWS alle credenziali statiche.
- Non salvare access key e secret key nel repository.
- Gestire sempre `AwsException` nel livello applicativo.
- Iniettare client specifici nei test invece di chiamare AWS reale.
- Usare `endpoint` e `usePathStyleEndpoint` per MinIO/localstack/S3 compatibili.
- Evitare metodi con valori demo o placeholder nel codice produttivo: passare sempre parametri espliciti.

## Documentazione AWS

- S3: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-examples.html
- SES: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/ses-examples.html
- SNS: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/sns-examples.html
