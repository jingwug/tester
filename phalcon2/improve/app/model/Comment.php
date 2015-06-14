<?php
/***************************************
*$File: app/controllers/Comment.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class Comment extends ModelBase
{
    public function initialize() {
        parent::initialize();
    }





    /**
     * 评论数量
     */
    public function countFilmComment($id) {

        $sql = " select count(1) as total from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='critic'  ";
        $row = BalanceDb::instance()->fetchOne($sql);
        return $row ? $row['total'] : 0;
    }



    public function getFilmCritic($id,$page = 1, $pagesize = 6) {
        $page = $page > 1 ? $page : 1;
        $offset = $pagesize * ($page - 1);

        $sql = "select a.id as nid,a.*,b.*,u.* from ys_news  as a  LEFT JOIN  ys_film as b on a.master_id=b.id LEFT JOIN ys_user as u on u.id=author_id where a.master_type='critic' ORDER BY $orderBy desc limit :offset,:pagesize";
        return BalanceDb::instance()->fetchAll(
            $sql,
            array('offset' => $offset, 'pagesize' => $pagesize),
            array('offset' => PDO::PARAM_INT, 'pagesize' => PDO::PARAM_INT)
        );
    }

}