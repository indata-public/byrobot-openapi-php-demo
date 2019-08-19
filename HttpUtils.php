<?php

date_default_timezone_set("GMT");

class HttpUtils {
    /**
     * 发送Get请求
     *
     * @param String $url
     * @param String $ak_id
     * @param String $ak_secret
     * @param Array $param
     * @return void
     */
    public static function sendGet (String $url, String $ak_id, String $ak_secret, Array $param = []) {
        try {
            // 时间格式：Thu, 13 Dec 2018 01:27:17 GMT
            $date = date("D, d M Y H:i:s e");
            // 加密规则：时间 + "\n" + appKey
            $sign = self::generateSign($date, $ak_id, $ak_secret);

            // 拆分URL
            $parseUrl = parse_url($url);

            if (count($parseUrl) < 3) {
                return 'Url Exception';
            }

            // 拼接参数
            if ($param) {
                $url = $url . '?' . http_build_query($param);
            }

            //协议
            $port = 80;
            $host = '';
            $errno = '';
            $errorMsg = '';
            $timeout = 30;

            if ($parseUrl ['scheme'] == 'https') {
                $port = empty($parseUrl ['port']) ? 443 : $parseUrl ['port'];
                $host = $parseUrl ['host'];
                $host = 'ssl://'.$host;
            } else {
                $port = empty($parseUrl ['port']) ? 80 : $parseUrl ['port'];
                $host = $parseUrl ['host'];
            }

            print_r($port);

            // 执行连接
            $fp = fsockopen($host, $port, $errno, $errorMsg, $timeout);

            // 尝试3次连接
            if (!$fp) {
                $fp = fsockopen($host, $port, $errno, $errorMsg, $timeout);
            }

            if (!$fp) {
                $fp = fsockopen($host, $port, $errno, $errorMsg, $timeout);
            }

            if (!$fp) {
                $fp = fsockopen($host, $port, $errno, $errorMsg, $timeout);
            }

            if (!$fp) {
                return 'Connect fail: ' . $host;
            }

            // 拼接请求字符串
            $out = '';

            $out .= "GET ${url} HTTP/1.1\r\n";
            $out .= "Host: ${parseUrl['host']}\r\n";
            $out .= "Accept: application/json\r\n";
            $out .= "Content-Type: application/json\r\n";
            $out .= "Accept-Charset: utf-8\r\n";
            $out .= "ContentType: utf-8\r\n";
            $out .= "datetime: ${date}\r\n";
            $out .= "appkey: ${ak_id}\r\n";
            $out .= "sign: ${sign}\r\n";
            $out .= "Connection: close\r\n\r\n";

            fputs($fp, $out);

            // 集阻塞/非阻塞模式流,$block==true则应用流模式
            stream_set_blocking($fp, true);

            // 设置流的超时时间
            stream_set_timeout($fp, 30);

            fwrite($fp, $out);

            // 从封装协议文件指针中取得报头／元数据
            $status = stream_get_meta_data($fp);

            // 未超时
            if (!$status['timed_out']) {
                $header = '';
                $content = '';
                $limit = 0;

                while (!feof($fp)) {
                    $header .= $h = fgets($fp);

                    if ($h && ($h == "\r\n" || $h == "\n")) break;

                    if (strpos($h, 'Content-Length:') !== false) {
                        $limit = intval(substr($header, 15));
                    }
                }

                $stop = false;

                while (!feof($fp) && !$stop) {
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));

                    $content .= $data;

                    if ($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }

                $content = preg_replace_callback(
                    '/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)' .
                        '((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
                    create_function(
                        '$matches',
                        'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'
                    ),
                    $content
                );

                return $content;
            } else {
                return "Connect to host: " . $parseUrl ['host'] . " time out";
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 发送Post请求
     *
     * @param String $url
     * @param String $ak_id
     * @param String $ak_secret
     * @param array $param
     * @return void
     */
    public static function sendPost (String $url, String $ak_id, String $ak_secret, array $param = []) {
        try {
            // 时间格式：Thu, 13 Dec 2018 01:27:17 GMT
            (String)$date = date("D, d M Y H:i:s e");
            // 加密规则：时间 + "\n" + appKey
            (String)$sign = self::generateSign($date, $ak_id, $ak_secret);

            // 拆分URL
            $parseUrl = parse_url($url);

            if (count($parseUrl) < 3) {
                return 'Url Exception';
            }

            //协议
            (Int)$port = 80;
            (String)$host = '';
            (String)$errno = '';
            (String)$errstr = '';
            (Int)$timeout = 30;

            if ($parseUrl['scheme'] == 'https') {
                $port = empty($parseUrl['port']) ? 443 : $parseUrl['port'];
                $host = 'ssl://' . $host;
            } else {
                $port = empty($parseUrl['port']) ? 80 : $parseUrl['port'];
                $host = $parseUrl['host'];
            }

            // 执行连接
            $fp = fsockopen($host, $port, $errno, $errstr, $timeout);

            // 尝试3次连接
            if (!$fp) {
                $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
            }

            if (!$fp) {
                $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
            }

            if (!$fp) {
                $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
            }

            if (!$fp) {
                return '连接失败: ' . $host;
            }

            // 拼接请求字符串
            (String)$out = '';

            $out .= "POST ${url} HTTP/1.1\r\n";
            $out .= "Host: ${parseUrl['host']}\r\n";
            $out .= "Content-type:application/json\r\n";

            // 拼接参数
            if ($param) {
                (String)$postData = '';
                (Int)$lengthData = 0;

                $postData = trim(json_encode($param));
                var_dump($postData);
                $lengthData = strlen($postData);

                $out .= "Accept: application/json\r\n";
                $out .= "Content-Type: application/json\r\n";
                $out .= "Accept-Charset: utf-8\r\n";
                $out .= "ContentType: utf-8\r\n";
                $out .= "datetime: ${date}\r\n";
                $out .= "appkey: ${ak_id}\r\n";
                $out .= "sign: ${sign}\r\n";
                $out .= "Content-length:${lengthData}\r\n";
                $out .= "Connection: close\r\n\r\n";
                $out .= "${postData}\r\n\r\n";
            }
echo $out;
            fputs($fp, $out);

            // 集阻塞/非阻塞模式流,$block==true则应用流模式
            stream_set_blocking($fp, true);

            // 设置流的超时时间
            stream_set_timeout($fp, 30);

            fwrite($fp, $out);

            // 从封装协议文件指针中取得报头／元数据
            $status = stream_get_meta_data($fp);

            // 未超时
            if (!$status['timed_out']) {
                (String)$header = '';
                (String)$content = '';
                (Int)$limit = 0;

                while (!feof($fp)) {
                    $header .= $h = fgets($fp);

                    if ($h && ($h == "\r\n" || $h == "\n")) break;

                    if (strpos($h, 'Content-Length:') !== false) {
                        $limit = intval(substr($header, 15));
                    }
                }

                $stop = false;

                while (!feof($fp) && !$stop) {
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));

                    $content .= $data;

                    if ($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }

                $content = preg_replace_callback(
                    '/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)' .
                        '((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
                    create_function(
                        '$matches',
                        'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'
                    ),
                    $content
                );

                return $content;
            } else {
                return "Connect to host: " . $parseUrl ['host'] . " time out";
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 加密
     * 规则：GMT时间 + "\n" + appKey
     *
     * @param String $date 时间：Thu, 13 Dec 2018 01:27:17 GMT
     * @param String $appKey
     * @param String $appSecret
     * @return void
     */
    private static function generateSign (String $date, String $appKey, String $appSecret) {
        $stringToSign = $appKey . "\n" . $date;
        if (function_exists('hash_hmac')) {
            return base64_encode(hash_hmac("sha1", $stringToSign, $appSecret, true));
        }
    }
}
