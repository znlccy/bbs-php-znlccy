<?php
/**
 * Created by PhpStorm * User: Administrator
 * Date: 2018/8/9
 * Time: 19:35
 * Comment: 论坛验证器
 */

namespace app\admin\validate;

class Forum extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'                 => 'require|number',
        'title'              => 'require|max:60',
        'content'            => 'require',
        'start'              => 'require|number|lt:limit',
        'limit'              => 'require|number|min:0',
        'register'           => 'number|min:0',
        'address'            => 'require',
        'location'           => 'require',
        'apply_time'         => 'require|gt:end_time',
        'begin_time'         => 'require',
        'end_time'           => 'require|gt:begin_time',
        'audit_method'       => 'require',
        'page_size'          => 'require|number',
        'jump_page'          => 'require|number',
        'min_register'       => 'number|min:0',
        'max_register'       => 'number|gt:min_register',
        'forum_begin'        => 'date',
        'forum_end'          => 'date',
        'registration_begin' => 'date',
        'registration_end'   => 'date',
        'status'             => 'require|number',

    ];

    //验证消息
    protected $message = [

    ];

    //验证领域
    protected $field = [
        'title'        => '标题',
        'content'      => '论坛简介',
        'limit'        => '人数上限',
        'address'      => '论坛地址',
        'location'     => '论坛详细地址',
        'apply_time'   => '论坛时间',
        'begin_time'   => '论坛开始时间',
        'end_time'     => '论坛结束时间',
        'audit_method' => '审核方式',
        'page_size'    => '每页数量',
        'jump_page'    => '页码',
        'min_register' => '人数最小值',
        'max_register' => '人数最大值',
        'status'       => '审核状态',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['page_size', 'jump_page', 'min_register', 'max_register', 'forum_begin', 'forum_end', 'registration_begin', 'registration_end'],
        'save'   => ['id' => 'number', 'title', 'content', 'start', 'limit', 'address', 'location', 'apply_time', 'begin_time', 'end_time', 'audit_method'],
        'detail' => ['id'],
        'delete' => ['id'],
        'check'  => ['id', 'status'],
    ];
}
