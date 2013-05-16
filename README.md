微信公众平台 PHP SDK
=====

介绍
-----
简单的微信公众平台 PHP SDK ，通过调用相应的接口，使你可以轻松地开发微信 App 。

用法
-----
1. Clone 或下载项目源码。

2. 打开 `/example/server.php` 文件，将实例化 `MyWechat` 时传入的参数改为你的 Token 。

3. 进入[微信公众平台](https://mp.weixin.qq.com/)，高级功能，开启开发模式，并设置接口配置信息。其中 `URL` 为 `/example/server.php` 的实际位置， `Token` 为上一步设置的 Token 。

4. 向你的微信公众号发送消息并测试吧！

TODO
-----
1. 完善文档和注释；
2. 完善异常处理；
3. 提供 Composer 方式安装。
