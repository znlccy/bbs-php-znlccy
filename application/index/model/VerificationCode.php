<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13
 * Time: 16:41
 * Comment: 短信验证码模型
 */

namespace app\index\model;

class VerificationCode extends BasisModel {

    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_verification_code';

    protected $autoWriteTimestamp = 'datetime';

}