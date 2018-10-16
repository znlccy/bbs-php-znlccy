<?php
/**
 * Created by PhpStorm * User: Administrator
 * Date: 2018/8/9
 * Time: 19:34
 * Comment: 大赛验证器
 */

namespace app\admin\validate;

class Competition extends BaseValidate
{

    //验证规则
    protected $rule = [
        'id'           => 'require|number',
        'name'         => 'require|max:60',
        'limit'        => 'require|number|min:0',
        'time'         => 'require|date',
        'begin_time'   => 'require|date',
        'end_time'     => 'require|date',
        'audit_method' => 'require|number',
        'page_size'    => 'require|number',
        'jump_page'    => 'require|number',
        'min_register' => 'number|min:0',
        'max_register' => 'number|gt:min_register',
        'rich_text'     => 'require',
        'status'       => 'require|number',
        'forum_id'     => 'require|number',
        'team_id'      => 'require|number',
    ];

    //验证领域
    protected $field = [
        'id'           => '大赛id',
        'name'         => '名称',
        'limit'        => '人数限制',
        'time'         => '大赛时间',
        'begin_time'   => '报名开始时间',
        'end_time'     => '报名结束时间',
        'audit_method' => '审核方式',
        'page_size'    => '每页数量',
        'jump_page'    => '页码',
        'min_register' => '人数最小值',
        'max_register' => '人数最大值',
        'rich_text'    => '富文本内容',
        'status'       => '审核状态',
        'forum_id'     => '论坛id',
        'team_id'      => '团队id'
    ];

    //验证场景
    protected $scene = [
        'index'        => ['page_size', 'jump_page', 'min_register', 'max_register'],
        'save'         => ['id' => 'number', 'name', 'limit', 'time', 'begin_time', 'end_time', 'audit_method', 'rich_text'],
        'detail'       => ['id'],
        'delete'       => ['id'],
        'check'        => ['id', 'status'],
        'checked_team' => ['id'],
        'add_forum'    => ['id', 'forum_id'],
        'delete_forum' => ['id', 'forum_id'],
        'add_team'    => ['id', 'team_id'],
        'delete_team' => ['id', 'team_id'],
    ];
}
