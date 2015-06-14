<?php
/***************************************
*$File: app/controllers/ToolSMS.php
*$Description:
*$Author: ZhangBin
*$Time:  2015/4/19
****************************************/
 class ToolSMS {
     /*
      * 生成手机验证码
      */
     public static function getSMS(){
         $code = "";
         for ($i = 0; $i < 6; $i++) {
             $code .= rand(0, 9);
         }
         return $code;
     }

     /*
      * 发送手机短信
      * $param string $code 要发送的内容
      * $param string $phone 手机号码
      */
     public static function send($code,$phone){
         $tool_curl = new ToolCurl();
         $url = "http://sh2.ipyy.com/sms.aspx";
         $data = [
             'userid' => 'CM',
             'account' => 'yingshi888',
             'password' => 'yingshi888',
             'mobile' => $phone,
             'content' => '您申请的金融平台的验证码为：'.$code.'（1分钟内有效，如非本人操作请忽略或咨询4008883381，本条免费）',
             'sendTime' => '',
             'action' => 'send',
             'extno' => '',
         ];
         $response = $tool_curl->post($url,$data);
         return $response;

     }

 }