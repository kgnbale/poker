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

    //自动包含路径
    public $path_autoinclude =  [
        __APP__.'deploy'.DS
    ];

    //注入一个类，来自定义框架里的一些事件，如报错处理，
    public $register    = 'event\\Framework';

    //文件缓存配置
    public $cache            = [
        'timeout'   => 86400,
        'ext'       => '.cache',
    ];

}