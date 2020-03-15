<?php
namespace HttpServer\controller;
/**
 * http协议用户数据接口, 负责请求参数校验, 通过tcp客户端请求服务端数据.
 * Created by PhpStorm.
 * User: luojinlong
 * Date: 18-10-9
 * Time: 下午4:43
 */
use HttpServer\component\Controller;
use HttpServer\conf\ENVConf;
use Tars\client\CommunicatorConfig;
use PFrame\Libs\Services\InitializeService as IntSvs;

class UserController extends Controller
{
    public function actionUserInfoByMobile()
    {

    }
}