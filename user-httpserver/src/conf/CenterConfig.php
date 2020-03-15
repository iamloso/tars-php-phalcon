<?php
/**
 * 中心业务配置
 * Class CenterConfig
 */
class CenterConfig extends \Phalcon\Config
{
    public function __construct(array $arrayConfig)
    {
        parent::__construct($arrayConfig);
    }
}

$configData = [];

return new CenterConfig($configData);
