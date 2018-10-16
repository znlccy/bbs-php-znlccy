<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:02
 * Comment: 活动验证器
 */

namespace app\index\validate;

use think\Validate;

class Active extends BasisValidate {

    //手机验证正则表达式
    protected $regex = [ 'mobile' => '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(166)|(19([8,9]))|(18[0,9]))\d{8}$/'];

    //验证规则
    protected $rule = [
        'id'            => 'number',
        'title'         => 'max:60',
        'content'       => 'max:255',
        'picture'       => 'image',
        'limit'         => 'number',
        'register'      => 'number',
        'status'        => 'number',
        'address'       => 'max:255',
        'location'      => 'max:255',
        'begin_time'    => 'date',
        'end_time'      => 'date',
        'apply_time'    => 'date',
        'audit_method'  => 'number',
        'page_size'     => 'number',
        'jump_page'     => 'number',
        'username'      => 'max:255',
        'email'         => 'email',
        'mobile'        => 'length:11|regex:mobile',
        'career'        => 'max:255',
        'company'       => 'max:255',
        'occupation'    => 'max:255'
    ];

    //验证消息
    /*protected $message = [
        'id.require'    => '活动主键必填',
        'id.number'     => '活动主键是数字',
        'title.max'     => '活动标题最大填写长度为60',
        'content'       => '活动内容最大填写长度为255',
        'picture'       => '活动图片必须是图片格式',
        'limit'         => '活动人数限制必须是数字',
        'page_size'     => '分页大小必须是数字',
        'jump_page'     => '跳转页必须是数字'
    ];*/

    //验证领域
    protected $field = [
        'id'            => '活动主键',
        'title'         => '活动标题',
        'content'       => '活动内容',
        'picture'       => '活动图片',
        'limit'         => '活动人数限制',
        'register'      => '活动注册人数',
        'status'        => '活动状态',
        'address'       => '活动简写地址',
        'location'      => '活动详细地址',
        'begin_time'    => '活动开始时间',
        'end_time'      => '活动结束时间',
        'apply_time'    => '活动申请时间',
        'audit_method'  => '活动审核方式',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页',
        'username'      => '用户名',
        'email'         => '用户邮箱',
        'mobile'        => '用户手机',
        'career'        => '用户职业',
        'company'       => '用户所在公司或者机构',
        'occupation'    => '用户所在行业'
    ];

    //验证场景
    protected $scene = [
        'index'         => ['page_size', 'jump_page'],
        'introduce'     => ['id' => 'require|number'],
        'registration'  => ['id' => 'require|number'],
        'apply'         => ['id' => 'require|number', 'username' => 'require|max:255', 'mobile' => 'require|length:11|regex: mobile', 'email' => 'require|email', 'career' => 'require|max:255', 'occupation' => 'require|max:255', 'company' => 'require|max:255'],
    ];

}