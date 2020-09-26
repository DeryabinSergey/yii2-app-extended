<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
	    'urlManager' => [
		    'enablePrettyUrl' => true,
		    'showScriptName' => false,
		    'rules' => [
		    ],
	    ],
    ],
	'container' => [
		'definitions' => [
			\yii\widgets\LinkPager::class => \yii\bootstrap4\LinkPager::class,
			\yii\data\Pagination::class => [
				'pageSize' => 25
			],
			\yii\bootstrap4\LinkPager::class => [
				'maxButtonCount' => 8,
				'nextPageLabel' => false,
				'prevPageLabel' => false,
				'firstPageLabel' => '&larr;',
				'lastPageLabel' => '&rarr;',
				'options' => [
					'class' => 'd-flex pagination justify-content-center',
				]
			]
		]
	]
];
