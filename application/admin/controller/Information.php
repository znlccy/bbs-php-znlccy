<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 11:05
 * Comment: 消息控制器
 */
namespace app\admin\controller;

use app\admin\model\Information as InformationModel;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Validate;
use think\Loader;
use app\admin\model\Admin;

class Information extends BaseController
{

    /**
     * 消息管理api接口
     */
    public function index()
    {
        /* 获取客户端提供的数据 */
        $page_size    = request()->param('page_size', 8);
        $jump_page    = request()->param('jump_page', 1);
        $status       = request()->param('status');
        $title        = request()->param('title');
        $publisher_id = request()->param('publisher');
        $start_time   = request()->param('start_time');
        $end_time     = request()->param('end_time');

        /* 验证 */
        $data = [
            'page_size'  => $page_size,
            'jump_page'  => $jump_page,
            'status'     => $status,
            'start_time' => $start_time,
            'end_time'   => $end_time,
        ];

        $validate = Loader::validate('Information');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 组合过滤条件 */
        $conditions = [];
        if ($status || $status === 0) {
            $conditions['status'] = $status;
        }

        if ($title) {
            $conditions['title'] = ['like', '%' . $title . '%'];
        }

        if ($publisher_id) {
            $conditions['publisher'] = $publisher_id;
        }

        if ($start_time && $end_time) {
            $conditions['publish_time'] = ['between time', [$start_time, $end_time]];
        }

        $information = InformationModel::with(['user' => function ($query) {
            $query->withField("id,username,real_name");
        }])->where($conditions)
        // ->field('publisher', true)
            ->paginate($page_size, false, ['page' => $jump_page])->each(function ($item, $key) {
            unset($item['publisher']);
            return $item;
        });

        /* 返回数据 */
        return json([
            'code'    => '200',
            'message' => '获取消息列表成功',
            'data'    => $information,
        ]);
    }

    /**
     * 消息保存
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id           = request()->param('id');
        $title        = request()->param('title');
        $publisher_id = session('admin.id');
        $publish_time = date('Y-m-d H:i:s', time());
        $rich_text     = request()->param('rich_text');

        /* 验证规则 */
        $data = [
            'id'           => $id,
            'title'        => $title,
            'publisher'    => $publisher_id,
            'publish_time' => $publish_time,
            'rich_text'     => $rich_text,
        ];
        $validate = Loader::validate('Information');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (!empty($id)) {
            /* 更新数据 */
            $information = new InformationModel;
            $result      = $information->save($data, ['id' => $id]);
        } else {
            /* 保存数据 */
            $information = new InformationModel;
            $result      = $information->save($data);
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
     * 消息详情
     */
    public function detail()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id'       => $id,
        ];
        $validate = Loader::validate('Information');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = InformationModel::where('id', $id)->with(['user' => function ($query) {
            $query->withField("id,username,real_name");
        }])->find();

        unset($detail->publisher);

        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 消息删除
     */
    public function delete()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id'       => $id,
        ];
        $validate = Loader::validate('Information');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $result = InformationModel::destroy($id);

        if ($result) {
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败!'];
            return json($data);
        }
    }

    /**
     * 发布人下拉列表
     */
    public function publisher()
    {
        $publishers = Admin::field('id,nickname')->select();
        if (!empty($publishers)) {
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $publishers]);
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }
    }
}
