<?php
/**
*
* 版权所有：e次元科技<qwadmin.qiawei.com>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：前台控制器演示。
*
**/
namespace Home\Controller;
use Think\Controller;
use Vendor\Page;
class IndexController extends ComController {
    public function index(){
        //资讯信息
        $whereNewsIndustry['tag']="industry";
        $whereTrends['tag']="trends";
        $whereSkill['tag']="skill";
        $parentIndustryId=M('Category')->where($whereNewsIndustry)->field('id')->find();
        $parentNewsTrendsId=M('Category')->where($whereTrends)->field('id')->find();
        $parentNewsSkillId=M('Category')->where($whereSkill)->field('id')->find();
        $newsIndustry=M('Article')->where('sid='.$parentIndustryId['id'])->order("aid desc")->limit(5)->select();
        $newsTrends=M('Article')->where('sid='.$parentNewsTrendsId['id'])->order("aid desc")->limit(5)->select();
        $newsSkill=M('Article')->where('sid='.$parentNewsSkillId['id'])->order("aid desc")->limit(5)->select();
        $this->assign('newsIndustry',$newsIndustry);
        $this->assign('trends',$newsTrends);
        $this->assign('skill',$newsSkill);
        //案例成果
        $case=M('Case')->order("aid desc")->limit(6)->select();
        $this->assign('case',$case);
		$this -> display();
    }
    /*  表单反馈*/
    public function feedback(){
            $data['name'] = isset($_POST['name'])?$_POST['name']:false;
            $data['email'] = isset($_POST['email'])?$_POST['email']:false;
            $data['content'] = isset($_POST['content'])?$_POST['content']:false;
            $data['t'] = time();
            /* $data['ip'] = get_client_ip();
             import('ORG.Net.IpLocation');// 导入IpLocation类
             $Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
             $location  = $Ip->getlocation('203.34.5.66'); // 获取某个IP地址所在的位置
             $info =  $location['country'].$location['area']; */
            $aid = M('Feedback')->add($data);
            if($aid){
                //$this->ajaxReturn($_POST['name'],'发送成功',1);
                $this->success("已经成功发送！请耐心等候");
            }else{
                $this->error("发送失败，请重新发送");
            }
        }
        
}