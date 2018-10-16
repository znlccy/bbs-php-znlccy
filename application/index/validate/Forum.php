<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:03
 * Comment: 论坛验证器
 */

namespace app\index\validate;

use think\Validate;

class Forum extends BasisValidate {

    //手机验证正则表达式
    protected $regex = [ 'mobile' => '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(166)|(19([8,9]))|(18[0-9]))\d{8}$/'];

    //验证规则
    protected $rule = [
        'id'                => 'number',
        'forum_id'          => 'number',
        'username'          => 'max:30',
        'mobile'            => 'length:11|regex:mobile',
        'email'             => 'email',
        'career'            => 'max:255',
        'occupation'        => 'max:255',
        'company'           => 'max:255',
        'page_size'         => 'number',
        'jump_page'         => 'number',
    ];

    //验证消息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'id'                => '论坛主键',
        'forum_id'          => '论坛主键',
        'username'          => '用户名',
        'mobile'            => '用户手机',
        'email'             => '用户邮箱',
        'career'            => '用户职业',
        'occupation'        => '用户行业',
        'company'           => '公司机构名称',
        'page_size'         => '分页大小',
        'jump_page'         => '跳转页'
    ];

    //验证场景
    protected $scene = [
        'index'             => ['page_size' => 'number', 'jump_page' => 'number'],
        'introduce'         => ['id' => 'require|number'],
        'apply'             => ['forum_id' => 'require|number', 'username' => 'require|max:300', 'mobile' => 'require|length:11|regex:mobile', 'email'  => 'require|email', 'career' => 'require|max:255', 'occupation' => 'require|max:255', 'company' => 'require|max:255'],
    ];

}