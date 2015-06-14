<?php
namespace Module\Frontend\Controller;

class FilmController extends ControllerBase
{
	public function initialize() {
		parent::initialize ();
		$this->tag->setTitle('影片选座');
	}

	/**
	 * 热映电影展示
	 */
	public function heatAction() {
		$status = 1;
		$city = $this->session->get ( 'cityName' );
		$city_code = $this->session->get ( 'cityCode' );
		$cinemaFilmsArr = \BFilm::instance()->getCinemaFilms ( $city_code, $status );
		if ($cinemaFilmsArr) {
			// 查询影片的类型
			\BFilm::instance()->getFilmArray($cinemaFilmsArr);
		}
		// 获取电影的类型和版本
		$filmType = \BFilm::instance()->getFilmType ();

		// 查询关注排行


		$this->view->setVar("title", '热映电影');

		$this->view->setVar("type", $filmType);
		$this->view->setVar("city", $city);
		$this->view->setVar("film_info", $cinemaFilmsArr);
		$this->view->pick('film/heat');
	}

	/**
	 * 即将上映电影展示
	 */
	public function upcomingAction()
	{
		$status = 0;
		$city = $this->session->get ( 'cityName' );
		$city_code = $this->session->get ( 'cityCode' );
		$filmsArr = \BFilm::instance()->getFilms ( array (
				"status = $status"
		) );
		if ($filmsArr)
		{
			// 查询影片的类型
			\BFilm::instance()->getFilmArray ( $filmsArr );
		}
		// 获取电影的类型和版本
		$filmType = \BFilm::instance()->getFilmType ();

		// 查询关注排行
		$attenFlims = \BFilm::instance()->getAttenFlims();

		$this->view->setVar	("title", '即将上映电影');
		$this->view->setVar ( "attenFlims", $attenFlims );
		$this->view->setVar ( "type", $filmType );
		$this->view->setVar ( "city", $city );
		$this->view->setVar ( "film_info", $filmsArr );
		$this->view->pick ( 'film/upcoming' );
	}

	/**
	 * 热映电影展示ajax
	 */
	public function heatAjaxAction()
	{
		$date_time = date ( "Ymd" );
		$city = $this->session->get ( 'cityName' );
		$city_code = $this->session->get ( 'cityCode' );
		$status = 1;
		$type = $this->request->getPost ( "type", "string" ) ? $this->request->getPost ( "type", "string" ) : '';
		$format = $this->request->getPost ( "format", "string" ) ? $this->request->getPost ( "format", "string" ) : '';
		$keyword = $this->request->getPost ( "keyword", "string" ) ? $this->request->getPost ( "keyword", "string" ) : '';
		$order = $this->request->getPost ( "order", "string" ) ? $this->request->getPost ( "order", "string" ) : '';
		// 当前城市热映电影查询
		$CinemaFilm = new \CinemaFilm();
		$film = $CinemaFilm->find( array (
				"city_code = $city_code AND date = $date_time",
				"group" => "film_id"
		) );

		// 查询热映影片的详情
		$film_str = '';
		$sql_str = " WHERE f.status=$status";
		foreach($film as $v) {
			$film_str .= ',' . $v->film_id;
		}

		if($film_str) {
			$film_str = substr ( $film_str, 1 );
			$sql_str .= " AND f.id IN ($film_str)";
			if ($this->request->isPost ()) {
				if ($type && $format) {
					$sql_str .= " AND fi.attribute = 'type' AND fi.`data`='$type' AND fi.`film_id` IN
                    (SELECT film_id FROM ys_film_info WHERE attribute = 'format' AND data = '$format' AND film_id IN ($film_str))";
				} elseif ($type) {
					$sql_str .= " AND fi.attribute = 'type' AND fi.`data`='$type'";
				} elseif ($format) {
					$sql_str .= " AND fi.attribute = 'format' AND fi.`data`='$format'";
				}

				if ($keyword) {
					$sql_str .= " AND f.name LIKE '%$keyword%'";
				}
			}
			if ($order && strpos ( $order, "art_date" )) {
				$sql_order = ' ORDER BY f.' . $order;
			} else {
				$sql_order = ' ORDER BY fh.' . $order;
			}
			$sql = "SELECT f.*,fh.cinemas,fh.attention,fh.audience,fh.score,fh.screen FROM ys_film f
            LEFT JOIN ys_film_heat fh ON f.id=fh.film_id
            LEFT JOIN ys_film_info fi ON f.id=fi.film_id" . $sql_str . " GROUP BY f.id" . $sql_order;

			$film_info = \BalanceDb::instance()->fetchAll ( $sql );
		}

		if ($film_info) {
			// 查询影片的类型
			$film_info = $this->getType ( $film_info, $film_str );
		} else {
			$film_info = null;
		}
		echo json_encode($film_info);
		exit;
	}

