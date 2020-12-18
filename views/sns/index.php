<?php

/**
 * @var $snsClient Aws\Sns\SnsClient
 * @var $this yii\web\View
 */

// Set Title and Breadcrumbs
$this->title = Yii::t('aws', 'Amazon SNS');
$this->params['breadcrumbs'][] = $this->title;

echo '<pre>'; var_dump($snsClient); echo '</pre>';
