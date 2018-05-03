<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/20
 * Time: 16:30
 */

return [
    'default' => 'memcached',

    'stores'  => [
        'redis' => [
            'driver' => 'redis',
            'cluster' => false,
            'connections' => [
                [
                    'host'     => '127.0.0.1',
                    'port'     => 6379,
                    'database' => 0
                ]
            ]
        ],
        'memcached' => [
            'driver' => 'memcached',
            'servers' => [
                [
                    'host'    => '127.0.0.1',
                    'port'    => 11211,
                    'weight'  => 100
                ]
            ]
        ]
    ]
];