<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 20:02
 * Comment: 论坛模型
 */

namespace app\index\model;

class Forum extends BasisModel {

    /**
     * 自动读取和写入时间
     * @var string
     */
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_forums';
}