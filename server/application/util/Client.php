<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace command;

use deploy\Config;
use nb\console\Command;
use nb\console\input\Input;
use nb\console\output\Ask;
use nb\console\output\Output;
use nb\console\output\Question;
use nb\console\Pack;

/**
 * Client
 *
 * @package command
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/6
 */
class Client implements Command {

    public function configure(Pack $cmd) {
        $cmd->setName('client');
    }

    /**
     * 获取已经存在的Debug日志
     * @return object
     */
    protected function get(){
        $bpath = Config::getx('path_temp');
        if(is_file($bpath.'debug.log')){
            return json_decode(file_get_contents($bpath.'debug.log'),true);
        }
        return null;
    }

    /**
     * 记录Debug日志
     * @param $log
     * @throws \Exception
     */
    protected function put($log) {
        $bpath = Config::getx('path_temp');
        if (!is_dir($bpath) && !mkdir($bpath,0777,true)) {
            throw new \Exception('Create bug dir is fail!');
        }
        file_put_contents($bpath.'debug.log', json_encode($log));
    }


    public function execute(Input $input, Output $output) {
        $json = $this->get();

        $data = $json[count($json)-1];
        //e($data);
        if(!empty($data['log'])) {
            $output->writeln('Log');
            foreach ($data['log'] as $k=>$v ){
                $output->writeln($v);
            }
        }

        if(!empty($data['e'])) {
            $output->writeln('Exception');
            foreach ($data['e'] as $k=>$v){
                $output->writeln($v['type'].':'.$v['message'].':'.$v['file'].':'.$v['line']);
            }
        }
    }

    public function execute_bak(Input $input, Output $output) {
        $client = new \swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $client->on('connect', function($cli) use ($input,$output){
            $cli->send("Hello NB Framework\n");
        });
        $client->on('receive', function($cli, $data) use ($input,$output) {
            $output->writeln($data);
            $question = new Question('发送数据(exit退出)');
            $ask = new Ask($input, $output, $question);
            $answer = $ask->run();
            if($answer == 'exit') {
                die();
            }
            $cli->send($answer."\n");
        });
        $client->on('error', function($cli){
            echo "connect failed\n";
        });
        $client->on('close', function($cli){
            echo "connection close\n";
        });
        $client->connect("127.0.0.1", 9502, 0.5);

    }

    /**
     * 用户验证
     * @param Input $input
     * @param Output $output
     */
    function interact(Input $input, Output $output){}

    /**
     * 初始化
     * @param Input $input An InputInterface instance
     * @param Output $output An OutputInterface instance
     */
    function initialize(Input $input, Output $output){}
}