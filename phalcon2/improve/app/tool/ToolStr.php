<?php
/******************************
 * $File: ToolStr.php
 * $Description: 通用字符串处理方法
 * $Author: zhangyanan
 * $Time:2015-4-18
 ******************************/
class ToolStr{
	/*
	 * 函数说明：获取内容分页
	 *
	 * @access  public
	 * @param   $url  string  url规则
	 * @param   $page  int  页码
	 * @return            string  返回替换后内容
	 */
	public static function GetPage($count,$limit=10)
	{

		//获取除page参数外的其他参数
		$totalpage = ceil($count/$limit);
		if($totalpage < 2){
			return null;
		}
		if(!isset($_GET['page']) || !intval($_GET['page']) || $_GET['page'] > $totalpage) $page = 1;
		else $page = $_GET['page'];

		$query_str = explode('?',$_SERVER['QUERY_STRING']);
		if(isset($query_str[1])&&!empty($query_str[1])){	//有参数
			$query_str = explode('&',$query_str[1]);
		}else{
			$query_str = '';
		}

		if(isset($query_str[0]) && $query_str[0] != '')
		{
			$query_strs = '';

			foreach($query_str as $k)
			{
				$query_str_arr = explode('=', $k);

				if(strstr($query_str_arr[0],'page') == '')
				{
					$query_str_arr[0] = isset($query_str_arr[0]) ? $query_str_arr[0] : '';
					$query_str_arr[1] = isset($query_str_arr[1]) ? $query_str_arr[1] : '';

					$query_strs .= $query_str_arr[0].'='.$query_str_arr[1].'&';

				}
			}

			$nowurl = '?'.$query_strs;
		}
		else
		{
			$nowurl = '?';
		}

		$previous 	= $page - 1;
		$next 		= $page + 1;

		$page_content = '';
		if($previous < 1){
			$page_content .= '<span class="prev over">上一页</span>';
		}else{
			$page_content .= '<span class="prev"><a class="a1" href="'.$nowurl.'page='.$previous.'">上一页</a></span>';
		}

		//显示数字页码
		$first_flg = 0;
		$last_flg = 0;
		$num_list = '';
		for($i=1; $i<=$totalpage; $i++)
		{
			if($page == $i){
				$num_list .= '<a href="javascript:;" class="active">'.$i.'</a>';
			}elseif( str_replace("-", "",$i-$page)<3 ){
				$num_list .= '<a href="'.$nowurl.'page='.$i.'" >'.$i.'</a>';
			}elseif($i-$page>=3){
				$last_flg = 1;
			}elseif($page-$i>=3){
				$first_flg = 1;
			}else{
				$num_list .= '<a href="'.$nowurl.'page='.$i.'" >'.$i.'</a>';
			}
		}
		if($first_flg == 1){
			$page_content .= ' ...';
		}
		$page_content .= $num_list;
		if($last_flg == 1){
			$page_content .= '... ';
		}

		//显示尾页的裢接
		if($next > $totalpage)
		{
			$page_content .= '<span class="next over">下一页</span>';
		}else{
			$page_content .= '<span class="next"><a class="a1" href="'.$nowurl.'page='.$next.'">下一页</a></span>';
		}

		return $page_content;
	}

}
