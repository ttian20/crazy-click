<?php
require_once dirname(__FILE__) . '/env.php';
$common = array(
    //'DEBUG' => true,
    'ENV' => 'DEV',

    'URL_MODEL' => 2,
    'URL_CASE_INSENSITIVE' => true,
    'URL_CASE_INSENSITIVES' => true,
    'URL_HTML_SUFFIX' => '',

    'LOG_RECORD' => false,
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN',
    'LOG_TYPE' => 'File', 

    'TMPL_ENGINE_TYPE' => 'Smarty', //模版引擎配置
    'TMPL_ENGINE_CONFIG' => array(
         'left_delimiter' => '<{',
         'right_delimiter' => '}>',
    ),

    //for upload
    'UPLOAD_SITEIMG_QINIU' => array (
        'maxSize' => 5 * 1024 * 1024,//文件大小
        'rootPath' => './',
        'saveName' => array ('uniqid', ''),
        'driver' => 'Qiniu',
        'subName' => '',
        'driverConfig' => array (
            'secrectKey' => 'CXttxdSfLgXclD_N0DKTHfBOK2miLfMRn5oDFkOr',
            'accessKey' => 'Utrebjyb9UZDv4DrEz2nBHfDAP1poKaGRdPuQY5y',
            'domain' => 'yunmao-pic.qiniudn.com',
            'bucket' => 'yunmao-pic',
        ),
    ),

    'PAGE_LIMIT' => 10,
);

return array_merge($common, $conf);
