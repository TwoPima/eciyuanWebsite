<?php
/**
*
* 版权所有：e次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-20
* 版    本：1.0.0
* 功能说明：公用控制器。
*
**/

namespace Admin\Controller;

use Common\Controller\BaseController;
use Think\Auth;

class ComController extends BaseController
{
    public $USER;

    public function _initialize()
    {
        C(setting());
        $user = cookie('user');
        $this->USER = $user;
        $url = U("login/index");
        if (!$user) {
            header("Location: {$url}");
            exit(0);
        }
        $m = M();
        $prefix = C('DB_PREFIX');
        $UID = $this->USER['uid'];
        $userinfo = $m->query("SELECT * FROM {$prefix}auth_group g left join {$prefix}auth_group_access a on g.id=a.group_id where a.uid=$UID");
        $Auth = new Auth();
        $allow_controller_name = array('Upload');//放行控制器名称
        $allow_action_name = array();//放行函数名称
        if ($userinfo[0]['group_id'] != 1 && !$Auth->check(CONTROLLER_NAME . '/' . ACTION_NAME, $UID) && !in_array(CONTROLLER_NAME, $allow_controller_name) && !in_array(ACTION_NAME, $allow_action_name)) {
            $this->error('没有权限访问本页面!');
        }

        $user = member(intval($UID));
        $this->assign('user', $user);


        $current_action_name = ACTION_NAME == 'edit' ? "index" : ACTION_NAME;
        $current = $m->query("SELECT s.id,s.title,s.name,s.tips,s.pid,p.pid as ppid,p.title as ptitle FROM {$prefix}auth_rule s left join {$prefix}auth_rule p on p.id=s.pid where s.name='" . CONTROLLER_NAME . '/' . $current_action_name . "'");
        $this->assign('current', $current[0]);


        $menu_access_id = $userinfo[0]['rules'];

        if ($userinfo[0]['group_id'] != 1) {

            $menu_where = "AND id in ($menu_access_id)";

        } else {

            $menu_where = '';
        }
        $menu = M('auth_rule')->field('id,title,pid,name,icon')->where("islink=1 $menu_where ")->order('o ASC')->select();
        $menu = $this->getMenu($menu);
        $this->assign('menu', $menu);

    }
    /*
     *
     * 处理二级菜单
     * 
     * 
     */

    protected function getMenu($items, $id = 'id', $pid = 'pid', $son = 'children')
    {
        $tree = array();
        $tmpMap = array();

        foreach ($items as $item) {
            $tmpMap[$item[$id]] = $item;
        }

        foreach ($items as $item) {
            if (isset($tmpMap[$item[$pid]])) {
                $tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
            } else {
                $tree[] = &$tmpMap[$item[$id]];
            }
        }
        return $tree;
    }
    /* 审核状态
     * status参数
     * 1：不通过
     * 2：通过
     * 模型用常量获取
     */
    public function status(){
        $id = isset($_REQUEST['id'])?$_REQUEST['id']:false;
        //id无效的
        if(!$id){
            $this->error('参数错误！');
        }
     
        if ($_REQUEST['val']=="1"){
            //申请审核通过
            $val="2";
            $tip="已经审核通过";
        }else{
            //关闭通过
            $val="1";
            $tip="已经审核不通过";
        }
        $model=M(CONTROLLER_NAME);
        
        $map['id']  = $id;
        $data['status']=$val;
        if($model->where($map)->save($data)){
            $this->success($tip);
        }else{
            $this->error($tip);
        }
      }
}