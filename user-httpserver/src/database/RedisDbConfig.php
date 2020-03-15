<?php
/**
 * 批量创建redis连接服务, 服务名称使用配置key值。
 */
 return   array(

    'redis'        => array(
        'host'         => '127.0.0.1',
        'auth'         => 'innolife.2017',
        'port'         => 6379,
        'lifetime'     => 3600,
        'selectdb'     => 0,
        'prefix'       => '',
),

    'redisOld'    => array(
        'host'         => '123.57.81.151',
        'auth'         => 'innolife.2017',
        'port'         => 6389,
        'lifetime'     => 3600,
        'selectdb'     => 0,
        'prefix'       => '',
),
     'redisMysql'    => array(
         'host'         => '52.80.133.229',
         'auth'         => 'innolife.2017',
         'port'         => 6379,
         'lifetime'     => 3600,
         'selectdb'     => 0,
         'prefix'       => '',
     ),
);