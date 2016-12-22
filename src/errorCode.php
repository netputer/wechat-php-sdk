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
    static  $OK = 0;
    static  $ValidateSignatureError = -40001;
    static  $ParseXmlError = -40002;
    static  $ComputeSignatureError = -40003;
    static  $IllegalAesKey = -40004;
    static  $ValidateAppidError = -40005;
    static  $EncryptAESError = -40006;
    static  $DecryptAESError = -40007;
    static  $IllegalBuffer = -40008;
    static  $EncodeBase64Error = -40009;
    static  $DecodeBase64Error = -40010;
    static  $GenReturnXmlError = -40011;
  }
