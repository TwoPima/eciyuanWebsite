<?php
/**
*
* 版权所有：亿次元科技（www.eciyuan.net）
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-06-21
* 版    本：1.0.0
* 功能说明：微信端-清真寺控制器演示。
*
**/
namespace Mobile\Controller;
use Think\Controller;
use Vendor\Page;
class OrganizeController extends ComController {
    public function index(){
        $Organize=M('Organize')->where('status=2')->order('create_time desc')->select();
        $OrganizeInfo=M('OrganizeInfo')->where('status=2')->order('create_time desc')->select();
        $this->assign('Organize',$Organize);
        $this->assign('OrganizeInfo',$OrganizeInfo);
        $this->display();
    }
    /* 入驻 */
    public function add(){
        if ($_POST['submitAdd']=="1"){
                 $result = D('Mosque')->create();
                 if ($result){
                    // 验证通过 可以进行其他数据操作
                    $this->qrcode($_POST['name'],D('Mosque')->add($result));
            }else {
                $this->error(D('Mosque')->getError());
            }
               
        }else{
                $this->display();
            }
    }
    public function detail(){
        if (empty($_GET['sid'])){
            //公益组织
            $where['organize_id']=$_GET['id'];
            $model=M('Organize');
            //上一篇
            $front=$model->where("organize_id<".$_GET['id'])->order('organize_id desc')->find();
            //下一篇
            $after=$model->where("organize_id>".$_GET['id'])->order('organize_id desc')->find();
            $typeHtml="oganizeDetail";
        }else{
            //公益信息
              $where['sid']=$_GET['sid'];
              $model=M('OrganizeInfo');
              //上一篇
              $front=$model->where("sid<".$_GET['sid'])->order('sid desc')->find();
              //下一篇
              $after=$model->where("sid>".$_GET['sid'])->order('sid desc')->find();
              $typeHtml="detail";
        }
          $result=$model->where($where)->find();
          
          $this->assign('detail',$result);
          
        
          $this->assign('front',$front);//上一条
          $this->assign('after',$after);//下一条
          
          $this -> display($typeHtml);
      }
  
}