<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author NetPuter <netputer@gmail.com>
 */
  include_once "wxBizMsgCrypt.php";
  include_once "config.php";
  /**
   * 微信公众平台处理类
   */
  class Wechat {

    /**
     * 调试模式，将错误通过文本消息回复显示
     *
     * @var boolean
     */
    private $debug;

    /**
     * 以数组的形式保存微信服务器每次发来的请求
     *
     * @var array
     */
    private $request;

    /**
     * WXBizMsgCrypt
     *
     * @var WXBizMsgCrypt
     */
    private $msgCryptor;

    /**
     * If msg is in crypt mode
     *
     * @var boolean
     */
    private $encrypted = false;

    /**
     * 初始化，判断此次请求是否为验证请求，并以数组形式保存
     *
     * @param string $token 验证信息
     * @param boolean $debug 调试模式，默认为关闭
     */
    public function __construct($config=array('token'=>'', 'aeskey'=>'', 'appid'=>'', 'debug' => FALSE)) {
      
      $token = $config['token'];
      $aeskey = $config['aeskey'];
      $appid = $config['appid'];
      $debug = $config['debug'];
      
      if (!$this->validateSignature($token)) {
        exit('签名验证失败');
      }
      
      if ($this->isValidateIncomingConn()) {
        // 网址接入验证
        exit($_GET['echostr']);
      }
      
      if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
        exit('缺少数据');
      }

      $this->debug = $debug;
      set_error_handler(array(&$this, 'errorHandler'));
      // 设置错误处理函数，将错误通过文本消息回复显示

      if (isset($_GET['encrypt_type'])) {
        $this->encrypted = $_GET['encrypt_type'] == 'aes';
      }

      if ($this->encrypted) {
        $this->msgCryptor = new wxBizMsgCrypt($token, $aeskey, $appid);
      }

      $this->savePostData();

    }

    private function savePostData() {
      $xml = '';

      if ($this->encrypted) {
        $errCode = $this->msgCryptor->decryptMsg($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $GLOBALS['HTTP_RAW_POST_DATA'], $xml);

        if ($errCode != 0) exit($errCode);

      } else {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
      }

      $xml = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);        

      $this->request = array_change_key_case($xml, CASE_LOWER);
      // 将数组键名转换为小写，提高健壮性，减少因大小写不同而出现的问题
    }

    /**
     * 判断此次请求是否为验证请求
     *
     * @return boolean
     */
    private function isValidateIncomingConn() {
      return isset($_GET['echostr']);
    }

    /**
     * 验证此次请求的签名信息
     *
     * @param  string $token 验证信息
     * @return boolean
     */
    private function validateSignature($token) {
      if ( ! (isset($_GET['signature']) && isset($_GET['timestamp']) && isset($_GET['nonce']))) {
        return FALSE;
      }
      
      $signature = $_GET['signature'];
      $timestamp = $_GET['timestamp'];
      $nonce = $_GET['nonce'];

      $signatureArray = array($token, $timestamp, $nonce);
      sort($signatureArray,SORT_STRING);

      return sha1(implode($signatureArray)) == $signature;
    }

    /**
     * 获取本次请求中的参数，不区分大小
     *
     * @param  string $param 参数名，默认为无参
     * @return mixed
     */
    protected function getRequest($param = FALSE) {
      if ($param === FALSE) {
        return $this->request;
      }

      $param = strtolower($param);

      if (isset($this->request[$param])) {
        return $this->request[$param];
      }

      return NULL;
    }

    /**
     * 用户关注时触发，用于子类重写
     *
     * @return void
     */
    protected function onSubscribe() {}

    /**
     * 用户取消关注时触发，用于子类重写
     *
     * @return void
     */
    protected function onUnsubscribe() {}

    /**
     * 收到文本消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onText() {}

    /**
     * 收到图片消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onImage() {}

    /**
     * 收到地理位置消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onLocation() {}

    /**
     * 收到链接消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onLink() {}

    /**
     * 收到自定义菜单消息时触发，用于子类重写
     *
     * @return void
     */    
    protected function onClick() {}

    /**
     * 收到地理位置事件消息时触发，用于子类重写
     *
     * @return void
     */    
    protected function onEventLocation() {}

    /**
     * 收到语音消息时触发，用于子类重写
     *
     * @return void
     */        
    protected function onVoice() {}

    /**
     * 扫描二维码时触发，用于子类重写
     *
     * @return void
     */        
    protected function onScan() {}

    /**
     * 收到未知类型消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onUnknown() {}


    /**
      * 输出消息
      * @return [encypted] msg
      */

    private function sendout($msg) {
      if ($this->encrypted) {
        $errCode = $this->msgCryptor->encryptMsg($msg, $this->getRequest('timestamp'), $this->getRequest('nonce'), $msg);
        if ($errCode != 0) exit ($errCode);
      }
      exit($msg);
    }

    /**
     * 回复文本消息
     *
     * @param  string  $content  消息内容
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function responseText($content, $funcFlag = 0) {
      $this->sendout(new TextResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $content, $funcFlag));
    }

    /**
     * 回复音乐消息
     *
     * @param  string  $title       音乐标题
     * @param  string  $description 音乐描述
     * @param  string  $musicUrl    音乐链接
     * @param  string  $hqMusicUrl  高质量音乐链接，Wi-Fi 环境下优先使用
     * @param  integer $funcFlag    默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function responseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0) {
      $this->sendout(new MusicResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $title, $description, $musicUrl, $hqMusicUrl, $funcFlag));
    }

    /**
     * 回复图文消息
     * @param  array   $items    由单条图文消息类型 NewsResponseItem() 组成的数组
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function responseNews($items, $funcFlag = 0) {
      $this->sendout(new NewsResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $items, $funcFlag));
    }

    /**
     * 分析消息类型，并分发给对应的函数
     *
     * @return void
     */
    public function run() {
      switch ($this->getRequest('msgtype')) {

        case 'event':
          switch ($this->getRequest('event')) {

            case 'subscribe':
              $this->onSubscribe();
              break;

            case 'unsubscribe':
              $this->onUnsubscribe();
              break;

            case 'SCAN':
              $this->onScan();
              break;

            case 'LOCATION':
              $this->onEventLocation();
              break;

            case 'CLICK':
              $this->onClick();
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

        case 'voice':
          $this->onVoice();
          break;

        default:
          $this->onUnknown();
          break;

      }
    }

    /**
     * 自定义的错误处理函数，将 PHP 错误通过文本消息回复显示
     * @param  int $level   错误代码
     * @param  string $msg  错误内容
     * @param  string $file 产生错误的文件
     * @param  int $line    产生错误的行数
     * @return void
     */
    public function errorHandler($level, $msg, $file, $line) {
      if ( ! $this->debug) {
        return;
      }

      $error_type = array(
        // E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        // E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',
        // E_CORE_ERROR        => 'Core Error',
        // E_CORE_WARNING      => 'Core Warning',
        // E_COMPILE_ERROR     => 'Compile Error',
        // E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Strict',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
      );

      $template = <<<ERR
PHP 报错啦！

%s: %s
File: %s
Line: %s
ERR;

      $this->responseText(sprintf($template,
        $error_type[$level],
        $msg,
        $file,
        $line
      ));
    }

  }

  /**
   * 用于回复的基本消息类型
   */
  abstract class WechatResponse {

    protected $toUserName;
    protected $fromUserName;
    protected $funcFlag;
    protected $template;

    public function __construct($toUserName, $fromUserName, $funcFlag) {
      $this->toUserName = $toUserName;
      $this->fromUserName = $fromUserName;
      $this->funcFlag = $funcFlag;
    }

    abstract public function __toString();

  }

  /**
   * 用于回复的文本消息类型
   */
  class TextResponse extends WechatResponse {

    protected $content;

    public function __construct($toUserName, $fromUserName, $content, $funcFlag = 0) {
      parent::__construct($toUserName, $fromUserName, $funcFlag);

      $this->content = $content;
      $this->template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[%s]]></Content>
  <FuncFlag>%s</FuncFlag>
</xml>
XML;
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

  /**
   * 用于回复的音乐消息类型
   */
  class MusicResponse extends WechatResponse {

    protected $title;
    protected $description;
    protected $musicUrl;
    protected $hqMusicUrl;

    public function __construct($toUserName, $fromUserName, $title, $description, $musicUrl, $hqMusicUrl, $funcFlag) {
      parent::__construct($toUserName, $fromUserName, $funcFlag);

      $this->title = $title;
      $this->description = $description;
      $this->musicUrl = $musicUrl;
      $this->hqMusicUrl = $hqMusicUrl;
      $this->template = <<<XML
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
  <FuncFlag>%s</FuncFlag>
</xml>
XML;
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

  /**
   * 用于回复的图文消息类型
   */
  class NewsResponse extends WechatResponse {

    protected $items = array();

    public function __construct($toUserName, $fromUserName, $items, $funcFlag) {
      parent::__construct($toUserName, $fromUserName, $funcFlag);

      $this->items = $items;
      $this->template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[news]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>
    %s
  </Articles>
  <FuncFlag>%s</FuncFlag>
</xml>
XML;
    }

    public function __toString() {
      return sprintf($this->template,
        $this->toUserName,
        $this->fromUserName,
        time(),
        count($this->items),
        implode($this->items),
        $this->funcFlag
      );
    }

  }

  /**
   * 单条图文消息类型
   */
  class NewsResponseItem {

    protected $title;
    protected $description;
    protected $picUrl;
    protected $url;
    protected $template;

    public function __construct($title, $description, $picUrl, $url) {
      $this->title = $title;
      $this->description = $description;
      $this->picUrl = $picUrl;
      $this->url = $url;
      $this->template = <<<XML
<item>
  <Title><![CDATA[%s]]></Title>
  <Description><![CDATA[%s]]></Description>
  <PicUrl><![CDATA[%s]]></PicUrl>
  <Url><![CDATA[%s]]></Url>
</item>
XML;
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
