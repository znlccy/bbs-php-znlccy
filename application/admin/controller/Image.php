<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 11:37
 * Comment: 分类服务控制器
 */
namespace app\admin\controller;

use app\admin\controller\Base;
use think\Controller;

class Image extends BaseController {

    public function upload(){

        $picture = request()->file('picture');

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
            }else{
                $data = ['code' => '404', 'message' => '图片上传错误'];
                return json($data);
            }
        }else{
            $data = ['code' => '404', 'message' => '图片上传错误'];
            return json($data);
        }

        $data = ['code' => '200', 'message' => '上传成功', 'data' => $picture];
        return json($data);

    }
}