<?php

/**
 * @var $s3Client cinghie\aws\models\S3
 * @var $this yii\web\View
 */

// Set Title and Breadcrumbs
$this->title = Yii::t('aws', 'Amazon S3');
$this->params['breadcrumbs'][] = $this->title;

var_dump($s3Client);