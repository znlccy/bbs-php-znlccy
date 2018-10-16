<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:04
 * Comment: 回顾验证器
 */

namespace app\index\validate;

use think\Validate;

class Review extends BasisValidate {

    //验证规则
    protected $rule = [
        'id'                => 'number',
        'page_size'         => 'number',
        'jump_page'         => 'number'
    ];

    //验证信息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'id'                => '回顾主键',
        'page_size'         => '分页大小',
        'jump_page'         => '跳转页'
    ];

    //验证场景
    protected $scene = [
        'index'             => ['page_size', 'jump_page'],
        'detail'            => ['id|require'],
        ''
    ];

}