	/**
	 * 即将上映电影展示ajax
	 */
	public function upcomingAjaxAction() {
		$status = 0;
		$type = $this->request->getPost ( "type", "string" ) ? $this->request->getPost ( "type", "string" ) : '';
		$format = $this->request->getPost ( "format", "string" ) ? $this->request->getPost ( "format", "string" ) : '';
		$keyword = $this->request->getPost ( "keyword", "string" ) ? $this->request->getPost ( "keyword", "string" ) : '';
		$order = $this->request->getPost ( "order", "string" ) ? $this->request->getPost ( "order", "string" ) : '';
		$sql_str = " WHERE f.status=$status";

		if ($this->request->isPost ()) {
			if ($type && $format) {
				$sql_str .= " AND fi.attribute = 'type' AND fi.`data`='$type' AND fi.`film_id` IN
                    (SELECT film_id FROM ys_film_info WHERE attribute = 'format' AND data = '$format')";
			} elseif ($type) {
				$sql_str .= " AND fi.attribute = 'type' AND fi.`data`='$type'";
			} elseif ($format) {
				$sql_str .= " AND fi.attribute = 'format' AND fi.`data`='$format'";
			}

			if ($keyword) {
				$sql_str .= " AND f.name LIKE '%$keyword%'";
			}
		}
		if ($order && strpos ( $order, "art_date" )) {
			$sql_order = ' ORDER BY f.' . $order;
		} else {
			$sql_order = ' ORDER BY fh.' . $order;
		}
		$sql = "SELECT f.*,fh.cinemas,fh.attention,fh.audience,fh.score,fh.screen FROM ys_film f
            LEFT JOIN ys_film_heat fh ON f.id=fh.film_id
            LEFT JOIN ys_film_info fi ON f.id=fi.film_id" . $sql_str . " GROUP BY f.id" . $sql_order;

		$film_info = \BalanceDb::instance()->fetchAll ( $sql );
		if ($film_info) {
			// 查询影片的类型
			$film_info = $this->getType ( $film_info );
		} else {
			$film_info = null;
		}
		die ( json_encode ( $film_info ) );
	}

	/**
	 * 获取电影类型
	 */
	public function getType($data, $id_str = "") {
		$result_arr = array ();
		if ($id_str) {
			$type_info = \FilmInfo::find ( "film_id IN ($id_str)" );
		} else {
			$type_info = \FilmInfo::find ();
		}
		if ($type_info) {
			foreach ( $type_info as $vtype ) {
				if (isset ( $result_arr [$vtype->film_id] [$vtype->attribute] ) && ! empty ( $result_arr [$vtype->film_id] [$vtype->attribute] )) {
					$result_arr [$vtype->film_id] [$vtype->attribute] .= ' ' . $vtype->data;
				} else {
					$result_arr [$vtype->film_id] [$vtype->attribute] = $vtype->data;
				}
			}
		}

		foreach ( $data as &$v ) {
			if (isset ( $result_arr [$v ['id']] ['type'] ) && ! empty ( $result_arr [$v ['id']] ['type'] )) {
				$v ['type'] = $result_arr [$v ['id']] ['type'];
			}
			if (isset ( $result_arr [$v ['id']] ['format'] ) && ! empty ( $result_arr [$v ['id']] ['format'] )) {
				$v ['format'] = $result_arr [$v ['id']] ['format'];
			}
		}

		return $data;
	}

