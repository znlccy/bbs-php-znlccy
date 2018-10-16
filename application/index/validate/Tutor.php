<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:00
 * Comment: 导师验证器
 */

namespace app\index\validate;

class Tutor extends BasisValidate {

    //验证规则
    protected $rule = [
        'competition_id' => 'number',
        'page_size'      => 'number',
        'jump_page'      => 'number',
    ];

    //验证领域
    protected $message = [
        'competition_id' => '大赛id',
        'page_size'      => '每页数量',
        'jump_page'      => '页码',
    ];

    //验证场景
    protected $scene = [
        'index' => ['competition_id' => 'require|number', 'page_size' => 'number', 'jump_page' => 'number'],
    ];
}
