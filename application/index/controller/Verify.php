<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 16:42
 * Comment: 图形验证码控制器
 */
namespace app\index\controller;

use think\captcha\Captcha;
use think\Config;
use think\Controller;

class Verify extends Controller {

    /**
     * 获得图形验证码
     */
    public function attain() {
        ob_clean();
        $config = Config::get('captcha');
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

}