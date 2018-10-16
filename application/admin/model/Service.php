<?php

namespace app\admin\model;

use think\Model;

class Service extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'tb_service';
    
    public function setRichTextAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getRichTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    public function category()
    {
    	return $this->hasOne('Category', 'id', 'category_id');
    }
}
