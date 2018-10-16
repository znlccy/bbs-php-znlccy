<?php

namespace app\admin\model;

use think\Model;

class News extends BaseModel
{
//    protected $autoWriteTimestamp = 'datetime';

    protected $table = 'tb_news';

    /**
     * 富文本标签转义
     */
    public function setRichTextAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getRichTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}
