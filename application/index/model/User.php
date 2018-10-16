<?php
/**
 * Created by PhpStorm.
 * User: SYSTEM
 * Date: 2018/8/4
 * Time: 12:26
 */

namespace app\index\model;

class User extends BasisModel {
    protected $autoWriteTimestamp = 'datetime';
    /**
     * 自动插入数据库的字段
     * @var array
     */
    protected $auto = ['login_ip'];

    /**
     * 插入的时候，初始数据
     * @var array
     */
    protected $insert = ['status' => 1];

    /**
     * 创建时间
     * @var string
     */
    protected $createTime = 'register_time';

    /**
     * 更新时间
     * @var string
     */
    protected $updateTime = 'update_time';


    //关联的数据表
    protected $table = 'tb_user';
}