<?php
interface Services_YouTube_Adapter {

    /**
     * Execute a YouTube API method with the given parameters
     *
     * @param string $prefix     Unknown
     * @param string $method     Method to call
     * @param array  $parameters Method args
     *
     * @throws Services_YouTube_Exception
     * @return string Raw Response
     */
    public function execute($prefix, $method, $parameters);
}
