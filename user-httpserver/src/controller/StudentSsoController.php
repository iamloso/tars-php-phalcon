<?php
namespace HttpServer\controller;
/**
 * 学生登录、注册、找回密码相关接口
 * Created by PhpStorm.
 * User: luojinlong
 * Date: 18-10-9
 * Time: 下午3:15
 */
use HttpServer\component\Controller;
use HttpServer\conf\ENVConf;
use Tars\client\CommunicatorConfig;
use PFrame\Libs\Services\InitializeService as IntSvs;

class StudentSsoController extends Controller
{
    public function actionGetUidByMobile()
    {
        $version = $this->request->data['get']['version'];
        $phoneNum= $this->request->data['post']['phonenum'];

        $this->sendRaw('success: version='.$version.' phone='.$phoneNum);
    }

}