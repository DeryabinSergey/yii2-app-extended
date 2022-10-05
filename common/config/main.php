<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
	    'authManager' => [
		    'class' => \yii\rbac\DbManager::class,
	    ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
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
			\yii\widgets\LinkPager::class => \yii\bootstrap5\LinkPager::class,
			\yii\data\Pagination::class => [
				'pageSize' => 25
			],
			\yii\bootstrap5\LinkPager::class => [
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
