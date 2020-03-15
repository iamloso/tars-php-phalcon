<?php

use Phalcon\CLI\Console as ConsoleApp;
use PFrame\Libs\Common\SLog;

// 定义应用目录路径
defined('APP_CLI_PATH') || define('APP_CLI_PATH', realpath(dirname(__FILE__)));

define ( 'SITE_NAME', 'limi-user-server' );

define ( 'APP_NAME', 'user-httpserver' );

define ( 'PROJECT_NAME', SITE_NAME.'/'.APP_NAME);

define ( 'PROJECT_PATH', realpath (__DIR__ ) );

$CenterConfig = include PROJECT_PATH."/conf/CenterConfig.php";

$MultiDbConfig = include PROJECT_PATH . "/database/MultiDbconfig.php";

$Config = include PROJECT_PATH . "/libs/FrameworkInit.php";

if ($Config->application->debug) {
    ini_set ( 'display_errors', '1' );
    error_reporting ( E_ALL);
}else{
    error_reporting ( E_ERROR );
}

require PROJECT_PATH.'/vendor/autoload.php';
/**
 * 处理console应用参数
 */
$arguments = array();
$configPath = isset($argv[1]) ? $argv[1] : '';
if (strpos($configPath, '--config=') !== false) {
    $arguments['task'] = 'main';
    $arguments['action'] = 'main';
    $arguments['params'] = $argv;
} else {
    foreach($argv as $k => $arg) {

        if($k == 1) {
            $arguments['task'] = $arg;
        } elseif($k == 2) {
            $arguments['action'] = $arg;
        } elseif($k >= 3) {
            $arguments['params'][] = $arg;
        }
    }
}
// 定义全局的参数， 设定当前任务及动作
define('CURRENT_TASK',   (isset($arguments['task']) ? $arguments['task'] : null));
define('CURRENT_ACTION', (isset($arguments['action']) ? $arguments['action'] : null));

SLog::writeLog("Task Run : time_start:".date("Y-m-d H:i:s")." task:". CURRENT_TASK . " action:".CURRENT_ACTION. " params:". json_encode( $arguments),SLog::INFO );
try {
    $console = new ConsoleApp();
    $console->setDI($di);
    // 处理参数
    $console->handle($arguments);
} catch (\Phalcon\Exception $e){
    SLog::writeLog ( $e, SLOG::ERROR, $Config->logFilePath->error );
    echo $e->getMessage();
    exit(255);
}
SLog::writeLog ("Task Run : time_end:".date("Y-m-d H:i:s")." task:". CURRENT_TASK . " action:".CURRENT_ACTION. " params:". json_encode( $arguments) ,SLog::INFO );

