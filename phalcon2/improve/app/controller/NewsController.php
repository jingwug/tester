<?php
namespace Module\Frontend\Controller;

class NewsController extends ControllerBase
{
	public function initialize()
	{
	    header("Content-type: text/html; charset=utf-8");
		parent::initialize();
	}

	/**
	 * 新片预告
	 */
	public function previewAction()
	{
		$type = $this->request->getQuery('type');
		$page = $this->request->getQuery('page');

		$type = $type != 'heat' ? 'upcomming' : 'heat';

		$heatFilms = array();
		$films = array();
		if($type == 'heat') {
			$pagesize = 6;
			$heatFilms = \BFilm::instance()->getHeatPreviewFilms($page, $pagesize);
			$total = \BFilm::instance()->countHeatPreviewFilms();
		} else {
			$pagesize = 8;
			$films = \BFilm::instance()->getPreviewFilms($page, $pagesize);
			$total = \BFilm::instance()->countPreviewFilms();
		}

		$this->view->setVar	("title", '新片预告');
		$this->view->setVar('type', $type);
		$this->view->setVar('films', $films);
		$this->view->setVar('heatFilms', $heatFilms);
		$this->view->setVar('paginator', \TPaginator::factory($total, $pagesize));
	}



	public function newsAction(){


	    $page = $this->request->getQuery('page')?$this->request->get('page'):1;

	    $pagesize = 6;
	    $news = \News::instance()->getNews($page, $pagesize);
	    $total = \News::instance()->countNews();

	    $this->view->setVar	("title", '资讯');
	    $this->view->setVar('news', $news);
	    $this->view->setVar('paginator', \TPaginator::factory($total, $pagesize));



	}


	public function newsContentAction(){
             $id = $this->request->getQuery('id');
             $newscontent=\News::instance()-> getNewsContent($id);

             $this->view->setVar('content',$newscontent);



	}
	
	public function ajaxgetprevueAction(){
		$pid = $this->request->getPost('id','string')?$this->request->getPost('id','string'):'';
		echo  json_encode( \News::instance()->getprevue($pid));
		exit;
		
	}

}
