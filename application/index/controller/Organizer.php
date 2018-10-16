<?php

namespace app\index\controller;

use app\index\model\Competition;
use app\index\model\Organizer as OrganizerModel;
use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;

class Organizer extends BasisController
{
    /**
     * 获取更多组织机构列表
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
            'competition_id' => $competition_id,
            'page_size'       => $page_size,
            'jump_page'       => $jump_page,
        ];

        $validate = Loader::validate('Organizer');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competion  = Competition::get($competition_id);
        $organizers = $competion->organizers()
            ->with(['group'])
            ->paginate($page_size, false, ['page' => $jump_page])
            ->each(function ($item) {
                unset($item['pivot'], $item['group_id']);
            });

        return json(['code' => '200', 'data' => $organizers]);
    }
    
}
