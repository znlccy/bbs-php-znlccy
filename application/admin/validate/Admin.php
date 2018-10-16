<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:26
 * Comment: 管理员验证器
 */

namespace app\admin\validate;

class Admin extends BaseValidate {
    protected $table = 'tb_admin';

    //手机验证正则表达式
    protected $regex = [ 'mobile' => '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(166)|(19([8,9]))|(18[0-9]))\d{8}$/'];

    //验证规则
    protected $rule = [
        'id'            => 'number',
        'mobile'        => 'length:11|regex:mobile',
        'code'          => 'length:6|number',
        'password'      => 'alphaDash|length:8,25',
        'username'      => 'max:60|alphaDash',
        'confirm_pass'  => 'alphaDash|length:8,25|confirm:password',
        'real_name'     => 'max:40',
        'status'        => 'number',
        'role_id'       => 'array',
        'page_size'     => 'number',
        'jump_page'     => 'number',
        'register_start'=> 'date',
        'register_end'  => 'date',
        'login_start'   => 'date',
        'login_end'     => 'date',
        'login_ip'      => 'max:120',
        'create_ip'     => 'max:120'
    ];

    //验证消息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'id'            => '管理员Id',
        'mobile'        => '手机号码',
        'code'          => '短信验证码',
        'password'      => '用户密码',
        'username'      => '用户名',
        'confirm_pass'  => '确认密码',
        'real_name'     => '真实姓名',
        'status'        => '用户状态',
        'role_id'       => '角色主键',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页',
        'register_start'=> '注册开始日期',
        'register_end'  => '注册结束日期',
        'login_start'   => '登录开始日期',
        'login_end'     => '登录结束日期',
        'login_ip'      => '登录ip',
        'create_ip'     => '创建ip'
    ];

    //验证场景
    protected $scene = [
        'mobile_login'          => ['mobile' => 'require|length:11|regex:mobile', 'code' => 'require|length:6|number'],
        'account_login'         => ['mobile' => 'require|length:11|regex:mobile', 'password' => 'require|length:8,25|alphaDash'],
        'admin_list'            => ['page_size' => 'number', 'jump_page' => 'number', 'id' => 'number', 'status' => 'number', 'real_name' => 'min:0', 'register_start' => 'date', 'register_end' => 'date', 'login_start' => 'date', 'login_end' => 'date', 'login_ip' => 'max:120', 'create_ip' => 'max:120'],
        'detail'                => ['id' => 'require|number'],
        'change_password'       => ['password' => 'require|length:8,25|alphaDash', 'confirm_pass' => 'require|alphaDash|length:8,25|confirm:password']
    ];
}