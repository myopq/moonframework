<?php
declare(strict_types=1);

return [
    'app' => [
        'env' => 'develop', // production develop
        'name' => 'demo',
    ],

    'db' => [
		'default' => [
			'dbdsn' => 'mysql:dbname=test;host=localhost',
			'dbuser' => 'root',
			'dbpwd' => 'test',
			'enable_slave' => false,
		]
    ],
    
    'view' => [
        'themes' => 'default',
        'vars' => []
    ],

    'default_lang' => 'en',
    'default_timezone' => 'Etc/GMT+0',
];