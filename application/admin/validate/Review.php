<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:48
 * Comment: 回顾验证器
 */

namespace app\admin\validate;

class Review extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'           => 'number',
        'title'        => 'require|max:60',
        'content'      => 'require',
        'publish_time' => 'require|date',
        'recommend'    => 'number',
        'page_size'    => 'require|number',
        'jump_page'    => 'require|number',
    ];

    //验证领域
    protected $field = [
        'title'        => '标题必须填写',
        'content'      => '简介必须填写',
        'publish_time' => '发布时间必须填写',
        'page_size'    => '每页数量必须填写',
        'jump_page'    => '页码必须填写',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['page_size', 'jump_page', 'recommend'],
        'save'   => ['id', 'title', 'content', 'publish_time', 'recommend'],
        'detail' => ['id'],
    ];
}
