<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 19:09
 * Comment: 联系我们控制器
 */
namespace app\index\controller;

use think\Controller;

class Contact extends Controller {

    /**
     * 联系我们首页
     */
    public function index() {
        return $this->fetch();
    }

}