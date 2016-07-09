<?php
/**
 *
 * 版权所有：e次元科技<qwadmin.qiawei.com>
 * 作    者：马晓成<857773627@qq.com>
 * 日    期：2016-04-21
 * 版    本：1.0.0
 * 功能说明：导航二级菜单详细控制器。
 *
 */
namespace Home\Controller;
use Think\Controller;
use Vendor\Page;
class CategoryController extends ComController {
    public function index(){
        //提取栏目内容
        $categoryModel=M('Category');
        $where['pid']=$_GET['pid'];
        $result=$categoryModel->where($where)->select();
        $this->assign('CateName',$result);
        //提取文章详情
        $detail=M('Article')->where("sid=".$_GET['id'])->select();
        //上一篇
        $front=M('Article')->where("sid<".$_GET['id'])->order('sid desc')->find();
        //下一篇
        $after=M('Article')->where("sid>".$_GET['id'])->order('sid desc')->find();
        $this->assign('detail',$detail);
        $this->assign('front',$front);//上一条
        $this->assign('after',$after);//下一条
        $this->display();
    }
    
}