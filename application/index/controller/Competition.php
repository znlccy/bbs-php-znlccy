<?php

namespace app\index\controller;

use think\Loader;
use think\Db;
use app\index\model\Competition as CompetitionModel;
use app\index\model\OrganizerGroup as OrganizerGoupsModel;
use app\index\model\Team as TeamModel;
use app\index\model\CompetitionTeams as CompetitionTeamsModel;

class Competition extends BasisController
{
    /**
     * 显示大赛首页api接口
     */
    public function index()
    {
        $page = config('pagination');
       /* 获取客户端提交过来的数据 */
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        //验证数据
        $validate_data = [
            'page_size'       => $page_size,
            'jump_page'       => $jump_page,
        ];

        //实例化验证器
        $validate = Loader::validate('Competition');
        $result   = $validate->scene('index')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //实例化大赛模型
        $competition_model = new CompetitionModel();
        $forum = $competition_model->order('id', 'desc')->paginate($page_size, false, ['page' => $jump_page]);

        /* 返回客户端数据 */
        return json(['code'=> '200', 'message' => '获取大赛列表成功', 'data' => $forum]);
    }

    public function introduce()
    {
        $page = config('pagination');
        $id       = request()->param('id', 0);
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        //验证的数据
        $validate_data = [
            'id'       => $id,
            'page_size' => $page_size,
            'jump_page' => $jump_page,
        ];

        //实例化验证器
        $validate = Loader::validate('Competition');
        $result   = $validate->scene('introduce')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //实例化大赛模型
        $competition_model = new CompetitionModel();
        $competition = $competition_model->where('id', $id)->where('status', '=', '1')->field('id,picture,rich_text')->find();
        // 论坛活动
        $forums = $competition->forums()->field('tb_forums.id, apply_time, title, content, picture')->limit(2)->select();
        foreach ($forums as $key => $forum) {
            unset($forum->pivot);
        }
        // 参赛团队
        $teams = $competition->teams()->where('show_type=1')->field('picture,company')->limit(8)->select();
        foreach ($teams as $key => $team) {
            unset($team->pivot);
        }
        // 左侧大赛新闻
        $news_id_list = [];
        $news_left = $competition->news()->field('tb_news.id,title,content,picture')->where('picture <> ""')->order('recommend desc, publish_time desc')->limit(8)->select();
        foreach ($news_left as $key => $new_left) {
            $news_id_list[] = $new_left['id'];
            unset($new_left->pivot);
        }
        // 右侧大赛新闻
        $news_right = $competition->news()->field('tb_news.id,title,content')->where('tb_news.id', 'not in', $news_id_list)->limit(8)->select();
        foreach ($news_right as $key => $new_right) {
            unset($new_right->pivot);
        }
        // 创业导师
        $tutors = $competition->tutors()->field('name,introduction,picture')->limit(6)->select();
        foreach ($tutors as $key => $tutor) {
            unset($tutor->pivot);
        }
        // 服务商
        $sps = $competition->sps()->field('name,picture')->limit(12)->select();
        foreach ($sps as $key => $sp) {
            unset($sp->pivot);
        }
        // 投资机构
        $investors = $competition->investors()->field('picture')->limit(15)->select();
        foreach ($investors as $key => $investor) {
            unset($investor->pivot);
        }
        // 组织机构
        $organizers = $competition->organizers()->field('picture,group_id')->select();
        // 组织机构分组
        $organizer_groups_model = new OrganizerGoupsModel();
        $organizer_groups = $organizer_groups_model->select()->toArray();
        $org = [];
        foreach ($organizer_groups as $key => $group) {
            foreach ($organizers as $key => $organizer) {
                if ($group['id'] === $organizer['group_id']) {
                    $group['organizer'][] = $organizer['picture'];
                }
            }
            if (!empty($group['organizer'])) {
                    $org[] = $group;
            }
        }

        // 判断用户是否已报名
        $user_mobile = session('user.mobile');
        $result      = Db::table('tb_teams t')
            ->join('tb_competition_teams ct', 't.id = ct.team_id')
            ->where(['t.mobile' => $user_mobile, 'ct.competition_id' => $id])
            ->find();
        if ($result) {
            $user_status = 1;
        } else {
            $user_status = 0;
        }

        $detail = ['user_status' => $user_status, 'competition' => $competition, 'forums' => $forums, 'teams' => $teams, 'news_left' => $news_left, 'news_right' => $news_right, 'tutors' => $tutors, 'sps' => $sps, 'investors' => $investors, 'organizers' => $org];

        // var_dump($news);die;
        if ($competition) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 大赛报名
     */
    public function apply()
    {
        /* 获取客户端提交过来的用户信息 */
        $competition_id = request()->param('competition_id');
        $project_name = request()->param('project_name');
        $project_area = request()->param('project_area');
        $content = request()->param('content');
        $company = request()->param('company');
        $register = request()->param('register');
        $picture = request()->file('picture');
        $province_id = request()->param('province_id');
        $city_id = request()->param('city_id');
        $username = request()->param('username');
        $mobile = request()->param('mobile');
        $video = request()->param('video');
        $user_id = session('user.id');
        $business_plan = request()->file('business_plan');

        // 判断用户是否已报名
        $result = Db::table('tb_competition_teams ct')
            ->join('tb_teams t', 't.id = ct.team_id', 'left')
            ->where(['t.user_id' => $user_id, 'ct.competition_id' => $competition_id])
            ->find();

        if ($result) {
            return json(['code' => '400', 'message' => '您已报名该大赛,无需重复提交']);
        }

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
                $ext = $info->getExtension();
                if (!in_array($ext, $allow_ext)) {
                    return json(['code' => '401', 'message' => '商业计划书仅支持(rar、zip)格式']);
                }
                /*echo '文件扩展名:' . $info->getExtension() .'<br>';*/
                //输出文件格式
                /*echo '文件详细的路径加文件名:' . $info->getSaveName() .'<br>';*/
                //输出文件名称
                /*echo '文件保存的名:' . $info->getFilename();*/
                $sub_path = str_replace('\\', '/', $info->getSaveName());
                $business_plan = '/uploads/' . $sub_path;
            }
        }

