<?php
namespace Module\Frontend\Controller;

class OrderController extends ControllerBase
{
	public function initialize()
	{
		parent::initialize ();
		$this->tag->setTitle('影片选座');
	}



	/**
	 * 订单中心_影片选座
	 */
	public function buyAction()
	{
		$userInfo = $this->session->get('userInfo');

		$cinema_film = $this->request->get('id');
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
		$FilmSchedule_starttime = date('n月j日',time($Arrays_Temps[0]))." 星期".$Arrays_Weeks[(int)$Arrays_Nums]." ".substr($Arrays_Temps[1],0,5);

		//传参至页面
		$this->view->setVar	( "title", '影片选座');
		$this->view->setVar ( "Cinema_id", $DatasArrays_Cinema[0]["id"] );
		$this->view->setVar ( "Cinema_name", $DatasArrays_Cinema[0]["name"] );
		$this->view->setVar ( "CinemaScreen_name", $DatasArrays_CinemaScreen[0]["screen_name"] );
		$this->view->setVar ( "Film_name", $DatasArrays_Film[0]["name"] );
		$this->view->setVar ( "Film_image", $DatasArrays_Film[0]["image"] );
		$this->view->setVar ( "Film_types", $DatasArrays_Film[0]["types"] );
		$this->view->setVar ( "Film_formats", $DatasArrays_Film[0]["formats"] );
		$this->view->setVar ( "FilmSchedule_starttime", $FilmSchedule_starttime );
		$this->view->setVar ( "FilmSchedule_standard_price", $DatasArrays_FilmSchedule[0]["price_real"]/100 );
		$this->view->setVar ( "FilmSchedule_screenid", $DatasArrays_FilmSchedule[0]["id"] );
		$this->view->setVar ( "FilmSchedule_id", $DatasArrays_FilmSchedule[0]["id"] );
		$this->view->setVar ( "phone", $userInfo?$userInfo["phone"]:'');
		$this->view->pick ( 'order/buySeat' );
	}

	/**
	 * 订单中心_影片选座_JSON
	 */
	public function buyseatjsonAjaxAction()
	{
		$RetsArrays = array();
		$Arrays 	= array();
		$RetsDatas["cinemaid"] = $this->request->get('cinemaid');
		$RetsDatas["screenid"] = $this->request->get('screenid');
		//影厅信息
		/* $Objs = new \CinemaScreen();
		$Datas = $Objs->find(array( "id = ".$RetsDatas["screenid"]));
		$DatasArrays_CinemaScreen = $Datas->toArray(); */
		//座位信息
		$Objs = new \FilmScheduleSeat();
		$Datas = $Objs->find(array( " schedule_id=".$RetsDatas["screenid"]));
		$DatasArrays_CinemaScreenSeat = $Datas->toArray();



		if(isset($DatasArrays_CinemaScreenSeat[0])){

		    $screen=\CinemaScreen::findfirst('id='.$DatasArrays_CinemaScreenSeat[0]['screen_id']);
		    if(empty($screen)) exit;

			//座位矩阵
			$Arrays["rows"] = (int)$screen->x;
			$Arrays["cols"] = (int)$screen->y;
			//座位组
			foreach($DatasArrays_CinemaScreenSeat as $key=>$val){

				if($val["status"]=='-1'){
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["columnId"] = "";
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["seatNo"] = "";
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["st"] = "E";
				}else if($val["status"]=='0'){
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["columnId"] = "".$val["y_coord"];
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["seatNo"] = $val["x_coord"].$val["y_coord"];
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["st"] = "N";
				}else if($val["status"]=='3'){
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["columnId"] = "".$val["y_coord"];
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["seatNo"] = $val["x_coord"].$val["y_coord"];
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["st"] = "LO";
				}else if($val["status"]=='2'){
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["columnId"] = "".$val["y_coord"];
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["seatNo"] = $val["x_coord"].$val["y_coord"];
					$Arrays["seats"][$val["x_coord"]-1]["columns"][$val["y_coord"]-1]["st"] = "LK";
				}
				$Arrays["seats"][$val["x_coord"]-1]["rowId"] = "".($val["x_coord"]);
				$Arrays["seats"][$val["x_coord"]-1]["rowNum"] = (int)($val["x_coord"]);
			}
			$RetsArrays = $Arrays;
		}
		echo json_encode($RetsArrays);exit;
	}

