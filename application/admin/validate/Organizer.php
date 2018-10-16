<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:48
 * Comment: 组织验证器
 */

namespace app\admin\validate;

class Organizer extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'             => 'require|number',
        'competition_id' => 'require|number',
        'page_size'      => 'require|number',
        'jump_page'      => 'require|number',
        'name'           => 'require|max:60',
        'group_id'       => 'require',
        'recommend'      => 'require',
    ];

    //验证领域
    protected $field = [
        'competition_id' => '大赛id',
        'page_size'      => '每页数量',
        'jump_page'      => '页码',
        'name'           => '名称',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['competition_id', 'page_size', 'jump_page'],
        'save'   => ['id' => 'number', 'name', 'group_id', 'recommend'],
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}
