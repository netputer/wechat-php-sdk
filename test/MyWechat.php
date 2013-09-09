<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>
 * @license    MIT License
 */ 

  /**
   * 测试对象
   */
  class MyWechat extends Wechat {

    /**
     * 方法getRequest的测试钩子
     */
    public function publicGetRequest($param = FALSE) {
      return parent::getRequest($param);
    }

    /**
     * 方法responseText的测试钩子
     */
    public function publicResponseText($content, $funcFlag = 0) {
      parent::responseText($content, $funcFlag);
    }

    /**
     * 方法responseMusic的测试钩子
     */
    public function publicResponseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0) {
      parent::responseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag);
    }

    /**
     * 方法responseNews的测试钩子
     */
    public function publicResponseNews($items, $funcFlag = 0) {
      parent::responseNews($items, $funcFlag);
    }



    /**
     * 用户关注时触发，已mock
     *
     * @return void
     */
    protected function onSubscribe() {
      // 已mock
    }

    /**
     * 用户取消关注时触发，已mock
     *
     * @return void
     */
    protected function onUnsubscribe() {
      // 已mock
    }

    /**
     * 收到文本消息时触发，已mock
     *
     * @return void
     */
    protected function onText() {
      // 已mock
    }

    /**
     * 收到图片消息时触发，已mock
     *
     * @return void
     */
    protected function onImage() {
     // 已mock
    }

    /**
     * 收到地理位置消息时触发，已mock
     *
     * @return void
     */
    protected function onLocation() {
      // 已mock
    }

    /**
     * 收到链接消息时触发，已mock
     *
     * @return void
     */
    protected function onLink() {
      // 已mock
    }

    /**
     * 收到未知类型消息时触发，已mock
     *
     * @return void
     */
    protected function onUnknown() {
      // 已mock
    }

  }
?>