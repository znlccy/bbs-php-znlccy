<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use app\index\model\Team as TeamModel;
use app\index\model\Competition;

class Team extends BasisController
{
    /**
     * 获取更多团队列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $page = config('pagination');
        /* 获取前端提交的数据 */
        $competition_id = request()->param('competition_id');
        $page_size       = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page       = request()->param('jump_page', $page['JUMP_PAGE']);

        /* 验证 */
        $data = [
            'competition_id'  => $competition_id,
            'page_size'       => $page_size,
            'jump_page'       => $jump_page,
        ];

        $validate = Loader::validate('Team');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = Competition::get($competition_id);
        $teams = $competition->teams()
            ->with(['province', 'city'])
            ->paginate($page_size, false, ['page' => $jump_page])
            ->each(function ($item) {
                unset($item['pivot']);
            });

        return json(['code' => '200', 'data' => $teams]);
    }
    /**
     *团队详情
     *
     */
    public function detail()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Team');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = TeamModel::with(['province', 'city'])->where('id', $id)->find();
        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }
}
