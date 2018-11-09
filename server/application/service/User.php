<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace service;

use nb\Server;
use util\Redis;

/**
 * Auth
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 */
class User extends \nb\Service {

    //登录
    protected function login() {
        list($name,$pass) = $this->input('name','pass');

        $user = \model\User::name($name);
        if($user->empty) {
            $this->code = 4061;
            $this->msg = '用户不存在';
            return false;
        }

        if(!password_verify($pass,$user->pass)) {
            $this->code = 4062;
            $this->msg = '密码错误';
            return false;
        }

        if($user->token) {
            Redis::delete('token:'.$user->token);
        }

        //记录token
        $token = md5($user->name.time());
        Redis::set('token:'.$token, $user->name);

        //并将token保存到user
        \model\User::uptoken($name,$token);

        $this->msg = '登录成功!';
        $this->data = $token;
        return true;
    }

    //重连
    protected function reconnection() {
        $token = $this->input('token');

        $name = Redis::get('token:'.$token);
        if(!$name) {
            $this->msg = '登录过期，请重新登录';
            return false;
        }

        $user = \model\User::name($name);
        if($user->empty) {
            $this->msg = '用户不存在';
            return false;
        }

        //记录session
        $server = Server::driver();
        Redis::set('fd:'.$server->fd,$user->name);

        $this->msg = '重连成功!';
        return true;
    }


    //注册
    protected function register() {
        list($name,$pass) = $this->input('name','pass');

        $user = \model\User::name($name);
        if($user->have) {
            $this->code = 417;
            $this->msg = '用户已经存在';
            return false;
        }

        if(!trim($name)) {
            $this->code = 402;
            $this->msg = '请填写用户名';
            return false;
        }

        $pass = password_hash($pass,PASSWORD_DEFAULT);

        $row = \model\User::insert([
            'name'=>$name,
            'pass'=>$pass
        ]);

        if($row) {
            $this->msg = '注册成功';
            return true;
        }
        $this->code = 500;
        $this->msg = '系统错误';
        return false;
    }

}