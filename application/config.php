<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // 加密密钥
    'secret_key' => 'abc123',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 8,
    ],

    //验证码配置
    'captcha'                => [
        // 验证码字符集合
        'codeSet' => '12345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        //字体大小
        'fontSize'  => 17,
        //验证码长度
        'length'    => 4,
        //是否画混淆曲线
        'useCurve'  => false,
        //验证码图片高度
        'imageH'    => 35,
        //验证码图片宽度
        'imageW'    => 150,
        //验证成功后是否重置
        'reset'     => true
    ],

    //数据迁移
    'migration' => [
        'path' => ROOT_PATH .'vendor/gmars/tp5-rbac/'
    ],

    //短信验证码
    'sms'       => [
        'url'       => 'https://api.mysubmail.com/message/xsend.json',
        'appid'     => '13363',
        'project'   => 'TjMOF3',
        'signature' => '3432bfd823119abb670aa0618c388b4f'
    ],

    // +----------------------------------------------------------------------
    // | 状态码配置
    // +----------------------------------------------------------------------
    'status'                    => [
        //状态码常量

        // 1xx 状态码
        'CONTINUE'              => '100',                   //（继续） 请求者应当继续提出请求
        'SWITCHING_PROTOCOL'    => '101',                   //（切换协议） 请求者已要求服务器切换协议，服务器已确认并准备切换。

        // 2xx 状态码
        'SUCCESS'               => '200',                   //（成功） 服务器已成功处理了请求
        'CREATED'               => '201',                   //（已创建） 请求成功并且服务器创建了新的资源
        'UPDATED'               => '202',                   //（已更新） 请求成功并且服务器更新了新的资源
        'DELETED'               => '204',                   //（已删除） 请求成功并且服务器删除了新的资源

        // 3xx 状态码
        'NOT_MODIFIED'          => '304',                   //（未修改） 自从上次请求后，请求的网页未修改过

        // 4xx 状态码
        'ERROR_REQUEST'         => '400',                   //（错误请求） 服务器不理解请求的语法
        'UNAUTHORIZED'          => '401',                   //（未授权） 请求要求身份验证
        'FORBID'                => '403',                   //（禁止） 服务器拒绝请求。
        'NOT_FOUND'             => '404',                   //（未找到） 服务器找不到请求的网页。
        'METHOD_DISABLE'        => '405',                   //（方法禁用） 禁用请求中指定的方法。
        'NOT_ACCEPTED'          => '406',                   //（不接受） 无法使用请求的内容特性响应请求的网页。
        'PROXY_AUTHORIZATION'   => '407',                   //（需要代理授权） 此状态代码与 401（未授权）类似，但指定请求者应当授权使用 代理。
        'REQUEST_TIMEOUT'       => '408',                   //（请求超时） 服务器等候请求时发生超时。
        'CONFLICT'              => '409',                   //（冲突） 服务器在完成请求时发生冲突。 服务器必须在响应中包含有关冲突的信 息。
        'NO_VALID'              => '411',                   //（需要有效值） 服务器不接受不含有效内容的请求。
        'MEET_PRECONDITIONS'    => '412',                   //（未满足前提条件） 服务器未满足请求者在请求中设置的其中一个前提条件。
        'PHYSICAL_OVERSIZE'     => '413',                   //（请求实体过大） 服务器无法处理请求，因为请求实体过大，超出服务器的处理能 力。

        // 5xx 状态码
        'INNER_ERROR'           => '500'                    //（服务器内部错误） 服务器遇到错误，无法完成请求
    ],

    'message'                   => [
        //状态码常量

        // 1xx 状态码
        'CONTINUE'              => '继续',                  //（继续） 请求者应当继续提出请求
        'SWITCHING_PROTOCOL'    => '切换协议',              //（切换协议） 请求者已要求服务器切换协议，服务器已确认并准备切换。

        // 2xx 状态码
        'SUCCESS'               => '操作成功',              //（成功） 服务器已成功处理了请求
        'CREATED'               => '创建成功',              //（已创建） 请求成功并且服务器创建了新的资源
        'UPDATED'               => '更新成功',              //（已更新） 请求成功并且服务器更新了新的资源
        'DELETED'               => '删除成功',              //（已删除） 请求成功并且服务器删除了新的资源

        // 3xx 状态码
        'NOT_MODIFIED'          => '未修改',                //（未修改） 自从上次请求后，请求的网页未修改过

        // 4xx 状态码
        'ERROR_REQUEST'         => '错误请求',              //（错误请求） 服务器不理解请求的语法
        'UNAUTHORIZED'          => '未授权',                //（未授权） 请求要求身份验证
        'FORBID'                => '禁止',                  //（禁止） 服务器拒绝请求。
        'NOT_FOUND'             => '未找到',                //（未找到） 服务器找不到请求的网页。
        'METHOD_DISABLE'        => '方法禁用',              //（方法禁用） 禁用请求中指定的方法。
        'NOT_ACCEPTED'          => '不接受',                //（不接受） 无法使用请求的内容特性响应请求的网页。
        'PROXY_AUTHORIZATION'   => '需要代理授权',          //（需要代理授权） 此状态代码与 401（未授权）类似，但指定请求者应当授权使用 代理。
        'REQUEST_TIMEOUT'       => '请求超时',              //（请求超时） 服务器等候请求时发生超时。
        'CONFLICT'              => '冲突',                  //（冲突） 服务器在完成请求时发生冲突。 服务器必须在响应中包含有关冲突的信 息。
        'NO_VALID'              => '需要有效值',            //（需要有效值） 服务器不接受不含有效内容的请求。
        'MEET_PRECONDITIONS'    => '未满足前提条件',        //（未满足前提条件） 服务器未满足请求者在请求中设置的其中一个前提条件。
        'PHYSICAL_OVERSIZE'     => '请求实体过大',          //（请求实体过大） 服务器无法处理请求，因为请求实体过大，超出服务器的处理能 力。

        // 5xx 状态码
        'INNER_ERROR'           => '服务器内部错误'         //（服务器内部错误） 服务器遇到错误，无法完成请求

    ],

    // +----------------------------------------------------------------------
    // | 分页配置
    // +----------------------------------------------------------------------
    'pagination'                => [
        'PAGE_SIZE'             => '8',                     //（页码数）每页显示多少条数据
        'JUMP_PAGE'             => '1'                      //（跳转页）跳转至第几页
    ]
];
