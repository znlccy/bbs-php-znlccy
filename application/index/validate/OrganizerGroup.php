<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:48
 * Comment: 组织组验证器
 */

namespace app\index\validate;

class OrganizerGroup extends BasisValidate {

    //验证规则
    protected $rule = [
        'id'             => 'number',
        'name'           => 'max:30',
    ];

    //验证领域
    protected $field = [
        'name'           => '名称',
    ];

    //验证场景
    protected $scene = [
        'save'   => ['id' => 'require|number', 'name' => 'require|max:80'],
        'detail' => ['id' => 'require|number'],
        'delete' => ['id' => 'require|number'],
    ];
}