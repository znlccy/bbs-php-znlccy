<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Loader;
use app\admin\model\Team as TeamModel;
use app\admin\model\Competition;

class Team extends Controller
{
    /**
     * 团队列表
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

        $validate = Loader::validate('Team');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = Competition::get($competition_id);
        $teams = $competition->teams()
            -> where('show_type=1')
            ->with(['province', 'city'])
            ->paginate($page_size, false, ['page' => $jump_page])
            ->each(function ($item) {
                unset($item['pivot']);
            });

        return json(['code' => '200', 'data' => $teams]);
    }


    /**
     * 团队新增/更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id = request()->param('id');
        $project_name = request()->param('project_name');
        $project_area = request()->param('project_area');
        $content = request()->param('content');
        $company = request()->param('company');
        $register    = request()->param('register', 0);
        $picture = request()->file('picture');
        $province_id = request()->param('province_id');
        $city_id = request()->param('city_id');
        $username = request()->param('username');
        $mobile = request()->param('mobile');
        $user_id = session('user.id');
        $video = request()->param('video');
        $business_plan = request()->file('business_plan');
//        dump($id);die;

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
                $sub_path     = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
            }
        }

        // 移动文件到框架应用根目录/public/uploads
        if ($business_plan) {
            $info = $business_plan->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                //成功上传后，获取上传信息
                //输出jpg
                $allow_ext = ['rar', 'zip'];
                $ext       = $info->getExtension();
                if (!in_array($ext, $allow_ext)) {
                    return json(['code' => '401', 'message' => '商业计划书仅支持(rar、zip)格式']);
                }
                /*echo '文件扩展名:' . $info->getExtension() .'<br>';*/
                //输出文件格式
                /*echo '文件详细的路径加文件名:' . $info->getSaveName() .'<br>';*/
                //输出文件名称
                /*echo '文件保存的名:' . $info->getFilename();*/
                $sub_path      = str_replace('\\', '/', $info->getSaveName());
                $business_plan = '/uploads/' . $sub_path;
            }
        }
        /* 验证 */
        $data = [
            'id'              => $id,
            'project_name'    => $project_name,
            'project_area'    => $project_area,
            'content'         => $content,
            'company'         => $company,
            'register'        => $register,
            'picture'         => $picture,
            'province_id'     => $province_id,
            'city_id'         => $city_id,
            'username'        => $username,
            'mobile'          => $mobile,
            'video'           => $video,
            'user_id'         => $user_id,
            'business_plan'   => $business_plan,
        ];

        $validate = Loader::validate('Team');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (!empty($id)) {
            if (empty($picture)) {
                unset($data['picture'],$data['business_plan']);
            }
            $team = new TeamModel;
            $result      = $team->save($data, ['id' => $id]);
        } else {
            $team = new TeamModel;
            $result      = $team->save($data);
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

    /**
     * 团队删除
     *
     */
    public function delete()
    {
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id' => $id,
        ];
        $validate = Loader::validate('Team');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $result = TeamModel::destroy($id);
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
