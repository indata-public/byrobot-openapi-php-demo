<?php
/**
 * Created by PhpStorm.
 * User: zhongm
 * Date: 2018-12-13
 * Time: 20:59
 */

include './HttpUtils.php';


//TODO app key  需要修改
$APP_KEY = "Op6vmcwAWfkA6fZo";
//TODO app SECRET  需要修改
$APP_SECRET = "4sl40P7BLcdXiTTZNrhdlfpegv5nSI";

//线上环境
$BASE_URL = "http://api.byrobot.cn";
$byRobot_OpenApi = new HttpUtils();


$GET_COMPANY_URL = "http://api.byrobot.cn/openapi/v1/company/getCompanys";

$call = "https://api.byrobot.cn/openapi/v1/task/singleCallByMobile";
$sendPost = $byRobot_OpenApi::sendPost($call, $APP_KEY, $APP_SECRET, [
    'mobile' => 'xxxxxxxxxx',
    'companyId' => xxxxxx,
    'robotDefId' => xxxx,
    'sceneDefId' => xxxxx,
    'sceneRecordId' => xxxxx,
    'userName' => 'xxxxx',
     'variables' => array(""=>""),
]);

print_r($sendPost);