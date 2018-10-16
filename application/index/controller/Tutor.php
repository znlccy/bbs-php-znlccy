<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use think\Db;
use app\index\model\Competition;
use app\index\model\Tutor as TutorModel;

class Tutor extends BasisController
{
    /**
     * 获取更多导师列表
     *
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
            'competition_id'    => $competition_id,
            'page_size'         => $page_size,
            'jump_page'         => $jump_page,
        ];

        $validate = Loader::validate('Tutor');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = Competition::get($competition_id);
        $tutors      = $competition->tutors()
            ->paginate($page_size, false, ['page' => $jump_page])
            ->each(function ($item) {
                unset($item['pivot']);
            });

        return json(['code' => '200', 'data' => $tutors]);
    }
    
}
