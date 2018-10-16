<?php

namespace app\admin\model;

use think\Model;

class UserActive extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tb_user_active';

//    public function getStatusAttr($value)
//    {
//        $status = [1=>'审核',0=>'未审核'];
//        return $status[$value];
//    }

}
