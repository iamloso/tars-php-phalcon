<?php
namespace PFrame\Libs\Plugins\Cache;

use PFrame\Libs\Common\SLog;
/**
 * RabbitMq 操作类， 负责处理mq连接创建，选择chanel/exchange/queue 与消息发送及接收。
 * @email: luojinlong@innolife.cc
 * @time : 2018.04.24
 */

class RabbitMq
{
    /**
     * mq 连接句柄变量
     * @var null
     */
    public $rabbitMq = null;

    /**
     * 交换器对象句柄
     * @var null
     */
    public $exchangeObj = null;

    /**
     * 队列对象句柄
     * @var null
     */
    public $queueObj = null;

    /**
     * mq ip地址
     * @var string
     */
    public $host = '';
    /**
     * mq 端口号
     * @var string
     */
    public $port = '';
    /**
     * mq 虚拟地址
     * @var string
     */
    public $vhost= '';
    /**
     * 连接账号
     * @var string
     */
    public $userName = '';

    /**
     * 连接密码
     * @var string
     */
    public $password = '';

    /**
     * 队列定义信息
     * @var array
     */
    public $queueInfo = array();

    /**
     * 是否自动创建队列
     * @var bool
     */
    public $autoCreate = false;

    /**
     * 消息是否持久化
     * @var bool
     */
    public $isDurable  = false;

    public function __construct($DbConfig, $mqConfig='rabbitMq', $queueInfo = 'queueInfo')
    {
        $this->host = $DbConfig->$mqConfig->host;
        $this->port = $DbConfig->$mqConfig->port;
        $this->vhost= $DbConfig->$mqConfig->vhost;
        $this->userName = $DbConfig->$mqConfig->username;
        $this->password = $DbConfig->$mqConfig->password;
        $this->queueInfo= $DbConfig->$mqConfig->$queueInfo;
        $this->autoCreate= isset($this->queueInfo['autoCreate']) && $this->queueInfo['autoCreate'] ? true : false;
        $this->isDurable = isset($this->queueInfo['isDurable']) && $this->queueInfo['isDurable'] ? true : false;

        $this->connect();
    }

    /**
     * 创建mq连接
     * @throws \Exception
     */
    public function connect()
    {
        try {
            $this->rabbitMq = new \AMQPConnection(array('host' => $this->host, 'port' => $this->port, 'vhost' => $this->vhost, 'login' => $this->userName, 'password' => $this->password));
            $this->rabbitMq->connect();
            $this->initRabbitMq();

        } catch (\Exception $e) {
            SLog::writeLog('创建mq连接错误信息:'. $e, SLog::ERROR);
            return false;
        }

        return true;
    }

    /**
     * Rabbit Mq 资源初始化并分配
     * @return bool
     */
    public function initRabbitMq()
    {
        try {
            $checkResult = $this->checkConfig();
            if ($checkResult === false) {
                throw new \Exception('rabbit mq queue info 队列定义信息有误!');
            }
            $channel = new \AMQPChannel($this->rabbitMq);

            $this->exchangeObj = new \AMQPExchange($channel);
            $this->exchangeObj->setName($this->queueInfo['exChangeName']);
            $this->exchangeObj->setType(AMQP_EX_TYPE_DIRECT);
            if ($this->autoCreate) {
                $this->createExchange($this->isDurable);
            }
            $this->queueObj = new \AMQPQueue($channel);
            $this->queueObj->setName($this->queueInfo['queueName']);
            if ($this->autoCreate) {
                $this->createQueue($this->isDurable);
            }
            $this->queueObj->bind($this->queueInfo['exChangeName'], $this->queueInfo['routeKey']);

        } catch (\Exception $e) {
            SLog::writeLog( 'Rabbit Mq 资源初始化错误信息:' . $e, SLog::ERROR);
            return false;
        }
        return true;
    }

    /**
     * 创建交换机
     * @param bool $isDurable 交换机是否持久化
     * @return bool
     */
    public function createExchange($isDurable = false)
    {
        if ($isDurable) {
            $this->exchangeObj->setFlags(AMQP_DURABLE); //持久化
        }
        $this->exchangeObj->declareExchange();
        return true;
    }

    /**
     * 创建队列
     * @param bool $isDurable 队列是否持久化
     * @return bool
     */
    public function createQueue($isDurable = false)
    {
        if ($isDurable) {
            $this->queueObj->setFlags(AMQP_DURABLE); //持久化
        }
        $this->queueObj->declareQueue();
        return true;
    }

    /**
     * 检查队列定义信息
     * @return bool
     */
    public function checkConfig()
    {
        if (empty($this->queueInfo['exChangeName'])) {
            return false;
        }
        if (empty($this->queueInfo['queueName'])) {
            return false;
        }
        if (empty($this->queueInfo['routeKey'])) {
            return false;
        }

        return true;
    }

    /**
     * 发布消息动作
     * @param string|array $msgData 信息数据
     * @return bool
     */
    public function publishMsg($msgData)
    {
        if (empty($msgData)) {
            return false;
        }

        if (is_array($msgData)) {
            $msg = json_encode($msgData, JSON_UNESCAPED_UNICODE);
        } else {
            $msg = $msgData;
        }
        return $this->exchangeObj->publish($msg, $this->queueInfo['routeKey'], AMQP_MANDATORY, ['delivery_mode'=>2, 'priority'=> 9]);
    }

    /**
     * 接收消息
     * @param  function $callback 回调函数，处理接收后的消息
     * @return bool
     */
    public function receiveMsg($callback)
    {
        $this->queueObj->bind($this->queueInfo['exChangeName'], $this->queueInfo['routeKey']);

        $this->queueObj->consume($callback, AMQP_AUTOACK);
        return true;
    }


    public function __destruct()
    {
        $this->rabbitMq->disconnect();
    }
}