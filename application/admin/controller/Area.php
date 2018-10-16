<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Area as AreaModel;

class Area extends BaseController
{
    /**
     * 省市联动列表
     *
     */
    public function index()
    {
        $level = request()->param('level/d');
        $id = request()->param('id/d');
        switch ($level) {
            case 1:
                $area = AreaModel::where(['level' => $level])->field('id,name')->order('id')->select();
                break;
            case 2:
                if (empty($id)) {
                    return json(['code' => '404', 'message' => '获取地区列表失败']);
                }
                $area = AreaModel::where(['level' => $level, 'top_id' => $id])->field('id,name')->order('id')->select();
                break;
            default:
                $area = AreaModel::where(['level' => 1])->field('id,name')->order('id')->select();
                break;
        }
        if (!empty($area)) {
            return json(['code' => '200', 'message' => '地区列表获取成功', 'data' => $area]);
        } else {
            return json(['code' => '404', 'message' => '获取地区列表失败']);
        }
    }


}
