<?php
/**
*
* 版权所有：亿次元科技(www.eciyuan.net)
* 作    者：马晓成(ma.running@foxmail.com)
* 日    期：2016-06-20
* 版    本：1.0.0
* 功能说明：清真寺通知信息控制器。
*
**/

namespace Admin\Controller;
use Admin\Controller\ComController;
use Vendor\Tree;

class MosqueNoticeController extends ComController {

	public function add(){
		
		$category = M('Mosque')->field('id,pid,name')->order('sort asc')->select();
		$tree = new Tree($category);
		$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
		$category = $tree->get_tree(0,$str,0);
		$this->assign('category',$category);//导航
		$this -> display();
	}
		
	public function index($sid=0,$p=1){
		
		$sid = intval($sid);
		$p = intval($p)>0?$p:1;
		
		$article = M('MosqueNotice');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$prefix = C('DB_PREFIX');
		if($sid){
			$where = "{$prefix}mosque_notice.id=$sid";
		}else{
			$where = '';
		}
		$count = $article->where($where)->count();
		$list  = $article->field("{$prefix}mosque_notice.*,{$prefix}mosque.name")->where($where)->order("{$prefix}mosque_notice.id desc")->join("{$prefix}mosque ON {$prefix}mosque.id = {$prefix}mosque_notice.authorID")->limit($offset.','.$pagesize)->select();
		
		$page	=	new \Think\Page($count,$pagesize); 
		$page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);
		$this -> display();
	}
	
	public function del(){
		
		$aids = isset($_REQUEST['ids'])?$_REQUEST['ids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);
				$map['id']  = array('in',$aids);
			}else{
				$map = 'id='.$aids;
			}
			if(M('MosqueNotice')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}

	}
	
	public function edit($id){
		
		$aid = intval($id);
		$article = M('MosqueNotice')->where('id='.$id)->find();
		if($article){
			
			$category = M('Mosque')->field('id,pid,name')->order('sort asc')->select();
			$tree = new Tree($category);
			$str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
			$category = $tree->get_tree(0,$str,$article['id']);
			$this->assign('category',$category);//导航
			
			$this->assign('article',$article);
		}else{
			$this->error('参数错误！');
		}
		$this -> display();
	}
	
	public function update($id=0){
		
		$id = intval($_POST['id']);
		$data['authorID'] = isset($_POST['authorID'])?intval($_POST['authorID']):0;
		$data['title'] = isset($_POST['title'])?$_POST['title']:false;
		$data['url'] = isset($_POST['url'])?$_POST['url']:false;
		$data['content'] = isset($_POST['content'])?$_POST['content']:false;
		$data['update_time'] = time();
		if(!$data['authorID'] or !$data['title'] or !$data['content']){
			$this->error('警告！文章分类、文章标题及文章内容为必填项目。');
		}
		if($id){
			M('MosqueNotice')->data($data)->where('id='.$id)->save();
			addlog('编辑文章，AID：'.$id);
			$this->success('恭喜！文章编辑成功！');
		}else{
		    $data['create_time'] = time();
			$aid = M('MosqueNotice')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$id);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
			
		}
	}
}