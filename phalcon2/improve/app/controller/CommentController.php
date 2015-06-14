<?php
namespace Module\Frontend\Controller;

class CommentController extends ControllerBase
{
	public function initialize() {
		parent::initialize ();
	}

	/**
	 * 评论首页
	 */
	public function indexAction() {
	}

	public function detailsAction(){

	}

	public function ajaxaddcommentAction(){
		
	    if(empty($this->session->get('uid'))){
	    	$return['code']='-1';
	    	$return['msg']='Login';
	    	
	        echo json_encode($return);
	        exit;
	    }
  

		$return=array();
	    $commentValue       = $this->request->getPost("commentValue", "string")?$this->request->getPost("commentValue", "string"):'';
	    $newsid  = $this->request->getPost("nid", "string")?$this->request->getPost("nid", "string"):'';
        $userid=$this->session->get('uid');
       
        $newdata=array();
        $newdata['author_id']=$userid;
        $newdata['master_id']=$newsid;
        $newdata['master_type']='comment';
        $newdata['ext_key']='filmcomment';
        $newdata['up']=0;
        $newdata['down']=0;
        $newdata['content']=$commentValue;
     
        $newdata['ctime']=time();
        $db = \BalanceDb::instance();
        $res= $db->insert('ys_news',$newdata);
		if($res>0){
			$return['code']='1';
			$return['msg']='success';
		}
         echo json_encode($return);
         exit;

	}
	
	public function ajaxzanAction(){
		

		if(empty($this->session->get('uid'))){
			$return['code']='-1';
			$return['msg']='Login';
		
			echo json_encode($return);
			exit;
		}
		
		
		$nid       = $this->request->getPost("nid", "string")?$this->request->getPost("nid", "string"):'';
		$type       = $this->request->getPost("type", "string")?$this->request->getPost("type", "string"):'';
		if($type==1){
		 $zanarr=	$this->session->get('zanarr');
		 if(empty($zanarr)){
		 	$this->session->set('zanarr',array($nid));
		 }else{
		 	
		 	if(in_array($nid,$zanarr)){
		 		echo json_encode(array('code'=>2,'msg'=>'您已经赞过了'));exit;
		 		
		 	}else{
		 		array_push($zanarr, $nid);
		 		$this->session->set('zanarr',$zanarr);
		 	}
		 }
			
		}
		
		
		
		if($type==2){
			$caiarr=	$this->session->get('caiarr');
			if(empty($caiarr)){
				$this->session->set('caiarr',array($nid));
			}else{
		
				if(in_array($nid,$caiarr)){
					echo json_encode(array('code'=>2,'msg'=>'您已经踩过了'));exit;
					 
				}else{
					array_push($caiarr, $nid);
					$this->session->set('caiarr',$caiarr);
				}
			}
				
		}
		
	
		$res=  \News::instance()->updatenewUpDown($type, $nid);
		$resarr=array();
		if($res>0){$resarr['code']=1;
		
		
		}else{$resarr['code']=0;};
		echo  json_encode($resarr);
		exit;
	}

}
