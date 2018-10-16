<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use app\index\model\OrganizerGroup as Group;

class OrganizerGroup extends BasisController
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

}
