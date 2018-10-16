<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 16:05
 * Comment: 权限控制器
 */
namespace app\admin\controller;

use gmars\rbac\Tree;

use gmars\rbac\Rbac;
use think\Session;
use think\Loader;
use app\admin\model\AdminRole as AdminRoleModel;
use app\admin\model\Permission as PermissionModel;
use think\Controller;

class Permission extends Controller {

    public $treeList = array();

    /**
     * 权限节点api接口
     */
    public function node() {

        $admin_role_model = new AdminRoleModel();

        /* 获得权限节点 */
        $admin = Session::get('admin');
        $id = $admin['id'];
        $node = $admin_role_model->alias('ar')
            -> field('distinct tp.id, user_id, permission_id, name, path, pid, level, icon')
            ->where('level', '<>', '3')
            ->where('ar.user_id', '=', $id)
            ->join('tb_role_permission rp','ar.role_id = rp.role_id')
            ->join('tb_permission tp', 'tp.id = rp.permission_id')
            ->select();

//        dump($node);

        $tree = $this->buildTrees($node, 0);

        return json([
            'code'      => '200',
            'message'   => '获取权限节点成功',
            'data'      => $tree
        ]);
    }

    /**
     * 添加权限api接口     */
    public function add() {

        /* 获取客户端提交的数据 */
        $id = request()->param('id');
        $name = request()->param('name');
        $status = request()->param('status');
        $description = request()->param('description');
        $path = request()->param('path');
        $sort = request()->param('sort');
        $icon = request()->param('icon');
        $level = request()->param('level');
        $pid = request()->param('pid');


        $data = [
            'id'            => $id,
            'name'          => $name,
            'status'        => $status,
            'description'   => $description,
            'path'          => $path,
            'sort'          => $sort,
            'icon'          => $icon,
            'level'         => $level,
            'pid'           => $pid
        ];

        $validate = Loader::validate('Permission');

        $result   = $validate->scene('add')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $rbac = new Rbac();
        if (!empty($id)) {
            $update_data = [
                'id'            => $id,
                'name'          => $name,
                'status'        => $status,
                'description'   => $description,
                'path'          => $path,
                'sort'          => $sort,
                'icon'          => $icon,
                'level'         => $level,
                'pid'           => $pid,
//                'create_time'   => date('Y-m-d H:i:s', time())
                'create_time'   => time()
            ];
            $update_result = $rbac->editPermission($update_data);
            if ($update_result) {
                return json([
                    'code'      => '200',
                    'message'   => '更新权限成功'
                ]);
            }
        } else {
            $data = [
                'name'          => $name,
                'status'        => $status,
                'description'   => $description,
                'path'          => $path,
                'sort'          => $sort,
                'icon'          => $icon,
                'level'         => $level,
                'pid'           => $pid,
//                'create_time'   => date('Y-m-d H:i:s', time())
                'create_time'   => time()
            ];
            $add_result = $rbac->createPermission($data);
            if ($add_result) {
                return json([
                    'code'      => '200',
                    'message'   => '添加权限成功'
                ]);
            }
        }
    }

    /**
     * 权限管理api接口
     */
    public function node_list() {

        $page = config('pagination');
        /* 接收客户端发送的数据 */
        $id = request()->param('id');
        $name = request()->param('name');
        $sort = request()->param('sort');
        $level = request()->param('level');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $description = request()->param('description');
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        $permission_model = new PermissionModel();

        $data = [
            'id'            => $id,
            'name'          => $name,
            'sort'          => $sort,
            'level'         => $level,
            'status'        => $status,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'description'   => $description,
            'page_size'      => $page_size,
            'jump_page'      => $jump_page
        ];

        $validate = Loader::validate('Permission');

        $result   = $validate->scene('node_list')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /*组合条件过滤*/
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($name) {
            $conditions['name'] = ['like', '%' . $name .'%'];
        }

        if ($sort) {
            $conditions['sort'] = $sort;
        }

        if ($level) {
            $conditions['level'] = $level;
        }

        if ($status || $status === 0) {
            $conditions['status'] = $status;
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time', [$create_start, $create_end]];
        }

        if ($description) {
            $conditions['description'] = ['like', '%' . $description . '%'];
        }

        $data = $permission_model->where($conditions)->paginate($page_size, false, ['page' => $jump_page]);

        $permission = $this->getTree($data,0,0);

        return json([
            'code'      => '200',
            'message'   => '获得权限列表成功',
            'data'      => $data
        ]);

    }

    /**
     * 删除权限api接口
     */
    public function delete() {

        /* 获取客户端提交的数据 */
        $id = request()->param('id');

        $permission_model = new PermissionModel();
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Permission');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $result = $permission_model->where('id', '=', $id)->delete();

        if ($result) {
            return json([
                'code'      => '200',
                'message'   => '删除成功'
            ]);
        } else {
            return json([
                'code'      => '403',
                'message'   => '删除失败,数据库中可能不存在'
            ]);
        }

    }

    /**
     * 权限获取api接口
     */
    public function detail() {

        /* 获取客户端提供的数据 */
        $id = request()->param('id');

        $permission_model = new PermissionModel();
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Permission');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = $permission_model->where('id','=', $id)->find();

        if ($detail) {
            return json([
                'code'      => '200',
                'message'   => '获取权限详情成功',
                'data'      => $detail
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '获取权限详情失败，权限不存在'
            ]);
        }
    }

    /* 实现无限极分类 */
    private function getTree($arr,$pid,$step){
        global $tree;
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                $flg = str_repeat('└―',$step);
                $val['name'] = $flg.$val['name'];
                $tree[] = $val;
                $this->getTree($arr , $val['id'] ,$step+1);
            }
        }
        return $tree;
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