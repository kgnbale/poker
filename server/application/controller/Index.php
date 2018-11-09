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

use nb\Controller;

/**
 * Index
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/11
 */
class Index extends Controller {

    public function index() {
        $markdown = file_get_contents(__APP__.'API.md');
        $markdown = json_encode($markdown,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $this->assign('markdown',$markdown);
        $this->display(__APP__.'index');
    }


}