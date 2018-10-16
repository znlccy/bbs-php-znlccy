<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 16:20
 * Comment: 论坛回顾控制器
 */
namespace app\index\controller;

use think\Db;
use think\Loader;
use app\index\model\Review as ReviewModel;

class Review extends BaseController {

    /**
     * 创业论坛回顾api接口
     */
    public function index() {

        $page = config('pagination');
        /* 获取客户端提交过来的数据 */
        $page_size = request()->param('page_size', $page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $page['JUMP_PAGE']);

        /* 验证规则 */
        $validate_data = [
            'page_size'         => $page_size,
            'jump_page'         => $jump_page,
        ];

        //实例化验证器
        $validate = Loader::validate('Review');
        $result   = $validate->scene('index')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //实例化模型
        $review_model = new ReviewModel();
         $review = $review_model->order('id', 'desc')
            ->field('rich_text, recommend', true)
            ->paginate($page_size, false, ['page' => $jump_page]);

        /* 返回数据 */
        return json([
            'code'      => '200',
            'message'   => '查询回顾成功',
            'data'      => $review
        ]);
    }

    /**
     * 论坛回顾api接口
     */
    public function detail() {

        /* 获取客户端提交的数据 */
        $id = request()->param('id');

        /* 验证规则 */
        $validate_data = [
            'id' => $id,
        ];

        //实例化验证器
        $validate = Loader::validate('Review');
        $result   = $validate->scene('detail')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        //检查当前回顾是否存在
        $review_model = new ReviewModel();
        $return_review = $review_model->field('recommend',true)
            ->where('id', '=', $id)
            ->find();

        if ( empty($return_review) ){
            return json(['code' => '401', 'message' => 'ID不存在' ]);
        }

        /* 上一页数据 */
        $prev = $review_model
            ->field('rich_text,recommend',true)
            ->where('id', '<', $id)
            ->order('id desc')
            ->find();

        /* 下一页数据 */
        $next = $review_model
            ->field('rich_text,recommend',true)
            ->where('id', '>', $id)
            ->order('id asc')
            ->find();

        /* 最新论坛回顾 */
        $last_review = $review_model
            ->field('rich_text,recommend',true)
            ->where('id', '<>', $id)
            ->order('recommend', 'desc')
            ->order('id', 'desc')
            ->limit(3)
            ->select();

        $data = array_merge(['prev' => $prev], ['next' => $next], ['detail' => $return_review], ['last_review' => $last_review]);

        return json([
            'code'      => '200',
            'message'   => '获取创业论坛回顾详情成功',
            'data'      => $data
        ]);
    }

}