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
        'dtConverter' => [
            'class' => 'bupy7\datetime\converter\Converter',
            'patterns' => [
                'ru' => [
                    'displayTimeZone' => 'Europe/Moscow',
                    'displayDate' => "'Custom text 'dd.LL.yyyy",
                    'displayTime' => 'php:H:i',
                    'displayDateTime' => 'php:d.m.Y, H:i',
                ],
            ],
        ],
    ]
];