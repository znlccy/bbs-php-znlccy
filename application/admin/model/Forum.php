<?php

namespace app\admin\model;

use think\Model;

class Forum extends BaseModel
{
    protected $table ='tb_forums';
    
    public function setRichTextAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getRichTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}
