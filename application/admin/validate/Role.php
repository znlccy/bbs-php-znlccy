<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:49
 * Comment: 角色验证器
 */

namespace app\admin\validate;

class Role extends BaseValidate
{

    //验证规则
    protected $rule = [
        'page_size'         => 'number',
        'jump_page'         => 'number',
        'id'                => 'number',
        'status'            => 'number',
        'name'              => 'max:50',
        'active_start'      => 'date',
        'active_end'        => 'date',
        'description'       => 'max:120',
        'sort_num'          => 'number',
        'permission_id'     => 'array',
    ];

    //验证消息
    protected $field = [
        'page_size'   => '每页显示多少条',
        'jump_page'   => '跳转到第几页',
        'id'          => '角色主键',
        'name'        => '角色名称',
        'description' => '角色描述',
        'status'      => '角色状态',
        'sort_num'    => '角色排序',
        'permission_id'    => '权限主键'
    ];

    //验证场景
    protected $scene = [
        'role_list'             => ['page_size' => 'number', 'jump_page' => 'number', 'id' => 'number', 'status' => 'number', 'name' => 'max:80', 'active_start' => 'date', 'active_end' => 'date'],
        'add'                   => ['id' => 'number', 'name' => 'require|max:80', 'description' => 'require|max:120', 'status' => 'require|number', 'sort_num' => 'require|number'],
        'delete'                => ['id' => 'require|number'],
        'assign_role_permission'=> ['id' => 'require|number', 'permission_id' => 'require|array'],
        'get_role_permission'   => ['id' => 'require|number'],
    ];
}
