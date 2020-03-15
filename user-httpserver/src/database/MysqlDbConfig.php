<?php
/**
 * 批量创建mysql连接服务, 服务名称使用配置key值。
 */
return   array(
    'database' => array(
        'adapter'      => 'Mysql',
        'host'         => '127.0.0.1',
        'username'     => 'root',
        'password'     => 'root@appinside',
        'dbname'       => 'limi_users',
        'port'         => '3306',
    ),

    'databaseRead' => array(
        'adapter'      => 'Mysql',
        'host'         => '127.0.0.1',
        'username'     => 'root',
        'password'     => 'root@appinside',
        'dbname'       => 'limi_users',
        'port'         => '3306',
    ),
);