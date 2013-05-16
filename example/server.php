<?php

  require('../src/Wechat.php');

  class MyWechat extends Wechat {

    protected function onSubscribe() {
      $this->responseText('欢迎关注');
    }

    protected function onUnsubscribe() {
      // 悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。
    }

    protected function onText() {
      $this->responseText('收到了文字消息：' . $this->getRequest('content'));
    }

    protected function onImage() {
      $this->responseText('收到了图片消息：' . $this->getRequest('picurl'));
    }

    protected function onLocation() {
      $this->responseText('收到了位置消息：' . $this->getRequest('location_x') . ',' . $this->getRequest('location_y'));
    }

    protected function onLink() {
      $this->responseText('收到了链接：' . $this->getRequest('url'));
    }

    protected function onUnknown() {
      $this->responseText('收到了未知类型消息：' . $this->getRequest('msgtype'));
    }

  }

  $wechat = new MyWechat('Your Token');
  $wechat->run();
