<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace controller;

use nb\Response;
use util\Base;

/**
 * Register
 *
 * @package controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/17
 */
class Register extends Base {

    public $_rule = [
        'name'  =>  'require|alphaDash|max:25',
        'pass'  =>  'require|max:25',
    ];

    public function __error($msg,$args) {
        $this->error(402,$msg);
    }

    public function index() {
        Response::header('Access-Control-Allow-Origin', '*');
        Response::header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, PATCH, OPTIONS');
        Response::header('Access-Control-Allow-Headers', 'Authorization, User-Agent, Keep-Alive, Content-Type, X-Requested-With');

        $run = \service\User::run('register',function ($msg,$code) {
            $this->error($code,$msg);
        });

        $this->success($run->msg);
    }

}