<?

class baopay implements pay
{
	private $name 			= '宝付支付';
	private $description 	= '宝付支付';
	private $type 			= 1; 			//1->只能启动;2->可以添加
	private $logo 			= 'baopay'; 	//对应具体的接口图标名称，图片具体位置视网站而定
	private $version 		= '20140709';
	private $MemberID 		= '100000178';	//商户号
	private $TerminalID 	= '10000001';	//终端号
	private $Md5Key 		= 'abcdefg';	//商户秘钥

	//订单支付提交
	public function ToSubmit($data){
		error_log(print_r($data,1));
		$_MemberID 		= $this->MemberID; //商户号
		$_TerminalID 	= $this->TerminalID; //终端号
		$_Md5Key		= $this->Md5Key; //商户秘钥

		$_InterfaceVersion = '4.0'; //接口版本
		$_KeyType		= '1'; //数据加密类型，1代码MD5加密
		$_PayID			= ''; //支付银行
		// error_log($_PayID);
		$_TradeDate		= date('YmdHis'); //订单生成日期
		$_TransID		= $data['order_code']; //订单编码：order_code该名称视传入的$data而定
		$_OrderMoney	= $data['price_total']; //订单金额
		$_ProductName	= urldecode($data['film_name']); //商品名称：subject该值视传入的$data而定
		$_Amount		=  $data['num']; //商品数量
		$_Username		= $data['username']; //支付用户名
		$_AdditionalInfo= $data['note']; //订单附加信息
		$_PageUrl		= 'http://test.caimiao.com/dyweb/dyusers/diyou/account/baopay_recharge_return.php';//页面通知重定向地址,该值视网站地址而定
		$_ReturnUrl		= 'http://test.caimiao.com/dyweb/dyusers/diyou/account/baopay_recharge_notify.php'; //后台异步通知重定向地址，该值视网站地址而定
		$_NoticeType	= 1; //通知方式：1代表服务器和页面通知;0代表服务器通知

		$MARK = '|';
		$_Md5Sign		= md5($_MemberID.$MARK.$_PayID.$MARK.$_TradeDate.$MARK.$_TransID.$MARK.$_OrderMoney.$MARK.$_PageUrl.$MARK.$_ReturnUrl.$MARK.$_NoticeType.$MARK.$_Md5Key);

//		 $submitUrl		= "http://gw.baofoo.com/payindex";//@cm_hl_pay@
//		 $submitUrl		= "http://tgw.baofoo.com/payindex";
		 $submitUrl		= "http://vgw.baofoo.com/payindex";
//		$submitUrl 		=CM_SUBMIT_URL;
		$agentfield['postmethod']	= 'post';
		$agentfield['submitUrl']	= $submitUrl;
		$agentfield['MemberID']		= $_MemberID;
		$agentfield['TerminalID']	= $_TerminalID;
		$agentfield['InterfaceVersion']	= $_InterfaceVersion;
		$agentfield['KeyType']		= $_KeyType;
		$agentfield['PayId']		= $_PayID;
		$agentfield['TradeDate']	= $_TradeDate;
		$agentfield['TransID']		= $_TransID;
		$agentfield['OrderMoney']	= $_OrderMoney;
		$agentfield['ProductName']	= $_ProductName;
		$agentfield['Amount']		= $_Amount;
		$agentfield['Username']	= $_Username;
		$agentfield['AdditonalInfo']      = $_AdditionalInfo;
		$agentfield['PageUrl']		= $_PageUrl;
		$agentfield['ReturnUrl']	= $_ReturnUrl;
		$agentfield['Signature']	= $_Md5Sign;
		$agentfield['NoticeType']	= $_NoticeType;

		$formData = self::applyForm($agentfield);
		return array('url'=>$formData);
	}

	//
	private function applyForm($agentfield){
		$tmp_form="";
		$tmp_form.="<form name='applyForm' method='".$agentfield['postmethod']."' action='".$agentfield['submitUrl']."'>";

		unset($agentfield['submitUrl']);
		foreach($agentfield as $key => $val){
			$tmp_form.="<input type='hidden' name='".$key."' value='".$val."'>";
		}

		$tmp_form.="</form>";

		$tmp_form.="<script>document.forms['applyForm'].submit();</script>";
		return $tmp_form;
	}

	function GetFields(){
		return array(
			'member_id'=>array(
				'label'=>'商户号:',
				'type'=>'string'
			),
			'PrivateKey'=>array(
				'label'=>'私钥密码:',
				'type'=>'string'
			),
			'terminal_id'=>array(
				'label'=>'终端号:',
				'type'=>'string'
			)
		);
	}
}

?>