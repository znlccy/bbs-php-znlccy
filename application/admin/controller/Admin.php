<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 19:26
 * Comment：用户控制器
 */
namespace app\admin\controller;

use gmars\rbac\Rbac;
use think\Loader;
use think\Session;
use think\Validate;
use app\admin\model\Admin as AdminModel;
use app\admin\model\VerificationCode as VerificationCodeModel;
use app\admin\model\AdminRole  as AdminRoleModel;
use app\admin\model\Role as RoleModel;

class Admin extends BaseController {

    /**
     * 一般登录api接口
     */
    public function mobile_login() {

        /* 获取客户端提供的数据 */
        $mobile = request()->param('mobile');
        $code = request()->param('code');

        /* 验证规则 */
        $validate_data = [
            'mobile'     => $mobile,
            'code'       => $code,
        ];

        //实例化验证器
        $validate = Loader::validate('Admin');
        $result   = $validate->scene('mobile_login')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //实例化模型
        $admin_model = new AdminModel();
        $admin = $admin_model->where('mobile', '=', $mobile)
            ->where('status', '=','1')
            ->find();

        if ( empty($admin) ){
            return json(['code' => '402', 'message' => '登录失败']);
        }

        //比对短信验证码
        $verification_code_model = new VerificationCodeModel();
        $sms_code = $verification_code_model->where('mobile', '=', $mobile)->find();

        if (empty($sms_code)) {
            return json(['code' => '404', 'message' => '该手机还没有生成注册码']);
        }

        if (strtotime($sms_code['expiration_time']) - time() < 0) {
            return json(['code' => '405', 'message' => '验证码已经过期']);
        }

        if ($sms_code['code'] != $code) {
            return json(['code' => '407', 'message' => '登录失败']);
        }

        //更新用户登陆记录
        $data = [
            'login_time'     => date('Y-m-d H:i:s',time()),
            'login_ip'       => request()->ip(),
            'authentication' => 1
        ];

        //更新用户登录数据
        $result = $admin_model->where('mobile', '=', $mobile)->update($data);

        if ($result) {
            Session::set('admin',$admin);
            $token = general_token($mobile, time());
            Session::set('admin_token', $token);

            // 验证码使用一次后立即失效
            $verification_code_model->where('mobile', $mobile)->update(['create_time' => date('Y-m-d H:i:s', time())]);
            return json(['code' => '200', 'message' => '登录成功', 'admin_token' => $token, 'real_name' => $admin['real_name']]);
        }else{
            return json(['code' => '408', 'message' => '登录失败']);
        }
    }

    /**
     * 账号密码登录api接口
     */
    public function account_login() {

        //接收参数
        $mobile = request()->param('mobile');
        $password = request()->param('password');

        /* 验证规则 */
        $validate_data = [
            'mobile'     => $mobile,
            'password'   => $password,
        ];

        //实例化验证器
        $validate = Loader::validate('Admin');
        $result   = $validate->scene('account_login')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 进行逻辑处理 */

        //数据库实例化
        $admin_model = new AdminModel();
        $admin = $admin_model->where('mobile', '=', $mobile)
            ->where('password', '=', md5($password))
            ->where('status', '=', '1')
            -> find();

        //不存在该用户名
        if (empty($admin) ){
            return json(['code' => '402', 'message' => '登录失败']);
        }

        //检查是否实名
        if ( !($admin['authentication'] === 1) ){
            $authentication_data = ['mobile'=>$mobile];
            return json(['code' => '302', 'message' => '需进行手机真实性认证', 'data' => $authentication_data ]);
        }

        Session::set('admin',$admin);
        $token = general_token($mobile, $password);
        Session::set('admin_token', $token);
        return json(['code' => '200', 'message' => '登录成功', 'admin_token' => $token, 'real_name' => $admin['real_name']]);

    }

