<?php

namespace app\admin\model;

use think\Model;

class Information extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'tb_information';

    public function setRichtextAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getRichtextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
    public function user()
    {
    	return $this->hasOne('Admin', 'id', 'publisher');
    }
}
