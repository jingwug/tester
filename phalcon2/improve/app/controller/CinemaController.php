<?php

namespace Module\Frontend\Controller;

class CinemaController extends ControllerBase
{
	public function initialize() {
		parent::initialize();
	}

	//影院列表页
	public function indexAction() {
		$cityCode 	= $this->session->get('cityCode');

		$searchCinemaResult = $this->_searchCinemas();

		//三级地区列表
		$city = \Region::findFirst("code='{$cityCode}'");
		$regionList = \Region::find("parent_id='{$city->id}' AND display_level = 3");

		//影院类型列表
		$filmType = \BFilm::instance()->getFilmType();

		$this->view->setVar("title", '影院列表');
		$this->view->setVar("format", $filmType['format']);
		$this->view->setVar("params", $searchCinemaResult['params']);
		$this->view->setVar("total", $searchCinemaResult['total']);
		$this->view->setVar("regionList", $regionList);
		$this->view->setVar("city", $city);
		$this->view->setVar("cinemas", $searchCinemaResult['cinemas']);
		$this->view->setVar("page", \TPaginator::factory($total, $searchCinemaResult['pagesize']));
		$this->view->pick('cinema/index');
	}

	//影院详情页
	public function cinemaInfoAction() {
		$id = $this->request->getQuery('id');
		$date = date('Ymd');
		//查询影院基本信息
		$cinema_info 					= \Cinema::findFirst("id='".$id."'") ? \Cinema::findFirst("id='".$id."'")->toArray():'';
		$cinema_info['heat'] 			= \CinemaHeat::findFirst("cinema_id='".$id."'") ? \CinemaHeat::findFirst("cinema_id='".$id."'")->toArray():'';
		$cinema_info['banner'] 			= \Banner::find("cid=2") ? \Banner::find("cid=2")->toArray():'';
		//$cinema_info['img'] 			= \CinemaImage::findFirst("cinema_id='".$id."'") ? explode(',', \CinemaImage::findFirst("cinema_id='".$id."'")->image):'';
		 $master_type='cinema';
		 $ext_key='propaganda';
		$cinema_info['img'] 			= \File::find("master_id=$id and master_type='$master_type' and ext_key='$ext_key'")->toArray();


		$cinema_info['count_film'] 		= \CinemaFilm::count("cinema_id='".$id."' AND date = '".$date."'");
		$cinema_info['count_schedule'] 	= \FilmSchedule::count("cinema_id='".$id."' AND date = '".$date."'");
		$weekarray=array("日","一","二","三","四","五","六");
		$date_arr = array();
		for($i=0;$i<7;$i++){
			$time = strtotime('+'.$i.' days');
			if($i == 0){
				$date_arr[] = array('date'=>date('Ymd',$time),'tit'=>'今天','show'=>str_replace('月0','月',str_replace('年0','',date('年m月d日',$time))));
			}elseif($i == 1){
				$date_arr[] = array('date'=>date('Ymd',$time),'tit'=>'明天','show'=>str_replace('月0','月',str_replace('年0','',date('年m月d日',$time))));
			}elseif($i == 2){
				$date_arr[] = array('date'=>date('Ymd',$time),'tit'=>'后天','show'=>str_replace('月0','月',str_replace('年0','',date('年m月d日',$time))));
			}else{
				$date_arr[] = array('date'=>date('Ymd',$time),'tit'=>"星期".$weekarray[date("w",$time)],'show'=>str_replace('月0','月',str_replace('年0','',date('年m月d日',$time))));
			}
		}

		$this->view->setVar("title", $cinema_info['name']);
		$this->view->setVar("date_arr", $date_arr);
		$this->view->setVar("cinema_info", $cinema_info);
		$this->view->setVar("cinema_id", $id);
		$this->view->pick('cinema/cinema');
	}

	//影院列表ajax请求
	public function getListAjaxAction() {
		//影院列表
		$searchCinemaResult = $this->_searchCinemas();
		echo json_encode(array('status'=>'success','data'=>$searchCinemaResult,));
		exit;
	}

	/**
	 * 搜索影院
	 * @return type
	 */
	private function _searchCinemas() {
		$cityCode = \Phalcon\DI::getDefault()->getSession()->get('cityCode');
		$keyword = trim($this->request->getQuery('keyword'));
		$countyCode = $this->request->getQuery('countyCode');
		$parking = $this->request->getQuery('parking');
		$refund = $this->request->getQuery('refund');
		$effect = $this->request->getQuery('effect');
		$activity = $this->request->getQuery('activity');
		$page = $this->request->getQuery('page');
		$orderTemp = $this->request->getQuery('order');
		$orderTemp = empty($orderTemp) ? 'score desc' : $orderTemp;
		$orderArr = explode(" ", $orderTemp);
		$orderField = 'score';
		if(isset($orderArr[0]) && in_array($orderArr[0], array('score', 'attention'))) {
			$orderField = strtolower($orderArr[0]);
		}
		$orderSort = 'desc';
		if(isset($orderArr[1]) && in_array(strtolower($orderArr[1]), array('asc', 'desc'))) {
			$orderSort = strtolower($orderArr[1]);
		}

		$pagesize = 6;
		$params = array();
		$params['city_code'] = $cityCode;
		$params['pagesize'] = $pagesize;
		$params['order'] = $orderField.' '.$orderSort;
		if((!empty($countyCode)) && strtolower($countyCode) != 'all') $params['county_code'] = $countyCode;
		if(!empty($parking)) $params['parking'] = $cityCode;
		if(!empty($refund)) $params['is_refund'] = $refund;
		if(!empty($effect)) $params['screen_effect'] = $effect;
		if(!empty($activity)) $params['have_activity'] = $activity;
		//if(!empty($keyword)) $params['name'] = $name;
		if(!empty($page)) $params['page'] = $page;

		//影院数据
		$total = \BCinema::countByWordRegionEffectParkRefundActive($params);
		$cinemas = \BCinema::findAllByWordRegionEffectParkRefundActive($params);
		foreach($cinemas as $key => $cinema) {
			$playingSchedules = \BCinema::findPlayedScheduleByCinemaidDate($cinema['id']);
			$films = array();
			foreach($playingSchedules as $schedule) {
				$filmNames[] = array(
					'film_id' => $schedule['film_id'],
					'schedule_id' => $schedule['schedule_id'],
					'film_name' => $schedule['film_name']
				);
			}
			$cinemas[$key]['playingFilms'] = $films;
		}
		return array('params' => $params, 'total' => $total, 'pagesize' => $pagesize, 'cinemas' => $cinemas);
	}

}
