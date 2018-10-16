<?php

namespace app\index\model;

use think\Model;

class Investor extends BasisModel
{
    protected $table = 'tb_investors';

    protected $autoWriteTimestamp = 'timestamp';

}
