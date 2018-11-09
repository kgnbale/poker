<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * 编辑器钩子
 */
return;
\nb\Hook::init([
    "editor:rich"=>[
        ['ol\sblog\editor\Upload','reader']
    ],
    "themes/add.htm:richEditor"=>[
        ['ol\sblog\editor\Test','index']
    ]
]);
