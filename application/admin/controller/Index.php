<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 9:46
 * Comment: 后台首页控制器
 */
namespace  app\admin\controller;

use think\Controller;

class Index extends BaseController {

    /**
     * 后台首页
     */
    public function index() {
        return $this->fetch();
    }

}