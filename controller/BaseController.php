<?php

class BaseController{
    private $mErrorCode = [
        '500' => 'HTTP/1.1 500 Internal Server Error',
        '422' => 'HTTP/1.1 422 Unprocessable Entity',
    ];
     
    public function __call($name, $arguments)
    {
        $this->setResponse('',['HTTP/1.1 404 Not Found']);
    }

    protected function responseOK($data, $message = ''){
        $payload = [
            'status' => 'success',
            'message'=> $message,
            'payload'=> $data
        ];

        return $this->setResponse(json_encode($payload),
        ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
    }

    protected function responseErr($errCode, $message){
        $payload = [
            'status' => 'error',
            'message'=> $message,
            'payload'=> null
        ];

        return $this->setResponse(json_encode($payload),
        ['Content-Type: application/json', $this->mErrorCode[$errCode]]);
    }

    protected function setResponse($data, $httpHeader = []){
        header_remove('Set-Cookie');

        if(is_array($httpHeader) && count($httpHeader)){
            foreach($httpHeader as $header){
                header($header);
            }
        }

        echo $data;
        exit;
    }

    protected function getUriSegments(){
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        return $uri;
    }

    protected function getQueryStringParam(){
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }
}