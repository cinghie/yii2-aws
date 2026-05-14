<?php

use yii\helpers\Html;

/**
 * @var $buckets Aws\Result|null
 * @var $this yii\web\View
 */

// Set Title and Breadcrumbs
$this->title = Yii::t('aws', 'Amazon S3');
$this->params['breadcrumbs'][] = $this->title;

if ($buckets !== null && isset($buckets['Buckets'])) {
	echo Html::tag('h2', Html::encode(Yii::t('aws', 'Buckets')));
	echo '<ul>';

	foreach ($buckets['Buckets'] as $bucket) {
		echo Html::tag('li', Html::encode($bucket['Name']));
	}

	echo '</ul>';
}
