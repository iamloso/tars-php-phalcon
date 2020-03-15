<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common;

class BaseService {
    use Common\ShareTrait;

    private static $arrInstance;
    public $centerConfig;
    public $config;
    public $multiConfig;

    /**
     * 存储redis缓存实例句柄
     * @var mixed|null
     */
    public $redisMysql = NULL;

    private function __clone(){}
    
    public function __construct() {
        $this->centerConfig = \Phalcon\DI::getDefault()->getShared('centerConfig');
        $this->multiConfig  = \Phalcon\DI::getDefault()->getShared('multiConfig');
        $this->config       = \Phalcon\DI::getDefault()->getShared('config');

        if ($this->redisMysql == NULL) {
            $this->redisMysql = $this->getShared('redisMysql');
        }
    }
    
    /**
     * 支持多个对象的单例 
     */
    public static function getInstance(){
        $className = get_called_class();
        if(!isset(self::$arrInstance[$className])){
            self::$arrInstance[$className] = new $className();
        }
        return self::$arrInstance[$className];
    }
}