	/**
	 * 影院某天电影列表ajax
	 */
	public function getDateFilmAjaxAction() {
		$status     = 1;
		$date       = $this->request->getPost("date", "string")?$this->request->getPost("date", "string"):'';
		$cinema_id  = $this->request->getPost("cinema_id", "string")?$this->request->getPost("cinema_id", "string"):'';

		//当前影院当天上映电影查询
		$CinemaFilm = new \CinemaFilm();
		$film = $CinemaFilm->find(array( "cinema_id = $cinema_id AND date = $date"));


		//查询热映影片的详情
		$film_str = '';
		$sql_str = " WHERE f.status=$status";
		foreach($film as $v){
			$film_str .= ','.$v->film_id;
		}

		if($film_str){
			$film_str = substr($film_str,1);
			$sql_str .= " AND f.id IN ($film_str)";
			$sql = "SELECT f.*,fh.cinemas,fh.attention,fh.audience,fh.score,fh.screen FROM ys_film f
            LEFT JOIN ys_film_heat fh ON f.id=fh.film_id
            LEFT JOIN ys_film_info fi ON f.id=fi.film_id".$sql_str." GROUP BY f.id";
			$film_info = \BalanceDb::instance()->fetchAll($sql);
		}

		if(isset($film_info)&&!empty($film_info)){
			//查询影片的类型
			$film_info = $this->getType($film_info,$film_str);
		}else{
			$film_info = null;
		}


		echo json_encode($film_info);
		exit;
	}

	/**
	 * 电影的排期信息
	 */
	public function getFilmListAjaxAction()
	{
		$status     = 1;
		$date       = $this->request->getPost("date", "string")?$this->request->getPost("date", "string"):'';
		$cinema_id  = $this->request->getPost("cinema_id", "string")?$this->request->getPost("cinema_id", "string"):'';
		$film_id  	= $this->request->getPost("film_id", "string")?$this->request->getPost("film_id", "string"):'';

		//当前影院当天上映电影查询FilmSchedule
		$film = \FilmSchedule::find(array("cinema_id = $cinema_id AND film_id = $film_id AND date = $date"))->toArray();
		foreach($film as $k=>&$v){
			$v['cinemaScreen'] = \CinemaScreen::findFirst("id = ".$v['screen_id'])->toArray();
		}
		die(json_encode($film));
	}

	/**
	 * 订单中心_影片选座
	 */
	public function buyAction()
	{
		$userInfo = $this->session->get('userInfo');
		//验证会员是否已登录
		if (empty($userInfo)) {
			$url = $this->url->get('user/login');
			header("Location:$url");
			exit;
		}
		$cinema_film = $this->request->getQuery('id');
		//影片排期信息
		$Objs = new \FilmSchedule();
		$Datas = $Objs->find(array( "id = $cinema_film"));
		$DatasArrays_FilmSchedule = $Datas->toArray();
		//影院信息
		$Objs = new \Cinema();
		$Datas = $Objs->find(array( "id = ".$DatasArrays_FilmSchedule[0]["cinema_id"]));
		$DatasArrays_Cinema = $Datas->toArray();
		//影厅信息
		$Objs = new \CinemaScreen();
		$Datas = $Objs->find(array( "id = ".$DatasArrays_FilmSchedule[0]["screen_id"]));
		$DatasArrays_CinemaScreen = $Datas->toArray();
		//影片信息
		$Objs = new \Film();
		$Datas = $Objs->find(array( "id = ".$DatasArrays_FilmSchedule[0]["film_id"]));
		$DatasArrays_Film = $Datas->toArray();

		//场次信息
		$Arrays_Temps = explode(" ",$DatasArrays_FilmSchedule[0]["starttime"]);
		$Arrays_Weeks = array("日","一","二","三","四","五","六");
		$Arrays_Nums = date("w",strtotime($DatasArrays_FilmSchedule[0]["starttime"]));
		$FilmSchedule_starttime = $Arrays_Temps[0]." 星期".$Arrays_Weeks[(int)$Arrays_Nums]." ".substr($Arrays_Temps[1],0,5);

		//传参至页面
		$this->view->setVar ( "Cinema_id", $DatasArrays_Cinema[0]["id"] );
		$this->view->setVar ( "Cinema_name", $DatasArrays_Cinema[0]["name"] );
		$this->view->setVar ( "CinemaScreen_name", $DatasArrays_CinemaScreen[0]["screen_name"] );
		$this->view->setVar ( "Film_name", $DatasArrays_Film[0]["name"] );
		$this->view->setVar ( "Film_types", $DatasArrays_Film[0]["types"] );
		$this->view->setVar ( "Film_formats", $DatasArrays_Film[0]["formats"] );
		$this->view->setVar ( "FilmSchedule_starttime", $FilmSchedule_starttime );
		$this->view->setVar ( "FilmSchedule_standard_price", $DatasArrays_FilmSchedule[0]["standard_price"]/100 );
		$this->view->setVar ( "FilmSchedule_screenid", $DatasArrays_FilmSchedule[0]["screen_id"] );
		$this->view->setVar ( "FilmSchedule_id", $DatasArrays_FilmSchedule[0]["id"] );
		$this->view->setVar ( "phone", $userInfo["phone"] );
		$this->view->pick ( 'film/buySeat' );
	}

