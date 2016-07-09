<?php
/**
*
* 版权所有：e次元科技<qwadmin.qiawei.com>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：前台公用控制器。
*
**/
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller {
 	public function index() {  
    	header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
        $this->display('Public:404');  
    }  
    public function _empty() {  
        header('HTTP/1.1 404 Not Found');  
        $this->display('Public:404');  
    }  
}
?>