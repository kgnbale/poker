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
use nb\Collection;
use nb\Pool;

/**
 * Stat
 *
 * @package model
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/12/19
 */
class Stat extends Collection {

    public function init() {
        return Pool::value('model\\Stat',function (){
            $stat = Redis::hGetAll('stat');
            $stat or $stat = [];
            return new Stat($stat);
        });
    }

}