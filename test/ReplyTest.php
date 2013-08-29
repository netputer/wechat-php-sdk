<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>
 * @license    MIT License
 */
  
  require_once __DIR__ . '/SdkTestBase.php';

  /**
   * Reply Test
   */
  class WechatSdkReplyTest extends WechatSdkTestBase {
    protected $response;

    protected function setUp() {
      parent::setUp();

      $this->response = NULL;
    }

    private function setResponse($response) {
      $xml = (array) simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
      $this->response = array_change_key_case($xml, CASE_LOWER);
    }

    private function getResponseField($key) {
      if (isset($this->response[$key])) {
        return $this->response[$key];
      } else {
        return NULL;
      }
    }

    public function testReplyText() {
      ExitTestHelper::init();

      $this->fillTextMsg('收到文本消息');
      $wechat = new MyWechat($this->token);

      // 发出回复
      $wechat->publicResponseText('回复文本消息');

      // 截获输出，解析
      $this->setResponse(ExitTestHelper::getFirstExitOutput());

      // 回复的to、from与填充的传入消息的to、from相反
      $this->assertEquals($this->fromUser, $this->getResponseField('tousername'));
      $this->assertEquals($this->toUser, $this->getResponseField('fromusername'));
      $this->assertEquals('0', $this->getResponseField('funcflag'));
      $this->assertEquals('text', $this->getResponseField('msgtype'));

      // 验证文本回复内容
      $this->assertEquals('回复文本消息', $this->getResponseField('content'));

      ExitTestHelper::clean();
    }

    public function testReplyMusic() {
      ExitTestHelper::init();

      $this->fillTextMsg('收到文本消息');
      $wechat = new MyWechat($this->token);

      // 回复音乐消息
      $wechat->publicResponseMusic('音乐标题',
                                   '音乐说明',
                                   'http://sample.net/music.mp3',
                                   'http://sample.net/hqmusic.mp3');

      // 截获输出，解析
      $this->setResponse(ExitTestHelper::getFirstExitOutput());

      // 回复的to、from与填充的传入消息的to、from相反
      $this->assertEquals($this->fromUser, $this->getResponseField('tousername'));
      $this->assertEquals($this->toUser, $this->getResponseField('fromusername'));
      $this->assertEquals('0', $this->getResponseField('funcflag'));
      $this->assertEquals('music', $this->getResponseField('msgtype'));

      // 验证音乐消息内容
      $music = array_change_key_case((array) $this->getResponseField('music'), CASE_LOWER);
      $this->assertEquals(array(
                            'title' => '音乐标题',
                            'description' => '音乐说明',
                            'musicurl' => 'http://sample.net/music.mp3',
                            'hqmusicurl' => 'http://sample.net/hqmusic.mp3'),
                            $music);

      ExitTestHelper::clean();
    }

    public function testReplyNews() {
      ExitTestHelper::init();

      $this->fillTextMsg('收到文本消息');
      $wechat = new MyWechat($this->token);

      $items = array(
        new NewsResponseItem('Travis CI',
                                   'Free Hosted Continuous Integration Platform for the Open Source Community',
                                   'https://travis-ci.org/netputer/wechat-php-sdk.png',
                                   'https://travis-ci.org/netputer/wechat-php-sdk'),
        new NewsResponseItem('Travis CI 2',
                                   '2 Free Hosted Continuous Integration Platform for the Open Source Community',
                                   'https://travis-ci.org/netputer/wechat-php-sdk.png',
                                   'https://travis-ci.org/netputer/wechat-php-sdk')
      );

      // 回复图文消息
      $wechat->publicResponseNews($items);

      // 截获输出，解析
      $this->setResponse(ExitTestHelper::getFirstExitOutput());

      // 回复的to、from与填充的传入消息的to、from相反
      $this->assertEquals($this->fromUser, $this->getResponseField('tousername'));
      $this->assertEquals($this->toUser, $this->getResponseField('fromusername'));
      $this->assertEquals('0', $this->getResponseField('funcflag'));
      $this->assertEquals('news', $this->getResponseField('msgtype'));

      // 验证图文消息内容
      $this->assertEquals('2', $this->getResponseField('articlecount'));

      $articles = (array) $this->getResponseField('articles');
      $article = array_change_key_case((array) $articles['item'][0], CASE_LOWER);
      $this->assertEquals(array(
                            'title' => 'Travis CI',
                            'description' => 'Free Hosted Continuous Integration Platform for the Open Source Community',
                            'picurl' => 'https://travis-ci.org/netputer/wechat-php-sdk.png',
                            'url' => 'https://travis-ci.org/netputer/wechat-php-sdk'),
                            $article);
      $article = array_change_key_case((array) $articles['item'][1], CASE_LOWER);
      $this->assertEquals(array(
                            'title' => 'Travis CI 2',
                            'description' => '2 Free Hosted Continuous Integration Platform for the Open Source Community',
                            'picurl' => 'https://travis-ci.org/netputer/wechat-php-sdk.png',
                            'url' => 'https://travis-ci.org/netputer/wechat-php-sdk'),
                            $article);

      ExitTestHelper::clean();
    }


  }
?>