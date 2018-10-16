<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:49
 * Comment: 服务提供商验证器
 */

namespace app\admin\validate;

class Sp extends BaseValidate {

    //验证规则
    protected $rule = [
        'id'             => 'require|number',
        'competition_id' => 'require|number',
        'page_size'      => 'require|number',
        'jump_page'      => 'require|number',
        'name'           => 'require|max:30',
    ];

    //验证领域
    protected $message = [
        'competition_id' => '大赛id',
        'page_size'      => '每页数量',
        'jump_page'      => '页码',
        'name'           => '名称',
    ];

    //验证场景
    protected $scene = [
        'index' => ['competition_id', 'page_size', 'jump_page'],
        'save'  => ['name'],
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}