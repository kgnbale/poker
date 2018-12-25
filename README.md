# 斗地主

此项目为测试NB在Websocket方面应用的练手项目，所以没有做严格的安全处理和功能测试，仅供参考和学习。
如果你想帮助完善，欢迎PR！

## 环境要求

1. Linux/MAC, PHP 7.0 +, PHP-Sqlite, Redis
2. [Swoole 2.1.3](https://github.com/swoole/swoole-src/releases) +
3. [NB Framework](https://github.com/swoole/swoole-src/releases)


## 安装

1.下载项目
```shell
git clone https://github.com/nbcx/poker.git
cd ./poker/server
git clone https://github.com/nbcx/framework.git nb
```
2.运行，需要确保你的redis安装并启动。
```shell
cd bin

./server start
```
根据你的php安装方式，上面的命令可能执行错误，你可以用你自己完整的php路径方式启动：
```shell
/usr/bin/php server start
```

3.浏览器访问`http://127.0.0.1:9503`,如果显示API文档，则表示成功！


## 测试
源码目录下的client是一个简单的web功能测试客户端，直接打开index.html注册登录就可以使用。
当然，你也可以用nginx来访问，只需这样配置一下：
```
server {
    listen 80;
    server_name xxxxx.cn;
    index index.html;
    root /home/www/poker/client/;

    location ~ .*\.(svg|woff2|map|html|woff|ttf|ico|css|js|gif|jpg|jpeg|png|bmp|swf)$ {
        expires 90d;
    }
}
```

## 功能

- [x] 登录注册
- [x] 房间列表
- [x] 创建房间和密码房间
- [x] 进入房间和退出房间
- [x] 游戏准备和取消准备
- [x] 抢地主
- [x] 发牌
- [x] 出牌以及出牌规则验证和大小比较
- [x] 房间聊天
- [x] 上线下线通知
- [ ] 出牌等待时间不能超过规定时间
- [ ] 托管

## 技术交流

QQ群: 1985508