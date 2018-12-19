# 阅读文档之前，需要知道：

1.请求地址以`http`开头的，都是http协议的接口。以`ws`开头的，都是Websocket协议的接口

2.返回结果中，code为0，永远表示接口请求成功，如果其它值，则表示请求失败，可以对应下表找出错误原因：

|code |说明|
|:-----  |:-------  |
|0      |  成功|
|401    |  未登录|
|402    |  参数缺失|
|404    |  请求不存在|
|500    |  系统错误|
|4061   |  登录用户不存在|
|4062   |  登录密码错误|
|417    |  注册用户名已经存在|
|409    |  游戏房间已满，无法进入|
|410    |  房间密码错误|
|202    |  进入了已经进入的房间|
|4031   |  你已经在一个房间了，需要退出房间，才能进入另一个房间|
|4032   |  不能从大厅退出房间|
|4033   |  出牌规则错误|
|4034   |  只能当玩家状态(play)为`room`时，才可以准备游戏|
|4035   |  还没有到出牌的时间|
|4036   |  头家必须出牌|
|4037   |  出了你没有的牌|


3.Websocket协议接口，返回值中将总是包括`action`字段，来处理服务器推送消息的类别，对照表如下：

|action |说明|
|:-----  |:-------  |
|reply-xxx  |该消息为对客户端请求的回应，xxx为请求中的action值，`/`替换为`-`|
|push-room-enter   |有玩家进入房间|
|push-room-quit    |有玩家退出房间|
|push-play-ready   |有玩家发起或取消游戏准备|

> 所有接口回复都是以`reply`开头，所有推送消息都是以`push`开头

4.Websocket协议接口请求格式为json，也就是将请求参数包装为json字符串后发送给服务器，如获取用户信息接口：
``` javascript
{
  "action":"user",
  "token":"adfjdskajfdisidfff",
}
```

# 关于游戏规则相关介绍
#### 房间使用说明
房间共有三个状态:等待(wait),打牌(startd),结束清算(end)

|status |说明|
|:-----  |:-------  |
|wait    |这是房间的初始状态，表示人数不够或不是所有人都是ready状态，此时可以自用退出房间|
|startd  |当房间人数达到三人，且所有玩家都是ready状态，房间将进入此状态|
|end     |本局游戏结束，系统发放奖励阶段，此时可以自由退出房间|

#### 人物游戏状态
人物的游戏状态用play表示

|play |说明|
|:-----  |:-------  |
|hall    |玩家登录游戏后默认的游戏状态，此时可以选择房间进入|
|room    |玩家进入房间后的状态，此时不能在进入其它房间，可以进行ready操作|


# 1.注册

### 请求地址
> http://101.132.78.226:9503/register

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|name     |ture    |sting   |用户名|
|pass     |ture    |sting   |密码|

### 返回结果
``` javascript
{
    "code": 0, //0为成功，其它为失败
    "msg":'注册成功'  //提示信息
}
```


# 2.登录

### 请求地址
> http://101.132.78.226:9503/login

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|name     |ture    |sting   |用户名|
|pass     |ture    |sting   |密码|

### 返回结果
``` javascript
{
      "code": 0,
      "msg":"登录成功",
      "token": "adfjdskajfdisidfff",
}
```

# 3.获取用户信息

### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`user`|
|token   |ture    |sting   |登录成功后获取的token值|

### 返回结果
``` javascript
{
  "code": 0,
  "action":"reply-user",
  "msg":"获取成功",
  "name":"collin", //用户名
  "coin":123   //可用积分.
  "play":"room", //用户游戏状态，hall表示在大厅，room表示在房间，ready表示准备游戏，doing正在游戏
  "room":0, //用户所在的房间号，0表示不在房间
  "seat":'a' //用户所在房间位置
}
```

# 4.获取房间列表

### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`room`|
|token   |ture    |sting   |登录成功后获取的token值|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-room",
    "msg": "", 
    "rooms": [
        {
            "id": "1", 
            "name": "房间2", 
            "status": "start", 
            "a":0,//空位置
            "b":0,
            "c":0,
        }, 
        {
            "id": "2", 
            "name": "房间3", 
            "status": "start", 
            "a":{
                "name": "cloklo", 
                "coin": "0"
            }, 
            "b":{
                "name": "cloklo2", 
                "coin": "0"
            },
            "c":0 //空位置
        }, 
    ]
}
```

# 5.进入游戏房间

### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`room/enter`|
|token   |ture    |sting   |登录成功后获取的token值|
|id      |ture    |int     |房间id，值为0时，由系统分配房间|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-room-enter",
    "msg": "", 
    "id": "2", 
    "name": "房间3", 
    "status": "start", 
    "a": {
       "name": "cloklo", 
       "coin": "0"
    }, 
    "b":{
       "name": "cloklo2", 
       "coin": "0"
    },
    "c":0  //空位置
}
```

### 触发推送
此接口请求成功后，将向房间其余玩推送你进入房间的信息，格式如下：
``` javascript
{
    "action":"push-room-enter",
    "msg": "", 
    "name": "cloklo", 
    "coin": "0", 
    "seat":'a' //座位
}
```

# 6.退出游戏房间
### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`room/quit`|
|token   |ture    |sting   |登录成功后获取的token值|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-room-quit",
    "msg": "退出成功",
    "seat":'a' //座位 
}
```

### 触发推送
此接口请求成功后，将向房间其余玩推送你退出房间的信息，格式如下：
``` javascript
{
    "action":"push-room-quit",
    "msg": "cloklo退出房间了", 
    "seat":'a' //座位
}
```

# 7.准备游戏
### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`play/ready`|
|token   |ture    |sting   |登录成功后获取的token值|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-play-ready",
    "msg": "准备/取消准备",
}
```

### 触发推送1
此接口请求成功后，将向房间其余玩推送你的操作状态：
``` javascript
{
    "action":"push-play-ready",
    "ready": 1,   //是否准备游戏，1是，0不是
    "seat":'a' //座位
}
```

### 触发推送2
此接口请求成功后，如果所有玩家都是准备状态，将开始游戏：
``` javascript
{
    "action":"push-play-start",
    "call":'a',   //该那个座位叫地主
    "poker": [
        {"id":2,"val":23,"name":"xxxx"},
        {"id":2,"val":23,"name":"xxxx"}
    ] 
}
```

# 8.抢地主
### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`play/rob`|
|token   |ture    |sting   |登录成功后获取的token值|
|rob     |ture    |int     |是否叫/抢地主，1是，0不是|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-play-rob",
    "msg": "叫/抢地主",
    "call":'a',   //下一个抢地主的位置，当产生地主，其值为0,
}
```

### 触发推送
此接口请求成功后，将向房间所有玩家推送当前抢地主信息
``` javascript
{
    "action":"push-play-rob",
    "landowner":0,  //当值不为0，则表示地主产生，值为地主座位
    "cd":0,//是否有人叫过地主，0为没有，1为叫过，以此确定下一个玩家是抢地主还是叫地主
    "call":'a',   //下一个抢地主的位置,当产生地主时其值为0
    "pocket":[],//底牌，当地主产生时，才有此值
    
    "poker":[],//如果当前用户是地主，则产生此值，为加上底牌后重新排序的所有牌
}
```


# 9.出牌
### 请求地址
> ws://101.132.78.226:9503

### 请求参数

|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`play/lead`|
|token   |ture    |sting   |登录成功后获取的token值|
|poker   |ture    |sting   |为一组扑克ID，如'12,34,55,66'，不出牌则为空字符串|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-play-lead",
    "msg": "顺子",
    "nexter":'a',   //当前应该出牌座位,如果游戏结束，其值为0
    "residue":12 //你剩余的牌数
}
```

### 触发推送1
此接口请求成功后，将向房间其余玩推送当你的出牌，并告知下一个出牌座位
``` javascript
{
    "action":"push-play-lead",
    "seat":0,  //出牌玩家
    "poker":[12,34,35],//所出的牌
    "nexter":'a'   //当前需要出牌的座位,如果游戏结束，其值为0,
    "residue":12 //当前出牌玩家剩余牌数
}
```

