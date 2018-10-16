<?php

namespace app\admin\model;

use think\Model;

class Area extends BaseModel
{
    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_area';


    protected $resultSetType = 'collection';
}
