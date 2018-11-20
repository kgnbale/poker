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

use deploy\Config;
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

    //洗牌
    public function shuffle(){
        $tmp = $arr = Config::$o->poker;
        foreach($arr as $k=>$v){
            $index = rand(0,54 - $k -1);
            $key = array_search($tmp[$index], $arr);
            $cards[$key] = $tmp[$index];
            unset($tmp[$index]);
            $tmp = array_values($tmp);
        }

        //玩家1
        $a = array_slice($cards, 3, 17, true);
        ksort($a);

        //玩家2
        $b =array_slice($cards, 20, 17, true);
        ksort($b);

        //玩家3
        $c = array_slice($cards, 37, 17, true);
        ksort($c);

        $d = array_slice($cards, 0, 3, true);
        ksort($d);

        e([$a,$b,$c,$d]);
    }


}