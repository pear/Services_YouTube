<?php
require_once 'XML/RPC2/Client.php';
require_once 'Services/YouTube/Adapter.php';

class Services_YouTube_Adapter_XML_RPC2 implements Services_YouTube_Adapter {

    /**
     * URI of the XML RPC path
     */
    const XMLRPC_PATH = '/api2_xmlrpc';

    public function execute($prefix, $method, $parameters) {
        $options = array('prefix' => $prefix);
        try {
            $url = 'http://' . Services_YouTube::URL . self::XMLRPC_PATH;

            $client = XML_RPC2_Client::create($url, $options);
            $result = $client->$method($parameters);
        } catch (XML_RPC2_FaultException $e) {
            $msg = 'XML_RPC Failed :' . $e->getMessage();
            throw new Services_YouTube_Exception($msg);
        } catch (Exception $e) {
            throw new Services_YouTube_Exception($e->getMessage());
        }
        return $result;
    }
}
