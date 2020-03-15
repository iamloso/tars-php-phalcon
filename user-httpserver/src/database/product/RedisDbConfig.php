<?php
/**
 * 批量创建redis连接服务, 服务名称使用配置key值。
 */
 return   array(

     'redis'        => array(
         'host'         => '127.0.0.1',
         'auth'         => 'innolife.2018.^6&7*8(9)o',
         'port'         => 6379,
         'lifetime'     => 3600,
         'selectdb'     => 0,
         'prefix'       => '',
 ),

    'redisOld'    => array(
        'host'         => '10.25.134.38',
        'auth'         => '',
        'port'         => 6379,
        'lifetime'     => 3600,
        'selectdb'     => 0,
        'prefix'       => '',
),
     'redisMysql'        => array(
         'host'         => '127.0.0.1',
         'auth'         => 'innolife.2018.^6&7*8(9)o',
         'port'         => 6379,
         'lifetime'     => 3600,
         'selectdb'     => 0,
         'prefix'       => '',
     ),
);