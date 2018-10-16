<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 19:31
 * Comment: 权限模型
 */
namespace app\admin\model;

use think\Model;

class Permission extends BaseModel {

    /**
     * 自动写入时间
     * @var string
     */
    protected $autoWriteTimestamp = 'datetime';

    
    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_permission';

}