<?php
namespace PFrame\Libs\Plugins\Cache;

trait MysqlQueueTrait
{
    /**
     * 消息数组
     * @var array
     */
    public $msgData = array();

    /**
     * 生产insert事件消息
     * @param array $data
     * @return bool
     */
    public function produceInsertMsg($data = array())
    {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model params data:'.json_encode($data, JSON_UNESCAPED_UNICODE));
        if (empty($data)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'params is empty!', 'ERROR');
            return false;
        }
        $this->initMysqlQueueService();

        $this->msgData['data']  = $data;
        $this->msgData['eventType'] = 'insert';

        return $this->productMsg($this->msgData);
    }

    /**
     * 生产update事件消息
     * @param $data
     * @param $condition
     * @return bool
     */
    public function produceUpdateMsg($data, $condition)
    {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model params data:'.json_encode($data, JSON_UNESCAPED_UNICODE). ' params condition:'.json_encode($condition, JSON_UNESCAPED_UNICODE));

        if (empty($data)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'update params is empty!', 'ERROR');
            return false;
        }

        if (empty($condition)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition params is empty!', 'ERROR');
            return false;
        }
        $this->initMysqlQueueService();

        $list['updateData'] = $data;
        $list['condition']  = $condition;

        $this->msgData['data'] = $list;
        $this->msgData['eventType'] = 'update';

        return $this->productMsg($this->msgData);
    }

    /**
     * 初始化mysql消息队列
     */
    public function initMysqlQueueService()
    {
        $this->mysqlQueueMq = $this->getShared('mysqlQueue');

        $this->msgData['dbService'] = $this->dbService;
        $this->msgData['tableName'] = $this->getSource();

        return true;
    }

    /**
     * 生产数据消息
     * @param array $data
     * @return bool
     */
    public function productMsg($data = array())
    {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model params data:'.json_encode($data, JSON_UNESCAPED_UNICODE));

        $result = $this->mysqlQueueMq->publishMsg($data);

        if (empty($result)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, '生产数据库事件消息失败!', 'ERROR');
            return false;
        }

        return true;
    }
}
