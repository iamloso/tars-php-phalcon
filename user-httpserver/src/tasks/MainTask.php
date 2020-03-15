<?php
use \Tars\cmd\Command;
use PFrame\Libs\Services\InitializeService as IntSvs;

class MainTask extends \Phalcon\Cli\Task
{
    /**
     * 调用 swoole 服务
     * @param $argv
     */
    public function mainAction($argv)
    {
        //php tarsCmd.php  conf restart
        $configPath = $argv[1];
        $pos = strpos($configPath, '--config=');

        $configPath = substr($configPath, $pos + 9);

        $cmd = strtolower($argv[2]);

        $class = new Command($cmd, $configPath);
        $class->run();
    }

    public function testAction($data)
    {
        $data = IntSvs::getInstance('TestService')->test();
        var_dump($data);die;
    }

    public function testServantAction()
    {

    }
}
