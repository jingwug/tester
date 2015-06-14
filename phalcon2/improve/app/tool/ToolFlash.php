<?php
/***************************************
*$File: app/controllers/ToolFlash.php
*$Description:
*$Author: ZhangBin
*$Time:  2015/4/19
****************************************/
class ToolFlash{
    /*
     * 推送成功消息
     * $param string $content 消息
     * $param string $url 跳转地址
     */
    public static function success($content,$url=''){
        $msg['status'] = 'success';
        $msg['content'] = $content;
        if($url){
            $msg['url'] = $url;
        }
        echo json_encode($msg);
        exit;
    }

    /*
     * 推送失败消息
     * $param string $content 消息
     * $param string $url 跳转地址
     */
    public static function error($content,$url=''){
        $msg['status'] = 'error';
        $msg['content'] = $content;
        if($url){
            $msg['url'] = $url;
        }
        echo json_encode($msg);
        exit;
    }
}
