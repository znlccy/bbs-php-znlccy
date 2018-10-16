<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:00
 * Comment: 团队验证器
 */

namespace app\admin\validate;

class Team extends BaseValidate {

    //验证规则
    protected $rule = [
        'id'             => 'require|number',
        'competition_id' => 'require|number',
        'page_size'      => 'require|number',
        'jump_page'      => 'require|number',
        'project_name'    => 'require|max:50',
        'project_area'    => 'require|max:30',
        'content'         => 'require',
        'register'        => 'require|number',
        'province_id'     => 'require|number',
        'city_id'         => 'require|number',
        'username'        => 'require|max:30',
        'mobile'          => 'require|number|length:11',
        'company'         => 'require',
        'video'           => 'require|url',
//        'user_id'         => 'require|number'
    ];

    //验证领域
    protected $field = [
        'competition_id' => '大赛id',
        'page_size'      => '每页数量',
        'jump_page'      => '页码',
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
        'index' => ['competition_id', 'page_size', 'jump_page'],
        'save'  => ['id' => 'number', 'project_name', 'project_area', 'content', 'register',  'province_id', 'city_id', 'username', 'mobile', 'company', 'video'],
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}