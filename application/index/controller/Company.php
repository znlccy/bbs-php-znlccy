<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 17:28
 * Comment: 公司控制器
 */
namespace app\index\controller;

use think\Controller;

class Company extends Controller {

    /**
     * 关于我们界面
     */
    public function aboutUs() {
        return $this->fetch();
    }

    /**
     * 联系我们界面
     */
    public function contactUs() {

    }

    /**
     * 公司创始人界面
     */
    public function founder() {
        return $this->fetch();
    }

    /**
     * 公司文化界面
     */
    public function culture() {
        return $this->fetch();
    }

    /**
     * 公司荣誉界面
     */
    public function honor() {
        return $this->fetch();
    }

    /**
     * 公司地址界面
     */
    public function address() {
        return $this->fetch();
    }

    /**
     * 友情链接界面
     */
    public function links() {
        return $this->fetch();
    }

    /**
     * 技术团队界面
     */
    public function team() {
        return $this->fetch();
    }
}