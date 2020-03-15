<?php
namespace PFrame\Libs\Common;


use PFrame\Libs\Common\SLog;
use PFrame\Libs\Services\InitializeService as IntSvs;
/**
 * 通用share trait
 * @author luojinlong
 */
trait ShareTrait
{
    /**
     * 多进程处理任务
     * @param $processNum 开启进程数
     * @param $serviceClass 处理任务类(命名空间方式)
     * @param $serviceFunc  处理任务方法
     * @param array $dataList 任务方法传递参数
     * @return bool
     */
    public function multiProcessTask($processNum, $serviceClass, $serviceFunc, $dataList = array())
    {
        SLog::writeLog(__CLASS__ .' class '. __FUNCTION__ . ' function ' . __LINE__ . ' line input params:'.func_num_args());
        if (empty($processNum) || $processNum < $this->config['processNum']['minNum']) {
            SLog::writeLog('配置进程数有误！不能少于3个！', SLog::ERROR);
            return false;
        }

        if ($processNum > $this->config['processNum']['maxNum']) {
            SLog::writeLog('配置进程数过高！', SLog::ERROR);
            return false;
        }

        if (empty($serviceClass) || empty($serviceFunc)) {
            SLog::writeLog('进程处理任务对象不存在！', SLog::ERROR);
            return false;
        }
        SLog::writeLog(__CLASS__ .' class '. __FUNCTION__ . ' function ' . __LINE__ . ' line 准备开启'.$processNum.'个进程处理 :'.$serviceClass.'->'.$serviceFunc.'任务');
        for ($i = 0; $i < $processNum; $i++){
            $pid = pcntl_fork();

            if ($pid == -1) {
                SLog::writeLog('创建多进程失败!', SLog::ERROR);
                return false;
            } elseif ($pid) {
                $pid = posix_getpid();
                $pPid = posix_getppid();
                SLog::writeLog("创建第{$i}个进程成功:父进程id($pid)|子进程id($pPid)");

            } else {// 子进程处理
                SLog::writeLog("第{$i}个进程执行任务");
                IntSvs::getInstance($serviceClass)->$serviceFunc($dataList);
                exit;// 一定要注意退出子进程,否则pcntl_fork() 会被子进程再fork,带来处理上的影响。
            }
        }

        /**
         * 等待子进程执行结束
         */
        while (pcntl_waitpid(0, $status) != -1) {
            $whileNum = 1;
            $status = pcntl_wexitstatus($status);
            $this->taskLog("第{$whileNum}个Child进程 $status completed");
            $whileNum++;
        }
        SLog::writeLog(__CLASS__ .' class '. __FUNCTION__ . ' function ' . __LINE__ . ' line'. $processNum.'个进程处理 :'.$serviceClass.'->'.$serviceFunc.'任务结束！');
        return true;
    }

    /**
     * 获取di依赖注入
     * @param $configKey
     * @return mixed
     */
    public function getShared($configKey)
    {
        $di = \Phalcon\DI::getDefault();
        if(empty($di[$configKey])){
            SLog::writeLog(__CLASS__ .' class '. __FUNCTION__ . ' function ' . __LINE__ . ' line'. ' getShared:'.$configKey.' error', SLog::ERROR);
            return false;
        }

        $res = $di->getShared($configKey);
        return $res;
    }
    /**
     * 跟踪日志， 记录类方法请求参数数据。
     * @param $class
     * @param $func
     * @param $line
     * @param $args
     * @throws \ReflectionException
     */
    public function traceLog($class, $func, $line, $args)
    {
        $ReflectionClass  = new \ReflectionMethod ($class,$func);
        $log = ' 行号:'.$line. ' 参数：';
        $params = $ReflectionClass->getParameters();
        foreach ($params as $key=>$value) {
            if ($value->name == 'autoParams') {
                $log .=  $value->name .":" . json_encode(array_values($args),JSON_UNESCAPED_UNICODE). "  ";
            } else {
                $log .=  $value->name .":" .(isset($args[$key]) && is_array($args[$key]) ? json_encode($args[$key],JSON_UNESCAPED_UNICODE): (isset($args[$key]) ? $args[$key] : '')). "  ";
            }

            unset($args[$key]);
        }
        $this->serviceLog($log);
    }

    /**
     * Service类业务日志
     * @param $log
     * @param string $logType
     */
    public function serviceLog($log,$logType='INFO')
    {
        if ( $logType == 'ERROR' ) {
            $logType = SLog::ERROR;
        } elseif ( $logType == 'WARNING' ) {
            $logType = SLog::WARNING;
        } elseif ( $logType == 'DEBUG' ) {
            $logType = SLog::DEBUG;
        } elseif ( $logType == 'CRITICAL' ) {
            $logType = SLog::CRITICAL;
        } else {
            $logType = SLog::INFO;
        }
        $debugInfo = debug_backtrace();
        $debugFunction = array_column($debugInfo, 'function');
        $debugFunction = array_reverse($debugFunction);
        $debugClass    = array_column($debugInfo, 'class');

        $processFunction = '';
        $callFunction = '';
        $className = '';
        foreach ($debugClass as $classInfo) {
            if (strrpos($classInfo, 'Services') != false) {
                $className = str_replace("\\", ".", $classInfo);
                break;
            }

        }

        $flag = false;
        foreach ($debugFunction as $function) {
            if (strrpos($function, 'Process') != false) {
                $processFunction = $function;
                $flag = true;
            }
            if ($flag && !in_array($function, ['traceLog', 'serviceLog'])) {
                if (empty($callFunction)) {
                    $callFunction = $function;
                } else {
                    $callFunction .= '->'.$function;
                }
            }
        }

        $logPath  = TMP_PATH_LOG.'services_log/'.$className.'@'.$processFunction.'.log';
        $log = $className ."->".$callFunction.' '.$log;
        SLog::writeLog($log,$logType,$logPath);
    }
}
