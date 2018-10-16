<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13
 * Time: 13:26
 * Comment: 团队模型
 */

namespace app\index\model;

class Team extends BasisModel {

    /**
     * 管理的数据表
     * @var string
     */
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
