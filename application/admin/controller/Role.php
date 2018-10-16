<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23
 * Time: 19:40
 * Comment: 角色控制器
 */
namespace app\admin\controller;


use app\admin\model\RolePermission;
use gmars\rbac\Rbac;
use think\Db;
use think\Loader;
use app\admin\model\Role as RoleModel;
use app\admin\model\AdminRole as AdminRoleModel;
use app\admin\model\RolePermission as RolePermissionModel;

class Role extends BaseController {

    /**
     * 角色管理api接口
     */
    public function role_list() {

        $page = config('pagination');
        /* 获取客户端提供的数据 */
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        $id = request()->param('id');
        $status = request()->param('status');
        $name = request()->param('name');
        $active_start = request()->param('active_start');
        $active_end = request()->param('active_end');

        $role_model = new RoleModel();

        $data = [
            'page_size'      => $page_size,
            'jump_page'      => $jump_page,
            'id'             => $id,
            'status'         => $status,
            'name'           => $name,
            'active_start'   => $active_start,
            'active_end'     => $active_end
        ];
        $validate = Loader::validate('Role');

        $result   = $validate->scene('role_list')->check($data);
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

        if ($name) {
            $conditions['name'] = ['like', '%' . $name . '%'];
        }

        if ($active_start || $active_end) {
            $conditions['create_time'] = ['between time', [$active_start, $active_end]];
        }

        $role = $role_model->where($conditions)
            ->paginate($page_size, false, ['page' => $jump_page]);

        return json([
            'code'      => '200',
            'message'   => '获取角色名称成功',
            'data'      => $role
        ]);
    }

    /**
     * 添加角色api接口
     */
    public function add() {
        /* 获取客户端提供的 */
        $id = request()->param('id');
        $name = request()->param('name');
        $description = request()->param('description');
        $status = request()->param('status',1);
        $sort = request()->param('sort_num', 0);

        $data = [
            'id'            => $id,
            'name'          => $name,
            'description'   => $description,
            'status'        => $status,
            'sort_num'      => $sort
        ];

        $validate = Loader::validate('Role');

        $result   = $validate->scene('add')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $rbac = new Rbac();
        /* 封装用户数据为数组 */
        $insert_data = [
            'name' => $name,
            'status' => $status,
            'description' => $description,
            'sort_num' => $sort,
            'create_time' => date('Y-m-d H:i:s', time()),
            'parent_id' => 0
        ];

        $update_data = [
            'id'    => $id,
            'name' => $name,
            'status' => $status,
            'description' => $description,
            'sort_num' => $sort,
            'update_time' => date('Y-m-d H:i:s', time()),
            'parent_id' => 0
        ];

        if (!empty($id)) {
            $update_result = $rbac->editRole($update_data);
            if($update_result) {
                return json([
                    'code'      => '200',
                    'message'   => '更新角色成功'
                ]);
            }
        } else {
            $insert_result = $rbac->createRole($insert_data);
            if($insert_result) {
                return json([
                    'code'      => '200',
                    'message'   => '添加角色成功'
                ]);
            }
        }
    }

    /**
     * 删除角色api接口
     */
    public function delete() {

        $rbac = new Rbac();
        $role_model = new RoleModel();
        $admin_role_model = new AdminRoleModel();
        $role_permission_model = new RolePermissionModel();

        /* 获取客户端提交的数据 */
        $role_id = request()->param('id');

        $data = [
            'id'        => $role_id
        ];

        $validate = Loader::validate('Role');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $role = $role_model->where('id', '=', $role_id)
            ->find();
        if ($role == null) {
            return json(['code' => '404', 'message' => '查询数据为空', 'data' => null]);
        } else {
            if ($role_id == 1) {
                return json([
                    'code'      => '401',
                    'message'   => '超级管理员不允许删除'
                ]);
            } else {
                $role_model->where('id', '=', $role_id)->delete();
                $admin_role_model->where('role_id', '=', $role_id)->delete();
                $role_permission_model->where('role_id', '=', $role_id)->delete();
                return json([
                    'code'      => '200',
                    'message'   => '删除角色成功'
                ]);
            }
        }
    }

    /**
     * 角色详情api接口
     */
    public function detail() {
        /* 获取客户端提交过来的角色主键 */
        $roleid = request()->param('id');

        $data = [
            'id'    => $roleid
        ];

        $validate = Loader::validate('Role');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $role = Db::table('tb_role')
            ->where('id', '=', $roleid)
            ->find();
        return json([
            'code'      => '200',
            'message'   => '查询角色成功',
            'data'      => $role
        ]);

    }
    /**
     * 分配角色权限api接口
     */
    public function assign_role_permission() {

        /* 获取客户端提交过来的角色主键 */
        $role_id = request()->param('id');
        $permission_id = request()->param('permission_id/a');

        $role_permission_model = new RolePermissionModel();

        $data = [
            'id'            => $role_id,
            'permission_id' => $permission_id
        ];

        $validate = Loader::validate('Role');

        $result   = $validate->scene('assign_role_permission')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $role = $role_permission_model->where('role_id', '=', $role_id)->find();
        //如果有了就更新没有就添加
        if ($role) {
            $delete = $role_permission_model
                ->where('role_id', '=', $role_id)
                ->delete();

            $rbacObj = new Rbac();
            $assign_result = $rbacObj->assignRolePermission($role_id, $permission_id);
            if ($assign_result) {
                return json([
                    'code'      => '200',
                    'message'   => '分配权限成功'
                ]);
            } else {
                return json([
                    'code'      => '401',
                    'message'   => '分配权限失败'
                ]);
            }
        } else {
            $rbacObj = new Rbac();
            $assign_result = $rbacObj->assignRolePermission($role_id, $permission_id);
            if ($assign_result) {
                return json([
                    'code'      => '200',
                    'message'   => '分配权限成功'
                ]);
            } else {
                return json([
                    'code'      => '401',
                    'message'   => '分配权限失败'
                ]);
            }
        }

    }

    /**
     * 获取角色权限api接口
     */
    public function get_role_permission() {
        /* 获取客户端提供的数据 */
        $id = request()->param('id');

        $role_permission_model = new RolePermissionModel();

        //验证数据
        $data = [
            'id'    => $id
        ];

        //实例化验证器
        $validate = Loader::validate('Role');
        $result   = $validate->scene('get_role_permission')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $user_role = $role_permission_model-> where('role_id', '=', $id)-> select();

        $user_role_list = [];
        foreach ( $user_role as $value ){
            $user_role_list[] = $value['permission_id'];
        }

        $role_data = Db::table('tb_permission') -> select();
        for ( $i = 0; $i < count($role_data); $i++ ){
            if (in_array($role_data[$i]['id'], $user_role_list)) {
                $role_data[$i]['role_status'] = 1;
            } else {
                $role_data[$i]['role_status'] = 0;
            }
        }

        $role_data = $this->buildTrees($role_data, 0);

        if ($role_data) {
            return json([
                'code'      => '200',
                'message'   => '角色信息',
                'data'      => $role_data
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '数据库中不存在',
            ]);
        }
    }

    public function buildTrees($data, $pId)
    {
        $tree_nodes = array();
        foreach($data as $k => $v)
        {
            if($v['pid'] == $pId)
            {
                $v['child'] = $this->buildTrees($data, $v['id']);
                $tree_nodes[] = $v;
            }
        }
        return $tree_nodes;
    }
}