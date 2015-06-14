<?php
namespace Module\Frontend\Controller;

/***************************************
*$File: app/controllers/ImageController.php
*$Description:
*$Author: lideqiang
*$Time:  2015/06/05
****************************************/
class ImageController extends ControllerBase
{
    public function initialize() {
        parent::initialize();
    }

    /*
     * 会员中心页面
     */
    public function imageAction(){
        $operateId = $this->request->get('operateId');
        \Captcha::install()->image($operateId);
    }

    /*
     * 刷新验证码
     */
    public function refreshAction() {
        $captcha = \Captcha::install()->generate();
        $this->session->set('captachaID',$captcha['operateId']);
        $this->session->set('code',$captcha['captcha']);
        die(json_encode('/image/image?operateId='.$captcha['operateId']));
    }

}