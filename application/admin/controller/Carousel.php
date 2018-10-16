<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18
 * Time: 10:27
 * Comment: 轮播上传控制器
 */
namespace app\admin\controller;

use app\admin\model\Carousel as CarouselModel;
use think\Controller;
use think\Loader;

class Carousel extends BaseController
{

    /**
     * 上传轮播图片
     */
    public function upload()
    {
        //获取表单上传文件
        $id      = request()->param('id');
        $picture = request()->file('picture');
        $title   = request()->param('title');
        $url     = request()->param('url');
        $sort    = request()->param('sort');
        $status  = request()->param('status', '1');

        if (empty($picture) && empty($id)) {
            return json([
                'code'    => '404',
                'message' => '上传文件为空',
            ]);
        }

        if ($picture) {
            //移动到框架应用根目录/public/uploads/目录下
            $info = $picture->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                //成功上传后，获取上传信息
                $path    = str_replace('\\', '/', $info->getSaveName());
                $picture = '/uploads/' . $path;
            }
        }

        $data = [
            'id'      => $id,
            'title'   => $title,
            'url'     => $url,
            'sort'    => $sort,
            'status'  => $status,
            'pubtime' => date('Y-m-d H:i:s'),
        ];

        $validate = Loader::validate('Carousel');

        $result = $validate->scene('upload')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //如果未上传图片则不更新
        if (!empty($picture)) {
            $data['picture'] = $picture;
        }

        if (!empty($id)) {
            $active = new CarouselModel;
            $result = $active->save($data, ['id' => $id]);
        } else {
            $active = new CarouselModel;
            $result = $active->save($data);
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
     * 删除轮播api接口
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete()
    {

        /* 接收客户端提交的数据 */
        $id = request()->param('id/d');

        $validate_data = [
            'id' => $id,
        ];

        $validate = Loader::validate('Carousel');

        $result = $validate->scene('delete')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $delete_result = CarouselModel::where('id', '=', $id)->delete();

        if ($delete_result) {
            return json([
                'code'    => '200',
                'message' => '删除成功',
            ]);
        } else {
            return json([
                'code'    => '404',
                'message' => '删除失败',
            ]);
        }
    }

    /**
     * 轮播详情api接口
     */
    public function detail()
    {

        /* 获取客户端提供的数据 */
        $id = request()->param('id');

        /* 验证 */
        $validate_data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Carousel');

        $result = $validate->scene('detail')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = CarouselModel::where('id', '=', $id)->find();

        if ($detail) {
            return json([
                'code'    => '200',
                'message' => '获取轮播详情成功',
                'data'    => $detail,
            ]);
        }
    }

    /**
     * 轮播列表api接口
     */
    public function carousel_list()
    {
        $page = config('pagination');

        /* 获取客户端提供的数据 */
        $id        = request()->param('id');
        $title     = request()->param('title');
        $url       = request()->param('url');
        $sort      = request()->param('sort');
        $status    = request()->param('status');
        $pub_start = request()->param('pub_start');
        $pub_end   = request()->param('pub_end');
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        /* 验证 */
        $validate_data = [
            'id'        => $id,
            'title'     => $title,
            'url'       => $url,
            'sort'      => $sort,
            'status'    => $status,
            'pub_start' => $pub_start,
            'pub_end'   => $pub_end,
            'page_size' => $page_size,
            'jump_page' => $jump_page,
        ];

        $validate = Loader::validate('Carousel');

        $result = $validate->scene('carousel_list')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 过滤筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($title) {
            $conditions['title'] = ['like', '%' . $title . '%'];
        }

        if ($url) {
            $conditions['url'] = ['like', '%' . $url . '%'];
        }

        if ($sort) {
            $conditions['sort'] = $sort;
        }

        if ($status || $status === 0) {
            $conditions['status'] = $status;
        }

        if ($pub_start && $pub_end) {
            $conditions['pubtime'] = ['between time', [strtotime($pub_start), strtotime($pub_end)]];
        }

        $list = CarouselModel::where($conditions)
            ->paginate($page_size, false, ['page' => $jump_page]);

        return json([
            'code'    => '200',
            'message' => '获取轮播成功',
            'data'    => $list,
        ]);
    }
}
