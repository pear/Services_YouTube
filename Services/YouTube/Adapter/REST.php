<?php
require_once 'HTTP/Request2.php';
require_once 'Services/YouTube/Adapter.php';

class Services_YouTube_Adapter_REST implements Services_YouTube_Adapter {

    protected $request;

    public function __construct(HTTP_Request2 $request = null) {
        if (empty($request)) {
            $request = new HTTP_Request2();
        }   

        $this->setRequest($request);
    }

    public function setRequest(HTTP_Request2 $request) {
        $this->request = $request;
    }

    public function getRequest() {
        return $this->request;
    }

    /**
     * URI of the REST path
     */
    const REST_PATH = '/api2_rest';

    public function execute($prefix, $method, $parameters) {
        $url = 'http://' . Services_YouTube::URL . self::REST_PATH . '?method=' . $prefix . $method;
        foreach ($parameters as $key => $val) {
            $url .= '&' . $key . '=' . urlencode($val);
        }

        $request = $this->getRequest();
        $request->setURL($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);

        $response = $request->send();

        $body = $response->getBody();

        return $body;
    }
}
