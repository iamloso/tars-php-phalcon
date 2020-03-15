<?php

namespace HttpServer\servant\UserHttpServer\UserBaseServer\UserObj\classes;

class OutStruct extends \TARS_Struct {
	const CODE = 0;
	const DESC = 1;
	const RESULT = 2;
	const DATA = 3;


	public $code=''; 
	public $desc=''; 
	public $result=''; 
	public $data; 


	protected static $_fields = array(
		self::CODE => array(
			'name'=>'code',
			'required'=>true,
			'type'=>\TARS::STRING,
			),
		self::DESC => array(
			'name'=>'desc',
			'required'=>true,
			'type'=>\TARS::STRING,
			),
		self::RESULT => array(
			'name'=>'result',
			'required'=>true,
			'type'=>\TARS::STRING,
			),
		self::DATA => array(
			'name'=>'data',
			'required'=>true,
			'type'=>\TARS::MAP,
			),
	);

	public function __construct() {
		parent::__construct('UserHttpServer_UserBaseServer_UserObj_OutStruct', self::$_fields);
		$this->data = new \TARS_Map(\TARS::STRING,\TARS::STRING);
	}
}
