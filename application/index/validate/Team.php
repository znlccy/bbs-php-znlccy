<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:00
 * Comment: 团队验证器
 */

namespace app\index\validate;

class Team extends BasisValidate {

    //手机验证正则表达式
    protected $regex = [ 'mobile' => '/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(166)|(19([8,9]))|(18[0-9]))\d{8}$/'];

    //验证规则
    protected $rule = [
        'id'              => 'number',
        'competition_id'  => 'number',
        'page_size'       => 'number',
        'jump_page'       => 'number',
        'project_name'    => 'max:50',
        'project_area'    => 'max:30',
        'content'         => 'max:255',
        'register'        => 'number',
        'province_id'     => 'number',
        'city_id'         => 'number',
        'username'        => 'max:30',
        'mobile'          => 'number|length:11',
        'company'         => 'max:255',
        'video'           => 'max:255',
        'business_plan'   => 'max:255',
    ];

    //验证领域
    protected $message = [
        'competition_id'  => '大赛id',
        'page_size'       => '每页数量',
        'jump_page'       => '页码',
        'project_name'    => '项目名称',
        'project_area'    => '项目领域',
        'content'         => '项目简介',
        'company'         => '公司机构名称',
        'register'        => '团队人数',
        'picture'         => '团队形象图',
        'province_id'     => '所在省份',
        'city_id'         => '所在城市',
        'username'        => '用户名',
        'mobile'          => '手机号',
        'video'           => '路演视频地址',
        'business_plan'   => '商业计划书',
    ];

    //验证场景
    protected $scene = [
        'index' => ['competition_id' => 'require|number', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'  => ['id' => 'require|number', 'project_name' => 'require|max:50', 'project_area' => 'require|max:30', 'content' => 'require|max:255', 'register' => 'require|number', 'picture' => 'require', 'province_id' => 'require|number', 'city_id' => 'require|number', 'username' => 'require|max:30', 'mobile' => 'require|length:11|regex:mobile', 'company' => 'require|max:255', 'video' => 'require|max:255', 'business_plan' => 'require|max:255'],
        'detail' => ['id' => 'require|number'],
        'delete' => ['id' => 'require|number'],
    ];
}