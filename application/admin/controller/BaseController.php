<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27
 * Time: 10:27
 * Comment: 后台controller基础类控制器
 */
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use gmars\rbac\Rbac;

class BaseController extends Controller
{
    // 用户id
    protected $user_id = 0;
    // 当前访问权限名称
    protected $permission;
    // 当前模块名称
    protected $module;
    // 当前控制器名称
    protected $controller;
    // 当前操作名称
    protected $action;
    // 无需验证方法
    protected  $except_auth = [
        'Admin' => ['login', 'account_login', 'role', 'mobile_login'],
        'Information' => ['publisher'],
        'Service' => ['category'],
        'Image' => ['upload'],
        'Area' => ['index'],
        'Competition'=> ['checked_team'],
        'OrganizerGroup' => ['select']
    ];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->module = $request->module();
        $this->controller = $request->controller();
        $this->action = $request->action();
//        $this->permission = strtolower('/'. $this->module . '/' . $this->controller . '/' . $this->action);
        $this->permission = preg_replace('/\?.*$/U', '', $request->url());
        //过滤不需要登陆的行为
        if (isset($this->except_auth[$this->controller]) && in_array($this->action, $this->except_auth[$this->controller])) {
            return true;
        } else {
            // 判断用户登录状态
            if (session('?admin')) {
                /* 验证token */
                // 获取客户端传来的token
                $client_token = $request->header('access-token');
                if ( !(!empty($client_token) && $this->checkToken($client_token)) ) {
                	return $this->returnMsg(401, '请先登录系统'); // session('admin_token')
                }
                $this->user_id = session('admin.id'); // 从session中获取  session('admin.id')
                //检查管理员操作权限
                $this->checkPriv();
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
    	if (Session::has('admin_token')){
    		// 获取服务端存储的token
    		$server_token = Session::get('admin_token');
    		if ($server_token == $client_token) {
    			return true;
    		}
    	}
    	return false;
    }

    /**
     * 权限验证
     */
    public function checkPriv()
    {
        //无需验证的操作
        $uneed_check = ['login', 'logout'];
        if ($this->controller == 'Index') {
            //后台首页控制器无需验证
            return true;
        } elseif (in_array($this->action,$uneed_check)) {
            return true;
        } else {
            $rbacObj = new Rbac();
            $rbacObj->cachePermission($this->user_id);
            if (!$rbacObj->can($this->permission)) {
                return $this->returnMsg(404, '没有操作权限');
            }
        }
    }

    /**
     * @param $code
     * @param string $msg
     * @param array $data
     */
    public function returnMsg($code, $msg = '', $data = []) {

        $return_data['code'] = $code;
        $return_data['message'] = $msg;
        if (!empty($data)) {
            $return_data['data'] = $data;
        }

        echo json_encode($return_data);
        die;
    }

}
