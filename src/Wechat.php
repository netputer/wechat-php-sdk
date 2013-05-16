<?php

  class Wechat {

    private $request;

    public function __construct($token) {
      if ($this->isValid() && $this->validateSignature($token)) {
        exit($_GET['echostr']);
      }

      $xml = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);

      $this->request = array_change_key_case($xml, CASE_LOWER);
    }

    private function isValid() {
      return isset($_GET['echostr']);
    }

    private function validateSignature($token) {
      $signature = $_GET['signature'];
      $timestamp = $_GET['timestamp'];
      $nonce = $_GET['nonce'];

      $signatureArray = array($token, $timestamp, $nonce);
      sort($signatureArray);

      return sha1(implode($signatureArray)) == $signature;
    }

    public function getRequest($key = FALSE) {
      if ($key === FALSE) {
        return $this->request;
      }

      $key = strtolower($key);

      if (isset($this->request[$key])) {
        return $this->request[$key];
      }

      return NULL;
    }

    protected function onSubscribe() {}
    protected function onUnsubscribe() {}
    protected function onText() {}
    protected function onImage() {}
    protected function onLocation() {}
    protected function onLink() {}
    protected function onUnknown() {}

    public function run() {
      // 分析消息类型，分发给相应的处理函数
      switch ($this->getRequest('msgtype')) {

        case 'event':
          switch ($this->getRequest('event')) {

            case 'subscribe':
              $this->onSubscribe();
              break;

            case 'unsubscribe':
              $this->onUnsubscribe();
              break;

          }

          break;

        case 'text':
          $this->onText();
          break;

        case 'image':
          $this->onImage();
          break;

        case 'location':
          $this->onLocation();
          break;

        case 'link':
          $this->onLink();
          break;

        default:
          $this->onUnknown();
          break;

      }
    }

    protected function responseText($content, $funcFlag = 0) {
      exit(new TextResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $content, $funcFlag));
    }

    protected function responseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0) {
      exit(new MusicResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $title, $description, $musicUrl, $hqMusicUrl, $funcFlag));
    }

    protected function responseNews($items, $funcFlag = 0) {
      exit(new NewsResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $items, $funcFlag));
    }

  }



  class WechatResponse {

    protected $toUserName;
    protected $fromUserName;
    protected $funcFlag;

  }

  class TextResponse extends WechatResponse {

    protected $content;

    protected $template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[%s]]></Content>
  <FuncFlag>%s<FuncFlag>
</xml>
XML;

    public function __construct($toUserName, $fromUserName, $content, $funcFlag = 0) {
      $this->toUserName = $toUserName;
      $this->fromUserName = $fromUserName;
      $this->content = $content;
      $this->funcFlag = $funcFlag;
    }

    public function __toString() {
      return sprintf($this->template,
        $this->toUserName,
        $this->fromUserName,
        time(),
        $this->content,
        $this->funcFlag
      );
    }

  }

  class MusicResponse extends WechatResponse {

    protected $title;
    protected $description;
    protected $musicUrl;
    protected $hqMusicUrl;

    protected $template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[music]]></MsgType>
  <Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
  </Music>
  <FuncFlag>%s<FuncFlag>
</xml>
XML;

    public function __construct($toUserName, $fromUserName, $title, $description, $musicUrl, $hqMusicUrl, $funcFlag) {
      $this->toUserName = $toUserName;
      $this->fromUserName = $fromUserName;
      $this->title = $title;
      $this->description = $description;
      $this->musicUrl = $musicUrl;
      $this->hqMusicUrl = $hqMusicUrl;
      $this->funcFlag = $funcFlag;
    }

    public function __toString() {
      return sprintf($this->template,
        $this->toUserName,
        $this->fromUserName,
        time(),
        $this->title,
        $this->description,
        $this->musicUrl,
        $this->hqMusicUrl,
        $this->funcFlag
      );
    }

  }

  class NewsResponse extends WechatResponse {

    protected $items = array();

    protected $template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[news]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>
    %s
  </Articles>
  <FuncFlag>%s<FuncFlag>
</xml>'
XML;

    public function __construct($toUserName, $fromUserName, $items, $funcFlag) {
      $this->toUserName = $toUserName;
      $this->fromUserName = $fromUserName;
      $this->items = $items;
      $this->funcFlag = $funcFlag;
    }

    public function __toString() {
      return sprintf($this->template,
        $this->toUserName,
        $this->fromUserName,
        time(),
        count($this->items),
        implode(NULL, $this->items),
        $this->funcFlag
      );
    }

  }

  class NewsResponseItem {

    protected $title;
    protected $description;
    protected $picUrl;
    protected $url;

    protected $template = <<<XML
<item>
  <Title><![CDATA[%s]]></Title>
  <Description><![CDATA[%s]]></Description>
  <PicUrl><![CDATA[%s]]></PicUrl>
  <Url><![CDATA[%s]]></Url>
</item>
XML;

    public function __construct($title, $description, $picUrl, $url) {
      $this->title = $title;
      $this->description = $description;
      $this->picUrl = $picUrl;
      $this->url = $url;
    }

    public function __toString() {
      return sprintf($this->template,
        $this->title,
        $this->description,
        $this->picUrl,
        $this->url
      );
    }

  }