### 触发推送2
此接口请求成功后，如果你已经没有剩余扑克，你为胜利者，系统将向房间所有玩家推送结束清算信息
``` javascript
{
    "action":"push-play-end",
    "landowner":0, //1地主胜利，0平民胜利
    "a":20,  //该局a位玩家所得积分
    "b":-10, //该局b位玩家所得积分
    "c":-10  //该局c位玩家所得积分
}
```

# 10.房间同步
### 请求地址
> ws://101.132.78.226:9503

### 请求参数
|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`room/synchro`|
|token   |ture    |sting   |登录成功后获取的token值|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-room-synchro",
    "msg": "", 
    "id": "2", 
    "name": "房间3", 
    "status": "start", 
    "a": {
       "name": "cloklo", 
       "coin": "0"
    }, 
    "b":{
       "name": "cloklo2", 
       "coin": "0"
    },
    "c":[]
}
```


# 11.房间聊天

### 请求地址
> ws://101.132.78.226:9503

### 请求参数

|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`chat/room`|
|token   |ture    |sting   |登录成功后获取的token值|
|text    |ture    |sting   |聊天内容|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-chat-room",
    "msg": "发送成功",
}
```

### 触发推送
此接口请求成功后，将向房间其余玩推送你的聊天内容
``` javascript
{
    "action":"push-chat-room",
    "seat":"a",  //发送聊天人的座位
    "text":"大家好",    //聊天的内容
}
```

# 12.创建游戏房间

### 请求地址
> ws://101.132.78.226:9503

### 请求参数

|参数|必填|类型|说明|
|:-----  |:-------|:-----|-----  |
|action  |ture    |sting   |请求动作,值固定为`room/establish`|
|token   |ture    |sting   |登录成功后获取的token值|
|name    |ture    |sting   |房间名字，中英文|
|pass    |false   |sting   |房间密码，留空为不设置密码|

### 返回结果
``` javascript
{
    "code": 0, 
    "action":"reply-room-establish",
    "msg": "房间创建成功",
    "id": "7", 
}
```
#### PS:
当房间创建成功，默认你已经加入此房间，此时你可以通过同步接口获取房间详细数据

# 支付相关说明

#### 商品列表
|商品ID|名称|价格|欢乐豆数量|
|:-----  |:-------|:-----|-----  |
|ddz_coin_10    |小包欢乐豆    |1   |10|
|ddz_coin_100   |大包欢乐豆    |9   |100|


## 获取商品列表接口

### 请求地址
> http://101.132.78.226:9503/call/product

### 返回结果
``` javascript
{
      "code": 0,
      "msg":"获取成功",
      "product": [{
          id: "ddz_coin_10",
          name: "小包欢乐豆",
          price: 1, //售价，单位元
          coin: 10, //获得欢乐豆数量，单位个
          
      },{
          id: "ddz_coin_100",
          name: "大包欢乐豆",
          price: 9, 
          coin: 10,
      }]
}
```


### 触发推送
当用户购买商品成功后且游戏在线，将收到购买消费推送
``` javascript
{
    "action":"push-pay",
    "msg":"你的购买已完成",
    "serial":"5d41402abc4b2a76b9719d911017c592",//本地订单号
    "order_id":"PB628718120717292738747",  //第三方订单号
    "status":0,    //订单状态，0为成功，其它为失败
}
```

# 关于断线处理

当玩家网络中断时，如果玩家在游戏房间中，将自动踢出该玩家，并向房间其它玩家推送退出消息
``` javascript
{
    "code": 0, 
    "action":"push-room-quit",
    "msg": "退出成功",
    "seat":'a' //座位 
}
```
如果已经在发牌阶段，则向房间其它玩家推送掉线消息
``` javascript
{
    "code": 0, 
    "action":"push-user-online",
    "msg": "网络异常",
    "seat":'a' //座位 
    "online":0 //掉线状态
}
```

如果已经在发牌阶段，当玩家重新链接时，则向房间其它玩家推送其上线消息
``` javascript
{
    "code": 0, 
    "action":"push-user-online",
    "msg": "网络异常",
    "seat":'a' //座位 
    "online":1 //上线状态
}
```