<?php
namespace PFrame\Libs\Extensions;
use Innolife\empty_message_t;
use PFrame\Libs\Common;

class BaseTask extends \Phalcon\CLI\Task
{
    use Common\ShareTrait;

    public $centerConfig;
    public $config;
    public $lockFile;

    public function runAction()
    {
        $this->centerConfig = \Phalcon\DI::getDefault()->getShared('centerConfig');
        $this->config       = \Phalcon\DI::getDefault()->getShared('config');

        $this->createLockFile();
    }

    /**
     * task类业务日志
     * @param string $log
     * @param string $logType
     * @return null
     */
    public function taskLog($log,$logType='INFO'){
        if( $logType == 'ERROR' ) {
            $logType = Common\SLog::ERROR;
        } elseif ( $logType == 'WARNING' ) {
            $logType = Common\SLog::WARNING;
        } elseif ( $logType == 'DEBUG' ) {
            $logType = Common\SLog::DEBUG;
        } elseif ( $logType == 'CRITICAL' ) {
            $logType = Common\SLog::CRITICAL;
        } else {
            $logType = Common\SLog::INFO;
        }
        $className = str_replace("\\", "-", get_class($this)); //get_called_class()
        $logPath  = TMP_PATH_LOG.'task_log/'.$className.'.log';

        $log = $className ." ". $log;
        Common\SLog::writeLog($log,$logType,$logPath);
    }

    /**
     * 创建锁文件
     * @return bool
     */
    public function createLockFile()
    {
        $className = str_replace("\\", "-", get_class($this)); //get_called_class()
        $this->lockFile = TMP_PATH_LOG.'../task_'.$className.'.pid';
        return true;
    }

    /**
     * 跑脚本加锁
     * @param $lockFile
     * @return bool
     */
    public function enterLock($lockFile = ''){
        $lockFile = empty($lockFile) ? $this->lockFile : $this->lockFile.'.'.$lockFile;
        if(empty($lockFile)){
            return false;
        }
        if(file_exists($lockFile)){
            return false;
        }
        $fpLock = fopen( $lockFile, 'w+');
        fclose( $fpLock );

        return true;
    }

    /**
     * 检查跑脚本加锁
     * @param $lockFile
     * @return bool
     */
    public function releaseLock($lockFile = ''){
        $lockFile = empty($lockFile) ? $this->lockFile : $this->lockFile.'.'.$lockFile;
        if (empty($lockFile)){
            return false;
        }
        if(unlink($lockFile)){
            //echo "删除锁文件成功\n";
        } else {
            echo "删除锁文件失败\n";
        }
    }

    public function test()
    {
        echo 'a';
    }

    /**
     * 多进程接收消息
     * @param $pNum 进程数量
     * @param string $taskFunc 匿名函数 具体执行的任务
     * @return bool
     */
//    public function multiProcess($pNum, $taskFunc='')
//    {
//        if (empty($pNum) || $pNum < 3) {
//            $this->taskLog('配置进程数有误！不能少于3个！', 'ERROR');
//            return false;
//        }
//
//        if ($pNum > 500) {
//            $this->taskLog('配置进程数过高！', 'ERROR');
//            return false;
//        }
//        for ($i = 0; $i < $pNum; $i++){
//            $pid = pcntl_fork();
//
//            if ($pid == -1) {
//                $this->taskLog('创建多进程失败!', 'ERROR');
//                return false;
//            } elseif ($pid) {
//                $pid = posix_getpid();
//                $pPid = posix_getppid();
//                $this->taskLog("创建第{$i}个进程成功:父进程id($pid)|子进程id($pPid)");
//
//            } else {// 子进程处理
//                // 业务处理 begin
//                $this->taskLog("第{$i}个进程执行任务");
//                $this->rabbitMq->receiveMsg($i);
//
//                // 业务处理 end
//
//                exit;// 一定要注意退出子进程,否则pcntl_fork() 会被子进程再fork,带来处理上的影响。
//            }
//        }
//
//        /**
//         * 等待子进程执行结束
//         */
//        while (pcntl_waitpid(0, $status) != -1) {
//            $whileNum = 1;
//            $status = pcntl_wexitstatus($status);
//            $this->taskLog("第{$whileNum}个Child进程 $status completed");
//            $whileNum++;
//        }
//        return true;
//    }
}
