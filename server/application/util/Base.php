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

/**
 * Base
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/11/1
 */
class Base extends \nb\Controller {

    protected function success($msg='请求成功',array $data=null) {
        $redata = [
            "code"=> 0,
            "msg"=>$msg,
        ];
        $data and $redata = array_merge($redata,$data);
        b('success',$redata);
        quit(json_encode($redata));
    }

    protected function error($code,$msg) {
        $redata = [
            'code'=>$code,
            'msg'=>$msg
        ];
        b('error',$redata);
        quit(json_encode($redata));
    }

}