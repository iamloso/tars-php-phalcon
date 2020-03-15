<?php
return array(
    'appName' => 'UserHttpServer',
    'serverName' => 'UserBaseServer',
    'objName' => 'UserObj',
    'withServant' => false, //决定是服务端,还是客户端的自动生成
    'tarsFiles' => array(
        './userServer.tars',
    ),
    'dstPath' => '../src/servant',
    'namespacePrefix' => 'HttpServer\servant',
);
