<?php
/**
 * 批量创建rabbit mq 连接服务, 服务名称使用配置key值。
 */
return   array(

    'rabbitMq'     => array(
        'host'         => '127.0.0.1',
        'port'         => '5672',
        'username'     => 'innolife-message',
        'password'     => 'innolife.2018',
        'vhost'        => '/innolife_dev',
        'queueInfo'    => array(
            'exChangeName' => 'innolife_dev_binlog_change',
            'queueName'    => 'innolife_dev_binlog_data',
            'routeKey'     => 'innolife_dev_binlog_data'
        ),
    ),
    'mysqlQueue'     => array(
        'host'         => '127.0.0.1',
        'port'         => '5672',
        'username'     => 'guest',
        'password'     => 'guest',
        'vhost'        => '/',
        'queueInfo'    => array(
            'exChangeName' => 'innolife_mysql_queue_change',
            'queueName'    => 'innolife_mysql_queue_data',
            'routeKey'     => 'innolife_mysql_queue_data',
            'autoCreate'   => '1',
            'isDurable'    => '1'
        ),
    ),
);