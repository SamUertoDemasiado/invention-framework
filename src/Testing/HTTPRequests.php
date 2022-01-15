<?php


namespace OSN\Framework\Testing;


trait HTTPRequests
{
    public function createRequest($method, $uri, $version = "1.1", $headers = [], $params = [])
    {
        $headers_base = [
            "Connection" => "Close"
        ];

        if ($method != 'GET' && count($params) > 0)
            $headers_base["Content-Type"] = "application/x-www-form-urlencoded";

        $headers_new = array_merge($headers_base, $headers);
        $headers_str = "$method $uri HTTP/$version\r\n";

        foreach ($headers_new as $header => $value) {
            $headers_str .= "$header: $value\r\n";
        }

        $content = '';

        foreach ($params as $param => $value) {
            $content .= "$param=" . urlencode($value) . "&";
        }

        if ($method != 'GET' && count($params) > 0)
            $headers_str .= "Content-Length: " . (strlen($content) - 1) . "\r\n";

        $headers_str .= "\r\n";
        $headers_str .= $content;

        return count($params) > 0 ? substr($headers_str, 0, strlen($headers_str) - 1) : $headers_str;
    }

    public function sendRequest(string $request)
    {
        $socket = $this->socket();
        fputs($socket, $request);
        $socketdata = fgets($socket);

        while(!feof($socket)){
            $socketdata .= fgets($socket);
            if (preg_match("/\r\n\r\n/", $socketdata))
                break;
        }

        fclose($socket);
        return $socketdata;
    }

    public function sendGET($uri, $params = [], $headers = [], $version = "1.1")
    {
        return $this->sendRequest($this->createRequest("GET", $uri, $version, $headers, $params));
    }

    public function sendPOST($uri, $params = [], $headers = [], $version = "1.1")
    {
        return $this->sendRequest($this->createRequest("POST", $uri, $version, $headers, $params));
    }

    public function sendPUT($uri, $params = [], $headers = [], $version = "1.1")
    {
        return $this->sendRequest($this->createRequest("PUT", $uri, $version, $headers, $params));
    }

    public function sendDELETE($uri, $params = [], $headers = [], $version = "1.1")
    {
        return $this->sendRequest($this->createRequest("DELETE", $uri, $version, $headers, $params));
    }

    protected function socket()
    {
        return fsockopen("localhost", env('SERVER_PORT'));
    }
}
