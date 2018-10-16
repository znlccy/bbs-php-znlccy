<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:33
 * Comment: 轮播验证器
 */

namespace app\admin\validate;

class Carousel extends BaseValidate {

    //验证规则
    protected $rule = [
        'id'        => 'number',
        'title'     => 'max:60',
        'sort'      => 'number',
        'url'      => 'url',
        'pub_start' => 'date',
        'pub_end'   => 'date',
        'page_size'  => 'number',
        'jump_page'  => 'number',
        'status'    => 'require|number',
    ];

    //验证消息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'id'        => '大赛主键',
        'title'     => '轮播标题',
        'url'       => '轮播跳转url地址',
        'sort'      => '轮播排序',
        'status'    => '轮播状态',
        'pub_start' => '轮播创建',
        'pub_end'   => '轮播创建',
        'page_size' => '每页数量',
        'jump_page' => '页码'
    ];

    //验证场景
    protected $scene = [
        'upload' => ['id', 'title' => 'require|max:80', 'picture', 'url' => 'require|url', 'sort' => 'require|number', 'status' => 'number'],
        'delete' => ['id' => 'require|number'],
        'detail' => ['id' => 'require|number'],
        'carousel_list' => [
            'id'        => 'number',
            'title'     => 'max:60',
            'picture',
            'url'       => 'url',
            'sort'      => 'number',
            'status'    => 'number',
            'pub_start' => 'date',
            'pub_end'   => 'date',
            'page_size' => 'number',
            'jump_page' => 'number'
        ]
    ];
}