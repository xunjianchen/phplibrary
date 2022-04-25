<?php
/**
 * Created by PhpStorm.
 * User: cjx
 * Date: 2022/4/24
 * Time: 9:36
 */

namespace Phplibrary;


class HttpClient
{
    public static function sockGet($url, $isBlock = true): string
    {
        $info = parse_url($url);
        $fp = fsockopen($info["host"], 80, $errno, $errstr, 3);
        if (false == $fp) {
            return false;
        }
        $head = "GET " . $info['path'] . "?" . $info["query"] . " HTTP/1.0\r\n";
        $head .= "Host: " . $info['host'] . "\r\n";
        $head .= "\r\n";
        fwrite($fp, $head);  //fputs是fwrite的别名
        $result = '';
        if (!$isBlock) return $result;
        while (!feof($fp)) {
            $result .= fgets($fp);
        }
        return $result;
    }

    public static function sockPost($url, $query, $isBlock = true): string
    {
        $query = http_build_query($query);
        $info = parse_url($url);
        $fp = fsockopen($info["host"], 80, $errno, $errstr, 3);
        $head = "POST " . $info['path'] . " HTTP/1.0\r\n";
        $head .= "Host: " . $info['host'] . "\r\n";
        $head .= "Referer: " . $info['scheme'] . "://" . $info['host'] . $info['path'] . "\r\n";
        $head .= "Content-type: application/x-www-form-urlencoded\r\n";
        $head .= "Content-Length: " . strlen($query) . "\r\n";
        $head .= "\r\n";
        $head .= $query;
        fputs($fp, $head);
        $result = '';
        if (!$isBlock) return $result;
        while (!feof($fp)) {
            $result .= fgets($fp);
        }
        return $result;
    }

    public static function curlPost($url, $data, $withCookie = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($withCookie) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . "/cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // 已经获取到内容，没有输出到页面上。
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }


    public static function curlGet($url, $withCookie = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);//登陆后要从哪个页面获取信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        if ($withCookie) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . "/cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }


}