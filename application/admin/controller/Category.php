<?php

namespace app\admin\controller;

use app\admin\model\Category as CategoryModel;
use think\Controller;
use think\Loader;
use think\Validate;

class Category extends BaseController
{
    /**
     * 显示服务分类列表
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $page = config('pagination');
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        $category = CategoryModel::order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        /* 验证 */
        $data = [
            'page_size' => $page_size,
            'jump_page' => $jump_page,
        ];

        $validate = Loader::validate('Category');

        $result = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 返回数据 */
        return json([
            'code'    => '200',
            'message' => '获取类目列表成功',
            'data'    => $category,
        ]);
    }

    /**
     * 类目编辑.
     *
     * @return \think\Response
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id   = request()->param('id');
        $name = request()->param('name');

        /* 验证 */
        $data = [
            'category_name' => $name,
        ];

        $validate = Loader::validate('Category');

        $result = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (empty($id)) {
            $category = new CategoryModel;
            $result   = $category->save($data);
        } else {
            $category = new CategoryModel;
            $result   = $category->save($data, ['id' => $id]);
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
     * 类目详情
     */
    public function detail()
    {
        $id = request()->param('id');

        /* 验证 */
        $data = [
            'id' => $id,
        ];

        $validate = Loader::validate('Category');

        $result = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = CategoryModel::where('id', $id)->find();

        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }
}
