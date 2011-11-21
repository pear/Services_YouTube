<?php
// All tests are require Cache_Lite.

require_once 'Services/YouTube.php';
require_once 'Services/YouTube/Adapter/REST.php';
require_once 'Services/YouTube/Adapter/XML_RPC2.php';
require_once 'HTTP/Request2/Response.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

class YouTubeTest extends PHPUnit_Framework_TestCase
{
    const DEV_ID = 'E88fqlcDtlM';

    // {{{ setter tests using Mock class
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

    public static function adapters() {
        return array(array(new Services_YouTube_Adapter_REST()),
                     array(new Services_YouTube_Adapter_XML_RPC2()));
    }

    public function populateMockData($adapter, $file) {
        if ($adapter instanceof Services_YouTube_Adapter_REST) {
            $response = new HTTP_Request2_Response('HTTP/1.1 200 OK');
            $response->appendBody(file_get_contents(dirname(__FILE__) . '/data/' . $file));

            $mock = new HTTP_Request2_Adapter_Mock();
            $mock->addResponse($response);

            $request = new HTTP_Request2();
            $request->setAdapter($mock);

            $adapter->setRequest($request);
        }

        return $adapter;
    }

    /**
     * @dataProvider adapters() 
     */
    public function testGetProfile($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'get_profile.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->getProfile('ganchiku');
        $profile = $data->user_profile;
        $this->assertEquals('Shin', (string)$profile->first_name);
        $this->assertEquals('Ohno', (string)$profile->last_name);
    }

    /**
     * @dataProvider adapters() 
     */
    public function testListFavoriteVideos($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'list_favorite_videos.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->listFavoriteVideos('ganchiku');
        $videos = $data->xpath('//video');
        $this->assertTrue(is_array($videos));
    }

    /**
     * @dataProvider adapters() 
     */
    public function testListFriends($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'list_friends.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->listFriends('ganchiku');
        $this->assertTrue(isset($data->friend_list));
        // i have no friends... orz...
        $this->assertEquals(0, (int)$data->friend_list);
    }
    // }}}

    // {{{ video API
    /**
     * @dataProvider adapters() 
     */
    public function testGetDetails($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'get_details.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->getDetails('rdwz7QiG0lk');
        $video = $data->video_details;
        $this->assertEquals('YouTube', (string)$video->author);
    }

    /**
     * @dataProvider adapters() 
     */
    public function testListByTag($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'list_by_tag.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->listByTag('YouTube');
        $videos = $data->xpath('//video');
        $this->assertTrue(is_array($videos));

        $this->markTestIncomplete("Needs to be split");
        // Pager Response
        $data = $youtube->listByTag('YouTube', 1, 20);
        $videos = $data->xpath('//video');
        $this->assertTrue(is_array($videos));

        $data = $youtube->listByTag('YouTube', 2, 10);
        $videos = $data->xpath('//video');
        $this->assertTrue(is_array($videos));
    }

    /**
     * @dataProvider adapters() 
     */
    public function testListByRelated($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'list_by_related.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->listByRelated('YouTube');
        $videos = $data->xpath('//video');
        $this->assertTrue(is_array($videos));

        $this->markTestIncomplete("Needs to be split");
        // Pager Response
        $data = $youtube->listByRelated('YouTube', 1, 20);
        $videos = $data->xpath('//video');
        $this->assertTrue(is_array($videos));

        $data = $youtube->listByrelated('YouTube', 2, 10);
        $videos = $data->xpath('//video');

        $this->assertTrue(is_array($videos));
    }

    /**
     * @dataProvider adapters() 
     */
    public function testListByUser($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'list_by_user.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->listByUser('ganchiku');
        $this->assertEquals(0, (int)$data->video_list);
    }

    /**
     * @dataProvider adapters() 
     */
    public function testListFeatured($adapter)
    {
        $adapter = $this->populateMockData($adapter, 'list_featured.xml');
        $youtube = new Services_YouTube(self::DEV_ID, $adapter);

        $data = $youtube->listFeatured();
        $videos = $data->xpath('//video');
        $this->assertEquals(100, count($videos));
    }

    // }}}
}

// {{{ Mock Class FOR GETTER
class Services_YouTubeMock extends Services_YouTube
{
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
