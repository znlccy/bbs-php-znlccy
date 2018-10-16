<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Validate;
use think\Loader;
use app\admin\model\Competition;
use app\admin\model\News as NewsModel;

class News extends BaseController
{
    /**
     * 获取大赛新闻列表
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

        $validate = Loader::validate('News');

        $result   = $validate->scene('index')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competion = Competition::get($competition_id);
        $news      = $competion->news()
            ->paginate($page_size, false, ['page' => $jump_page])
            ->each(function ($item) {
                unset($item['pivot']);
            });

        return json(['code' => '200', 'data' => $news]);
    }

    /**
     * 新闻新增/更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id             = request()->param('id');
        $competition_id = request()->param('competition_id');
        $title          = request()->param('title');
        $content        = request()->param('content');
        $recommend      = request()->param('recommend');
        $picture        = request()->file('picture');
        // 移动图片到框架应用根目录/public/images
        if ($picture ) {
            $info = $picture->move(ROOT_PATH . 'public' . DS . 'images');
            if ($picture ) {
                /*echo '文件保存的名:' . $info->getFilename();*/
                $sub_path     = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
            }
        }
        $rich_text      = request()->param('rich_text');
        $publish_time    = date('Y-m-d H:i:s', time());

        /* 验证 */
        $data = [
            'title'       => $title,
            'content'     => $content,
            'recommend'   => $recommend,
            'competition_id' => $competition_id
        ];

        $validate = Loader::validate('News');

        $result   = $validate->scene('save')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $insert_data = [
            'title'       => $title,
            'content'     => $content,
            'recommend'   => $recommend,
            'publish_time'=> $publish_time,
            'picture'     => $picture,
            'rich_text'   => $rich_text
        ];
        if (!empty($id)) {
            // 更新
            $news   = new NewsModel;
            $result = $news->save($insert_data, ['id' => $id]);
        } else {
            // 新增
            if (empty($competition_id)) {
                return json(['code' => '401', 'message' => '大赛id为空']);
            }
            // 保存关联数据
            $competition = Competition::get($competition_id);
            if ($competition) {
                $result      = $competition->news()->save($insert_data);
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
     * 新闻详细
     */
    public function detail()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id'    => $id,
        ];
        $validate = Loader::validate('News');

        $result   = $validate->scene('detail')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $detail = NewsModel::where('id', $id)->find();

        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除新闻
     *
     */
    public function delete()
    {
        $id     = request()->param('id');
        /* 验证 */
        $data = [
            'id'    => $id,
        ];
        $validate = Loader::validate('News');

        $result   = $validate->scene('delete')->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        $result = NewsModel::destroy($id);
        if ($result) {
            // 删除中间表数据
            Db::table('tb_competition_news')->where('news_id', $id)->delete();
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败!'];
            return json($data);
        }
    }
}
