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
use nb\Model;

/**
 * Order
 *
 * @package model
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/12/10
 */
class Order extends Model {

    public static function __config() {
        return ['order o'];
    }

}