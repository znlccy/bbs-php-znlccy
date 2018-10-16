<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13
 * Time: 11:33
 * Comment: 组织分组
 */

namespace app\index\model;

class OrganizerGroup extends BasisModel {

    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_organizer_groups';
    protected $resultSetType = 'collection';
}