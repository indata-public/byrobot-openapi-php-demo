<?php
/**
 * Created by PhpStorm.
 * User: zhongm
 * Date: 2018-12-13
 * Time: 20:59
 */

include './HttpUtils.php';


//TODO app key  需要修改
$APP_KEY = "xxxxxxxxxxx";
//TODO app SECRET  需要修改
$APP_SECRET = "xxxxxxxxxxxxxxxx";

//线上环境
$BASE_URL = "https://api.byrobot.cn";
$byRobot_OpenApi = new HttpUtils();


$GET_COMPANY_URL = "https://api.byrobot.cn/openapi/v1/company/getCompanys";

$sendGet = $byRobot_OpenApi::sendGet($GET_COMPANY_URL, $APP_KEY, $APP_SECRET);
var_dump($sendGet);

