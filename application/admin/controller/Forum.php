<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23
 * Time: 19:39
 * Comment: 论坛后台控制器
 */
namespace app\admin\controller;

use app\admin\model\Forum as ForumModel;
use app\admin\model\UserForum;
use app\admin\model\Competition;
use think\Controller;
use think\Loader;
use think\Validate;

class Forum extends Controller
{
    /**
     * 论坛管理api接口
     */
    public function index()
    {
        /* 获取客户端提供的数据 */
        $page_size = request()->param('page_size', 8);
        $jump_page = request()->param('jump_page', 1);
        $competition_id = request()->param('competition_id');
        $status    = request()->param('status');
        $title     = request()->param('title');
        $id        = request()->param('id');

        // 人数
        $min_register = request()->param('min_register');
        $max_register = request()->param('max_register');

        // 论坛时间
        $forum_begin = request()->param('forum_begin');
        $forum_end   = request()->param('forum_end');
        // 报名时间
        $registration_begin = request()->param('registration_begin');
        $registration_end   = request()->param('registration_end');

        /* 验证 */
        $data = [
            'page_size'    => $page_size,
            'jump_page'    => $jump_page,
            'min_register' => $min_register,
            'max_register' => $max_register,
        ];

        $validate = Loader::validate('Forum');

        $result = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 组合过滤条件 */
        $conditions = [];

        switch ($status) {
            case -1:
                // 禁用
                $conditions['status'] = $status;
                break;
            case 1:
                // 全部
                $conditions['status'] = $status;
                break;
            case 2:
                // 预告期
                $conditions['begin_time'] = ['>', date('Y-m-d H:i:s', time())];
                break;
            case 3:
                // 报名中
                $conditions['begin_time'] = ['<=', date('Y-m-d H:i:s', time())];
                $conditions['end_time']   = ['>=', date('Y-m-d H:i:s', time())];
                break;
            case 4:
                // 报名结束(status => 4)
                $conditions['end_time'] = ['<', date('Y-m-d H:i:s', time())];
                break;
            case 5:
                // 论坛结束(status => 5)
                $conditions['apply_time'] = ['<', date('Y-m-d H:i:s', time())];
                break;
            default:
                break;
        }

        if ($min_register && $max_register) {
            $conditions['register'] = ['between', [$min_register, $max_register]];
        }

        if ($title) {
            $conditions['title'] = ['like', '%' . $title . '%'];
        }

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($registration_begin && $registration_end) {
            $conditions['begin_time'] = ['between time', [$registration_begin, $registration_end]];
        }

        if ($forum_begin && $forum_end) {
            $conditions['apply_time'] = ['between time', [$forum_begin, $forum_end]];
        }

        //实例化论坛模型
        if (empty($competition_id)) {
            $forum_model = new ForumModel();
            $forum = $forum_model->where($conditions)
                ->order('id', 'desc')
                ->paginate($page_size, false, ['page' => $jump_page]);
        } else {
            $competition = Competition::get($competition_id);
            $forum      = $competition->forums()
                ->paginate($page_size, false, ['page' => $jump_page])
                ->each(function ($item) {
                    unset($item['pivot']);
                });
        }

        /* 返回数据 */
        return json(['code' => '200', 'message' => '获取论坛列表成功', 'data' => $forum]);
    }

    /**
     * 论坛保存
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id             = request()->param('id');
        $competition_id = request()->param('competition_id');
        $title          = request()->param('title');
        $content        = request()->param('content');
        $recommend      = request()->param('recommend', 0);
        $picture        = request()->file('picture');
        $start          = request()->param('start');
        $limit          = request()->param('limit');
        $register       = request()->param('register', 0);
        $status         = request()->param('status', 0);
        $address        = request()->param('address');
        $location       = request()->param('location');
        $apply_time     = request()->param('apply_time');
        $begin_time     = request()->param('begin_time');
        $end_time       = request()->param('end_time');
        $audit_method   = request()->param('audit_method', 0);
        $rich_text      = request()->param('rich_text');

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
            'title'        => $title,
            'content'      => $content,
            'recommend'    => $recommend,
            'picture'      => $picture,
            'start'        => $start,
            'limit'        => $limit,
            'register'     => $register,
            'status'       => $status,
            'address'      => $address,
            'location'     => $location,
            'apply_time'   => $apply_time,
            'begin_time'   => $begin_time,
            'end_time'     => $end_time,
            'audit_method' => $audit_method,
            'rich_text'    => $rich_text,
        ];

        $validate = Loader::validate('Forum');

        $result = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (!empty($id)) {
            if (empty($picture)) {
                unset($data['picture']);
            }
            $forum  = new ForumModel;
            $result = $forum->save($data, ['id' => $id]);
        } else {
            $forum  = new ForumModel;
            $result = $forum->save($data);
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
     * 论坛详情
     */
    public function detail()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Forum');

        $result = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = ForumModel::where('id', $id)
            ->find();
        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 论坛删除
     */
    public function delete()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Forum');

        $result = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $result = ForumModel::where('id', $id)->delete();

        if ($result) {

            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败'];
            return json($data);
        }
    }

    /**
     * 论坛审核列表
     */
    public function enroll_list()
    {
        //TODO{
        /* 获取客户端提供的数据 */
        $pagesize = request()->param('page_size/d', 8);
        $jumppage = request()->param('jump_page/d', 1);
        $id = request()->param('id');

        /* 验证规则 */
        $rule = [
            'page_size'   => 'require|number',
            'jump_page'   => 'require|number',
            'id' => 'require|number|min:0',
        ];
        $data = [
            'page_size'        => $pagesize,
            'jump_page'        => $jumppage,
            'id'    => $id,
        ];
        $msg = [
            'page_size'   => '单页数量',
            'jump_page' => '页码',
            'id' => '论坛ID',
        ];

        $validate = new Validate($rule, $msg);
        $result   = $validate->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $forum = new UserForum;
        $result = $forum -> where('forum_id', '=', $id)
            -> alias('au')
            -> join('tb_user u', 'au.user_id = u.id')
            -> field('au.id, u.mobile as user_mobile, au.forum_id, au.status, au.register_time, au.username, au.mobile, au.email, au.career, au.occupation, au.company')

            ->paginate($pagesize, false, ['page' => $jumppage]);
//            -> select();
        if ($result) {
            return json(['code' => '200', 'message' => '查询成功', 'data' => $result]);
        } else {
            return json(['code' => '404', 'message' => '查询失败']);
        }


    }

    /**
     * 论坛审核
     */
    public function check()
    {
        $id     = request()->param('id');
        $status = request()->param('status');

        /* 验证 */
        $data = [
            'id'     => $id,
            'status' => $status,
        ];
        $validate = Loader::validate('Forum');

        $result = $validate->scene('check')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $forum  = new UserForum;
        $result = $forum->save($data, ['forum_id' => $id]);
        if ($result) {
            return json(['code' => '200', 'message' => '审核通过']);
        } else {
            return json(['code' => '404', 'message' => '审核失败']);
        }
    }
}
