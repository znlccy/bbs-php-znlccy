<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:04
 * Comment: 短信验证码验证器
 */

namespace app\index\validate;

class Sms extends BasisValidate {

    //验证规则
    protected $rule = [
        'mobile'        => 'length:11',
    ];

    //验证消息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'mobile'        => '手机号码'
    ];

    //验证场景
    protected $scene = [
        'attain'          => [],
    ];

}