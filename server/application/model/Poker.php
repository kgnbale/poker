<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace model;

use deploy\Config;
use nb\Collection;
use util\Auth;

/**
 * Poker
 *
 * @package model
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/31
 */
class Poker extends Collection {

    public function __construct(array $pokerd=[]) {
        $poker = Config::$o->poker;
        //按战力至从小到大
        rsort($pokerd);
        foreach ($pokerd as &$v) {
            $v = $poker[$v];
        }
        $this->poker = $pokerd;
        $this->num = count($pokerd);
    }

    public function compare(array $poker) {
        $poker = new Collection($poker);
        $auth = Auth::init();

        //不需要压牌，验证出牌规则
        if($auth->seat == $poker->seat) {
            return $this->is;
        }

        //如果是核弹
        if($this->nbomb) {
            $this->is = 'nbomb';
            return true;
        }

        //需要压牌，比较规则和大小
        $rule = $poker->is;
        if(!$this->$rule) {
            if($this->bomb) {
                $this->is = 'bomb';
                return true;
            }
            return false;
        }
        $this->is = $rule;
        if($this->max > $poker->max) {
            return true;
        }
        return false;
    }

    protected function _is() {
        switch ($this->num) {
            case 1:
                //个
                return 'individual';
            case 2:
                //对子，核弹
                return $this->couplet?'couplet':($this->nbomb?"nbomb":false);
            case 3:
                //三不带
                return $this->plane?'plane':false;
            case 4:
                //三带一，炸弹
                return $this->bomb?'bomb':($this->plane?'plane':false);
            case 6:
            case 8:
                if($this->train) {
                    return 'train';
                }
            default:
                //顺子,顺对
                return $this->straights?'straights':($this->straight?'straight':($this->plane?'plane':false));
        }
        return false;
    }

    //顺子
    protected function _straight() {
        $poker = $this->poker;
        //判断起始位,开始的牌必须小于J，也就是低于11
        if($poker[0]['val'] > 10) {
            return false;
        }

        foreach ($poker as $k=>$v) {
            if(isset($poker[$k+1]) && $v['val']+1 !== $poker[$k+1]['val']) {
                return false;
            }
        }
        $this->max = $v['val'];
        return true;
    }

    //顺对
    protected function _straights() {
        $poker = $this->poker;
        //判断起始位,开始的牌必须小于K，也就是低于13
        if($poker[0]['val'] > 12) {
            return false;
        }

        if($this->num % 2 !== 0) {
            return false;
        }

        $poker = array_chunk($poker,2);

        foreach ($poker as $k=>$v) {
            if($v[0]['val'] !== $v[1]['val']) {
                return false;
            }

            if(isset($poker[$k+1]) && $v[0]['val']+1 !== $poker[$k+1][0]['val']) {
                return false;
            }
        }
        $this->max = $v[0]['val'];
        return true;
    }

    //炸弹
    protected function _bomb() {
        $poker = $this->poker;

        if($this->num !== 4) {
            return false;
        }
        list($a,$b,$c,$d) = $poker;
        if($a['val'] === $b['val'] && $b['val'] === $c['val'] && $c['val'] === $d['val']) {
            $this->max = $a['val'];
            return true;
        }
        return false;
    }

    //核弹
    protected function _nbomb() {
        $poker = $this->poker;

        if($this->num !== 2) {
            return false;
        }

        list($a,$b) = $poker;
        if($a['val'] === 19 && $b['val'] === 20) {
            $this->max = $b['val'];
            return true;
        }

        return false;
    }

    //个
    protected function _individual() {
        if($this->num === 1) {
            $poker = $this->poker;
            $this->max = $poker[0]['val'];
            return true;
        }
        return false;
    }

    //对子
    protected function _couplet() {
        $poker = $this->poker;

        if($this->num !== 2) {
            return false;
        }
        list($a,$b) = $poker;
        if($a['val'] === $b['val']) {
            $this->max = $b['val'];
            return true;
        }
        return false;
    }

    //飞机
    protected function _plane() {
        if($this->num % 3 === 0) {
            $chunk = $this->achunk;
            $nthree = count($chunk[3]);
            $length = $this->continuity($chunk);
            if($length === $nthree && $length*3 === $this->num) {
                $this->length = $length;
                return true;
            }

        }
        //三带一
        if($this->num % 4 === 0) {
            $chunk4t1 = $this->achunk4t1;
            $length = $this->continuity($chunk4t1);
            if($length>6) {
                $length = 5;
            }
            if($this->num === ($length * 3 + $length) ) {
                $this->length = $length;
                return true;
            }
        }
        //三带二
        if($this->num % 5 === 0) {
            $chunk4t1 = $this->achunk4t2;
            $nthree = count($chunk4t1[3]);
            $length = $this->continuity($chunk4t1);
            if($length !== $nthree) {
                return false;
            }

            if($length*3 + $length*2 === $this->num) {
                $this->length = $length;
                return true;
            }
        }
        return false;
    }

    private function continuity($chunk) {
        $length = 0;
        $three = $chunk[3];
        $nthree = count($chunk[3]);
        foreach ($three as $k=>$v) {
            $length ++;
            if(isset($three[$k+1]) && $v+1 !== $three[$k+1]) {
                break;
            }
            $this->max = $v;
        }
        $length2 = 0;
        if($length !== $nthree) {
            for ($i=$nthree-1; $i>=0; $i--) {
                $length2 ++;
                if(isset($three[$i-1]) && $three[$i]-1 !== $three[$i-1]) {
                    break;
                }

            }
            $this->max = $three[$nthree-1];
        }
        return $length > $length2?$length:$length2;
    }

    //火车
    protected function _train() {
        if($this->num !== 6 && $this->num !== 8) {
            return false;
        }

        $chunk = $this->achunk;
        $nFour = count($chunk[4]);

        if($nFour ===1 && (count($chunk[4])==2 || count($chunk[4])==1)) {
            $this->max = $chunk[4][0];
            return 'train1';
        }

        if($nFour ===2) {
            $this->max = $chunk[4][1];
            return 'train2';
        }
        return false;
    }

    //计算每种面值的牌数
    protected function _achunk() {
        $poker = $this->poker;
        $val = [];

        $chunk = [
            1=>[],
            2=>[],
            3=>[],
            4=>[]
        ];

        foreach ($poker as $k=>$v) {
            $val[$v['val']] = 0;
        }

        foreach ($poker as $k=>$v) {
            $val[$v['val']] = $val[$v['val']]+1;
        }

        foreach ($val as $k=>$v) {
            $chunk[$v][]=$k;
        }
        return $chunk;
    }

    //将4张一样带分割为一份3个的和一份一个的
    protected function _achunk4t1() {
        $chunk = $this->achunk;
        if(isset($chunk[4])) {
            foreach ($chunk[4] as $v) {
                $chunk[3][] = $v;
                $chunk[1][] = $v;
            }
            sort($chunk[3]);
        }
        return $chunk;
    }

    //将4张一样带分割为一份3个的和一份一个的
    protected function _achunk4t2() {
        $chunk = $this->achunk;
        if(isset($chunk[4])) {
            foreach ($chunk[4] as $v) {
                $chunk[2][] = $v;
                $chunk[2][] = $v;
            }
            sort($chunk[4]);
        }
        return $chunk;
    }

}