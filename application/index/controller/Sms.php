<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 16:18
 * Comment: 短信验证码控制器
 */
namespace app\index\controller;

use app\index\model\VerificationCode;
use think\Cache;
use app\index\model\Sms as SmsModel;
use think\Controller;
use app\index\model\User as UserModel;
use think\Loader;

class Sms extends BasisController {
    /**
     * 获取验证码api接口
     */
    public function attain() {
        /* 获取客户端提供的数据 */
        $mobile = request()->param('mobile');

        // 验证用户手机号
        $validate_data = [
            'mobile'        => $mobile
        ];

        $validate = Loader::validate('Sms');
        $result = $validate->scene('attain')->check($validate_data);

        if (!$result) {
            return json([
                'code'      => '401',
                'message'   => $validate->getError()
            ]);
        }
        $user_model = new UserModel();
        $user = $user_model->where('mobile', '=', $mobile)->find();


        // 单个用户60秒内不能连续发送
        $verification_code_model = new VerificationCode();
        $smsCode = $verification_code_model->where('mobile', $mobile)->find();
        if(time() - strtotime($smsCode['create_time']) < 60) {
            return json([
                'code' => '404',
                'message' => '操作过于频繁, 请在一分钟后重试'
            ]);
        }

        // 单个用户一天时间最多发送10条
        // 当天日期
        $today = date("Y-m-d");
        $key = $mobile . '-' . $today;
        if (Cache::has($key)) {
            if (Cache::get($key) == 10) {
                return json([
                    'code' => '404',
                    'message' => '对不起,同一用户一天最多只能发送10次验证码'
                ]);
            }
        } else {
            Cache::set($key, 0, new \DateTime('+1 day 00:00:00'));
        }

        $result = json_decode(send_code($mobile), true);

        if ($result['status'] == 'success') {
            return json([
                'code' => '200',
                'message' => '发送成功!'
            ]);
            // 当天该用户的发送次数+1
            Cache::inc($key);
        } else {
            return json([
                'code' => '404',
                'message' => '发送失败!'
            ]);
        }
    }
}