<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace util;

use deploy\Config;
use nb\Cache;
/**
 * Redis
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 *
 *
 */
class Redis extends Cache {

    public static function config() {
        return Config::$o->redis;
    }

    /**
     * 切换DB
     * @param int $db
     * @return $this
     */
    public static function select($db){
        self::driver()->select($db);
        return self::driver();
    }

    /**
     * 数据入队列
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param bool $right 是否从右边开始入
     * @return int
     */
    public static function push($key, $value, $right = true) {
        $value = json_encode($value);
        return $right ? self::driver()->rPush($key, $value) : self::driver()->lPush($key, $value);
    }

    /**
     * 数据出队列
     * @param string $key KEY名称
     * @param bool $left 是否从左边开始出数据
     * @return array
     */
    public static function pop($key, $left = true) {
        $val = $left ? self::driver()->lPop($key) : self::driver()->rPop($key);
        return json_decode($val, true);
    }

    public static function hmset($key,$data) {
        return self::driver()->hmset($key,$data);
    }

    /**
     * @param $key
     * @param $hashKey1
     * @param null $hashKey2
     * @param null $hashKeyN
     * @return mixed
     */
    public static function hdel($key, $hashKey1, $hashKey2 = null, $hashKeyN = null) {
        return call_user_func_array([self::driver(),'hdel'],func_get_args());
    }

}