<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:00
 * Comment: 导师验证器
 */

namespace app\admin\validate;

class Tutor extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'             => 'require|number',
        'competition_id' => 'require|number',
        'page_size'      => 'require|number',
        'jump_page'      => 'require|number',
        'name'           => 'require|max:30',
        'introduction'   => 'require',
    ];

    //验证领域
    protected $field = [
        'competition_id' => '大赛id',
        'page_size'      => '每页数量',
        'jump_page'      => '页码',
        'name'           => '名称',
        'introduction'   => '导师介绍',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['competition_id', 'page_size', 'jump_page'],
        'save'   => ['id' => 'number', 'name', 'introduction'],
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}
