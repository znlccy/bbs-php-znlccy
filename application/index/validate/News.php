<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:39
 * Comment: 新闻验证器
 */

namespace app\index\validate;

class News extends BasisValidate {

    //验证规则
    protected $rule = [
        'competition_id' => 'number',
        'page_size'      => 'number',
        'jump_page'      => 'number',
    ];

    //验证领域
    protected $field = [
        'competition_id' => '大赛id',
        'page_size'      => '每页数量',
        'jump_page'      => '页码必须',
        'title'          => '新闻标题',
        'recommend'      => '是否推荐',
        'publish_time'      => '发布时间',
        'content'        => '新闻内容',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['competition_id' => 'require|number', 'page_size' => 'number', 'jump_page' => 'number'],
    ];
}
