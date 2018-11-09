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

use nb\Server;

/**
 * Controller
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/12
 */
class Controller extends \nb\Controller {

    public function __before($token=null) {
        if(Auth::init($token)->empty) {
            $this->error('reply-login-fail',401,'没有登录');
            //没有登录，中断链接
            Server::driver()->close();
            return false;
        }
        return true;
    }

    protected function success($msg='请求成功',$action,array $data=null) {
        $redata = [
            'code'=> 0,
            'action'=> 'reply-'.$action,
            'msg'=>$msg,
        ];
        $data and $redata = array_merge($redata,$data);
        b('success',$redata);
        $this->reply($redata);
    }

    protected function error($action,$code,$msg) {
        $redata = [
            'code'=>$code,
            "action"=> 'reply-'.$action,
            'msg'=>$msg
        ];
        b('error',$redata);
        $this->reply($redata);
        quit();
    }

    protected function reply($data) {
        is_array($data) and $data = json_encode($data);
        return Server::driver()->reply($data);
    }


    protected function push($fd,$action,array $data=null) {
        $redata = [
            "action"=> 'push-'.$action,
        ];
        $data and $redata = array_merge($redata,$data);
        b('push-'.$fd,$redata);
        Server::driver()->push($fd,json_encode($redata));
    }

    protected function roompush($room,$filer,$action,$data) {
        foreach (['a','b','c'] as $v) {
            if($v === $filer) {
                continue;
            }
            $seat = $room->$v;
            if(!$seat) {
                continue;
            }
            $this->push($seat['fd'],$action,$data);
        }
    }
}