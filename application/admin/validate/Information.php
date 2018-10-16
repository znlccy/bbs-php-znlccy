<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:35
 * Comment: 信息验证器
 */

namespace app\admin\validate;

class Information extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'           => 'number',
        'page_size'    => 'number',
        'jump_page'    => 'number',
        'status'       => 'number',
        'title'        => 'require',
        'publisher'    => 'number',
        'start_time'   => 'date',
        'end_time'     => 'date',
        'publish_time' => 'date',
    ];

    //验证领域
    protected $field = [
        'page_size'    => '每页数量',
        'jump_page'    => '页码',
        'status'       => '状态',
        'title'        => '标题',
        'publisher'    => '发布人',
        'start_time'      => '发布开始时间',
        'end_time'        => '发布结束时间',
        'publish_time' => '发布时间',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['page_size' => 'number', 'jump_page' => 'number', 'status' => 'number', 'start_time' => 'date', 'end_time' => 'date'],
        'save'   => ['id' => 'require|number', 'publisher', 'title' => 'require|max:120', 'publish_time' => 'date'],
        'detail' => ['id' => 'require|number'],
        'delete' => ['id' => 'require|number'],
    ];
}
