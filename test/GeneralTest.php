<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>
 * @license    MIT License
 */
  
  require_once __DIR__ . '/SdkTestBase.php';

  /**
   * General Test
   */
  class WechatSdkGeneralTest extends WechatSdkTestBase {

    protected function setUp() {
      parent::setUp();
    }

    public function testApiValidation() {
      ExitTestHelper::init();

      $echostr = '9eabb7918cbad53305f7eae647cf1402e2fc7836';
      $_GET['echostr'] = $echostr;

      $wechat = new MyWechat($this->token);
      $this->assertEquals($echostr, ExitTestHelper::getFirstExitOutput(), 'Wechat API validation output should match the input.');

      ExitTestHelper::clean();
    }

    public function testBlankSignature() {
      ExitTestHelper::init();

      $_GET['signature'] = '';
      $wechat = new MyWechat($this->token);
      $this->assertEquals('签名验证失败', ExitTestHelper::getFirstExitOutput(), 'Signature verification should fail.');

      ExitTestHelper::clean();
    }

    public function testEmptyPOST() {
      ExitTestHelper::init();

      $wechat = new MyWechat($this->token);
      $this->assertEquals('缺少数据', ExitTestHelper::getFirstExitOutput(), 'SDK should output "no data" (in chinese, utf-8).');

      ExitTestHelper::clean();
    }

  }
?>