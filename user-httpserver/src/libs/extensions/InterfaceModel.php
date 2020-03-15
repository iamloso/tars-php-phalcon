<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Plugins\Cache;

class InterfaceModel extends BaseModel
{
    use Cache\MysqlQueueTrait;

    public $centerConfig;
    public $filter;

    /**
     * mysql 消息队列, 接收入库非事物操作数据信息.
     * @var
     */
    public $mysqlQueueMq;

    public function initialize() {
        $this->centerConfig = \Phalcon\DI::getDefault()->getShared('centerConfig');
        $this->filter = new \Phalcon\Filter();
        parent::initialize();

    }

    /**
     * 创建一条新数据
     * @param array $data (一维数组， 字段=>数据)
     * @return array
     * @throws \ReflectionException
     */
    public function createData(array $data): array
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());
        if (empty($data)) {
            $this->serviceLog('行号:'.__LINE__.' 创建数据操作： 新增数据 $data 为空', 'ERROR');
            return [];
        }

        try {
            $className = get_class($this);
            $Model = new $className();

            foreach ($data as $key => $value) {
                if (in_array($key, $this->columns)) {
                    $Model->$key = $value;
                } else {
                    $this->serviceLog('行号:'.__LINE__.' 创建数据操作： 字段'.$key.' is unknown', 'ERROR');
                    return false;
                }
            }

            if ($Model->create() == false) {
                foreach ($Model->getMessages() as $message){
                    $this->serviceLog('行号:'.__LINE__.' 创建数据操作： 创建数据失败， 失败信息:'.$message, 'ERROR');
                }
                return [];
            } else {
                return $Model->toArray();
            }
        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' 创建数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * 更新数据
     * @param array $condition 更新条件
     * (底层采用findFirst方式查询， $condition参数会透传下去， 使用方式参考:https://docs.phalconphp.com/en/3.4/db-models)
     * @param array $data 更新数据 (一维数组， 字段=>更新数据)
     * @return array
     * @throws \ReflectionException
     */
    public function updateData(array $condition, array $data): array
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());
        if (empty($condition)) {
            $this->serviceLog('行号:'.__LINE__.' 更新操作： 查询条件 $condition 为空', 'ERROR');
            return [];
        }
        if (empty($data)) {
            $this->serviceLog('行号:'.__LINE__.' 更新数据： 更新数据 $data 为空', 'ERROR');
            return [];
        }
        $this->__filter($condition);

        try {
            $className = get_class($this);
            $Model = new $className();

            $Model = $Model->findFirst($condition);
            if (empty($Model)) {
                $this->serviceLog('行号:'.__LINE__.' 更新操作：找不到更新数据', 'ERROR');
                return [];
            }
            foreach ($data as $key => $value) {
                if (in_array($key, $this->columns)) {
                    if ($value == 'auto') {
                        $Model->$key = $Model->$key+1;
                    } else {
                        $Model->$key = $value;
                    }
                } else {
                    $this->serviceLog('行号:'.__LINE__.' 更新操作： 字段'.$key.' is unknown', 'ERROR');
                    return [];
                }
            }

            if ($Model->update() == false) {
                foreach ($Model->getMessages() as $message){
                    $this->serviceLog('行号:'.__LINE__.' 更新操作： 更新失败， 错误信息'.$message, 'ERROR');
                }
                return [];
            } else {
                return $Model->toArray();
            }
        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' 更新数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return [];
        }
    }


    /**
     * 根据条件,获取一条数据
     * @param array $condition 查询条件
     * (底层采用findFirst方式查询， $condition参数会透传下去， 使用方式参考:https://docs.phalconphp.com/en/3.4/db-models)
     * @param mixed ...$autoParams
     * @return array
     * @throws \ReflectionException
     */
    public function getOneData(array $condition, ...$autoParams): array
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());

        try {
            if (empty($condition)) {
                $this->serviceLog('行号:'.__LINE__.' 查询操作： 查询条件 $condition 为空', 'ERROR');
                return [];
            }
            $this->__filter($condition);

            $Model = $this->findFirst($condition);
            if (empty($Model)) {
                $this->serviceLog('行号:'.__LINE__.' 查询操作： 查询结果为空!');
                return [];
            }
            return $Model->toArray();
        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' 查询数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * 根据条件， 查询多条用户数据
     * @param array $condition 查询条件
     * (底层采用find方式查询， $condition参数会透传下去， 使用方式参考:https://docs.phalconphp.com/en/3.4/db-models)
     * @return array
     * @throws \ReflectionException
     */
    public function getData(array $condition): array
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());
        try {
            if (empty($condition)) {
                $this->serviceLog('行号:'.__LINE__.' 多条查询操作： 查询条件 $condition 为空', 'ERROR');
                return [];
            }

            $this->__filter($condition);

            $Model = $this->find($condition);
            if (empty($Model)) {
                $this->serviceLog('行号:'.__LINE__.' 多条查询操作： 查询结果为空!');
                return false;
            }
            return $Model->toArray();
        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' 多条查询数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * @param array $condition
     * @return array
     * @throws \ReflectionException
     */
    public function sumData(array $condition): array
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());

        if (empty($condition)) {
            $this->serviceLog('行号:'.__LINE__.' sum数据操作： 查询条件 $condition 为空', 'ERROR');
            return [];
        }
        $this->__filter($condition);
        try {
            $className = get_class($this);
            $Model = new $className();

            $result = $Model->sum($condition);

            return $result;
        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' sum数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return [];
        }
    }
    /**
     * SQL insert 一条或多条数据
     * @param $fields
     *  array 单条：$fields =>array("id"=>"123","name"=>"abc",....),key为表列表,vulue为要插入的值
     *        多条：$fields =>array(
     *            array("id"=>"123","name"=>"abc",....),key为表列表,vulue为要插入的值
     *            array("id"=>"234","name"=>"bbc",....),key为表列表,vulue为要插入的值
     *            array("id"=>"456","name"=>"cbc",....),key为表列表,vulue为要插入的值
     *            )
     * @return bool
     * @throws \ReflectionException
     */
    public function insertData(array $fields): bool
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());
        if (empty ( $fields )) {
            $this->serviceLog('行号:'.__LINE__.' insert数据操作： 插入数据 $fields 为空', 'ERROR');
            return false;
        }
        try {
            // 数据库
            $db = $this->getDI ()->getdb ();
            $table = $this->getSource ();

            // 插入一条还是多条判断
            if (count ( $fields ) == count ( $fields, 1 )) { // 一维数组，插入一条数据
                $keys = array_keys ( $fields );
            } else { // 二维数组,插入多条数据
                $keys = array_keys ( $fields [0] );
            }
            // 插入列
            $columns = $values = '';
            foreach ( $keys as $k => $v ) {
                $columns .= $k != count ( $keys ) - 1 ? '`' .$v. '`' . ',' : '`'. $v. '`';
                $values .= $k != count ( $keys ) - 1 ? ':' . $v . ',' : ':' . $v;
            }

            $sql = "INSERT IGNORE INTO " . $table . " (" . $columns . ") " . "VALUES (" . $values . ")";
            $this->serviceLog('行号:'.__LINE__.' insert数据操作： sql语句:'.$sql, 'INFO');
            $dh = $db->prepare ( $sql );
            if (count ( $fields ) != count ( $fields, 1 )) {
                foreach ( $fields as $action ) {
                    $success = $dh->execute ( $action );
                    if ($success == false){
                        return false;
                    }
                }
            } else {
                $success = $dh->execute ( $fields );
            }

            return $success;
        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' 批量创建数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * 批量更新
     * @param array $condition
     * @param array $data
     * @return bool
     * @throws \ReflectionException
     */
    public function  updateDataMulti(array $condition, array $data ): bool
    {
        $this->traceLog(__CLASS__, __FUNCTION__, __LINE__, func_get_args());
        if (empty($data)) {
            $this->serviceLog('行号:'.__LINE__.' 批量更新操作： 查询条件 $data 为空', 'ERROR');
            return false;
        }

        if (empty($condition)) {
            $this->serviceLog('行号:'.__LINE__.' 批量更新操作： 查询条件 $condition 为空', 'ERROR');
            return false;
        }

        $setString = '';
        foreach ($data as $key => $value) {
            if (in_array($key, $this->columns)) {
                $setString .= " $key='$value', ";
            } else {
                $this->serviceLog('行号:'.__LINE__.' 批量更新操作： 字段'.$key.' is unknown', 'ERROR');
                return false;
            }
        }
        $this->__filter($condition);

        $setString = trim($setString, ', ');

        $con = '';
        foreach ($condition as $key => $value) {
            $con .= " $key='$value' and";
        }
        $con = trim($con, 'and');
        try {
            $Connection = \Phalcon\DI::getDefault()->get('database');
            $sql = "update {$this->getSource()} set $setString where $con ";
            $result = $Connection->execute($sql);
            return $result;

        } catch (\Exception $e) {
            $this->serviceLog('行号:'.__LINE__.' 批量更新数据操作： 捕获异常信息:'.$e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * 数据库查询参数过滤
     * @param array $condition
     */
    public function __filter(array &$condition): void
    {
        foreach ($condition as &$value) {
            $value = $this->filter->sanitize( $value, [ 'striptags', 'trim', ] );
        }
    }
}
