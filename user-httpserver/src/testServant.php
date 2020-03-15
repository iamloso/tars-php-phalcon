<?php
/**
 * Created by PhpStorm.
 * User: liangchen
 * Date: 2017/6/16
 * Time: 下午2:38.
 */
require_once './vendor/autoload.php';

//\Tars\registry\RouteTable::getInstance();
//$result = \Tars\registry\RouteTable::getRouteInfo("TestFrame.TestServer.Obj");
//echo "result:\n";
//var_dump($result);die;

//$wrapper = new \Tars\registry\QueryFWrapper("tars.tarsregistry.QueryObj@tcp -h 172.0.200.201 -p 17890",1,60000);
//$result = $wrapper->findObjectById("TestFrame.TestServer.Obj");
//var_dump($result);die;


// 指定主控ip
$config = new \Tars\client\CommunicatorConfig();
$config->setLocator('tars.tarsregistry.QueryObj@tcp -h 172.0.200.201 -p 17890');
$config->setModuleName('App.Server');
$config->setCharsetName('UTF-8');
$config->setSocketMode(2);
$config->setServantName('TestFrame.TestServer.Obj');
//$servant = new Server\Cservant\TestFrame\TestServer\Obj\ServiceServantInterfaceServant($config);
$servant = new HttpServer\servant\UserHttpServer\UserBaseServer\UserObj\ServiceInterfaceServant($config);

$data = $servant->request();

var_dump($data);die;
$struct1 = new \Server\cservant\TestFrame\TestServer\Obj\classes\SimpleStruct();
$outStruct = new \Server\cservant\TestFrame\TestServer\Obj\classes\OutStruct();
$struct1->id = 100;
$struct1->count=300;
$data = $servant->testStruct(1, $struct1, $outStruct);

var_dump($outStruct);die;


//$logServant = new \Tars\log\LogServant($config);
//$result = $logServant->logger('TestFrame', 'TestServer', 'ted.log', '%Y%m%d', ['hahahahaha']);

$config->setSocketMode(2);
$logServant = new \Tars\log\LogServant($config);
$result = $logServant->logger('TestFrame', 'TestServer', 'ted2.log', '%Y%m%d', ['huohuohuo']);


echo "Locator specified with default socketmode 1\n";
$name = 'ted';
$intVal = $servant->sayHelloWorld($name, $greetings);

var_dump($greetings);die;

//$aa = new \Server\cservant\TestFrame\TestServer\Obj\classes\SimpleStruct();
//$bb = new \Server\cservant\TestFrame\TestServer\Obj\classes\SimpleStruct();
//
//$shorts = ["test1","test2"];
//$v1 = new \TARS_VECTOR(\TARS::STRING); //定义一个string类型的vector
//foreach ($shorts as $short) {
//    $v1->pushBack($short); //依次吧test1，test2两个元素，压入vector中
//}

#$v2 = new \TARS_VECTOR();

//$v3 = new \TARS_VECTOR(\TARS::STRING);
//$data = $servant->testVector('100', $v1 = 200, $v2 = 300, $v3, $v4);
//$data = $servant->testComplicatedStruct(new \Server\cservant\TestFrame\TestServer\Obj\classes\ComplicatedStruct() ,  '100', $c, $d);
$data = $servant->testSelf();
var_dump($data);die;
//var_dump($greetings);die;
//$servant->testTafServer();
//
//$aa = new \Server\cservant\TestFrame\TestServer\Obj\classes\LotofTags();
//$bb = new \Server\cservant\TestFrame\TestServer\Obj\classes\LotofTags();
//$aa->id = 888;
//$greetings = $servant->testLofofTags($aa, $bb);
//var_dump($bb->count);die;
//$servant->testVector(55, $v1 = 1, $v2 = 2 , $v3, $v4);
//var_dump($bb->count);

// 直接指定服务ip
//$route['sIp'] = '172.50.58.95';
//$route['iPort'] = 10200;
//$routeInfo[] = $route;
//$config = new \Tars\client\CommunicatorConfig();
//$config->setRouteInfo($routeInfo);
//$config->setSocketMode(2); //1标识socket 2标识swoole同步 3标识swoole协程
//$config->setModuleName('PHPTest.PHPServer');
//$config->setCharsetName('UTF-8');
//
//$servant = new \PHPTest\PHPServer\obj\TestTafServiceServant($config);
//
//echo "Service ip and port specified with socket mode 2 (swoole client)\n";
//
//$name = 'ted';
//$intVal = $servant->sayHelloWorld($name, $greetings);
//var_dump($greetings);
