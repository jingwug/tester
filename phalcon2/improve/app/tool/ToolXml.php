<?php
/***************************************
*$File: app/controllers/ToolXml.php
*$Description:
*$Author: ZhangBin
*$Time:  2015/4/20
****************************************/
 class ToolXml{
     /*
      * xml转化成数组
      * $param string $data xml数据
      */
     public static function xmlToArray($data){
         $xml = simplexml_load_string($data);
         return json_decode(json_encode($xml),TRUE);
     }
 }