<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>
 * @license    MIT License
 */
  
  require_once __DIR__ . '/SdkTestBase.php';

  /**
   * Event Test
   */
  class WechatSdkEventTest extends WechatSdkTestBase {
    protected $mockBuilder;

    protected function setUp() {
      parent::setUp();

      $this->mockBuilder = $this->getMockBuilder('MyWechat')
                                ->setMethods(array('onSubscribe', 'onUnsubscribe', 'onText', 'onImage', 'onLocation', 'onLink', 'onUnknown'))
                                ->setConstructorArgs(array($this->token));
    }

    public function testGeneralFields() {
      ExitTestHelper::init();

      $this->fillTextMsg('填充消息');
      $wechat = $this->mockBuilder->getMock();

      // 无需执行run()， 所有字段应已解析完毕
      $this->assertEquals($this->toUser, $wechat->publicGetRequest('tousername'));
      $this->assertEquals($this->fromUser, $wechat->publicGetRequest('fromusername'));
      $this->assertEquals($this->time, $wechat->publicGetRequest('createtime'));
      $this->assertEquals($this->msgid, $wechat->publicGetRequest('msgid'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");
      ExitTestHelper::clean();

    }

    public function testEventOnSubscribe() {
      ExitTestHelper::init();

      $this->fillEvent('subscribe');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onSubscribe');

      $wechat->run();

      $this->assertEquals('', $wechat->publicGetRequest('eventkey'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

    public function testEventOnUnsubscribe() {
      ExitTestHelper::init();

      $this->fillEvent('unsubscribe');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onUnsubscribe');

      $wechat->run();

      $this->assertEquals('', $wechat->publicGetRequest('eventkey'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

    public function testEventOnUnknown() {
      ExitTestHelper::init();

      $this->fillUnknown('unknown info');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onUnknown');

      $wechat->run();

      $this->assertEquals('unknown info', $wechat->publicGetRequest('unknown'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

    public function testEventOnText() {
      ExitTestHelper::init();

      $this->fillTextMsg('填充文本消息');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onText');

      $wechat->run();

      $this->assertEquals('填充文本消息', $wechat->publicGetRequest('content'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

    public function testEventOnImage() {
      ExitTestHelper::init();

      $this->fillImageMsg('https://travis-ci.org/netputer/wechat-php-sdk.png');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onImage');

      $wechat->run();

      $this->assertEquals('https://travis-ci.org/netputer/wechat-php-sdk.png', $wechat->publicGetRequest('picurl'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

    public function testEventOnLocation() {
      ExitTestHelper::init();

      $this->fillLocationMsg('23.134521', '113.358803');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onLocation');

      $wechat->run();

      $this->assertEquals('23.134521', $wechat->publicGetRequest('location_x'));
      $this->assertEquals('113.358803', $wechat->publicGetRequest('location_y'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

    public function testEventOnLink() {
      ExitTestHelper::init();

      $this->fillLinkMsg('netputer/wechat-php-sdk', '微信公众平台 PHP SDK', 'https://github.com/netputer/wechat-php-sdk');
      $wechat = $this->mockBuilder->getMock();
      $wechat->expects($this->once())
             ->method('onLink');

      $wechat->run();

      $this->assertEquals('netputer/wechat-php-sdk', $wechat->publicGetRequest('title'));
      $this->assertEquals('微信公众平台 PHP SDK', $wechat->publicGetRequest('description'));
      $this->assertEquals('https://github.com/netputer/wechat-php-sdk', $wechat->publicGetRequest('url'));

      // 应无exit
      $this->assertFalse(ExitTestHelper::isThereExit(), "There shouldn't be any exit() was invoked.");

      ExitTestHelper::clean();
    }

  }
?>