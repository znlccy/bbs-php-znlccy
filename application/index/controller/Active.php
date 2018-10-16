<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 16:19
 * Comment： 活动控制器
 */

namespace app\index\controller;

use think\Loader;
use app\index\model\Active as ActiveModel;
use app\admin\model\UserActive as UserActiveModel;
use app\index\model\User as UserModel;

class Active extends BasisController {

    /**
     * 沙龙活动首页json数据
     */
    public function index()
    {
        $page = config('pagination');

        /* 获取客户端提交过来的数据 */
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        //验证数据
        $validate_data = [
            'page_size'         => $page_size,
            'jump_page'         => $jump_page,
        ];

        //实例化验证器
        $validate = Loader::validate('Active');
        //验证数据
        $result = $validate->scene('index')->check($validate_data);
        //验证结果
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $active = new ActiveModel();
        $active_data = $active->where('status', '=', '1')
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        /* 返回客户端数据 */
        return json(['code'=> '200', 'message' => '获取活动列表成功', 'data' => $active_data]);
    }

    /**
     * 沙龙活动介绍json数据
     */
    public function introduce()
    {
        /* 获取客户端提交过来的参数 */
        $id = request()->param('id');
        // 检查token是否有效 获取当前用户id
        $user_id = NULL;
        $client_token = request()->header('access-token');
        if ( $this -> check_token($client_token) ){
            $user_id = session('user.id');
        }

        //验证数据
        $validate_data = [
            'id'        => $id,
        ];

        //实例化验证器
        $validate = Loader::validate('Active');
        $result   = $validate->scene('introduce')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //获取该活动消息
        $active = new ActiveModel();
        $active_info = $active->where('id', '=', $id)->find();

        if (empty($activeinfo) || $activeinfo['status'] == 0 ){
            return json(['code' => '401', 'message' => '活动不存在']);
        }

        //整理活动状态
        $now_time = date('Y-m-d h:i:s', time());
        $active_status = 4; //正在报名中，默认值

        if ( $active_info['apply_time'] < $now_time ){
            $active_status = 1; //活动已经结束，超过了活动时间
        }elseif ( $activeinfo['begin_time'] > $now_time ){
            $active_status = 2; //活动尚未开始报名
        }elseif ( $activeinfo['end_time'] < $now_time ){
            $active_status = 3; //报名已经结束
        }else{
            if ( $active_info['limit'] != 0 ){
                if ( $active_info['limit'] <= $activeinfo['register'] ){
                    $active_status = 5; //报名人数已满
                }
            }
        }

        //用户报名状态
        if (empty($user_id) ){
            $user_status = 1; //用户未登录
        }else{
            $user_active_model = new UserActiveModel();
            $user_active = $user_active_model->where('user_id', '=', $user_id)->where('active_id', '=', $id)->find();

            if (empty($user_active) ){
                $user_status = 2; //用户未报名
            }else{
                if ( $user_active['status'] == 1 ){
                    $user_status = 3; //用户已经报名，已审核
                }else{
                    $user_status = 4; //用户已经报名，未审核
                }
            }
        }

        //修改活动相关状态数据
        $active_info['status'] = $active_status;
        $active_info['user_status'] = $user_status;

        return json([
            'code'    => '200',
            'message' => '获取活动介绍成功',
            'data'    => $active_info,
        ]);
    }

    /**
     * 沙龙活动报名json数据 暂弃
     */
    public function registration()
    {
        /* 1.获取客户端提交过来的数据 */
        $id = request()->param('id');

        //验证器数据
        $validate_data = [
            'id'        => $id
        ];

        //实例化验证器
        $validate = Loader::validate('Active');
        $result = $validate->scene('registration')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 从数据库中获取到用户信息 */
        $user_id = session('user.id');
        $user_model = new UserModel();
        $user = $user_model->where('id', $user_id)->find();

        /* 判断客户端提交的数据是否为空 */
        if (!isset($id) || empty($id)) {
            return json(['code' => '401', 'message' => '抱歉，提交过来的活动ID为空，请重新提交']);
        } else {
            $active_model = new ActiveModel();
            $active_info = $active_model->where('id', '=', $id)->find();
        }
        /* 返回数据 */
        return json(['code' => '200', 'message' => '获取信息成功', 'data' => ['active_info' => $active_info, 'user_info' => $user]]);
    }

    /**
     * 活动报名函数 暂弃
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    private function enroll($id)
    {
        $active_model = new ActiveModel();
        $register_info = $active_model->where('id', '=', $id)->find();

        $count = $register_info['register'];

        $register = $active_model->where('id', '=', $id)->update(['register' => $count + 1]);
    }

    /**
     * 沙龙活动报名
     */
    public function apply()
    {

        /* 获取客户端提交过来的用户信息 */
        $active_id  = request()->param('id');
        $username   = request()->param('username');
        $mobile     = request()->param('mobile');
        $email      = request()->param('email');
        $career     = request()->param('career');
        $occupation = request()->param('occupation');
        $company    = request()->param('company');

        //验证数据
        $validate_data = [
            'id'         => $active_id,
            'username'   => $username,
            'mobile'     => $mobile,
            'email'      => $email,
            'career'     => $career,
            'occupation' => $occupation,
            'company'    => $company,
        ];

        //实例化验证器
        $validate = Loader::validate('Active');
        $result   = $validate->scene('apply')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        // 判断用户是否已报名
        $user_id = session('user.id');
        $user_active_model = new UserActiveModel();
        $result  = $user_active_model->where(['user_id' => $user_id, 'active_id' => $active_id])->find();

        if ($result) {
            return json(['code' => '400', 'message' => '您已报名该活动,无需重复提交']);
        }

        //获取活动消息
        $active_model = new ActiveModel();
        $active_info = $active_model->where(['id' => $active_id])->find();

        //整理活动状态
        $now_time = date('Y-m-d h:i:s', time());

        if ( $active_info['limit'] != 0 ){
            if ( $active_info['limit'] <= $active_info['register'] ){
                return json(['code' => '400', 'message' => '报名人数已满']);
            }
        }

        if ( $active_info['apply_time'] < $now_time ){
            return json(['code' => '400', 'message' => '活动已结束']);
        }elseif ( $active_info['begin_time'] > $now_time ){
            return json(['code' => '400', 'message' => '活动报名未开始']);
        }elseif ( $active_info['end_time'] < $now_time ){
            return json(['code' => '400', 'message' => '活动报名已截止']);
        }

        //判断直接审核或者等待审核
        if ( $active_info['audit_method'] == 1 ){
            $user_active_status = 1;
        }else{
            $user_active_status = 0;
        }

        $data = ['user_id' => $user_id, 'active_id' => $active_id, 'register_time' => date("Y-m-d H:i:s", time()), 'status' => $user_active_status];
        $data = array_merge($data, $validate_data);

        $result = $user_active_model->insert($data);
        if ($result) {
            // 活动人数+1
            $active_model->where(['id' => $active_id])->setInc('register');
            return json(['code' => '200', 'message' => '提交成功']);
        } else {
            return json(['code' => '404', 'message' => '报名失败']);
        }

    }
}
