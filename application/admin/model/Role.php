<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 18:48
 * Comment: 角色模型
 */
namespace app\admin\model;

use think\Model;

class Role extends BaseModel {


    // 定义时间戳字段名
    protected $createTime = 'create_time';


    protected $insert = ['create_time'];

    protected $autoWriteTimestamp = 'datetime';
    
    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_role';

    public function permission() {
        return $this->belongsToMany('Permission', 'tb_role_permission', 'permission_id', 'role_id');
    }

}