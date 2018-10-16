<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:03
 * Comment: 大赛验证器
 */

namespace app\index\validate;

use think\Validate;

class Competitions extends Validate {

    //手机验证正则表达式
    protected $regex = [ 'mobile' => '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(166)|(19([8,9]))|(18[0-9]))\d{8}$/'];

    //验证场景
    protected $rule = [
        'id'                => 'number',
        'project_name'      => 'max:50',
        'project_area'      => 'max:30',
        'content'           => 'max:255',
        'register'          => 'number',
        'picture'           => 'max:255',
        'province_id'       => 'number',
        'city_id'           => 'number',
        'username'          => 'max:30',
        'mobile'            => 'length:11|regex:mobile',
        'company'           => 'max:255',
        'video'             => 'max:255',
        'business_plan'     => 'max:255',
        'page_size'         => 'number',
        'jump_page'         => 'number',
    ];

    //验证信息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'id'                => '大赛主键',
        'project_name'      => '项目名称',
        'project_area'      => '项目领域',
        'content'           => '项目简介',
        'register'          => '团队人数',
        'picture'           => '团队形象图',
        'province_id'       => '所在省份',
        'city_id'           => '所在城市',
        'username'          => '用户名',
        'mobile'            => '手机账号',
        'company'           => '公司机构名称',
        'video'             => '路演视频地址',
        'business_plan'     => '商业计划书',
        'page_size'         => '分页大小',
        'jump_page'         => '跳转页'
    ];

    //验证场景
    protected $scene = [
        'index'         => ['page_size', 'jump_page'],
        'introduce'     => ['id|require', 'page_size', 'jump_page'],
        'apply'         => ['project_name|require', 'project_area|require', 'content|require', 'register|require', 'picture|require', 'province_id|require', 'city_id|require', 'username|require', 'mobile|require', 'company|require', 'video|require', 'business_plan|require'],
    ];

}