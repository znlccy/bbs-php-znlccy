<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 18:48
 * Comment: 管理员模型
 */
namespace app\admin\model;

use think\Model;

class Admin extends BaseModel {

    /**
     * 自动写入和读取时间
     * @var string
     */
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_admin';

    /**
     * 关联的中间表
     * @return \think\model\relation\BelongsToMany
     */
    public function role() {
        return $this->belongsToMany('Role', 'tb_admin_role', 'role_id','user_id');
    }
}