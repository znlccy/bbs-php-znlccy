<?php

namespace app\admin\model;

use think\Model;

class Team extends BaseModel
{
    protected $table = 'tb_teams';

    /**
     *  关联省份
     */
    public function province()
    {
        return $this->hasOne('Area', 'id', 'province_id')->field('id,name');
    }

    /**
     *  关联城市
     */
    public function city()
    {
        return $this->hasOne('Area', 'id', 'city_id')->field('id,name');
    }

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }
}
