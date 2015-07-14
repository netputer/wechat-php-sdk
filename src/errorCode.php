<?php

/**
 * error code 说明.
 * <ul>
 *    <li>-40001: 签名验证错误</li>
 *    <li>-40002: xml解析失败</li>
 *    <li>-40003: sha加密生成签名失败</li>
 *    <li>-40004: encodingAesKey 非法</li>
 *    <li>-40005: appid 校验错误</li>
 *    <li>-40006: aes 加密失败</li>
 *    <li>-40007: aes 解密失败</li>
 *    <li>-40008: 解密后得到的buffer非法</li>
 *    <li>-40009: base64加密失败</li>
 *    <li>-40010: base64解密失败</li>
 *    <li>-40011: 生成xml失败</li>
 * </ul>
 */
  class ErrorCode
  {
    public  $OK = 0;
    public  $ValidateSignatureError = -40001;
    public  $ParseXmlError = -40002;
    public  $ComputeSignatureError = -40003;
    public  $IllegalAesKey = -40004;
    public  $ValidateAppidError = -40005;
    public  $EncryptAESError = -40006;
    public  $DecryptAESError = -40007;
    public  $IllegalBuffer = -40008;
    public  $EncodeBase64Error = -40009;
    public  $DecodeBase64Error = -40010;
    public  $GenReturnXmlError = -40011;
  }

