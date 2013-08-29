<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>
 * @license    MIT License
 */

  require __DIR__ . '/../src/Wechat.php';
  require __DIR__ . '/ExitTestHelper.php';
  require __DIR__ . '/MyWechat.php';

  /**
   * Test Base
   */
  class WechatSdkTestBase extends PHPUnit_Framework_TestCase {
    protected $token;
    protected $signature;
    protected $toUser;
    protected $fromUser;
    protected $time;
    protected $msgid;

    protected function setUp() {
      $this->token = 'wechat-php-sdk';

      $_GET['timestamp'] = time();
      $_GET['nonce'] = rand(10000000, 99999999);
      $signatureArray = array($this->token, $_GET['timestamp'], $_GET['nonce']);
      sort($signatureArray);
      $_GET['signature'] = sha1(implode($signatureArray));

      $this->signature = $_GET['signature'];

      $this->toUser = 'mp.weixin';
      $this->fromUser = 'fromUser';
      $this->time = time();
      $this->msgid = '1234567890123456';
    }

    protected function fillTextMsg($param) {
      $GLOBALS['HTTP_RAW_POST_DATA'] = "<xml>
  <ToUserName><![CDATA[$this->toUser]]></ToUserName>
  <FromUserName><![CDATA[$this->fromUser]]></FromUserName> 
  <CreateTime>$this->time</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[$param]]></Content>
  <MsgId>$this->msgid</MsgId>
</xml>";
    }

    protected function fillImageMsg($param) {
      $GLOBALS['HTTP_RAW_POST_DATA'] = "<xml>
  <ToUserName><![CDATA[$this->toUser]]></ToUserName>
  <FromUserName><![CDATA[$this->fromUser]]></FromUserName> 
  <CreateTime>$this->time</CreateTime>
  <MsgType><![CDATA[image]]></MsgType>
  <PicUrl><![CDATA[$param]]></PicUrl>
  <MsgId>$this->msgid</MsgId>
</xml>";
    }

    protected function fillLocationMsg($x, $y) {
      $GLOBALS['HTTP_RAW_POST_DATA'] = "<xml>
  <ToUserName><![CDATA[$this->toUser]]></ToUserName>
  <FromUserName><![CDATA[$this->fromUser]]></FromUserName>
  <CreateTime>$this->time</CreateTime>
  <MsgType><![CDATA[location]]></MsgType>
  <Location_X>$x</Location_X>
  <Location_Y>$y</Location_Y>
  <Scale>20</Scale>
  <Label><![CDATA[位置信息]]></Label>
  <MsgId>$this->msgid</MsgId>
</xml>";
    }

    protected function fillLinkMsg($title, $description, $url) {
      $GLOBALS['HTTP_RAW_POST_DATA'] = "<xml>
  <ToUserName><![CDATA[$this->toUser]]></ToUserName>
  <FromUserName><![CDATA[$this->fromUser]]></FromUserName>
  <CreateTime>$this->time</CreateTime>
  <MsgType><![CDATA[link]]></MsgType>
  <Title><![CDATA[$title]]></Title>
  <Description><![CDATA[$description]]></Description>
  <Url><![CDATA[$url]]></Url>
  <MsgId>$this->msgid</MsgId>
</xml>";
    }

    protected function fillUnknown($param) {
      $GLOBALS['HTTP_RAW_POST_DATA'] = "<xml>
  <ToUserName><![CDATA[$this->toUser]]></ToUserName>
  <FromUserName><![CDATA[$this->fromUser]]></FromUserName>
  <CreateTime>$this->time</CreateTime>
  <MsgType><![CDATA[who-knowns]]></MsgType>
  <Unknown><![CDATA[$param]]></Unknown>
  <MsgId>$this->msgid</MsgId>
</xml>";
    }

    protected function fillEvent($event, $eventKey = '') {
      $GLOBALS['HTTP_RAW_POST_DATA'] = "<xml>
  <ToUserName><![CDATA[$this->toUser]]></ToUserName>
  <FromUserName><![CDATA[$this->fromUser]]></FromUserName>
  <CreateTime>$this->time</CreateTime>
  <MsgType><![CDATA[event]]></MsgType>
  <Event><![CDATA[$event]]></Event>
  <EventKey><![CDATA[$eventKey]]></EventKey>
  <MsgId>$this->msgid</MsgId>
</xml>";
    }

  }
?>