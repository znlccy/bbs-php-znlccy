<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23
 * Time: 19:40
 * Comment: 服务控制器
 */
namespace app\admin\controller;

use app\admin\model\Category;
use app\admin\model\Service as ServiceModel;
use think\Controller;
use think\Loader;
use think\Validate;

class Service extends BaseController
{

    /**
     * 服务资源api接口
     */
    public function index()
    {
        /* 获取客户端提交的数据 */
        $page_size = request()->param('page_size', 8);
        $jump_page = request()->param('jump_page', 1);
        $id       = request()->param('id');
        $status   = request()->param('status');

        $data = [
            'id'        => $id,
            'status'    => $status,
            'page_size'  => $page_size,
            'jump_page' => $jump_page,
        ];
        $validate = Loader::validate('Service');

        $result = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        /* 组合过滤条件 */
        $conditions = [];
        if ($id) {
            $conditions['id'] = $id;
        }

        if ($status || $status === 0) {
            $conditions['status'] = $status;
        }

        /* 查询服务 */
        $service = ServiceModel::where($conditions)
            ->with(['category' => function ($query) {
                $query->withField("id,name");
            }])
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page])->each(function ($item, $key) {
            unset($item['category_id']);
            return $item;
        });

        /* 返回数据 */
        return json([
            'code'    => '200',
            'message' => '获取服务列表成功',
            'data'    => $service,
        ]);
    }

    /**
     * 服务资源保存
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id           = request()->param('id');
        $name         = request()->param('name');
        $description  = request()->param('description');
        $picture      = request()->file('picture');
        $category_id   = request()->param('category_id');
        $price        = request()->param('price');
        $recommend    = request()->param('recommend', 0);
        $address      = request()->param('address');
        $publish_time = date('Y-m-d H:i:s', time());
        $status       = request()->param('status', 0);
        $rich_text     = request()->param('rich_text');

        /* 判断客户端提交的数据是否为空 */
        if ((!isset($name) || empty($name)) && (!isset($description) || empty($description)) && (!isset($category_id) || empty($category_id))) {
            return json(['code' => '401', 'message' => '提交的数据有空值', 'data' => null]);
        }

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
                $sub_path = str_replace('\\', '/', $info->getSaveName());
                $picture  = '/images/' . $sub_path;
            }
        }
        /* 验证 */
        $data = [
            'name'         => $name,
            'description'  => $description,
            'picture'      => $picture,
            'category_id'   => $category_id,
            'price'        => $price,
            'recommend'    => $recommend,
            'address'      => $address,
            'publish_time' => $publish_time,
            'status'       => $status,
            'rich_text'     => $rich_text,
        ];

        $validate = Loader::validate('Service');

        $result = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (empty($id)) {
            $service = new ServiceModel;
            $result  = $service->save($data);
        } else {
            if (empty($picture)) {
                unset($data['picture']);
            }
            $service = new ServiceModel;
            $result  = $service->save($data, ['id' => $id]);
        }

        if ($result) {
            $data = ['code' => '200', 'message' => '保存成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '保存失败'];
            return json($data);
        }
    }

    /**
     * 服务资源详情
     */
    public function detail()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id'       => $id,
        ];
        $validate = Loader::validate('Service');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = ServiceModel::where('id', $id)->find();

        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 服务资源删除
     */
    public function delete()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id'       => $id,
        ];
        $validate = Loader::validate('Service');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $result = ServiceModel::where('id', $id)->delete();

        if ($result) {
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败'];
            return json($data);
        }
    }

    /**
     * 服务类目下拉列表
     */
    public function category()
    {
        $category = Category::field('id,name')->select();
        if (!empty($category)) {
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $category]);
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }
    }
}
