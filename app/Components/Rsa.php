<?php
/**
 * User: xiaoqing Email: liuxiaoqing437@gmail.com
 * Date: 2018/3/20
 * Time: 下午5:28
 * rsa签名类
 */

namespace App\Components;

class Rsa
{
    /**
     * 生成RSA密钥对
     * 注意:此函数性能一般，生成一个默认配置的密钥对在普通服务器上约需要20ms左右
     * 需要防止在短时间内生成大量密钥对，消耗大量的服务器资源，避免外部发起大量请求
     * @param array $config 配置，格式为openssl_pkey_new()使用的格式，留空则使用默认配置:RSA, sha512，1024bit
     * @return array [私钥, 公钥]
     */
    public static function genKeyPairs($config = []){
        $config = $config ?? [
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
                "digest_alg" => "sha512",
                "private_key_bits" => 1024,
            ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);
        $pubKey = openssl_pkey_get_details($res);
        openssl_free_key($res);

        return [$privKey, $pubKey["key"]];
    }

    /**
     * 使用RSA计算签名
     * @param string $data 待签名数据
     * @param string $privKey 私钥(必须经过格式化)
     * @return string $sign 签名字串
     */
    public static function sign($data, $privKey){
        $res = openssl_pkey_get_private($privKey);
        openssl_sign($data, $sig, $res);
        $sign = base64_encode($sig);
        openssl_free_key($res);
        return $sign;
    }

    /**
     * 使用RSA验证签名
     * @param string $data 待签名数据
     * @param string $sign 等验证签名字串
     * @param string $pubKey 公钥(必须经过格式化)
     * @return bool
     */
    public static function signVerify($data, $sign, $pubKey){
        $res = openssl_pkey_get_public($pubKey);
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    /**
     * 使用RSA公钥加密数据 117位数据分割加密
     * @param string $data 待加密数据
     * @param string $pubKey 公私钥(必须经过格式化)
     * @return string 加密后的数据(base64编码)
     */
    public static function publicEncryptPart(string $data, string $pubKey)
    {
        $crypto = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $pubKey);
            $crypto .= $encryptData;
        }

        return base64_encode($crypto);
    }

    /**
     * 使用RSA公钥加密数据
     * @param string $data 待加密数据
     * @param string $pubKey 公钥(必须经过格式化)
     * @return string 加密后的数据(base64编码)
     */
    public static function publicEncrypt($data, $pubKey){
        openssl_public_encrypt($data, $encrypted, $pubKey);
        return base64_encode($encrypted);
    }

    /**
     * 使用RSA公钥解密数据
     * @param string $data 待解密数据(base64编码)
     * @param string $pubKey 公钥(必须经过格式化)
     * @return string 解密后的数据
     */
    public static function publicDecrypt($data, $pubKey){
        openssl_public_decrypt(base64_decode($data), $decrypted, $pubKey);
        return $decrypted;
    }

    /**
     * 使用RSA私钥加密数据
     * @param string $data 待加密数据
     * @param string $privKey 私钥(必须经过格式化)
     * @return string 加密后的数据(base64编码)
     */
    public static function privateEncrypt($data, $privKey){
        openssl_private_encrypt($data, $encrypted, $privKey);
        return base64_encode($encrypted);
    }

    /**
     * 使用RSA私钥解密数据
     * @param string $data 待解密数据(base64编码)
     * @param string $privKey 私钥(必须经过格式化)
     * @return string 解密后的数据
     */
    public static function privateDecrypt($data, $privKey){
        openssl_private_decrypt(base64_decode($data), $decrypted, $privKey);
        return $decrypted;
    }

    /**
     * 使用RSA私钥解密数据 128位数据分割解密
     * @param string $data 待解密数据(base64编码)
     * @param string $privKey 私钥(必须经过格式化)
     * @return string 解密后的数据
     */
    public static function privateDecryptPart($data, $privKey)
    {
        $crypto = '';

        foreach (str_split(base64_decode($data), 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $privKey);
            $crypto .= $decryptData;
        }

        return $crypto;
    }

    /**
     * 去除私钥或公钥的头部和尾部，转换成不带格式的单行字符串
     * @param string 私钥或公钥
     * @return string
     */
    public static function stripKey($key){
        $key = str_replace('-----BEGIN PUBLIC KEY-----', '', $key);
        $key = str_replace('-----END PUBLIC KEY-----', '', $key);
        $key = str_replace('-----BEGIN PRIVATE KEY-----', '', $key);
        $key = str_replace('-----END PRIVATE KEY-----', '', $key);
        $key = str_replace('-----BEGIN RSA PRIVATE KEY-----', '', $key);
        $key = str_replace('-----END RSA PRIVATE KEY-----', '', $key);
        $key = str_replace("\n", '', $key);
        return $key;
    }

    /**
     * 格式化公钥(将单行的密钥转换成多行带格式的密钥)
     * @param string $pubKey 公钥
     * @return string
     */
    public static function formatPublicKey($pubKey){
        $pubKey = self::stripKey($pubKey);
        $pubKey = '-----BEGIN PUBLIC KEY-----'.PHP_EOL.chunk_split($pubKey, 64, PHP_EOL).'-----END PUBLIC KEY-----'.PHP_EOL;
        return $pubKey;
    }

    /**
     * 格式化私钥
     * @param string $privKey 私钥
     * @return string
     */
    public static function formatPrivateKey($privKey){
        $privKey = self::stripKey($privKey);
        $privKey = '-----BEGIN RSA PRIVATE KEY-----'.PHP_EOL.chunk_split($privKey, 64, PHP_EOL).'-----END RSA PRIVATE KEY-----';
        return $privKey;
    }
}
