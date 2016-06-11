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
class ComController extends Controller {

	public function _initialize(){
	    Load('extend');
	    import("ORG.Util.Page"); //载入分页类
		C(setting());
	  		//导航数据组装
		$result = M('Category')->where('topsite=1')->order('o ASC')->select();
		$article_cate_list = array();
		foreach ($result as $val) {
			if ($val['pid']==0) {
				$article_cate_list['parent'][$val['id']] = $val;
			} else {
				$article_cate_list['sub'][$val['pid']][] = $val;
			}
		}
		$this->assign('article_cate_list',$article_cate_list); 
		
		$links = M('links')->limit(10)->order('o ASC')->select();
		$this->assign('links',$links);
		
    }
    /* 
     * 
     * 处理二级菜单
     * */
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
    //详细操作；根据传过来的表名和类型取数据
    public function detail(){
        $table=$_GET['m'];
        $model=M($table);
        //$str=$model->getPk ();
        //$where[$str]=(int) $_GET['id'];
        $where['id']=$_GET['id'];
        $result_se=$model->where($where)->select();
        $result=array();
        foreach ($result_se as $value){
            $result[]=$value;
        }
        //上一篇
        $front=$model->where("id<".$_GET['id'])->order('id desc')->limit('1')->find();
        if (empty($front)) {
            $front="没有了！";
        }
        //下一篇
        $after=$model->where("id>".$_GET['id'])->order('id desc')->limit('1')->find();
        if (empty($after)) {
            $after="没有了！";
        }
        $this->assign('front',$front);
        $this->assign('after',$after);
        $this->assign('detail',$result);
        $this->display();
    }
    //根据分类名获取分类id
    public function getclass($class_name,$action_name){
        $m=M($action_name.'_class');
        $str=$m->getPk ();
        $where['type_name']=$class_name;
        $result=$m->where($where)->select();
        $cid=$result[0][$str];
        return $cid;
    }
    
    //搜索部分
    public function search(){
        //搜索会员信息
        $action_name=$this->getActionName();
        $model=D('Member_detail');
        $data = $model->create();
        $condition = array();
        $this->assign("list", $model->listNews('Member_detail',$page->firstRow, $page->listRows,$condition));
        $this->display($action_name);
    }
}