	/**
	 * 订单中心_影片下单
	 */
	public function ordersAction()
	{

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
		$schedule = \FilmSchedule::findFirst("id =".$order['schedule_id'])->toArray();
		if(empty($schedule)){
			die('放映场次出错，请重新购买或者联系客服');
		}

		$film 	= \Film::findFirst("id =".$schedule['film_id'])->toArray();
		$cinema = \Cinema::findFirst("id =".$schedule['cinema_id'])->toArray();
		$screen	= \CinemaScreen::findFirst("id =".$schedule['screen_id']." AND cinema_id = ".$schedule['cinema_id'])->toArray();
		$order['seat'] = '';
		$order['note'] = '';
		$sql_arr = array();
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
			$sql_arr[]= "x_coord=$v_arr[0] AND y_coord = $v_arr[1]";
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
		$order['screen_id'] 	= $schedule['screen_id'];
		$order['screen_name'] 	= $screen['screen_name'];
		$order['num'] 			= count($order['seats']);
		$order['price_total'] 	= $schedule['price_real']*$order['num'];
		$order['pay_id'] 		= 0;
		$order['status'] 		= 0;
		$order['ip'] 			= !empty($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'';
		$order['ctime'] 		= time();
		$order['utime'] 		= time();

		unset($order['seats']);
		unset($order['captcha']);

		//查询会员信息
		$userAccount = \UserAccount::findFirst("uid=$uid")?\UserAccount::findFirst("uid=$uid")->toArray():'';

		$Order = new \Order();
		//保存订单
		if($new_order = $Order->findFirst("order_code = '".$order['order_code']."' AND status =0")){
			if($order['status'] == 0){
				$this->session->remove('order');
				$this->view->setVar('film', $film);
				$this->view->setVar('order', $new_order->toArray());
				$this->view->setVar('schedule', $schedule);
				$this->view->setVar('userAccount', $userAccount);
				$this->view->setVar	("title", '菜苗影视订单');

//				echo('OK');
			}else{
				echo('找不到订单信息');
			}
		}else{
			$db = \BalanceDb::instance();
			try{
				$db->begin();
				foreach($sql_arr as $val){
					$db->execute("UPDATE `ys_cinema_screen_seat` SET status = 3 WHERE screen_id = ".$order['screen_id']." AND ".$val );
				}
				$db->insert("ys_order",$order,'iisssisisisisiiiisii');
				$db->commit();
				$order = \Order::findFirst("order_code = '".$order['order_code']."' AND status =0")->toArray();
				$this->session->remove('order');
				$this->view->setVar('film', $film);
				$this->view->setVar('order', $order);
				$this->view->setVar('schedule', $schedule);
				$this->view->setVar('userAccount', $userAccount);
				$this->view->setVar	("title", '菜苗影视订单');

			}catch (\Exception $e){
				$db->rollback();
				error_log('订单保存失败========'.$e->getMessage());
				die('生成订单失败，请重新购买或者联系客服');
			}

		}

	}

	/**
	 * 订单中心_查看订单
	 */
	public function orderInfoAction()
	{

		$id = isset($_GET['id'])?$_GET['id']:'';
		if(!is_numeric($id) || $id<1){
			die('参数错误');
		}
		$order = \Order::findFirst("id =".$id)->toArray();
		if(empty($order)){
			die('找不到订单信息，请联系客服');
		}
		$schedule = \FilmSchedule::findFirst("id =".$order['schedule_id'])->toArray();
		if(empty($schedule)){
			die('放映场次出错，请重新购买或者联系客服');
		}

		$film 	= \Film::findFirst("id =".$schedule['film_id'])->toArray();
//		$cinema = Cinema::findFirst("id =".$schedule['cinema_id'])->toArray();
//		$screen	= CinemaScreen::findFirst("id =".$schedule['screen_id']." AND cinema_id = ".$schedule['cinema_id'])->toArray();

		//查询会员信息
		$userAccount = \UserAccount::findFirst("uid=".$order['uid'])?\UserAccount::findFirst("uid=".$order['uid'])->toArray():'';

		if($order['status'] == 0){
			$this->session->remove('order');
			$this->view->setVar('film', $film);
			$this->view->setVar('order', $order);
			$this->view->setVar('schedule', $schedule);
			$this->view->setVar('userAccount', $userAccount);
			$this->view->setVar	("title", '菜苗影视订单');
			$this->view->pick('order/orders');

		}else{
			echo('找不到订单信息');
		}

	}


	/**
	 * 订单中心_支付ajax
	 */
	public function payAjaxAction()
	{
		$order_code = $this->request->getPost('order_code','string')?$this->request->getPost('order_code','string'):'';
		$paytype = $this->request->getPost('paytype','string')?$this->request->getPost('paytype','string'):1;
		if(empty($order_code)){
			\ToolFlash::error('找不到订单信息，请重试');
		}
		//订单信息
		$order = \Order::findFirst("order_code = '".$order_code."'")->toArray();
		if($order['status'] == -1 || (time()-$order['ctime']) >900){
			\ToolFlash::error('订单已过期或作废，请重新下单');
		}

		//会员信息
		if(!$uid = $this->session->get('uid')){
			$this->session->set('order',$order);
			$url = $this->url->get('user/login');
			header("Location:$url");
		}
		//会员余额
		$userAccount =\UserAccount::findFirst("uid = $uid")?\UserAccount::findFirst("uid = $uid")->toArray():'';
		if($paytype == 1){	//余额
			if($userAccount['account'] >= $order['price_total']){
				$expend = $order['price_total'];
				$account_log = array();
				$account_log['uid'] 	= $uid;
				$account_log['type'] 	= 2;
				$account_log['amount'] 	= $expend;
				$account_log['note'] 	= '{"film":"'.$order['film_name'].'","cinema":"'.$order['cinema_name'].'"}';
				$account_log['ctime'] 	= time();
				$db = \BalanceDb::instance();
				try {
					$db->begin();
					$db->execute("UPDATE `ys_order` SET status = 1 WHERE order_code = '$order_code'");
					$db->execute("UPDATE `ys_user_account` SET account = account-$expend,expense=expense+$expend WHERE uid = $uid");
					$db->insert("ys_user_account_log",$account_log,'iiisi');
					$db->commit();
					\ToolFlash::success('支付成功','/user/index');

				} catch (\Exception $e) {
					$db->rollback();
					error_log('支付订单失败，订单编号'.$order_code."====".$e->getMessage());
					\ToolFlash::error('支付失败，请联系客服。'.$e->getMessage());
				}
			}else{
				\ToolFlash::error('余额不足，请先充值，');
			}
		}else{		//在线支付
			$onlineType = $this->request->getPost('onlineType','string')?$this->request->getPost('onlineType','string'):1;
			if(empty($onlineType)){
				\ToolFlash::error('请选择正确的支付平台');
			}
			$classname = $onlineType;
			$payobj = new $classname();
			$result = $payobj->ToSubmit($order);
			$this->session->set('pay_info',$result);
			\ToolFlash::success('正在跳转到支付平台','/pay/'.$onlineType);
			error_log('$result======='.print_r($result,1));
		}
	}

}
