<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Db;
use app\admin\model\Competition;
use app\admin\model\Sps as SpModel;

class Sps extends BaseController
{
    /**
     * 获取服务商列表
     *
     * @return \think\Response
     */
    public function index()
    {
        /* 获取前端提交的数据 */
        $competition_id = request()->param('competition_id');
        $pagesize       = request()->param('pagesize/d', 8);
        $jumppage       = request()->param('jumppage/d', 1);

        /* 验证规则 */
        $rule = [
            'competition_id' => 'require|number',
            'pagesize'       => 'require|number',
            'jumppage'       => 'require|number',
        ];
        $data = [
            'competition_id' => $competition_id,
            'pagesize'       => $pagesize,
            'jumppage'       => $jumppage,
        ];
        $field = [
            'competition_id' => '大赛id',
            'pagesize'       => '每页数量',
            'jumppage'       => '页码',
        ];

        $validate = new Validate($rule, [], $field);
        $result   = $validate->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        $competion = Competition::get($competition_id);
        $sps      = $competion->sps()
            ->paginate($pagesize, false, ['page' => $jumppage])
            ->each(function ($item) {
                unset($item['pivot']);
            });

        return json(['code' => '200', 'data' => $sps]);
    }

    /**
     * 服务商新增/更新
     *
     */
    public function save()
    {
        /* 获取前端提交的数据 */
        $id          = request()->param('id');
        $competition_id = request()->param('competition_id');
        $name       = request()->param('name');
        $picture   = request()->file('picture');

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

        /* 验证规则 */
        $rule = [
            'name'       => 'require|max:30',
        ];
        $data = [
            'name'        => $name,
            'picture'     => $picture
        ];
        $field = [
            'name'        => '服务商名称',
        ];

        $validate = new Validate($rule, [], $field);
        $result   = $validate->check($data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }
        if (!empty($id)) {
            // 更新
            if (empty($picture)) {
                unset($data['picture']);
            }
            $sp = new SpModel;
            $result = $sp->save($data,['id' => $id]);
        } else {
            // 新增
            if (empty($competition_id)) {
                return json(['code' => '401', 'message' => '大赛id为空']);
            }
            // 保存关联数据
            $competition = Competition::get($competition_id);
            $result      = $competition->sps()->save($data);
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
     * 服务商详细
     */
    public function detail()
    {
        $id     = request()->param('id', 0);
        $detail = SpModel::where('id', $id)->find();
        if ($detail) {
            $data = ['code' => '200', 'message' => '获取详情成功!', 'data' => $detail];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '获取详情失败!'];
            return json($data);
        }
    }

    /**
     * 删除服务商
     *
     */
    public function delete()
    {
        $id     = request()->param('id', 0);
        $result = SpModel::destroy($id);
        if ($result) {
            // 删除中间表数据
            Db::table('tb_competition_sps')->where('sp_id', $id)->delete();
            $data = ['code' => '200', 'message' => '删除成功!'];
            return json($data);
        } else {
            $data = ['code' => '404', 'message' => '删除失败!'];
            return json($data);
        }
    }
}
