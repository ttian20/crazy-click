<?php
$conf = array(
    'LOG_RECORD' => true,
    'DB_SQL_LOG' => true,
    'ENV' => 'PROD',

    'BEAM' => true,
	//'配置项'=>'配置值'
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => 'cc', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'txg19831210', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8

    //mongo config
    'MONGO_CONFIG' => array(
        'db_type' => 'mongo',
        'db_user' => '',
        'db_pwd' => '',
        'db_host' => '10.200.112.3',
        'db_port' => '27017',
        'db_name' => 'nova_production',
        'db_charset'=> 'utf8',
    ),

    //redis config
    'REDIS_CONFIG' => array(
        'host' => '10.10.64.20',
        'port' => '6379',
        'db' => '10',
    ),

    //click server
    'CS_CONFIG' => array(
        'host' => 'http://api.sandbox.aymoo.com/api/',
        'appkey' => 'website',
        'appsecret' => 'add53958547f0a4159d81ebfa36684a2',
    ),
);
