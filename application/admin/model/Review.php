<?php

namespace app\admin\model;

use think\Model;

class Review extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tb_review';

    public function setRichTextAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getRichTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}
