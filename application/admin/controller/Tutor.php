<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use think\Db;
use app\admin\model\Competition;
use app\admin\model\Tutor as TutorModel;

class Tutor extends BaseController
{
    /**
     * 获取导师列表
     *
     */
    public function index()
    {
        /* 获取前端提交的数据 */
        $competition_id = request()->param('competition_id');
        $page_size       = request()->param('page_size/d', 8);
        $jump_page       = request()->param('jump_page/d', 1);

        /* 验证 */
        $data = [
            'competition_id' => $competition_id,
            'page_size'       => $page_size,
            'jump_page'       => $jump_page,
        ];

        $validate = Loader::validate('Tutor');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competion = Competition::get($competition_id);
        $tutors      = $competion->tutors()
            ->paginate($page_size, false, ['page' => $jump_page])
            ->each(function ($item) {
                unset($item['pivot']);
            });

        return json(['code' => '200', 'data' => $tutors]);
    }

    /**
     * 导师新增/更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id           = request()->param('id');
        $competition_id = request()->param('competition_id');
        $name         = request()->param('name');
        $introduction = request()->param('introduction');
        $picture      = request()->file('picture');

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

        $data = [
            'name'         => $name,
            'introduction' => $introduction,
            'picture'      => $picture,
        ];

        $validate = Loader::validate('Tutor');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        if (!empty($id)) {
            // 更新
            if (empty($picture)) {
                unset($data['picture']);
            }
            $tutor  = new TutorModel;
            $result = $tutor->save($data, ['id' => $id]);
        } else {
            // 新增
            if (empty($competition_id)) {
                return json(['code' => '401', 'message' => '大赛id为空']);
            }
            // 保存关联数据
            $competition = Competition::get($competition_id);
            if ($competition) {
                $result      = $competition->tutors()->save($data);
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
     *导师详情 
     *
     */
    public function detail()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Investor');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = TutorModel::where('id', $id)->find();
        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除创业导师
     *
     */
    public function delete()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Investor');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $result = TutorModel::destroy($id);
        if ($result) {
            // 删除中间表数据
            Db::table('tb_competition_tutors')->where('tutor_id', $id)->delete();
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败!'];
            return json($data);
        }
    }
}
