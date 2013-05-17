微信公众平台 PHP SDK
=====

介绍
-----
简单的微信公众平台 PHP SDK ，通过调用相应的接口，使你可以轻松地开发微信 App 。测试方法如下：

  1. Clone 或下载项目源码，上传至服务器。

  2. 进入[微信公众平台](https://mp.weixin.qq.com/)，高级功能，开启开发模式，并设置接口配置信息。修改 `URL` 为 `/example/server.php` 的实际位置，修改 `Token` 为 `weixin` （可自行在 `/example/server.php` 中更改）。

  3. 向你的微信公众号发送消息并测试吧！

用法
-----

直接浏览 `/example/server.php` 了解基本用法，以下为详细说明。

通过继承 `Wechat` 类进行扩展，通过重写 `onSubscribe()` 等方法响应关注等请求：

```php
class MyWechat extends Wechat {
  protected function onSubscribe() {} // 用户关注
  protected function onUnsubscribe() {} // 用户取消关注

  protected function onText() {
    // 收到文本消息时触发，此处为响应代码
  }

  protected function onImage() {} // 收到图片消息
  protected function onLocation() {} // 收到地理位置消息
  protected function onLink() {} // 收到链接消息
  protected function onUnknown() {} // 收到未知类型消息
}
```
-----

使用 `getRequest()` 可以获取本次请求中的参数（不区分大小写）：

```php
$this->getRequest();
// 无参数时，返回包含所有参数的数组

$this->getRequest('msgtype');
// 有参数且参数存在时，返回该参数的值

$this->getRequest('ghost');
// 有参数但参数不存在时，返回 NULL
```

所有请求均包含：

```
ToUserName    接收方帐号（该公众号ID）
FromUserName  发送方帐号（代表用户的唯一标识）
CreateTime    消息创建时间（时间戳）
MsgId         消息ID（64位整型）
```

文本消息请求：

```
MsgType  text
Content  文本消息内容
```

图片消息请求：

```
MsgType  image
PicUrl   图片链接
```

地理位置消息请求：

```
MsgType     location
Location_X  地理位置纬度
Location_Y  地理位置经度
Scale       地图缩放大小
Label       地理位置信息
```

链接消息请求：

```
MsgType      link
Title        消息标题
Description  消息描述
Url          消息链接
```

事件推送：

```
MsgType   event
Event     事件类型
EventKey  事件 Key 值，与自定义菜单接口中 Key 值对应
```

其中，事件类型 `Event` 的值包括以下几种：

```
subscribe    关注
unsubscribe  取消关注
CLICK        自定义菜单点击事件（未验证）
```
-----

使用 `responseText()` 方法回复文本消息：

```php
$this->responseText(
  $content,  // 消息内容
  $funcFlag  // 可选参数（默认为0），设为1时星标刚才收到的消息
);
```

使用 `responseMusic()` 方法回复音乐消息：

```php
$this->responseMusic(
  $title,        // 音乐标题
  $description,  // 音乐描述
  $musicUrl,     // 音乐链接
  $hqMusicUrl,   // 高质量音乐链接，Wi-Fi 环境下优先使用
  $funcFlag      // 可选参数，默认为0，设为1时星标刚才收到的消息
);
```

使用 `responseNews()` 方法回复图文消息：

```php
$this->responseNews(
  $items,    // 由单条图文消息类型 NewsResponseItem() 组成的数组
  $funcFlag  // 可选参数，默认为0，设为1时星标刚才收到的消息
)
```

其中单条图文消息类型 `NewsResponseItem()` 格式如下：

```php
$items[] = new NewsResponseItem(
  $title,        // 图文消息标题
  $description,  // 图文消息描述
  $picUrl,       // 图片链接
  $url           // 点击图文消息跳转链接
);
```
-----

最后，实例化 `MyWechat()` 并调用 `run()` 方法即可运行。

```php
$wechat = new MyWechat(
  $token,  // 你在公众平台设置的 Token
  $debug   // 调试模式，默认为 FALSE ，设为 TRUE 后可将错误通过文本消息回复显示
);

$wechat->run();
```

TODO
-----
1. 完善文档和注释；
2. 完善异常处理；
3. 提供 Composer 方式安装。
