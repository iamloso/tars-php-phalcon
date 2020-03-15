<?php
/**
 * 批量创建rabbit mq 连接服务, 服务名称使用配置key值。
 */
 return   array(

     'rabbitMq'     => array(
         'host'         => '127.0.0.1',
         'port'         => '5672',
         'username'     => 'innolife-canal',
         'password'     => 'innolife.2018.^6&7*8(9)o',
         'vhost'        => '/',
         'queueInfo'    => array(
             'exChangeName' => 'innouser_canal_binlog_change',
             'queueName'    => 'innouser_canal_binlog_data',
             'routeKey'     => 'innouser_canal_binlog_route'
         ),
     ),
     'mysqlQueue'     => array(
         'host'         => '127.0.0.1',
         'port'         => '5672',
         'username'     => 'innolife-mysql',
         'password'     => 'innolife.2018.^6&7*8(9)o',
         'vhost'        => '/',
         'queueInfo'    => array(
             'exChangeName' => 'innouser_mysql_queue_change',
             'queueName'    => 'innouser_mysql_queue_data',
             'routeKey'     => 'innouser_mysql_queue_route',
             'autoCreate'   => '1',
             'isDurable'    => '1'
         ),
     ),
);