<?php
namespace PFrame\Libs\Services;

use PFrame\Libs\Extensions\BaseService;
use PFrame\Libs\Models\InitializeModel as IntMs;

class TestService extends BaseService{

    public function test()
    {
        $data = IntMs::getInstance('Test')->getOneData('id=2');
        return $data['name'];
    }
}
