<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 14:38
 * Comment: API接口首页控制器
 */

namespace app\index\controller;

use app\index\model\Forum as ForumModel;
use app\index\model\Service as ServiceModel;
use app\index\model\Competitions as CompetitionModel;

class Index extends BasisController {

    /**
     * 网站首页
     * @return string
     * @throws \think\Exception
     */
    public function index()
    {
        /* 创业论坛 */
        $forum = new ForumModel();
        $forum_data = $forum->field('rich_text', true)
            ->where('recommend','=', '1')
            ->where('status', '=', '1')
            ->order('recommend','desc')
            ->order('id', 'desc')
            ->limit(6)
            ->select();

        /* 服务资源 */
        $service = new ServiceModel();
        $service_data = $service->field('rich_text', true)
            ->where('recommend', '=', '1')
            ->where('status', '=', '1')
            ->order('id', 'desc')
            ->limit(3)
            ->select();

        /* 大赛资源 */
        $competition = new CompetitionModel();
        $competition_data = $competition->field('rich_text', true)
            ->where('recommend', '=', '1')
            ->where('status', '=', '1')
            ->order('id', 'desc')
            ->limit(4)
            ->select();

        /* 组装数据 */
        $return_data = array_merge(['service_data' => $service_data, 'forum_data' => $forum_data,  'competition_data' => $competition_data]);

        /* 返回数据 */
        return $this->returnMsg('200', '获取信息成功', $return_data);
    }

}