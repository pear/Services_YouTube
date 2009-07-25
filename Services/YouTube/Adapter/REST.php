<?php
require_once 'HTTP/Request2.php';
require_once 'Services/YouTube/Adapter.php';

class Services_YouTube_Adapter_REST implements Services_YouTube_Adapter {


    /**
     * URI of the REST path
     */
    const REST_PATH = '/api2_rest';

    public function execute($prefix, $method, $parameters) {
        $url = 'http://' . Services_YouTube::URL . self::REST_PATH . '?method=' . $prefix . $method;
        foreach ($parameters as $key => $val) {
            $url .= '&' . $key . '=' . urlencode($val);
        }

        $request = new HTTP_Request2($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);

        $response = $request->send();

        return $response->getBody();
    }
}
