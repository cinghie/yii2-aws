<?php

/**
 * @var $sesClient Aws\Ses\SesClient
 * @var $this yii\web\View
 */

// Set Title and Breadcrumbs
$this->title = Yii::t('aws', 'Amazon SES');
$this->params['breadcrumbs'][] = $this->title;

echo '<pre>'; var_dump($sesClient); echo '</pre>';
