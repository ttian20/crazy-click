<?php
$conf = array(
	//'配置项'=>'配置值'
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '192.168.83.224', // 服务器地址
    'DB_NAME' => 'nova_passport', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '123456', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8

    //mongo config
    'MONGO_CONFIG' => array(
        'db_type' => 'mongo',
        'db_user' => '',
        'db_pwd' => '',
        'db_host' => '192.168.83.224',
        'db_port' => '27017',
        'db_name' => 'nova',
        'db_charset'=> 'utf8',
    ),

    //mongo config
    'REDIS_CONFIG' => array(
        'host' => '192.168.83.224',
        'port' => '6379',
        'db' => '9',
    ),

    'WX_OAUTH' => array(
            'appid' => 'wxcb75e29aeafbfd35',
            'appsecret' => 'bf55dd8463832d83979731c3629dd9d7',
    ),

    'TRACK_LOG' => false,
    'RABBIT_MQ' => array(
        'host' => '192.168.83.224',
        'port' => 5672,
        'login' => 'guest',
        'password' => 'guest',
        'vhost' => '/wdwd',
    ),

    'RABBIT_MQ_DELIVERY_EXCHANGE' => 'e.test.order.delivery',
    'RABBIT_MQ_DELIVERY_QUEUE' => 'q.test.order.delivery',
    'RABBIT_MQ_VERSION' => 1,
    'RABBIT_MQ_TRACK_EXCHANGE' => 'e.test.track.log',
    'RABBIT_MQ_TRACK_QUEUE' => 'q.test.track.log',

    # prism
    'PRISM' => array(
        'key' => 'wsa2bkd2',
        'secret' => 'mganehbtbnxv2kufc6hv',
        #'site' => 'http://openapi.dev.wdwd.com/my/app/',
        'site' => 'http://openapi.dev.wdwd.com',
    ),

    'PRISM_SANDBOX' => true,

    # prism sms
    'SMS' => array(
        'key' => 'DSMARQ',
        'secret' => '3UH0IE3NQ5S6M76WSLDP',
        'site' => 'https://openapi.ishopex.cn',
    ),

    'UCENTER' => array(
        'appkey' => '123481',
        'appsecret' => '6733812311559f31290f93ffef1xxcb',
    ),

    'TOP_CLIENT_ID' => '12226348',
    'TOP_CLIENT_SECRET' => '4ab5c26b2f2c89cbc43f4b40f549e288',
    'TOP_GATEWAY' => 'http://gw.api.matrix.shopex.cn/router/rest',

    'WEIBO_CLIENT_ID' => '1960902139',
    'WEIBO_CLIENT_SECRET' => '89a1dad11991ca8aa2d29c49cc737424',
#    'WEIBO_CLIENT_ID' => '1998084992',
#    'WEIBO_CLIENT_SECRET' => '8422f9ba70edadebdb72ba1d519025c7',
    'WEIBO_GATEWAY' => 'https://api.weibo.com/2/',

    'WEIBO_PAY_SIGN' => '93b5be5048e1621d7e7a',
    'WEIBO_PAY_UID' => '2334146241',

    'WEIDIAN_CLIENT_ID' => '385505026',
    'WEIDIAN_CLIENT_SECRET' => '6093f6e1a34b7e42a416a75a248e5815',
    'WEIDIAN_GATEWAY' => 'https://api.weibo.com/2/',

    'QQ_CLIENT_ID' => '101143520',
    'QQ_CLIENT_SECRET' => '16b78d02f6b06002546414c6b2ba7e28',
    'QQ_GATEWAY' => 'https://graph.qq.com/',

    'CASHIER_KEY' => '35k35yivpyyseboq',
    'CASHIER_SECRET' => 'ftnpdx4yts2rn6q5fzi7zbuavueleb3i',
    'CASHIER_GETWAY' => 'http://action.wdwd.com/checkout/',

    //for rabbit mq
    'MQ_TPSHOP' => array(
        'host' => '121.196.43.150',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'iloveshopex',
        'vhost' => 'nova',
    ),

    'NOVA_API_URL' => 'http://api.test.wdwd.com/', 
    'MANAGER_API_URL' => 'http://manager.test.wdwd.com/index.php/openapi/',

    'EVENT_WECHAT_TEST' => false,

    'YL_WX' => array(
        'appid' => 'wxcb75e29aeafbfd35',
        'mchid' => 10032530,
        'appsecret' => 'eu383ej9ikmxnr3jwn1lq1hdm10p93j2',
    ),

    //站点域名配置
    'LOCAL_URL' => 'http://m.test.wdwd.com/',
    'ITEM_URL' => 'http://item.test.wdwd.com/',
    'SITE_URL' => 'http://test.wdwd.com/',
    'EVENT_URL' => 'http://event.wdwd.com/',
    'API_URL' => 'http://api.test.wdwd.com/api.php',
    'ACTION_URL' => 'http://action.test.wdwd.com/',

    'GO_API' => 'http://192.168.83.224:7060/',
    'SHOWCASE_API' => 'http://192.168.83.224:7040/',
    'THIRD_API' => 'http://192.168.83.224:7010/',

    'CASHIER_GETWAY' => 'http://action.test.wdwd.com/checkout/',

    'LOCAL_URL1' => 'm.test.wdwd.com/',
    'BNOW_API' => 'http://api.bnow.wdwd.com/',

);
