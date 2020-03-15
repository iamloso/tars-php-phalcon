<?php

namespace HttpServer\servant\UserHttpServer\UserBaseServer\UserObj;

use Tars\client\CommunicatorConfig;
use Tars\client\Communicator;
use Tars\client\RequestPacket;
use Tars\client\TUPAPIWrapper;

use HttpServer\servant\UserHttpServer\UserBaseServer\UserObj\classes\OutStruct;
class ServiceInterfaceServant {
	protected $_communicator;
	protected $_iVersion;
	protected $_iTimeout;
	public $_servantName = "UserHttpServer.UserBaseServer.UserObj";

	public function __construct(CommunicatorConfig $config) {
		try {
			$this->_communicator = new Communicator($config);
			$this->_iVersion = $config->getIVersion();
			$this->_iTimeout = empty($config->getAsyncInvokeTimeout())?2:$config->getAsyncInvokeTimeout();
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function request() {
		try {
			$requestPacket = new RequestPacket();
			$requestPacket->_iVersion = $this->_iVersion;
			$requestPacket->_funcName = __FUNCTION__;
			$requestPacket->_servantName = $this->_servantName;
			$encodeBufs = [];

			$requestPacket->_encodeBufs = $encodeBufs;

			$sBuffer = $this->_communicator->invoke($requestPacket,$this->_iTimeout);
			$returnVal = new OutStruct();
			return TUPAPIWrapper::getStruct("",0,$returnVal,$sBuffer,$this->_iVersion);
			return $returnVal;

		}
		catch (\Exception $e) {
			throw $e;
		}
	}

}

