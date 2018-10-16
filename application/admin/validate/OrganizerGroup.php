<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:48
 * Comment: 组织组验证器
 */

namespace app\admin\validate;

class OrganizerGroup extends BaseValidate {

    //验证规则
    protected $rule = [
        'id'             => 'require|number',
        'name'           => 'require|max:30',
    ];

    //验证领域
    protected $field = [
        'name'           => '名称',
    ];

    //验证场景
    protected $scene = [
        'save'   => ['id' => 'number', 'name'],
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}