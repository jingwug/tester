<?php
/***************************************
*$File: app/controllers/ToolValidator.php
*$Description:用户用户输入验证
*$Author: ZhangBin
*$Time:  2015/4/19
****************************************/
 class ToolValidator {
     /*
      * 验证是否手机号码
      * $param string $number 字符串
      */
     public static function isMobile($number){
         return preg_match("/1[34578]{1}\d{9}$/",$number);
     }

     /*
      * 验证是否为邮箱
      * $param string $email 字符串
      */
     public static function isEmail($email){
         $regex = "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/i" ; //正则
         return preg_match( $regex, $email );
     }
 }