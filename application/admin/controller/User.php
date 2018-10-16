<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 13:29
 * Comment: 用户管理控制器
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\admin\model\User as UserModel;
use think\Loader;

class User extends BaseController {

    /**
     * 用户列表api接口
     */
    public function user_list() {
        $page = config('pagination');
        /* 接收客户端提供的参数 */
        $page_size = request()->param('page_size',$page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);
        $id = request()->param('id');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $login_start = request()->param('login_start');
        $login_end = request()->param('login_end');

        //验证的数据
        $data = [
            'page_size'     => $page_size,
            'jump_page'     => $jump_page,
            'id'            => $id,
            'status'        => $status,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'login_start'   => $login_start,
            'login_end'     => $login_end
        ];

        $validate = Loader::validate('User');

        $result   = $validate->scene('user_list')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($status || $status === 0) {
            $conditions['status'] = $status;
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time',[$create_start, $create_end]];
        }

        if ($login_start && $login_end) {
            $conditions['login_time'] = ['between time',[$login_start, $login_end]];
        }

        $user_model = new UserModel();
        $user = $user_model->where($conditions)
            ->paginate($page_size, false, ['page' => $jump_page]);
        return json([
            'code'      => '200',
            'message'   => '获取用户列表成功',
            'data'      => $user
        ]);
    }

    /**
     * 创建用户api接口
     */
    public function create() {
        $id = request()->param('id');
        $mobile = request()->param('mobile');
        $password = request()->param('password');
        $confirm_pass = request()->param('confirm_pass');
        $username = request()->param('username');
        $email = request()->param('email');
        $company = request()->param('company');
        $career = request()->param('career');
        $status = request()->param('status');
        $occupation = request()->param('occupation');

        $user_model = new UserModel();

        $data = [
            'id'            => $id,
            'mobile'        => $mobile,
            'password'      => $password,
            'confirm_pass'  => $confirm_pass,
            'username'      => $username,
            'email'         => $email,
            'status'        => $status,
            'company'       => $company,
            'career'        => $career,
            'occupation'    => $occupation
        ];

        $validate = Loader::validate('User');
        //如果是更新修改passwrod验证规则
        if (empty($id)){
            $result   = $validate->scene('create')->check($data);
            if (!$result) {
                return json(['code' => '401', 'message' => $validate->getError()]);
            }
        } else {
            $validate_data = [
                'id'            => $id,
                'username'      => $username,
                'email'         => $email,
                'company'       => $company,
                'career'        => $career,
                'status'        => $status,
                'occupation'    => $occupation
            ];
            $result   = $validate->scene('update')->check($validate_data);
            if (!$result) {
                return json(['code' => '401', 'message' => $validate->getError()]);
            }            
        }

        $insert_data = [
            'username'      => $username,
            'email'         => $email,
            'company'       => $company,
            'career'        => $career,
            'status'        => $status,
            'occupation'    => $occupation,
        ];

        if (empty($id)){
            $data_add = [
                'register_time' => date('Y-m-d H:i:s'),
                'mobile'        => $mobile,
                'password'      => md5($password)
            ];
            $insert_data = array_merge($insert_data, $data_add);
        }else{
            if (!empty($password) && !empty($confirm_pass)){
                $insert_data = [
                    'username'      => $username,
                    'email'         => $email,
                    'company'       => $company,
                    'career'        => $career,
                    'occupation'    => $occupation,
                ];
            }
        }

        if (!empty($id)) {

            $update_result = Db::table('tb_user')->where('id','=', $id)
                ->update($insert_data);

            if ($update_result) {
                return json([
                    'code'      => '200',
                    'message'   => '更新用户成功'
                ]);
            }else{
                return json([
                    'code'      => '404',
                    'message'   => '更新用户失败'
                ]);
            }
        } else {
            $insert_result = Db::table('tb_user')->insertGetId($insert_data);

            if ($insert_result) {
                return json([
                    'code'      => '200',
                    'message'   => '添加用户成功',
                    'id'        => $insert_result
                ]);
            }else{
                return json([
                    'code'      => '404',
                    'message'   => '添加用户失败'
                ]);
            }
        }

    }

    /**
     * 获取用户详情api接口
     */
    public function detail() {
        //获取前端提供的数据
        $id = request()->param('id');
        $user_model = new UserModel();
        /* 验证 */
        $data = [
            'id'        => $id
        ];

        $validate = Loader::validate('User');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $user = $user_model->where('id', '=', $id)->find();

        if ($user) {
            return json([
                'code'      => '200',
                'message'   => '获取用户成功',
                'data'      => $user
            ]);
        }
    }

    /**
     * 删除用户api接口
     */
    public function delete() {

        //获取前端提供的数据
        $id = request()->param('id');
        $user_model = new UserModel();

        /* 验证 */
        $data = [
            'id'        => $id
        ];

        $validate = Loader::validate('User');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $delete_result = $user_model->where('id', '=', $id)->delete();

        if ($delete_result) {
            return json([
                'code'      => '200',
                'message'   => '删除用户成功'
            ]);
        }else{
            return json([
                'code'      => '404',
                'message'   => '删除用户失败'
            ]);
        }
    }

}