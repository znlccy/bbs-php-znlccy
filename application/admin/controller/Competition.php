<?php

namespace app\admin\controller;

use app\admin\model\Competition as CompetitionModel;
use think\Db;
use think\Loader;
use think\Request;
use think\Validate;
use app\admin\model\TeamCompetition;
use app\admin\model\Team;

class Competition extends BaseController
{
    /**
     * 活动大赛列表
     *
     */
    public function index()
    {
        /* 获取客户端提供的数据 */
        $page_size = request()->param('page_size/d', 8);
        $jump_page = request()->param('jump_page/d', 1);
        $status    = request()->param('status');
        $name      = request()->param('name');
        $id        = request()->param('id');
        // 人数
        $min_register = request()->param('min_register/d');
        $max_register = request()->param('max_register/d');

        // 大赛时间
        $competition_begin = request()->param('competition_begin');
        $competition_end   = request()->param('competition_end');
        // 报名时间
        $registration_begin = request()->param('registration_begin');
        $registration_end   = request()->param('registration_end');

        /* 验证 */
        $data = [
            'page_size'    => $page_size,
            'jump_page'    => $jump_page,
            'min_register' => $min_register,
            'max_register' => $max_register,
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 组合过滤条件 */
        $conditions = [];
        switch ($status) {
            case -1:
                // 禁用
                $conditions['status'] = $status;
                break;
            case 1:
                // 全部
                $conditions['status'] = $status;
                break;
            case 2:
                // 预告期
                $conditions['begintime'] = ['>', date('Y-m-d H:i:s', time())];
                break;
            case 3:
                // 报名中
                $conditions['begintime'] = ['<=', date('Y-m-d H:i:s', time())];
                $conditions['endtime']   = ['>=', date('Y-m-d H:i:s', time())];
                break;
            case 4:
                // 报名结束(status => 4)
                $conditions['endtime'] = ['<', date('Y-m-d H:i:s', time())];
                break;
            case 5:
                // 活动结束(status => 5)
                $conditions['applytime'] = ['<', date('Y-m-d H:i:s', time())];
                break;
            default:
                break;
        }

        if (is_int($min_register) && is_int($max_register)) {
            $conditions['register'] = ['between', [$min_register, $max_register]];
        } elseif (is_int($min_register)) {
            $conditions['register'] = ['>=', $min_register];
        } elseif (is_int($max_register)) {
            $conditions['register'] = ['<=', $max_register];
        }

        if ($name) {
            $conditions['name'] = ['like', '%' . $name . '%'];
        }

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($registration_begin && $registration_end) {
            $conditions['begintime'] = ['between time', [$registration_begin, $registration_end]];
        }

        if ($competition_begin && $competition_end) {
            $conditions['time'] = ['between time', [$competition_begin, $competition_end]];
        }

        $competition = CompetitionModel::where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        /* 返回数据 */
        return json(['code' => '200', 'message' => '获取大赛列表成功', 'data' => $competition]);
    }

    /**
     * 大赛基本信息新增/更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id          = request()->param('id');
        $name        = request()->param('name');
        $picture     = request()->file('picture');
        $limit       = request()->param('limit');
        $register    = request()->param('register', 0);
        $status      = request()->param('status', 0);
        $time        = request()->param('time');
        $begin_time   = request()->param('begin_time');
        $end_time     = request()->param('end_time');
        $audit_method = request()->param('audit_method', 0);
        $rich_text   = request()->param('rich_text');

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

        /* 验证 */
        $data = [
            'name'        => $name,
            'picture'     => $picture,
            'limit'       => $limit,
            'register'    => $register,
            'status'      => $status,
            'time'        => $time,
            'begin_time'   => $begin_time,
            'end_time'     => $end_time,
            'audit_method' => $audit_method,
            'rich_text'   => $rich_text,
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        if (!empty($id)) {
            if (empty($picture)) {
                unset($data['picture']);
            }
            $competition = new CompetitionModel;
            $result      = $competition->save($data, ['id' => $id]);
        } else {
            $competition = new CompetitionModel;
            $result      = $competition->save($data);
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
     * 大赛详情
     *
     */
    public function detail()
    {
        $id = request()->param('id', 0);

        /* 验证 */
        $data = [
            'id' => $id,
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $detail = CompetitionModel::where('id', $id)->find();

        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除指定资源
     *
     */
    public function delete()
    {
        $id = request()->param('id', 0);
        /* 验证 */
        $data = [
            'id' => $id,
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = CompetitionModel::get($id);
        if ($competition) {
            $result = $competition->delete();
            if ($result) {
                // 删除关联关系中间表数据
                $competition->forums()->detach();
                $competition->news()->detach();
                $competition->teams()->detach();
                $competition->tutors()->detach();
                $competition->sps()->detach();
                $competition->investors()->detach();
                $competition->organizers()->detach();

                $data = ['code' => '200', 'message' => '删除成功!'];
                return json($data);
            } else {
                $data = ['code' => '404', 'message' => '删除失败'];
                return json($data);
            }
        } else {
            $data = ['code' => '404', 'message' => '无此大赛记录'];
            return json($data);
        }
    }


    /**
     * 大赛已报名列表
     */
    public function enroll_list()
    {
        //TODO{
        /* 获取客户端提供的数据 */
        $pagesize = request()->param('page_size/d', 8);
        $jumppage = request()->param('jump_page/d', 1);
        $id = request()->param('id');

        /* 验证规则 */
        $rule = [
            'page_size'   => 'require|number',
            'jump_page'   => 'require|number',
            'id' => 'require|number',
        ];
        $data = [
            'page_size'        => $pagesize,
            'jump_page'        => $jumppage,
            'id'    => $id,
        ];
        $msg = [
            'page_size'   => '单页数量',
            'jump_page' => '页码',
            'id' => '大赛ID',
        ];

        $validate = new Validate($rule, $msg);
        $result   = $validate->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = new TeamCompetition;
        $result = $competition -> where('competition_id', '=', $id)
            -> alias('tc')
            -> join('tb_teams t', 'tc.team_id = t.id')
            -> field('tc.id, tc.competition_id, tc.status, tc.register_time, t.project_name, t.project_area, t.company, t.register, t.picture, 
                t.city_id, t.province_id, t.username, t.mobile, t.video, t.business_plan')
            ->paginate($pagesize, false, ['page' => $jumppage]);
//            -> select();
        if ($result) {
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $result]);
        } else {
            return json(['code' => '404', 'message' => '获取列表失败']);
        }
    }

    /**
     * 大赛审核
     */
    public function check()
    {
        $id     = request()->param('id');
        $status = request()->param('status');
        /* 验证 */
        $data = [
            'id'     => $id,
            'status' => $status,
        ];
        $validate = Loader::validate('Competition');

        $result = $validate->scene('check')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition  = new TeamCompetition;
        $result = $competition->save($data, ['id' => $id]);
        if ($result) {
            return json(['code' => '200', 'message' => '审核通过']);
        } else {
            return json(['code' => '404', 'message' => '审核失败']);
        }
    }

    /**
     * 大赛添加论坛
     */
    public function add_forum()
    {
        $id = request()->param('id');
        $forum_id = request()->param('forum_id');

        /* 验证 */
        $data = [
            'id'    => $id,
            'forum_id'    => $forum_id
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('add_forum')->check($data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = CompetitionModel::get($id);
        if ($competition) {
            // 判断是否已添加过
            $result = Db::table('tb_competition_forums')->where(['competition_id' => $id, 'forum_id' => $forum_id])->find();
            if ($result) {
                return json(['code' => '404', 'message' => '已添加过此论坛活动']);
            }
            $result = $competition->forums()->attach($forum_id);
            if ($result) {
                return json(['code' => '200', 'message' => '添加成功!']);
            } else {
                return json(['code' => '404', 'message' => '添加失败!']);
            }
        } else {
            return json(['code' => '404', 'message' => '添加失败!']);
        }
    }

    /**
     * 大赛删除论坛
     */
    public function delete_forum()
    {
        $id = request()->param('id');
        $forum_id = request()->param('forum_id');
        /* 验证 */
        $data = [
            'id'    => $id,
            'forum_id'    => $forum_id
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('delete_forum')->check($data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = CompetitionModel::get($id);
        if ($competition) {
            $competition->forums()->detach($forum_id);
            return json(['code' => '200', 'message' => '删除成功!']);
        } else {
            return json(['code' => '404', 'message' => '删除失败!']);
        }

    }

    /**
     * 报名指定大赛且已审核过的团队列表
     */
    public function checked_team() {
        // 获取参数
        $id = request()->param('id');
        /* 验证 */
        $data = [
            'id'     => $id
        ];
        $validate = Loader::validate('Competition');
        $result = $validate->scene('checked_team')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $team_ids = TeamCompetition::where(['competition_id' => $id, 'status' => 1])->column('team_id');
        if (!empty($team_ids)) {
            $team = Team::whereIn('id',$team_ids)->field('id,company')->select();
            return json(['code' => '200', 'message' => '获取列表成功', 'data' => $team]);
        } else {
            return json(['code' => '404', 'message' => '暂无审核通过的团队']);
        }
    }

    /**
     * 大赛添加参数团队
     */
    public function add_team()
    {
        $id = request()->param('id');
        $team_id = request()->param('team_id');

        /* 验证 */
        $data = [
            'id'    => $id,
            'team_id'    => $team_id
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('add_team')->check($data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        //更新为展示
        $user = new TeamCompetition();
        // save方法第二个参数为更新条件
        $competition= $user->save([
            'show_type'  => 1
        ],['competition_id' => $id,'team_id' => $team_id]);

        if ($competition) {
            return json(['code' => '200', 'message' => '更新成功!']);
        } else {
            return json(['code' => '404', 'message' => '更新失败!']);
        }

//        $competition = CompetitionModel::get($id);
//        if ($competition) {
//            // 判断是否已添加过
//            $result = TeamCompetition::where(['competition_id' => $id, 'team_id' => $team_id])->find();
//            if ($result) {
//                return json(['code' => '404', 'message' => '已添加过此论坛活动']);
//            }
//            $result = TeamCompetition::create(['competition_id' => $id, 'team_id' => $team_id]);
//            if ($result) {
//                return json(['code' => '200', 'message' => '添加成功!']);
//            } else {
//                return json(['code' => '404', 'message' => '添加失败!']);
//            }
//        } else {
//            return json(['code' => '404', 'message' => '添加失败!']);
//        }
    }

    /**
     * 大赛删除参数团队
     */
    public function delete_team()
    {
        $id = request()->param('id');
        $team_id = request()->param('team_id');
        /* 验证 */
        $data = [
            'id'    => $id,
            'team_id'    => $team_id
        ];

        $validate = Loader::validate('Competition');

        $result = $validate->scene('delete_team')->check($data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competition = CompetitionModel::get($id);
        if ($competition) {
            $competition->teams()->detach($team_id);
            return json(['code' => '200', 'message' => '删除成功!']);
        } else {
            return json(['code' => '404', 'message' => '删除失败!']);
        }

    }



}
