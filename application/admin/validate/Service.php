<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:49
 * Comment: 服务验证器
 */

namespace app\admin\validate;

class Service extends BaseValidate {

    //验证规则
    protected $rule = [
        'id'           => 'number',
        'status'       => 'number',
        'page_size'    => 'require|number',
        'jump_page'    => 'require|number',
        'name'       => 'require|max:60',
        'category_id' => 'require|number',
        'price'      => 'require',
        'address'    => 'require',
    ];

    //验证領域
    protected $field = [
    	'status'     => '状态',
        'name'       => '名称',
        'category_id' => '分类',
        'price'      => '价格',
        'address'    => '联系地址',
        'page_size'  => '每页数量',
        'jump_page'  => '页码',
    ];

    //验证场景
    protected $scene = [
    	'index' => ['id', 'status', 'page_size', 'jump_page'],
    	'save'  => ['name', 'picture', 'category_id', 'price', 'recommend', 'address', 'publish_time', 'status'],
    	'detail' => ['id' => 'require|number'],
    	'delete' => ['id' => 'require|number'],
    ];
}