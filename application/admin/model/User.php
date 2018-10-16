<?php

namespace app\admin\model;

use think\Model;

class User extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'tb_user';
}
