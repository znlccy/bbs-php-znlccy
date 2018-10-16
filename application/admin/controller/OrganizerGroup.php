<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use app\admin\model\OrganizerGroup as Group;

class OrganizerGroup extends BaseController
{
    /**
     * 获取投资机构分组列表
     *
     */
    public function index()
    {
        $groups = Group::all();
        if (!empty($groups)) {
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $groups]);
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }
    }

    /**
     * 分组新建更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id   = request()->param('id');
        $name = request()->param('name');

        /* 验证规则 */
        $rule = [
            'name' => 'require|max:60',
        ];
        $data = [
            'name' => $name,
        ];
        $field = [
            'name' => '组织机构标题',
        ];

        $validate = Loader::validate('OrganizerGroup');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        if (!empty($id)) {
            // 更新
            $group = new Group;
            $result    = $group->save($data, ['id' => $id]);
        } else {
            $group = new Group;
            $result = $group->save($data);
        }

        if ($result) {
            $data = ['code' => '200', 'message' => '保存成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '保存失败!'];
            return json($data);
        }
    }

    /**
     * 分组详细
     */
    public function detail()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('OrganizerGroup');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = Group::where('id', $id)->find();
        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除指定资源
     *
     */
    public function delete()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Organizer');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $result = Group::destroy($id);
        if ($result) {
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败'];
            return json($data);
        }
    }

    /**
     * 角色下拉列表
     */
    public function select() {
        //实例化模型
        $role_model = new Group();
        $roles = $role_model->field('id,name')->select();
        if (!empty($roles)) {
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $roles]);
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }
    }

}
