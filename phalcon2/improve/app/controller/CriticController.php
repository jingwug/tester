<?php
namespace Module\Frontend\Controller;

class CriticController extends ControllerBase
{
	public function initialize() {
		parent::initialize ();
	}

	/**
	 * 评论首页
	 */
	public function indexAction() {
	    $page = $this->request->getQuery('page')?$this->request->getQuery('page'):1;
	    $orderBy = $this->request->getQuery('sort')!='comment'?'a.ctime':'comment';
	    $pagesize = 6;
	    $filmComment = \News::instance()->getFilmCritic($page, $pagesize,$orderBy);

	    $total = \News::instance()->countFilmComment();
	    $this->view->setVar	("orderby", $orderBy);
	    $this->view->setVar	("title", '资讯');
	    $this->view->setVar('FilmCommens', $filmComment);
	    $this->view->setVar('paginator', \TPaginator::factory($total, $pagesize));
	}

	public function detailAction() {
	    $page = $this->request->getQuery('page')?$this->request->getQuery('page'):1;
	    $id = $this->request->getQuery('id');
	    $orderBy = $this->request->getQuery('sort');
        $content= \News::instance()->getNewsCommentContent($id);
        $pagesize = 10;
         $commtents = \News::instance()->getComment($id,$page,$pagesize);
         $total = \News::instance()->countComment($id);

        $this->view->setVar('content', $content);
        $this->view->setVar('commtents', $commtents);
        $this->view->setVar('paginator', \TPaginator::factory($total, $pagesize));
	}

}
