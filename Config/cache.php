<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/20
 * Time: 16:30
 */

return [
    'default' => 'redis',

    'stores'  => [
        'redis' => [
            'driver' => 'redis',
            'cluster' => false,
            'connections' => [
                [
                    'host'     => 'dev-redis.a.pa.com',
                    'port'     => 6379,
                    'database' => 0
                ]
            ]
        ],
        'memcached' => [
            'dirver' => 'memcached',
            'servers' => [
                [
                    'host'    => 'dev-mem.a.pa.com',
                    'port'    => 11211,
                    'weight'  => 100
                ]
            ]
        ]
    ]
];