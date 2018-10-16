<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:34
 * Comment: 分类验证器
 */

namespace app\admin\validate;

class Category extends BaseValidate {

    //验证规则
    protected $rule = [
        'id'             => 'number',
        'page_size'      => 'number',
        'jump_page'      => 'number',
        'category_name'  => 'max:60',
    ];

    //验证消息
    protected $message = [

    ];

    protected $field = [
        'id'            => '主键id',
        'page_size'     => '每页数量',
        'jump_page'     => '页码',
        'category_name' => '名称',
    ];

    //验证场景
    protected $scene = [
        'index' => ['page_size' => 'number', 'jump_page' => 'number'],
        'save'  => ['category_name' => 'require|max:60'],
        'detail'=> ['id' => 'require|number']
    ];
}