<?php

namespace app\index\model;

use think\Model;

class News extends BasisModel
{
    protected $table = 'tb_news';

    // 新闻内容进行标签转义
    public function setContentAttr($value)
    {
        return htmlspecialchars($value);
    }

    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
}
