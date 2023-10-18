<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class HttpClient
{
    public static function get($url, $options = [], $content_type = 'application/json')
    {
        return self::makeRequest($url, 'GET', [], $options, $content_type);
    }

    public static function post($url, $params, $options = [], $content_type = 'application/json')
    {
        return self::makeRequest($url, 'POST', $params, $options, $content_type);
    }

    private static function init($url)
    {
        return curl_init($url);
    }

    private static function exec(&$ch)
    {
        return curl_exec($ch);
    }

    private static function close(&$ch)
    {
        return curl_close($ch);
    }

    private static function getDefaultOptions(&$streamVerboseHandle)
    {
        return [
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_STDERR => $streamVerboseHandle
        ];
    }

    private static function makeRequest($url, $method, $params = [], $options = [], $content_type = 'application/json')
    {
        $ch = self::init($url);
        //$fp = fopen("example_homepage.txt", "w");
        
        $streamVerboseHandle = fopen('php://temp', 'w+');
        $default_options = self::getDefaultOptions($streamVerboseHandle);

        $options = is_array($options) ? array_merge_recursive($options, $default_options) : $default_options;
        $options[CURLOPT_HTTPHEADER] = ['Content-Type' => $content_type];

        if ($method === 'GET') {
            $options[CURLOPT_HTTPGET] = true;
        } else {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $params;
        }

        // if ($options[CURLOPT_VERBOSE] == true) {
            echo("OPTIONS: <br>");
            print_r($options);
            echo("<hr>");
        // }
        self::setOpt($ch, $options);
        
        ob_start();
        // if ($options[CURLOPT_RETURNTRANSFER] == 1) {
            $result = self::exec($ch);
        // }

        if ($result === false) {
            printf(" cUrl error (#%d): %s<br>\n",
                   curl_errno($ch),
                   htmlspecialchars(curl_error($ch)))
                   ;
        }
        
        // rewind($streamVerboseHandle);
        // $verboseLog = stream_get_contents($streamVerboseHandle);
        
        // echo "cUrl verbose information:\n", 
        //      "<pre>", htmlspecialchars($verboseLog), "</pre>\n";

        $return = ob_get_clean();

        if(curl_error($ch)) {
            $return['error'] = curl_error($ch);
        }
        self::close($ch);
        //fclose($fp);

        return $return;
    }

    private static function setOpt(&$ch, $options)
    {
        print_r($options);
        curl_setopt_array($ch, $options);
    }
} 

/*

curl_setopt($curlHandle, CURLOPT_VERBOSE, true);

$streamVerboseHandle = fopen('php://temp', 'w+');
curl_setopt($curlHandle, CURLOPT_STDERR, $streamVerboseHandle);
*/