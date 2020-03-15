<?php
use PFrame\Libs\Common\SLog;

//设置默认时区
date_default_timezone_set("PRC");

if(!defined('TMP_PATH')) { define ( 'TMP_PATH','/tmp/');}

if(!defined('TMP_PATH_LOG')) { define ( 'TMP_PATH_LOG',TMP_PATH.'logs/'.PROJECT_NAME.'/'.date("Y-m-d")."/");}

/**
 * phalcon框架配置
 */
$Config = new \Phalcon\Config(array(
    'application' => array(
        'modelsDir'      =>  PROJECT_PATH.'/models/',
        'serviceDir'     =>  PROJECT_PATH.'/services/',
        'confDir'        =>  PROJECT_PATH.'/conf/',
        'pluginsDir'     =>  PROJECT_PATH.'/libs/plugins/',
        'commonDir'      =>  PROJECT_PATH.'/libs/common/',
        'extDir'         =>  PROJECT_PATH.'/libs/extensions/',
        'redisCahceDir'  =>  PROJECT_PATH.'/libs/plugins/cache/',
        'tasksDir'       =>  PROJECT_PATH.'/tasks/',
        'db'             =>  PROJECT_PATH.'/libs/plugins/db/',
        'cacheDir'       =>  TMP_PATH . 'logs/' .PROJECT_NAME.'/',
        'debug'          => true, //调试开关
        'dbDebug'        => true, //数据库调试开关
        'profilerDebug'  => true, //性能调试开关  数据库性能需要dbDebug开关打开
    ),

    'logFilePath' => array(
        'error'   => TMP_PATH_LOG.'task_error.log',
        'access'  => TMP_PATH_LOG.'task_access.log',
        'db'      => TMP_PATH_LOG.'task_db.log',
        'profile' => TMP_PATH_LOG.'task_profile.log',
    ),
    'processNum'  => array(
        'minNum'  => '3',
        'maxNum'  => '500'
    ),
));

/**
 * 注册类自动加载器
 */
$Loader = new \Phalcon\Loader();

$Loader->registerNamespaces(array(
    'Phalcon' => PROJECT_PATH . '/vendor/phalcon/incubator/Library/Phalcon/',
    'PFrame\Tasks'                 => $Config->application->tasksDir,
    'PFrame\Conf'                  => $Config->application->confDir,
    'PFrame\Libs\Models'           => $Config->application->modelsDir,
    'PFrame\Libs\Services'         => $Config->application->serviceDir,
    'PFrame\Libs\Plugins'          => $Config->application->pluginsDir,
    'PFrame\Libs\Common'           => $Config->application->commonDir,
    'PFrame\Libs\Extensions'       => $Config->application->extDir,
    'PFrame\Libs\Plugins\Cache'    => $Config->application->redisCahceDir,
    'PFrame\Libs\Plugins\Db'       => $Config->application->db,
));

$Loader->registerDirs(
    array(
        APP_CLI_PATH . '/tasks',
    )
);
$Loader->register();


// 使用CLI工厂类作为默认的服务容器
$di = new \Phalcon\DI\FactoryDefault\CLI ();

$di->set ( 'profiler', function () {
    return new \Phalcon\Db\Profiler ();
}, true );

if (!empty($MultiDbConfig['mysqlConfig'])) {
    foreach ($MultiDbConfig['mysqlConfig'] as $mysqlDbService => $MysqlConfig) {
        $di->setShared($mysqlDbService, function () use ($Config, $di, $MysqlConfig) {
            try {
                $eventsManager = new Phalcon\Events\Manager();
                // 从di中获取共享的profiler实例
                if ($Config->application->dbDebug) {
                    $profiler = $di->getProfiler();
                    $eventsManager->attach('db:beforeQuery', new PFrame\Libs\Plugins\Db\DbListener($profiler));
                    $eventsManager->attach('db:afterQuery',  new PFrame\Libs\Plugins\Db\DbListener($profiler));
                }
                $connection = new Phalcon\Db\Adapter\Pdo\Mysql (array(
                    'host' => $MysqlConfig->host,
                    'port' => $MysqlConfig->port,
                    'username' => $MysqlConfig->username,
                    'password' => $MysqlConfig->password,
                    'dbname' => $MysqlConfig->dbname,
                    "options" => array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'  // 设置编码
                    ),
                ));
            } catch (PDOException $e) {
                SLog::writeLog ( $e, SLog::ERROR, $Config->logFilePath->error );
                echo $e->getMessage();
                exit(254);
            }
            $connection->setEventsManager($eventsManager);
            return $connection;
        });
    }
}

/**
 * 批量注册Rabbit mq 实例服务
 */
//if (!empty($MultiDbConfig['mqConfig'])) {
//    $mqConfigList = $MultiDbConfig['mqConfig'];
//    foreach ($mqConfigList as $mqDbService => $MqConfig) {
//        $di->setShared ( $mqDbService, function () use($mqConfigList, $mqDbService){
//            try {
//                $rabbitMq = new PFrame\Libs\Plugins\Cache\RabbitMq($mqConfigList, $mqDbService);
//                return $rabbitMq;
//            } catch ( \Exception $e) {
//                throw $e;
//            }
//        } );
//    }
//}

/**
 * 批量注册redis 实例服务
 */
//if (!empty($MultiDbConfig['redisConfig'])) {
//    $redisConfigList = $MultiDbConfig['redisConfig'];
//    foreach ($redisConfigList as $redisDbService => $RedisConfig) {
//        $di->setShared ( $redisDbService, function () use($redisConfigList, $redisDbService){
//            try {
//                $cache = new PFrame\Libs\Plugins\Cache\RedisCache($redisConfigList, $redisDbService);
//                return $cache;
//            } catch ( \Exception $e ) {
//                throw $e;
//            }
//        } ); // end of cache
//    }
//}

/**
 * Start the session the first time some component request the session service
 */
$di->set ( 'session', function () {
    $session = new \Phalcon\Session\Adapter\Files ();
    $session->start ();

    return $session;
} );

$di->set ( 'config', $Config );

$di->set ( 'multiConfig', $MultiDbConfig);

$di->set ( 'centerConfig', $CenterConfig );
$di->set ( 'slog', function () {
    $SLog = new SLog ();
    return $SLog;
} );


return $Config;