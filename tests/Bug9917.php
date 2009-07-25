<?php
require_once 'Services/YouTube.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Services/YouTube/Adapter/XML_RPC2.php';

class Bug9917 extends PHPUnit_Framework_TestCase
{
    const DEV_ID = 'E88fqlcDtlM';

    public function test9917()
    {
        try {
            $user_id = "ganchiku";

            $youtube = new Services_YouTube(self::DEV_ID, new Services_YouTube_Adapter_XML_RPC2(), array('usesCache' => true));

            $user = $youtube->getProfile($user_id);
            $this->assertEquals('Shin', (string)$user->user_profile->first_name);
            $this->assertEquals('Ohno', (string)$user->user_profile->last_name);
        } catch (Services_YouTube_Exception $e) {
            echo $e;
        }
    }
}
?>
