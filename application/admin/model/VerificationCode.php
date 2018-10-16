<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13
 * Time: 16:41
 * Comment: 短信验证码模型
 */

namespace app\admin\model;

class VerificationCode extends BaseModel {

    /**
     * 关联的数据表
     * @var string
     */
    protected $table = 'tb_verification_code';

}