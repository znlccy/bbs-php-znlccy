<?php

namespace app\admin\controller;

use app\admin\model\Competition;
use app\admin\model\Organizer as OrganizerModel;
use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use think\Db;

class Organizer extends BaseController
{
    /**
     * 获取组织机构列表
     *
     */
    public function index()
    {
        /* 获取前端提交的数据 */
        $competition_id = request()->param('competition_id');
        $page_size       = request()->param('page_size', 8);
        $jump_page       = request()->param('jump_page', 1);

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

        $competition  = Competition::get($competition_id);
        if ($competition) {
            $organizers = $competition->organizers()
                ->with(['group'])
                ->paginate($page_size, false, ['page' => $jump_page])
                ->each(function ($item) {
                    unset($item['pivot'], $item['group_id']);
                });
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }

        return json(['code' => '200', 'data' => $organizers]);
    }

    /**
     * 组织机构新增/更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id             = request()->param('id');
        $competition_id = request()->param('competition_id');
        $name           = request()->param('name');
        $group_id       = request()->param('group_id');
        $picture        = request()->file('picture');
        $recommend      = request()->param('recommend');

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

        /* 验证规则 */
        $data = [
            'id'        => $id,
            'name'      => $name,
            'group_id'  => $group_id,
            'recommend' => $recommend,
            'picture'   => $picture,
        ];
        $validate = Loader::validate('Organizer');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        if (!empty($id)) {
            // 更新
            $organizer = new OrganizerModel;
            $result    = $organizer->save($data, ['id' => $id]);
        } else {
            // 新增
            if (empty($competition_id)) {
                return json(['code' => '401', 'message' => '大赛id为空']);
            }
            // 保存关联数据
            $competition = Competition::get($competition_id);
            if ($competition) {
                $result      = $competition->organizers()->save($data);
            } else {
                return json(['code' => '404', 'message' => '没有该大赛记录']);
            }
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
     * 组织机构详情
     *
     */
    public function detail()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Organizer');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = OrganizerModel::with(['group'])
            ->where('id', $id)->find();
        if ($detail) {
            unset($detail['group_id']);
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除组织机构
     *
     */
    public function delete()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Organizer');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $result = OrganizerModel::destroy($id);
        if ($result) {
            // 删除中间表数据
            Db::table('tb_competition_organiers')->where('organiers_id', $id)->delete();
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败!'];
            return json($data);
        }
    }
}