    /**
     * 添加用户处理api接口
     */
    public function assign_user_role() {

        //实例化权限控制器
        $rbac = new Rbac();

        /* 接收客户端提供的数据 */
        $id = request()->param('id');
        $mobile = request()->param('mobile');
        $password = request()->param('password');
        $confirm_pass = request()->param('confirm_pass');
        $real_name = request()->param('real_name');
        $status = request()->param('status');
        $role_id = request()->param('role_id/a');

        $admin_model = new AdminModel();
        $admin_role_model = new AdminRoleModel();
        $rule = [
            'id'            => 'number',
            'password'      => 'alphaDash|length:8,25',
            'confirm_pass'  => 'alphaDash|length:8,25|confirm:password',
            'real_name'     => 'require|max:40',
            'status'        => 'require|number',
            'role_id'       => 'require|array'
        ];

        //如果是更新修改passwrod验证规则
        if (empty($id)){
            $rule_add = [
                'mobile'        => 'require|length:11|unique:tb_admin',
                'password'      => 'require|alphaDash|length:8,25',
                'confirm_pass'  => 'require|alphaDash|length:8,25|confirm:password',
            ];
            $rule = array_merge($rule, $rule_add);
        }

        $message = [
            'id'            => 'ID',
            'mobile'        => '手机号',
            'password'      => '密码',
            'confirm_pass'  => '确认密码',
            'real_name'     => '姓名',
            'status'        => '状态',
            'role_id'       => '角色ID',
        ];

        $validate_data = [
            'id'            => $id,
            'mobile'        => $mobile,
            'password'      => $password,
            'confirm_pass'  => $confirm_pass,
            'real_name'     => $real_name,
            'status'        => $status,
            'role_id'       => $role_id
        ];

        $validate = new Validate($rule, [], $message);
        $result = $validate->check($validate_data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 封装数据 */
        $user_data = [
            'real_name'      => $real_name,
            'status'         => $status,
            'create_ip'      => request()->ip()
        ];

        if ( empty($id) ){
            $data_add = [
                'create_time'  => date('Y-m-d H:i:s'),
                'mobile'       => $mobile,
                'password'     => md5($password)
            ];
            $user_data = array_merge($user_data, $data_add);
        }else{
            if ( !empty($password) && !empty($confirmpass) ){
                $data_add = [
                    'password'  => md5($password)
                ];
                $user_data = array_merge($user_data, $data_add);
            }
        }

        $rbacObj = new Rbac();
        if (!empty($id)) {

            $update_result = $admin_model->where('id','=', $id)->update($user_data);
            $admin_role_model->where('user_id',$id)->delete();
            $result = $rbacObj->assignUserRole($id, $role_id);

            if ($result) {
                return json([
                    'code'      => '200',
                    'message'   => '更新成功'
                ]);
            }
        } else {
            /* 添加用户表之后，再添加用户角色表 */
            $uid = $admin_model->insertGetId($user_data);

            /* 添加用户角色表 */
            if ($uid) {
                /* 用户添加成功后，添加用户角色表 */
                $result = $rbacObj->assignUserRole($uid, $role_id);

                if ($result) {
                    return json([
                        'code'      => '200',
                        'message'   => '添加成功'
                    ]);
                } else {
                    return json([
                        'code'      => '403',
                        'message'   => '添加失败'
                    ]);
                }
            } else {
                return json([
                    'code'      => '403',
                    'message'   => '添加失败'
                ]);
            }
        }

    }

    /**
     * 用户管理api接口
     */
    public function admin_list() {
        $page = config('pagination');
        /* 获取客户端提供的数据 */
        $page_size = request()->param('page_size',$page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page',$page['JUMP_PAGE']);

        //过滤参数
        $id = request()->param('id');
        $status = request()->param('status/d');
        $real_name = request()->param('real_name');
        $register_start = request()->param('register_start');
        $register_end = request()->param('register_end');
        $login_start = request()->param('login_start');
        $login_end = request()->param('login_end');
        $login_ip = request()->param('login_ip');
        $create_ip = request()->param('create_ip');

        $validate_data = [
            'page_size'      => $page_size,
            'jump_page'      => $jump_page,
            'id'             => $id,
            'status'         => $status,
            'real_name'      => $real_name,
            'register_start' => $register_start,
            'register_end'   => $register_end,
            'login_start'    => $login_start,
            'login_end'      => $login_end,
            'login_ip'       => $login_ip,
            'create_ip'      => $create_ip
        ];

        //实例化验证器
        $validate = Loader::validate('Admin');
        $result = $validate->scene('admin_list')->check($validate_data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //过滤条件
        $conditions = [];
        if ($id) {
            $conditions['id'] = $id;
        }

        if ($status || $status === 0) {
            $conditions['status'] = $status;
        }

        if ($real_name) {
            $conditions['real_name'] = ['like', '%' . $real_name . '%'];
        }

        if ($register_start && $register_end) {
            $conditions['create_time'] = ['between time', [$register_start, $register_end]];
        }

        if ($login_start && $login_end) {
            $conditions['login_time'] = ['between time', [$login_start, $login_end]];
        }

        if ($login_ip) {
            $conditions['login_ip'] = ['like', '%' . $login_ip . '%'];
        }

        if ($create_ip) {
            $conditions['create_ip'] = ['like', '%' . $create_ip . '%'];
        }

        $admin_model = new AdminModel();
        $admin_data = $admin_model->where($conditions)
            ->with(['role' => function($query){
                $query->withField("pivot");
            }])->paginate($page_size, false, ['page' => $jump_page]);

        return json([
            'code'      => '200',
            'message'   => '获取列表成功',
            'data'      => $admin_data
        ]);
    }

    /**
     * 用户详情api接口
     */
    public function detail() {
        /* 获取客户端提供的数据 */
        $id = request()->param('id');

        //验证的数据
        $validate_data = [
            'id'    => $id
        ];

        //实例化验证器
        $validate = Loader::validate('Admin');
        $result = $validate->scene('detail')->check($validate_data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $admin_model = new AdminModel();
        $admin_data = $admin_model->where('id', '=', $id)
            ->with(['role' => function($query){
            $query->withField("pivot");
        }])->find();

        if ($admin_data) {
            return json([
                'code'      => '200',
                'message'   => '用户信息',
                'data'      => $admin_data
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '数据库中不存在',
            ]);
        }

    }

    /**
     * 角色下拉列表
     */
    public function role() {
        //实例化模型
        $role_model = new RoleModel();
        $roles = $role_model->where('status = 1')->field('id,name')->select();
        if (!empty($roles)) {
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $roles]);
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }
    }

    /**
     * 获取管理员信息api接口
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info() {
        //获取管理员session内容
        $admin = Session::get('admin');

        $id = $admin['id'];

        //实例化模型
        $admin_model = new AdminModel();
        if ($id) {
            $admin = $admin_model->where('id', '=', $id)->find();

            return json([
                'code'      => '200',
                'message'   => '获取管理员信息成功',
                'data'      => $admin
            ]);
        } else {
            return json([
                'code'      => '401',
                'message'   => '获取管理员信息失败'
            ]);
        }
    }

    /**
     * 管理员修改密码api接口
     */
    public function change_password() {

        //接收客户端提交的数据
        $password = request()->param('password');
        $confirm_pass= request()->param('confirm_pass');

        //实例化模型
        $admin_model = new AdminModel();

        //验证数据
        $validate_data = [
            'password'          => $password,
            'confirm_pass'      => $confirm_pass
        ];

        //实例化验证器
        $validate = Loader::validate('Admin');
        $result = $validate->scene('change_password')->check($validate_data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $admin = Session::get('admin');
        $id = $admin['id'];

        if ($id) {
            $update_data = [
                'password'      => md5($password)
            ];
            $admin_model->where('id', '=', $id)->update($update_data);

            return json([
                'code'      => '200',
                'message'   => '更新密码成功'
            ]);
        } else {
            return json([
                'code'      => '401',
                'message'   => '更新密码失败'
            ]);
        }
    }


}