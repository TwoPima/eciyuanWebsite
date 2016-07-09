<?php
/**
*
* 版权所有：亿次元科技(www.eciyuan.net)
* 作    者：马晓成(ma.running@foxmail.com)
* 日    期：2016-06-20
* 版    本：1.0.0
* 功能说明：组织机构公益信息控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class OrganizeInfoController extends ComController {

	public function add(){
		
		$category = M('Organize')->order('sort asc')->select();
			$this->assign('category',$category);//导航
		$this -> display();
	}
		
	public function index($sid=0,$p=1){
		
		$sid = intval($sid);
		$p = intval($p)>0?$p:1;
		
		$article = M('OrganizeInfo');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$prefix = C('DB_PREFIX');
		if($sid){
			$where = "{$prefix}organize_info.sid=$sid";
		}else{
			$where = '';
		}
		$count = $article->where($where)->count();
		$list  = $article->field("{$prefix}organize_info.*,{$prefix}organize.name")->where($where)->order("{$prefix}organize_info.organize_id desc")->join("{$prefix}organize ON {$prefix}organize.organize_id = {$prefix}organize_info.organize_id")->limit($offset.','.$pagesize)->select();
		$page	=	new \Think\Page($count,$pagesize); 
		$page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);
		$this -> display();
	}
	
	public function del(){
		
		$aids = isset($_REQUEST['sids'])?$_REQUEST['sids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);
				$map['sid']  = array('in',$aids);
			}else{
				$map = 'sid='.$aids;
			}
			if(M('OrganizeInfo')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}

	}
	
	public function edit($aid){
		
		$aid = intval($aid);
		$article = M('OrganizeInfo')->where('sid='.$aid)->find();
		if($article){
			$category = M('Organize')->order('sort asc')->select();
			$where['organize_id']=$article['organize_id'];
			$categoryName = M('Organize')->where($where)->find();
			$this->assign('category',$category);//导航
			
			$this->assign('categoryName',$categoryName);//当前的导航
			
			$this->assign('article',$article);
		}else{
			$this->error('参数错误！');
		}
		$this -> display();
	}
	
	public function update($sid=0){
		$sid = intval($sid);
		$data['organize_id'] = isset($_POST['organize_id'])?intval($_POST['organize_id']):0;
		$data['title'] = isset($_POST['title'])?$_POST['title']:false;
		$data['content'] = isset($_POST['content'])?$_POST['content']:false;
		$data['start_time'] = isset($_POST['start_time'])?$_POST['start_time']:false;
		$data['end_time'] = isset($_POST['end_time'])?$_POST['end_time']:false;
		$data['thumbnail'] = I('post.thumbnail','','strip_tags');
		$data['update_time'] = time();
		if(!$data['organize_id'] or !$data['title'] or !$data['content']){
			$this->error('警告！文章分类、文章标题及文章内容为必填项目。');
		}
		if($sid){
			M('OrganizeInfo')->data($data)->where('sid='.$sid)->save();
			addlog('编辑文章，AID：'.$sid);
			$this->success('恭喜！文章编辑成功！');
		}else{
		    $data['create_time'] = time();
			$sid = M('OrganizeInfo')->data($data)->add();
			if($sid){
				addlog('新增文章，AID：'.$sid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
			
		}
	}
}