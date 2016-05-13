<?php
/**
*
* ��Ȩ���У�ǡά����<qwadmin.qiawei.com>
* ��    �ߣ�����<hanchuan@qiawei.com>
* ��    �ڣ�2016-01-17
* ��    ����1.0.0
* ����˵������̨�ǳ���������
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
class LogoutController extends ComController {
    public function index(){
		cookie('user',null);
		$url = U("login/index");
		header("Location: {$url}");
		exit(0);
    }
}