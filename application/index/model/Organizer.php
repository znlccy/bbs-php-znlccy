<?php

namespace app\index\model;

use think\Model;

class Organizer extends BasisModel
{
    protected $table = 'tb_organizers';

    // 关联分组
    public function group()
    {
    	return $this->hasOne('OrganizerGroup', 'id', 'group_id');
    }
}
