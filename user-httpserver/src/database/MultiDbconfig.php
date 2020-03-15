<?php

/**
 * mysql/redis/rabbitmq 配置文件入口
 * Class MultiDbConfig
 */
class MultiDbConfig extends \Phalcon\Config
{
    public function __construct(array $arrayConfig)
    {
        parent::__construct($arrayConfig);
    }
}

$multiConfig = array();

$mysqlConfigPath = __DIR__ . "/MysqlDbConfig.php";
$redisConfigPath = __DIR__ . "/RedisDbConfig.php";
$rabbitMqConfigPath = __DIR__ . "/MqDbConfig.php";

if (file_exists($mysqlConfigPath)) {
    $mysqlConfig = include "$mysqlConfigPath";
    $multiConfig['mysqlConfig'] = $mysqlConfig;
}

if (file_exists($redisConfigPath)) {
    $redisConfig = include "$redisConfigPath";
    $multiConfig['redisConfig'] = $redisConfig;
}

if (file_exists($rabbitMqConfigPath)) {
    $rabbitMqConfig = include "$rabbitMqConfigPath";
    $multiConfig['mqConfig'] = $rabbitMqConfig;
}

return new MultiDbConfig($multiConfig);
