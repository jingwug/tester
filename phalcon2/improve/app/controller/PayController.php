<?php
namespace Module\Frontend\Controller;

class PayController extends ControllerBase
{
	public function initialize()
	{
		parent::initialize ();
		$this->tag->setTitle('用户支付');
	}


	/**
	 * 支付中心_支付分类
	 */
	public function indexAction()
	{
//		$pay_info = $this->session->get('pay_info');
//		echo($pay_info['url']);
	}


	/**
	 * 支付中心_宝付支付页面调用
	 */
	public function baopayAction()
	{
		$pay_info = $this->session->get('pay_info');
		echo($pay_info['url']);
		die();
	}

	/**
	 * 支付中心_宝付支付回调用
	 */
	public function baopayReturnAction()
	{
		if(isset($_REQUEST['Md5Sign']) && ($_REQUEST['Md5Sign']!="") && isset($_REQUEST['Result']) && ($_REQUEST['Result']!="")){

			$MemberID	= $_REQUEST['MemberID']; //商户号
			$TerminalID	= $_REQUEST['TerminalID']; //商户终端号
			$TransID	= $_REQUEST['TransID']; //商户流水号
			$Result		= $_REQUEST['Result']; //支付结果
			$ResultDesc	= $_REQUEST['ResultDesc']; //支付结果描述
			$FactMoney	= $_REQUEST['FactMoney']; //实际成交金额
			$AdditionalInfo	= $_REQUEST['AdditionalInfo'];
			$SuccTime	= $_REQUEST['SuccTime'];
			$Md5Sign	= $_REQUEST['Md5Sign']; //回传MD5验签字段

			$data['module'] = "account";
			$data['q'] = "get_payment_key";
			$data['method'] = "post";
			$data['nid'] = 'baopay';
			$result_payment = dy_get_server($data);
			$key = $result_payment['PrivateKey'];

			$Md5key		=$key;

			$MARK = "~|~";
			$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$Md5key);

			if($WaitSign == $Md5Sign)
			{
				if($Result==1){		//充值成功
					dy_get_server(array('trade_no'=>$_REQUEST['TransID'],'module'=>'account','q'=>'add_recharge_return','method'=>'post'));
					//在这个位置用户充值成功后进行相应的跳转
					if(empty($_SESSION['pay']['borrow_nid'])){

						echo "<script>alert('充值成功');location.href='/index.php?user&m=account/recharge/recharge_log';</script>";
					}else{
						echo "<script>alert('充值成功');location.href='/invest/a".$_SESSION['pay']['borrow_nid'].".html';</script>";
					}

				}else{
					if(empty($_SESSION['pay']['borrow_nid'])){

						echo "<script>alert('充值失败');location.href='/index.php?user&m=account/recharge/recharge_log';</script>";
					}else{
						echo "<script>alert('充值失败');location.href='/invest/a".$_SESSION['pay']['borrow_nid'].".html';</script>";
					}
				}
			}else{
				if(empty($_SESSION['pay']['borrow_nid'])){

					echo "<script>alert('充值失败');location.href='/index.php?user&m=account/recharge/recharge_log';</script>";
				}else{
					echo "<script>alert('充值失败');location.href='/invest/a".$_SESSION['pay']['borrow_nid'].".html';</script>";
				}
			}
		}

	}

	/**
	 * 支付中心_宝付支付回调用（异步）
	 */
	public function baopayReturnNotifyAction()
	{
		if(isset($_REQUEST['Md5Sign']) && ($_REQUEST['Md5Sign']!="") && isset($_REQUEST['Result']) && ($_REQUEST['Result']!="")){

			$MemberID		= $_REQUEST['MemberID']; //商户号
			$TerminalID		= $_REQUEST['TerminalID']; //商户终端号
			$TransID		= $_REQUEST['TransID']; //商户流水号
			$Result			= $_REQUEST['Result']; //支付结果
			$ResultDesc		= $_REQUEST['ResultDesc']; //支付结果描述
			$FactMoney		= $_REQUEST['FactMoney']; //实际成交金额
			$AdditionalInfo	= $_REQUEST['AdditionalInfo'];
			$SuccTime		= $_REQUEST['SuccTime'];
			$Md5Sign		= $_REQUEST['Md5Sign']; //回传MD5验签字段

			//$Md5key		='abcdefg';
			$data['module'] = "account";
			$data['q'] = "get_payment_key";
			$data['method'] = "post";
			$data['nid'] = 'baopay';
			$result_payment = dy_get_server($data);

			$key = $result_payment['PrivateKey'];

			$Md5key		=$key;

			$MARK = "~|~";
			$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$Md5key);

			if($WaitSign == $Md5Sign)
			{
				if($Result==1){
					dy_get_server(array('trade_no'=>$_REQUEST['TransID'],'module'=>'account','q'=>'add_recharge_return','method'=>'post'));
					echo "OK";
				}else{
					echo "error";
				}
			}else{
				echo "Md5CheckFail";
			}
		}

	}
}
