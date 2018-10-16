<?php

namespace app\admin\model;

use think\Model;

class Organizer extends BaseModel
{
    protected $table = 'tb_organizers';

    protected $autoWriteTimestamp = 'timestamp';

    // 关联分组
    public function group()
    {
    	return $this->hasOne('OrganizerGroup', 'id', 'group_id');
    }


}
