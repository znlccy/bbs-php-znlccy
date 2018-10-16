<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23
 * Time: 19:40
 * Comment: 活动回顾控制器
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Validate;
use think\Loader;
use app\admin\model\Review as ReviewModel;

class Review extends BaseController
{

    /**
     * 活动回顾api接口
     */
    public function index()
    {
        /* 获取客户端提供的数据 */
        $page_size = request()->param('page_size/d', 8);
        $jump_page = request()->param('jump_page/d', 1);
        $id        = request()->param('id');
        $title     = request()->param('title');
        $start_time = request()->param('start_time');
        $end_time   = request()->param('end_time');
        $recommend   = request()->param('recommend/d');

        /* 验证 */
        $data = [
            'page_size'       => $page_size,
            'jump_page'       => $jump_page,
            'title'       => $title,
            'recommend'   => $recommend,
        ];

        $validate = Loader::validate('Review');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 组合过滤条件 */
        $conditions = [];

        if ($title) {
            $conditions['title'] = ['like', '%' . $title . '%'];
        }

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($recommend || $recommend === 0) {
            $conditions['recommend'] = $recommend;
        }

        if ($start_time && $end_time) {
            $conditions['publish_time'] = ['between time', [$start_time, $end_time]];
        }

        $review = ReviewModel::where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        /* 返回数据 */
        return json([
            'code'      => '200',
            'message'   => '获取活动回顾列表成功',
            'data'      => $review
        ]);
    }


    /**
     * 活动回顾保存
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id          = request()->param('id');
        $title       = request()->param('title');
        $content     = request()->param('content');
        $picture     = request()->file('picture');
        $publish_time = date('Y-m-d H:i:s', time());
        $recommend   = request()->param('recommend',0);
        $rich_text    = request()->param('rich_text');

        // 移动图片到框架应用根目录/public/images
        if ($picture) {
            $info = $picture->move(ROOT_PATH . 'public' . DS . 'images');
            if ($info) {
                //成功上传后，获取上传信息
                //输出jpg
                /*echo '文件扩展名:' . $info->getExtension() .'<br>';*/
                //输出文件格式
                /*echo '文件详细的路径加文件名:' . $info->getSaveName() .'<br>';*/
                //输出文件名称
                /*echo '文件保存的名:' . $info->getFilename();*/
                $sub_path     = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
            }
        }

        /* 验证 */
        $data = [
            'title'       => $title,
            'content'     => $content,
            'picture'     => $picture,
            'publish_time' => $publish_time,
            'recommend'   => $recommend,
            'rich_text'    => $rich_text,
        ];

        $validate = Loader::validate('Review');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (empty($id)) {
            $review = new ReviewModel;
            $result = $review->save($data);
        } else {
            if (empty($picture)) {
                unset($data['picture']);
            }
            $review = new ReviewModel;
            $result = $review->save($data, ['id' => $id]);
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
     * 活动回顾详情
     */
    public function detail()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Review');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = ReviewModel::where('id', $id)->find();
            
        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除回顾api接口
     */
    public function delete() {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Review');
        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = ReviewModel::destroy($id);

        if ($detail) {
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
}
