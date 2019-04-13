<?php

/**
 * @var $buckets array
 * @var $s3Client Aws\S3\S3Client
 * @var $this yii\web\View
 */

// Set Title and Breadcrumbs
$this->title = Yii::t('aws', 'Amazon S3');
$this->params['breadcrumbs'][] = $this->title;

echo '<pre>'; var_dump($buckets); echo '</pre>';

echo '<pre>'; var_dump($s3Client); echo '</pre>';
