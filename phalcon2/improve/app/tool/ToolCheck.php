<?php
/******************************
 * $File: ToolStr.php
 * $Description: 通用校验方法
 * $Author: zhangyanan
 * $Time:2015-4-18
 ******************************/
class ToolCheck{
    /*
     * 校验邮箱
     */
    public static function is_email($email){
        $regex = "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/" ; //正则
        return preg_match($regex, $email);
    }

    /*
     * 校验手机号
     */
    public static function is_phone($phone){
        $regex = "/^(1)[34578]{1}\d{9}$/" ; //正则
        return preg_match($regex, $phone);
    }
}