	/**
	 * 订单中心_影片选座_JSON
	 */
	public function buyseatjsonAjaxAction() {
		$RetsArrays = array();
		$Arrays 	= array();
		$RetsDatas["cinemaid"] = $this->request->getQuery('cinemaid');
		$RetsDatas["screenid"] = $this->request->getQuery('screenid');
		//影厅信息
		$Objs = new \CinemaScreen();
		$Datas = $Objs->find(array( "id = ".$RetsDatas["screenid"]));
		$DatasArrays_CinemaScreen = $Datas->toArray();
		//座位信息
		$Objs = new \CinemaScreenSeat();
		$Datas = $Objs->find(array( "cinema_id=".$RetsDatas["cinemaid"]." and screen_id=".$RetsDatas["screenid"]." order by x_coord,y_coord"));
		$DatasArrays_CinemaScreenSeat = $Datas->toArray();

		if(isset($DatasArrays_CinemaScreen[0])){
			//座位矩阵
			$Arrays["cols"] = (int)$DatasArrays_CinemaScreen[0]["x"];
			$Arrays["rows"] = (int)$DatasArrays_CinemaScreen[0]["y"];
			//座位组
			foreach($DatasArrays_CinemaScreenSeat as $key=>$val){
				if($val["status"]==0){
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["columnId"] = "";
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["seatNo"] = "";
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["st"] = "E";
				}else if($val["status"]==1){
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["columnId"] = "".($val["y_coord"]+1);
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["seatNo"] = ($val["x_coord"]+1).($val["y_coord"]+1);
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["st"] = "N";
				}else if($val["status"]==2){
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["columnId"] = "".($val["y_coord"]+1);
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["seatNo"] = ($val["x_coord"]+1).($val["y_coord"]+1);
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["st"] = "LO";
				}else if($val["status"]==3){
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["columnId"] = "".($val["y_coord"]+1);
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["seatNo"] = ($val["x_coord"]+1).($val["y_coord"]+1);
					$Arrays["seats"][$val["x_coord"]]["columns"][$val["y_coord"]]["st"] = "LK";
				}
				$Arrays["seats"][$val["x_coord"]]["rowId"] = "".($val["x_coord"]+1);
				$Arrays["seats"][$val["x_coord"]]["rowNum"] = (int)($val["x_coord"]+1);
			}
			$RetsArrays = $Arrays;
		}
		echo json_encode($RetsArrays);exit;
	}

