<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 16:19
 * Comment： 论坛控制器
 */

namespace app\index\controller;

use app\index\model\UserForum;
use app\index\model\Competition;
use think\Controller;
use think\Db;
use think\Loader;
use think\Validate;
use app\index\model\Forum as ForumModel;
use app\index\model\UserForum as UserForumModel;
use think\Session;

class Forum extends BasisController
{

    /**
     * 创业论坛首页json数据
     */
    public function index()
    {
        $page = config('pagination');
        /* 获取客户端提交过来的数据 */
        $competition_id = request()->param('competition_id');
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        /* 验证规则 */
        $validate_data = [
            'page_size'         => $page_size,
            'jump_page'         => $jump_page,
        ];

        //实例化验证器
        $validate = Loader::validate('Forum');
        $result   = $validate->scene('index')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //实例化论坛模型
        if (empty($competition_id)) {
            $forum_model = new ForumModel();
            $forum = $forum_model->where('status', '=', '1')
                ->where('recommend', '=', '1')
                ->field('rich_text', true)
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

        $now_time = date('Y-m-d h:i:s', time());
        foreach ( $forum as $key => $value){
            //整理论坛状态
            $forum[$key]['forum_status'] = 4; //正在报名中，默认值

            if ( $forum[$key]['apply_time'] < $now_time ){
                $forum[$key]['forum_status'] = 1; //论坛已经结束，超过了论坛时间
            }elseif ( $forum[$key]['begin_time'] > $now_time ){
                $forum[$key]['forum_status'] = 2; //论坛尚未开始报名
            }elseif ( $forum[$key]['end_time'] < $now_time ){
                $forum[$key]['forum_status'] = 3; //报名已经结束
            }else{
                if ( $forum[$key]['limit'] != 0 ){
                    if ( $forum[$key]['limit'] <= $forum[$key]['register'] ){
                        $forum[$key]['forum_status'] = 5; //报名人数已满
                    }
                }
            }
        }


        /* 返回客户端数据 */
        return json(['code'=> '200', 'message' => '获取论坛列表成功', 'data' => $forum]);
    }

    /**
     * 创业论坛介绍json数据
     */
    public function introduce()
    {
        /* 获取客户端提交过来的参数 */
        $id = request()->param('id');
        // 检查token是否有效 获取当前用户id
        $user_id = NULL;
        $client_token = request()->header('access-token');
        if ( $this -> check_token($client_token) ) {
            $user_id = session('user.id');
        }

        /* 验证规则 */
        $validate_data = [
            'id'        => $id,
        ];

        //实例化验证器
        $validate = Loader::validate('Forum');
        $result   = $validate->scene('introduce')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //获取该论坛消息
        $forum_model = new ForumModel();
        $forum_info = $forum_model->where('id', '=', $id)->find();

        if ( empty($forum_info) || $forum_info['status'] == 0 ){
            return json(['code' => '401', 'message' => '论坛不存在']);
        }

        $forum_info['register'] = intval($forum_info['start']) + intval($forum_info['register']);
        $forum_info['limit'] = intval($forum_info['limit']);

        //整理论坛状态
        $now_time = date('Y-m-d h:i:s', time());
        $forum_status = 4; //正在报名中，默认值

        if ( $forum_info['apply_time'] < $now_time ){
            $forum_status = 1; //论坛已经结束，超过了论坛时间
        }elseif ( $forum_info['begin_time'] > $now_time ){
            $forum_status = 2; //论坛尚未开始报名
        }elseif ( $forum_info['end_time'] < $now_time ){
            $forum_status = 3; //报名已经结束
        }else{
            if ( $forum_info['limit'] != 0 ){
                if ( $forum_info['limit'] <= $forum_info['register'] ){
                    $forum_status = 5; //报名人数已满
                }
            }
        }

        //用户报名状态
        if (empty($user_id) ){
            $user_status = 1; //用户未登录
        }else{
            $user_forums_model = new UserForumModel();
            $user_forum = $user_forums_model
                ->where('user_id', '=', $user_id)
                ->where('forum_id', '=', $id)
                ->find();

            if ( empty($user_forum) ){
                $user_status = 2; //用户未报名
            }else{
                if ( $user_forum['status'] == 1 ){
                    $user_status = 3; //用户已经报名，已审核
                }else{
                    $user_status = 4; //用户已经报名，未审核
                }
            }
        }

        //修改论坛相关状态数据
        $forum_info['status'] = $forum_status;
        $forum_info['user_status'] = $user_status;

        return json([
            'code'    => '200',
            'message' => '获取论坛介绍成功',
            'data'    => $forum_info,
        ]);

    }

    /**
     * 创业论坛报名
     */
    public function apply()
    {

        /* 获取客户端提交过来的用户信息 */
        $forum_id  = request()->param('id');
        $username   = request()->param('username');
        $mobile     = request()->param('mobile');
        $email      = request()->param('email');
        $career     = request()->param('career');
        $occupation = request()->param('occupation');
        $company    = request()->param('company');

        /* 验证 */
        $validate_data = [
            'forum_id'   => $forum_id,
            'username'   => $username,
            'mobile'     => $mobile,
            'email'      => $email,
            'career'     => $career,
            'occupation' => $occupation,
            'company'    => $company,
        ];

        $validate = Loader::validate('Forum');
        $result   = $validate->scene('apply')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        // 判断用户是否已报名
        $user_id = session('user.id');
        $user_forum_model = new UserForumModel();
        $result  = $user_forum_model->where('status', '=', '1')->where(['user_id' => $user_id, 'forum_id' => $forum_id])->find();
        if ($result) {
            return json(['code' => '400', 'message' => '您已报名该论坛,无需重复提交']);
        }

        //获取论坛消息
        $forum_model = new ForumModel();
        $forum_info = $forum_model->where(['id' => $forum_id])
            ->find();

        //整理论坛状态
        $now_time = date('Y-m-d h:i:s', time());

        if ( $forum_info['limit'] != 0 ){
            if ( $forum_info['limit'] <= $forum_info['register'] ){
                return json(['code' => '400', 'message' => '报名人数已满']);
            }
        }

        if ( $forum_info['apply_time'] < $now_time ){
            dump($forum_info['apply_time']);
            return json(['code' => '400', 'message' => '论坛已结束']);
        }elseif ( $forum_info['begin_time'] > $now_time ){
            return json(['code' => '400', 'message' => '论坛报名未开始']);
        }elseif ( $forum_info['end_time'] < $now_time ){
            return json(['code' => '400', 'message' => '论坛报名已截止']);
        }

        //判断直接审核或者等待审核
        if ( $forum_info['audit_method'] == 1 ){
            $user_forum_status = 1;
        }else{
            $user_forum_status = 0;
        }

        $data = ['user_id' => $user_id, 'forum_id' => $forum_id, 'register_time' => date("Y-m-d H:i:s", time()), 'status' => $user_forum_status];
        $data = array_merge($data, $validate_data);

        $result = $user_forum_model->insert($data);
        if ($result) {
            // 论坛人数+1
            $forum_model->where(['id' => $forum_id])->setInc('register');
            return json(['code' => '200', 'message' => '提交成功']);
        } else {
            return json(['code' => '404', 'message' => '报名失败']);
        }

    }

    /**
     * Token验证
     * @param $client_token
     */
    public function check_token($client_token) {
        if (Session::has('access_token')){
            // 获取服务端存储的token
            $server_token = Session::get('access_token');
            if ($server_token == $client_token) {
                return true;
            }
        }
        return false;
    }

}
