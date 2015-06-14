<?php
namespace Module\Frontend\Controller;

class IndexController extends ControllerBase {

	public function initialize() {
		parent::initialize();
		$this->view->setMainView('index');
	}

	public function indexAction() {
	   $parent_id=	$this->session->get('city_parent_id');

	    $regionlist = \Region::find('parent_id='.$parent_id);
	    if(!empty($regionlist)){
	        $this->view->setVar ( 'reglist', $regionlist->toArray() );

	    }else{
	        $this->view->setVar ( 'reglist', array() );
	    }

	}

	public function getcinemaAction(){

	    $regcode=$_POST['code'];
	    $list= \Cinema::find('county_code='.$regcode)->toArray();
	    $returnHtml='';

	    if(!empty($list)){

	     foreach ($list as $item){
	            $returnHtml.='<a href="javascript:;" class=c_'.$item['id'].' onclick="select_cinema(\''.$item['id'].'\')" code="'.$item['id'].'" >'.$item['name'].'</a>';

	        }

	    }else{
	        $returnHtml='<span>没有找到相关影院</span>';
	    }

	    echo $returnHtml;
	    exit();

	}

  public function getfilmAction(){
      $cinemacode=$_POST['code'];
      $date=$_POST['date'];
      $list= \FilmSchedule::find("date=".$date."and cinema_id='".$cinemacode."'")->toArray();

  $returnHtml='';
	    if(!empty($list)){

	      foreach ($list as $item){
	            $cinema= \Film::findFirst('code="'.$item['film_code'].'"');
	            $returnHtml.='<a href="javascript:;" class=c_'.$cinema->id.' onclick="select_film(\''.$cinema->id.'\')" code="'.$item['film_code'].'" >'.$cinema->name.'</a>';
                unset($cinema);
	        }

	    }else{
	        $returnHtml='<span>没有找到相关影片</span>';
	    }

	    echo $returnHtml;
	    exit();


  }

	public function testerAction() {
		//Disable several levels
//		$this->view->disableLevel(array(
//			Phalcon\Mvc\View::LEVEL_LAYOUT      => true,
//			Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT => true
//		));
//		$this->view->pick('index/tester');
	}

}