	/**
	 * 订单中心_影片下单
	 */
	public function ordersAction() {
		$schedule_id 	= $this->request->getPost('filmschedule','string')?$this->request->getPost('filmschedule','string'):'';
		$phone 			= $this->request->getPost('phone','string')?$this->request->getPost('phone','string'):'';
		$captcha		= $this->request->getPost('captcha','string')?$this->request->getPost('captcha','string'):'';
		$seats			= $this->request->getPost('seats')?$this->request->getPost('seats'):'';
		if($this->request->isPost()){
			if(!$schedule_id || !$phone || !$captcha || !$seats){
				die('参数错误');
			}
			$order = array(
				'schedule_id'	=>$schedule_id,
				'phone'			=>$phone,
				'captcha'		=>$captcha,
				'seats'			=>$seats,
			);
		}else{
			if(!$order = $this->session->get('order')){
				$url = $this->url->get('/');
				header("Location:$url");
			}
		}
		if(!$uid = $this->session->get('uid')){
			$this->session->set('order',$order);
			$url = $this->url->get('user/login');
			header("Location:$url");
		}
		$schedule = FilmSchedule::findfirst("id =".$order['schedule_id'])->toArray();
		if(empty($schedule)){
			die('放映场次出错，请重新购买或者联系客服');
		}

		$film 	= \Film::findFirst("id =".$schedule['film_id'])->toArray();
		$cinema = \Cinema::findFirst("id =".$schedule['cinema_id'])->toArray();
		$screen	= \CinemaScreen::findFirst("id =".$schedule['screen_id']." AND cinema_id = ".$schedule['cinema_id'])->toArray();
		$order['seat'] = '';
		$order['note'] = '';
		foreach($order['seats'] as $key=>&$val){
			$v_arr = explode('_',$val);
			if($v_arr[0]<10){
				$val ='0'.$v_arr[0];
			}else{
				$val =$v_arr[0];
			}
			if($v_arr[1]<10){
				$val .='0'.$v_arr[1];
			}else{
				$val .=$v_arr[1];
			}
			if($key == 0){
				$order['seat'] .= $val;
				$order['note'] .= $v_arr[0].'排'.$v_arr[1].'座';
			}else{
				$order['seat'] .= ','.$val;
				$order['note'] .= ','.$v_arr[0].'排'.$v_arr[1].'座';
			}
		}
		//组装参数
		$userInfo = $this->session->get('userInfo');
		$order['order_code'] 	= date('Ymd').$order['schedule_id'].$order['seats'][0];
		$order['uid'] 			= $uid;
		$order['username'] 		= $userInfo['username'];
		$order['film_id'] 		= $schedule['film_id'];
		$order['film_name'] 	= $film['name'];
		$order['cinema_id'] 	= $schedule['cinema_id'];
		$order['cinema_name'] 	= $cinema['name'];
		$order['screen_id'] 	= $order['schedule_id'];
		$order['screen_name'] 	= $screen['screen_name'];
		$order['num'] 			= count($order['seats']);
		$order['price_total'] 	= $schedule['price_real']*$order['num'];
		$order['pay_id'] 		= 0;
		$order['status'] 		= 0;
		$order['ip'] 			= !empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'';
		$order['ctime'] 		= time();
		$order['utime'] 		= time();

		unset($order['schedule_id']);
		unset($order['seats']);

		//查询会员信息
		$userAccount = \UserAccount::findFirst("uid=$uid")->toArray();

		$Order = new \Order();
		//保存订单
		if($new_order = $Order->findFirst("order_code = '".$order['order_code']."'")){
			if($order['status'] == 0){
				$this->session->remove('order');
				$this->view->setVar('film', $film);
				$this->view->setVar('order', $new_order->toArray());
				$this->view->setVar('schedule', $schedule);
				$this->view->setVar('userAccount', $userAccount);
			}
		} else {
			if($Order->save($order)){
				//删除session购物车
				$this->session->remove('order');
				$this->view->setVar('film', $film);
				$this->view->setVar('order', $order);
				$this->view->setVar('schedule', $schedule);
				$this->view->setVar('userAccount', $userAccount);
			} else {
				foreach($Order->getMessages() as $message) {
					error_log('订单保存失败========'.$message);
				}
				die('生成订单失败，请重新购买或者联系客服');
			}
		}
	}

	/**
	 * 排期页面
	 */
	public function detailAction() {
		$filmId = $this->request->getQuery('id');
		$date = $this->request->getQuery('date');
		$date = empty($date) ? date('Ymd') : $date;
		$cinema_id = $this->request->getQuery('cid');
		$regions=$this->request->getQuery('reg');
		$regarr=null;
		if($regions){
		    $regarr= array('county_code'=>$regions);
		}else{
		  $regarr= array();
		}

		$mSchedule = new \FilmSchedule();
		$film = \BFilm::instance()->getFilm($filmId);
		if(!$film) exit('影片不存在');
		if(empty($cinema_id)) {
			$cinemaIds = \BFilm::instance()->getCinemaIdsByFilmIdAndDateAndRegion($filmId, $date);
		} else {
		    $cinemaIds=array($cinema_id);
		}
		$cinemas = \BFilm::instance()->getCinemasByIds($cinemaIds);

		$cinemaInfos = array();
		foreach($cinemas as $cinema) {
			$cinemaId = $cinema['id'];
			$schedules = \BFilm::instance()->getScheduleByFilmidAndCinemaidAndDateAndRegion($cinemaId, $filmId, $date,$regarr);
			$cinema['schedule'] = $schedules;
			if(!empty($regarr['county_code'])){
                if($cinema['county_code']==$regarr['county_code']){

                    $cinemaInfos[$cinemaId] = $cinema;
                }

			}else{
			    $cinemaInfos[$cinemaId] = $cinema;
			}

		}

		$this->view->setVar	("title", '《'.$film['name'].'》 上映时间');
		$this->view->setVar('reg', $regions);
		$this->view->setVar('date', $date);
		$this->view->setVar('film', $film);
		$this->view->setVar('cinemaInfos', $cinemaInfos);
	}

}