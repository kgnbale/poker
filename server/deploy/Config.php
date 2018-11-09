<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace deploy;

/**
 * Config
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 *
 * @property array upload 上传配置
 * @property array option 用户配置
 */
class Config extends \nb\Config {

    public $debug = true;

    public $namespace = [
        'sdk'=>'/home/www/sdk'
    ];

    //自动包含路径
    public $path_autoinclude =  [
        __APP__.'deploy'.DS
    ];


    //注入一个类，来自定义框架里的一些事件，如报错处理，
    public $register    = 'event\\Framework';

    public $server_tcp = [
        'driver'=>'tcp',
        'register'=>'event\\Tcp',//注册一个类，来实现swoole自定义事件
        'host'=>'0.0.0.0',
        'port'=>9502,
        'max_request'=>100,//worker进程的最大任务数
        'worker_num'=>2,//设置启动的worker进程数。
        'dispatch_mode'=>2,//据包分发策略,默认为2
        'debug_mode'=>3,
        'enable_gzip'=>0,//是否启用压缩，0为不启用，1-9为压缩等级
        'log_file'=>__APP__.'tmp'.DS.'swoole-tcp.log',
        'enable_pid'=>'/tmp/swoole.pid',
        'daemonize'=>false,
        //异步任务处理配置
        'task_worker_num'=>2,
    ];

    public $server = [
        'driver'=>'Websocket',
        'register'=>\event\Websocket::class,//  'event\\Websocket',//注册一个类，来实现swoole自定义事件
        'host'=>'0.0.0.0',
        'port'=>9503,
        'max_request'=>100,//worker进程的最大任务数
        'worker_num'=>2,//设置启动的worker进程数。
        'dispatch_mode'=>2,//据包分发策略,默认为2
        'debug_mode'=>3,
        'enable_gzip'=>0,//是否启用压缩，0为不启用，1-9为压缩等级
        'log_file'=>__APP__.'tmp'.DS.'swoole-socket.log',
        'enable_pid'=>__APP__.'tmp'.DS.'swoole.pid',
        'daemonize'=>false,
        //异步任务处理配置
        'task_worker_num'=>2,
        'enable_http'=>true,//启用内置的onRequest回调
    ];

    //文件缓存配置
    public $cache            = [
        'timeout'   => 86400,
        'ext'       => '.cache',
    ];

    public $i18n = [
        'path'=> __APP__.'lang/zh-cn.php'
    ];

    public $console = [
        'name'    => 'Demo Console',
        'version' => '1.0',
        'user'    => null,
        'commands'=>[
            'util\\Client'
        ]
    ];

    public $redis = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 1,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
        'driver'=>'\nb\cache\Redis'
    ];

    public $dao = [
        'driver' => 'mysql',
        'host' => 'where.cx',
        'port' => '3306',
        'dbname' => 'game',
        'user' => 'dev',
        'pass' => '123456',
        'connect' => 'false',
        'charset' => 'UTF8',
        //'prefix' => 'nb_', // 数据库表前缀
    ];
}