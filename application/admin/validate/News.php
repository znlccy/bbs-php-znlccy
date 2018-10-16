<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 19:39
 * Comment: 新闻验证器
 */

namespace app\admin\validate;

class News extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'             => 'require|number',
        'competition_id' => 'require|number',
        'page_size'      => 'require|number',
        'jump_page'      => 'require|number',
        'title'          => 'require|max:60',
        'content'        => 'require',
        'recommend'      => 'require|number',
        'publish_time'   => 'date',
    ];

    //验证消息
    protected $message = [
        'competition_id.require' => '大赛id必须填写',
        'competition_id.number'  => '大赛id必须为数字',
        'page_size.require'      => '每页数量必须填写',
        'page_size.number'       => '每页数量必须为数字',
        'jump_page.require'      => '页码必须填写',
        'jump_page.number'       => '页码必须为数字',
        'title.require'          => '新闻标题必须填写',
        'title.max'              => '新闻标题长度过长',
        'recommend.require'      => '是否推荐不能为空',
        'publish_time.date'      => '发布时间格式不正确',
        'content.require'        => '新闻内容不能为空',
    ];

    //验证场景
    protected $scene = [
        'index'  => ['competition_id', 'page_size', 'jump_page'],
        'save'   => ['title', 'recommend', 'publish_time'],
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}
