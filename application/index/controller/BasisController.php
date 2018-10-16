<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 16:47
 * Comment: 实现基础控制器
 */
namespace app\index\controller;

use think\Controller;
use think\Hook;
use think\Request;
use think\Session;

class BasisController extends Controller {

    // 用户id
    protected $user_id = 0;
    // 当前控制器名称
    protected $controller;
    // 当前操作名称
    protected $action;
    // 无需验证方法
    protected  $except_auth = [
        'User' => ['login', 'register', 'recover_pass', 'change_pass'],
        'Service' => ['more', 'index', 'detail', 'category'],
        'Review' => ['index', 'detail'],
        'Active' => ['index', 'introduce'],
        'Index' => ['index'],
        'Verify' => ['attain'],
        'Sms' => ['attain'],
        'Competition' => ['introduce'],
        'Investor' => ['index'],
        'News' => ['index'],
        'Team' => ['index'],
        'Tutor' => ['index'],
        'Forum' => ['index', 'introduce'],
        'Sp' => ['index'],
        'Image' => ['upload']
    ];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->controller =  $request->controller();
        $this->action =  $request->action();

        //过滤不需要登陆的行为
        if (isset($this->except_auth[$this->controller]) && in_array($this->action, $this->except_auth[$this->controller])) {
            return true;
        } else {
            // 判断用户登录状态
            if (session('?user')) {
                /* 验证token */
                // 获取客户端传来的token
                $client_token = $request->header('access-token');
                if ( !(!empty($client_token) && $this->checkToken($client_token)) ) {
                    return $this->returnMsg(401, '请先登录系统');
                }
            } else {
                return $this->returnMsg(401, '请先登录系统');
            }
        }
    }

    /**
     * Token验证
     * @param $client_token
     */
    public function checkToken($client_token) {
        if (Session::has('access_token')){
            // 获取服务端存储的token
            $server_token = Session::get('access_token');
            if ($server_token == $client_token) {
                return true;
            }
        }
        return false;
    }

    public function listern() {
        Hook::listen('response_send');
    }


    /**
     * @param $code
     * @param string $msg
     * @param array $data
     */
    public function returnMsg($code, $msg = '', $data = []) {

        $return_data['code'] = $code;
        $return_data['message'] = $msg;
        if ( !empty($data) ){
            $return_data['data'] = $data;
        }

        echo json_encode($return_data);
        die;
    }

}