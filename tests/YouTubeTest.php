<?php
// All tests are require Cache_Lite.

error_reporting(E_ALL|E_STRICT);
require_once '../YouTube.php';

class YouTubeTest extends PHPUnit2_Framework_TestCase
{
    const DEV_ID = 'E88fqlcDtlM';

    // {{{ setter tests using Mock class
    public function testSetDriver()
    {
        try {
            $youtube = new Services_YouTubeMock(self::DEV_ID);
            $this->assertEquals('rest', $youtube->getDriver());

            $youtube->setDriver('xmlrpc');
            $this->assertEquals('xmlrpc', $youtube->getDriver());

            $youtube->setDriver('rest');
            $this->assertEquals('rest', $youtube->getDriver());

            // throw Exception
            $youtube->setDriver("throw exception");
        } catch (Services_YouTube_Exception $e) {
            $this->assertEquals('Driver has to be "xmlrpc" or "rest"', $e->getMessage());
        }
    }

    public function testSetResponseFormat()
    {
        try {
            $youtube = new Services_YouTubeMock(self::DEV_ID);
            $this->assertEquals('object', $youtube->getResponseFormat());

            $youtube->setResponseFormat('array');
            $this->assertEquals('array', $youtube->getResponseFormat());

            $youtube->setResponseFormat('object');
            $this->assertEquals('object', $youtube->getResponseFormat());

            // throw Exception
            $youtube->setResponseFormat("throw exception");
        } catch (Services_YouTube_Exception $e) {
            $this->assertEquals('ResponseFormat has to be "object" or "array"', $e->getMessage());
        }
    }

    public function testSetUseCache()
    {
        $youtube = new Services_YouTubeMock(self::DEV_ID);
        $this->assertFalse($youtube->getUseCache());
        $this->assertEquals(array(), $youtube->getCacheOptions());

        $youtube->setUseCache(true);
        $this->assertTrue($youtube->getUseCache());
        $this->assertEquals(array(), $youtube->getCacheOptions());


        $youtube->setUseCache(true, array('lifeTime' => 18000));
        $this->assertTrue($youtube->getUseCache());
        $this->assertEquals(array('lifeTime' => 18000), $youtube->getCacheOptions());
    }

    // }}}

// {{{ user API
    public function testGetProfile()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->getProfile('ganchiku');
            $profile = $data->user_profile;
            $this->assertEquals('Shin', $profile->first_name);
            $this->assertEquals('Ohno', $profile->last_name);

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->getProfile('ganchiku');
            $profile = $data['user_profile'];
            $this->assertEquals('Shin', $profile['first_name']);
            $this->assertEquals('Ohno', $profile['last_name']);

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->getProfile('ganchiku');
            $profile = $data->user_profile;
            $this->assertEquals('Shin', $profile->first_name);
            $this->assertEquals('Ohno', $profile->last_name);

        } catch (Services_YouTube_Exception $e) {
            print $e;
        }

    }
    public function testListFavoriteVideos()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->listFavoriteVideos('ganchiku');
            $videos = $data->xpath('//video');
            $this->assertTrue(is_array($videos));

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->listFavoriteVideos('ganchiku');
            $this->assertTrue(is_array($data['video_list']));

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->listFavoriteVideos('ganchiku');
            $videos = $data->xpath('//video');
            $this->assertTrue(is_array($videos));

        } catch (Services_YouTube_Exception $e) {
            print $e;
        }
    }
    public function testListFriends()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->listFriends('ganchiku');
            $this->assertTrue(isset($data->friend_list));
            // i have no friends... orz...
            $this->assertEquals(0, $data->friend_list);

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->listFriends('ganchiku');
            $this->assertTrue(array_key_exists('friend_list', $data));

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->listFriends('ganchiku');
            $this->assertTrue(isset($data->friend_list));
            $this->assertEquals(0, $data->friend_list);

        } catch (Services_YouTube_Exception $e) {
            print $e;
        }
    }
    // }}}

    // {{{ video API
    public function testGetDetails()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->getDetails('rdwz7QiG0lk');
            $video = $data->video_details;
            $this->assertEquals('YouTube', $video->author);

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->getDetails('rdwz7QiG0lk');
            $video = $data['video_details'];
            $this->assertEquals('YouTube', $video['author']);

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->getDetails('rdwz7QiG0lk');
            $video = $data->video_details;
            $this->assertEquals('YouTube', $video->author);

        } catch (Services_YouTube_Exception $e) {
            print $e;
        }
    }

    public function testListByTag()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->listByTag('YouTube');
            $videos = $data->xpath('//video');
            $this->assertTrue(is_array($videos));

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->listByTag('YouTube');
            $this->assertTrue(is_array($data['video_list']));

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->listByTag('YouTube');
            $videos = $data->xpath('//video');
            $this->assertTrue(is_array($videos));

            //  Hmm... need to integrate pager parameters
        } catch (Services_YouTube_Exception $e) {
            print $e;
        }
    }

    public function testListByUser()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->listByUser('ganchiku');
            // i have not uploaded any videos... orz...
            $this->assertEquals(0, $data->video_list);

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->listByUser('ganchiku');
            $this->assertNull($data['video_list']);

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->listByUser('ganchiku');
            $this->assertEquals(0, $data->video_list);

        } catch (Services_YouTube_Exception $e) {
            print $e;
        }
    }

    public function testListFeatured()
    {
        try {
            $youtube = new Services_YouTube(self::DEV_ID);
            $youtube->setUseCache(true);
            $data = $youtube->listFeatured();
            $videos = $data->xpath('//video');
            $this->assertEquals(25, count($videos));

            // Array Response
            $youtube->setResponseFormat('array');
            $data = $youtube->listFeatured();
            $this->assertEquals(25, count($data['video_list']['video']));

            // XML_RPC driver
            $youtube->setDriver('xmlrpc');
            $youtube->setUseCache(false);
            $youtube->setResponseFormat('object');
            $data = $youtube->listFeatured();
            $videos = $data->xpath('//video');
            $this->assertEquals(25, count($videos));

        } catch (Services_YouTube_Exception $e) {
            print $e;
        }
    }
    // }}}
}

// {{{ Mock Class FOR GETTER
class Services_YouTubeMock extends Services_YouTube
{
    public function getDriver()
    {
        return $this->driver;
    }
    public function getResponseFormat()
    {
        return $this->responseFormat;
    }
    public function getUseCache()
    {
        return $this->useCache;
    }
    public function getCacheOptions()
    {
        return $this->cacheOptions;
    }
}
// }}}
?>
