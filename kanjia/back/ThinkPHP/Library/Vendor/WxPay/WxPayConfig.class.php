<?php
$c['appid'] != '';
$c['customer_id'] = $_SESSION['customer_id']; //此处有疑问？？
$incc = M()->table(DB_NAME.'.pay_config')->field('appid,appsecret,paysignkey,partnerid,apiclient_key_path,apiclient_cert_path')->where($c)->find(); 
define("APPID",$incc['appid']);
define("KEY",$incc['paysignkey']);
define("APPSECRET",$incc['appsecret']);
define("MCHID",$incc['partnerid']);
define("REPORT_LEVENL",1);
define("SSLKEY_PATH",'/opt/www/weixin_platform'.$incc['apiclient_key_path']);
define("SSLCERT_PATH",'/opt/www/weixin_platform'.$incc['apiclient_cert_path']);
