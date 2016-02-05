<?php
/**
 * Main config file of tests application.
 */
return [
    'id' => 'app-test',
    'timezone' => 'Europe/Moscow',
    'basePath' => __DIR__,
    'vendorPath' => __DIR__ . '/../vendor',
    'language' => 'ru',
    'components' => [
        'dtConverter' => 'bupy7\datetime\converter\Converter',
    ],
];