<?php

namespace app\index\model;

use think\Model;

class Area extends BasisModel
{
    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_area';


    protected $resultSetType = 'collection';
}