        /* 验证数据 */
        $insert_data = [
            'project_name' => $project_name,
            'project_area' => $project_area,
            'content' => $content,
            'company' => $company,
            'register' => $register,
            'picture' => $picture,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'user_id' => $user_id,
            'username' => $username,
            'mobile' => $mobile,
            'video' => $video,
            'business_plan' => $business_plan,
        ];

        $validate = Loader::validate('Competition');
        $result = $validate->scene('apply')->check($insert_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //获取论坛消息
        $competition_model = new CompetitionModel();
        $competition_info = $competition_model->where(['id' => $competition_id])->find();

        //整理论坛状态
        $now_time = date('Y-m-d h:i:s', time());

        if ($competition_info['limit'] != 0) {
            if ($competition_info['limit'] <= $competition_info['register']) {
                return json(['code' => '400', 'message' => '报名人数已满']);
            }
        }

        if ($competition_info['time'] < $now_time) {
            return json(['code' => '400', 'message' => '论坛已结束']);
        } elseif ($competition_info['begin_time'] > $now_time) {
            return json(['code' => '400', 'message' => '论坛报名未开始']);
        } elseif ($competition_info['end_time'] < $now_time) {
            return json(['code' => '400', 'message' => '论坛报名已截止']);
        }

        //判断直接审核或者等待审核
        if ($competition_info['audit_method'] == 1) {
            $user_competition_status = 1;
        } else {
            $user_competition_status = 0;
        }

        // 关联插入团队数据
        $team = new TeamModel($insert_data);
        $team->save();
        $team_id = $team->id;
        $data = ['team_id' => $team_id, 'competition_id' => $competition_id, 'register_time' => date("Y-m-d H:i:s", time()), 'status' => $user_competition_status];
        // 更新中间表
        $competition_teams_model = new CompetitionTeamsModel();
        $result = $competition_teams_model->where(['competition_id' => $competition_id, 'team_id' => $team_id])->insert($data);
        if ($result && $team) {
            // 论坛人数+1
            $competition_model->where(['id' => $competition_id])->setInc('register');
            return json(['code' => '200', 'message' => '提交成功']);
        } else {
            return json(['code' => '404', 'message' => '报名失败']);
        }
    }
}
