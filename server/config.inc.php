<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
return [
    //缓存服务器配置
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 1,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
        'driver'=>'\nb\cache\Redis'
    ],

    //服务器配置
    'server' => [
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
    ],

    //数据库配置
    'dao' => [
        'driver' => 'mysql',
        'host' => 'dev.io',
        'port' => '3306',
        'dbname' => 'game',
        'user' => 'dev',
        'pass' => '123456',
        'connect' => 'false',
        'charset' => 'UTF8',
    ],

    //扑克列表
    'poker'=>[
        //大小王
        ['id'=>1,'val'=>20,'name'=>'大王'],
        ['id'=>2,'val'=>19,'name'=>'小王'],

        //2
        ['id'=>3,'val'=>16,'name'=>'黑桃2'],
        ['id'=>4,'val'=>16,'name'=>'红心2'],
        ['id'=>5,'val'=>16,'name'=>'梅花2'],
        ['id'=>6,'val'=>16,'name'=>'方片2'],

        //A
        ['id'=>7,'val'=>14,'name'=>'黑桃A'],
        ['id'=>8,'val'=>14,'name'=>'红心A'],
        ['id'=>9,'val'=>14,'name'=>'梅花A'],
        ['id'=>10,'val'=>14,'name'=>'方片A'],

        //K
        ['id'=>11,'val'=>13,'name'=>'黑桃K'],
        ['id'=>12,'val'=>13,'name'=>'红心K'],
        ['id'=>13,'val'=>13,'name'=>'梅花K'],
        ['id'=>14,'val'=>13,'name'=>'方片K'],

        //Q
        ['id'=>15,'val'=>12,'name'=>'黑桃Q'],
        ['id'=>16,'val'=>12,'name'=>'红心Q'],
        ['id'=>17,'val'=>12,'name'=>'梅花Q'],
        ['id'=>18,'val'=>12,'name'=>'方片Q'],

        //J
        ['id'=>19,'val'=>11,'name'=>'黑桃J'],
        ['id'=>20,'val'=>11,'name'=>'红心J'],
        ['id'=>21,'val'=>11,'name'=>'梅花J'],
        ['id'=>22,'val'=>11,'name'=>'方片J'],

        //10
        ['id'=>23,'val'=>10,'name'=>'黑桃10'],
        ['id'=>24,'val'=>10,'name'=>'红心10'],
        ['id'=>25,'val'=>10,'name'=>'梅花10'],
        ['id'=>26,'val'=>10,'name'=>'方片10'],

        //9
        ['id'=>27,'val'=>9,'name'=>'黑桃9'],
        ['id'=>28,'val'=>9,'name'=>'红心9'],
        ['id'=>29,'val'=>9,'name'=>'梅花9'],
        ['id'=>30,'val'=>9,'name'=>'方片9'],

        //8
        ['id'=>31,'val'=>8,'name'=>'黑桃8'],
        ['id'=>32,'val'=>8,'name'=>'红心8'],
        ['id'=>33,'val'=>8,'name'=>'梅花8'],
        ['id'=>34,'val'=>8,'name'=>'方片8'],

        //7
        ['id'=>35,'val'=>7,'name'=>'黑桃7'],
        ['id'=>36,'val'=>7,'name'=>'红心7'],
        ['id'=>37,'val'=>7,'name'=>'梅花7'],
        ['id'=>38,'val'=>7,'name'=>'方片7'],


        //6
        ['id'=>39,'val'=>6,'name'=>'黑桃6'],
        ['id'=>40,'val'=>6,'name'=>'红心6'],
        ['id'=>41,'val'=>6,'name'=>'梅花6'],
        ['id'=>42,'val'=>6,'name'=>'方片6'],

        //5
        ['id'=>43,'val'=>5,'name'=>'黑桃5'],
        ['id'=>44,'val'=>5,'name'=>'红心5'],
        ['id'=>45,'val'=>5,'name'=>'梅花5'],
        ['id'=>46,'val'=>5,'name'=>'方片5'],

        //4
        ['id'=>47,'val'=>4,'name'=>'黑桃4'],
        ['id'=>48,'val'=>4,'name'=>'红心4'],
        ['id'=>49,'val'=>4,'name'=>'梅花4'],
        ['id'=>50,'val'=>4,'name'=>'方片4'],

        //3
        ['id'=>51,'val'=>3,'name'=>'黑桃3'],
        ['id'=>52,'val'=>3,'name'=>'红心3'],
        ['id'=>53,'val'=>3,'name'=>'梅花3'],
        ['id'=>54,'val'=>3,'name'=>'方片3'],
    ],

    //AnySDK相关配置
    'anysdk'=> [
        'privateKey'=>'555FAA0B1628AA5D90D404FBAE9C1F0C',
        //'enhancedKey'=>''
    ]
];