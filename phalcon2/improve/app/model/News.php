<?php
/***************************************
*$File: app/controllers/Notice.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class News extends ModelBase
{
    private static $instance;

    public function initialize() {
        parent::initialize();
    }

    /**
     * 实例化类，单例模式
     * @return News
     */
    public static function instance() {
        if(self::$instance == NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function updatenewUpDown($type,$nid) {
      if($type==1){
      	$sql = "update ys_news set up=up+1 where id=$nid";
      	 
      }else{
      	$sql = "update ys_news set down=down+1 where id=$nid";
      	 
      }
    	$res = BalanceDb::instance()->execute($sql);
    	return $res;
    }

    /**
     * 资讯
     * @param number $page
     * @param number $pagesize
     * @return unknown
     */
    public function getNews($page = 1, $pagesize = 8) {
        $page = $page > 1 ? $page : 1;
        $offset = $pagesize * ($page - 1);

        $sql = "select * from ys_news where ext_key='news' ORDER BY ctime desc limit :offset,:pagesize";
        return BalanceDb::instance()->fetchAll(
            $sql,
            array('offset' => $offset, 'pagesize' => $pagesize),
            array('offset' => PDO::PARAM_INT, 'pagesize' => PDO::PARAM_INT)
        );
    }

    /**
     * 统计
     */
    public function countNews() {

        $sql = " select  count(1) as total from ys_news where ext_key='news'";
        $row = BalanceDb::instance()->fetchOne($sql);
        return $row ? $row['total'] : 0;
    }

    /**
     * 统计影评
     */
    public function countFilmComment() {

        $sql = " select count(1) as total from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='critic'  ";
        $row = BalanceDb::instance()->fetchOne($sql);
        return $row ? $row['total'] : 0;
    }



    public function getFilmCritic($page = 1, $pagesize = 6,$orderBy) {
        $page = $page > 1 ? $page : 1;
        $offset = $pagesize * ($page - 1);

        $sql = "select a.id as nid,a.*,b.*,u.nickname,u.headimg,u.id as uid,u.username  from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='critic' ORDER BY $orderBy desc limit :offset,:pagesize";
        return BalanceDb::instance()->fetchAll(
            $sql,
            array('offset' => $offset, 'pagesize' => $pagesize),
            array('offset' => PDO::PARAM_INT, 'pagesize' => PDO::PARAM_INT)
        );
    }

    public function getNewsContent($id){
        $sql = "select * from ys_news where ext_key='news'and id=$id";
        $rearr=BalanceDb::instance()->fetchAll($sql)[0];
        $sql_file = "select * from ys_file where master_type='news'and master_id=$id";
          $rearr['scroll']=array();
        $files=BalanceDb::instance()->fetchAll($sql_file);
        foreach ($files as $itm){
             if($itm['ext_key']=='banner'){
                 $rearr['banner']=$itm['path'];
             }else{
                 array_push($rearr['scroll'],$itm['path']);

             }

        }

        return $rearr;

    }


    public function getNewsCommentContent($id){
        $sql = "select a.id as nid,a.*,b.*,u.nickname,u.headimg,u.id as uid from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='critic' and a.id=".$id;
        $rearr=BalanceDb::instance()->fetchOne($sql);


        return $rearr;

    }



    /**
     * 评论统计
     */
    public function countComment($id) {

        $sql = "select count(1) as total from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='comment' and master_id=$id ";
        $row = BalanceDb::instance()->fetchOne($sql);
        return $row ? $row['total'] : 0;
    }


/**
 *  获取评论
 * @param number $page
 * @param number $pagesize
 * @param unknown $orderBy
 */
    public function getComment($id,$page = 1, $pagesize = 6) {
        $page = $page > 1 ? $page : 1;
        $offset = $pagesize * ($page - 1);

        $sql = "select a.id as nid,a.*,b.*,u.nickname,u.headimg,u.id as uid,a.ctime as ctime ,u.username from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='comment' and master_id=$id ORDER BY a.ctime desc limit :offset,:pagesize";
        return BalanceDb::instance()->fetchAll(
            $sql,
            array('offset' => $offset, 'pagesize' => $pagesize),
            array('offset' => PDO::PARAM_INT, 'pagesize' => PDO::PARAM_INT)
        );
    }
    
    public  function getprevue($id){
    	$sql="select * from ys_news where master_type='prevue' and master_id=$id";
    	$plist=array();
    	$list= BalanceDb::instance()->fetchAll($sql);
    	foreach ($list as $pitm){
    		//主
    		array_push($plist, array('image'=>$pitm['image'],'video'=>$pitm['video'],'title'=>$pitm['title']));
    		//查询更多预告
    		$othersql="select * from ys_news_prevue_video where news_id=".$pitm['id'];
    		$otherlist= BalanceDb::instance()->fetchAll($othersql);
    		foreach ($otherlist as $oitm){
    			array_push($plist, array('image'=>$oitm['image'],'video'=>$oitm['video'],'title'=>$oitm['video_title']));
    		}
    		unset($othersql,$othersql);
    		
    	}
    	unset($list);
    
    	return $plist;
    	
    }


}