<?php
/**
 * Created by PhpStorm.
 * User: qinqin
 * Date: 2020-02-26
 * Time: 17:47
 */


function curl($url, $params = false, $ispost = 0, $https = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, "https://www.jisilu.cn/data/cbnew/"); //模拟来源网址
    if ($https) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
    }
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }

    $response = curl_exec($ch);

    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    //var_dump(curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

function push($msg,$key){

    foreach($key AS $k){

        $url    =   "https://api.day.app/{$k}/转债提醒/".$msg;

        curl($url);


    }


}


$key    =   [
    "yjD2ySdSyZKwEkvc5Anh5h",
    "hXa2sxpDaXFE43hUBpTBwa",
    "4SzhTKxKANsps8h2VnTQ3M"
];





$info   =   curl("https://www.jisilu.cn/data/cbnew/pre_list/?___jsl=LST___t=".time()*1000);

$msg    =   [];

if($info){
    $info   =   json_decode($info,true);

    $today  =   strtotime(date("Y")."-".date("m")."-".date("d"));

    foreach($info['rows'] AS $v){


        if($v['cell'] && isset($v['cell']['apply_date'])){

            echo strtotime($v['cell']['apply_date']);
            echo "--".$today;
            echo "\r\n";


            if(strtotime($v['cell']['apply_date'])==$today)
            {

                $tmp    =   $v['cell'];
                $msg[]    =   $tmp['apply_tips'].$tmp['bond_nm'];



            }



        }


    }


    foreach($msg AS $v){

        push($v,$key);


    }




    echo "over";

}