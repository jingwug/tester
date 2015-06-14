<?php
/***************************************
*$File: app/controllers/ToolDate.php
*$Description:
*$Author: ZhangBin
*$Time:  2015/4/19
****************************************/
 class ToolDate{
     /*
      * 数字转换成日期字符串
      * $param string $date 格式：20140102
      */
    public static function getShortDate($date){
        return date('m/d',strtotime($date));
    }

     /*
      * 数字转换成日期字符串
      * $param string $date 格式：20140102
      */
     public static function getDate($date){
         return date('Y/m/d',strtotime($date));
     }


      /**
       * 获取当前是一年的第几周
       * @return number
       */
      public static function getWeekNow(){
         $datearr = getdate();
         $year = strtotime($datearr['year'].'-1-1');
         $startdate = getdate($year);
         $firstweekday = 7-$startdate['wday'];//获得第一周几天
         $yday = $datearr['yday']+1-$firstweekday;//今年的第几天
         return ceil($yday/7)+1;//取到第几周
     }

 }