<?php
namespace App\Library\HttpRequest;

class CurlRequest implements HttpRequest
{
    private $handle = null;

    public function __construct($url) {
        $this->handle = curl_init($url);
        curl_setopt($this->handle,CURLOPT_HTTPHEADER,array(
          "Accept: application/json",
          'Content-Type: application/json'
        ));
        curl_setopt($this->handle,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER,false);
    }

    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute() {
        return curl_exec($this->handle);
    }

    public function getInfo($name) {
        return curl_getinfo($this->handle, $name);
    }

    public function getError()  {
        return curl_error($this->handle);
    }

    public function close() {
        curl_close($this->handle);
    }
}
