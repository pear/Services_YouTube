<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * PHP Client for YouTube API
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category Services
 * @package  Services_YouTube
 * @author   Shin Ohno <ganchiku@gmail.com>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  CVS: $Id$
 * @link     http://pear.php.net/Services_YouTube
 * @link     http://www.youtube.com/dev
 * @since    0.1
 */

/**
 * Services_YouTube exception class
 */
require_once 'Services/YouTube/Exception.php';
require_once 'Services/YouTube/Adapter.php';

require_once 'HTTP/Request2.php';

/**
 * Services_YouTube
 *
 * @category Services
 * @package  Services_YouTube
 * @author   Shin Ohno <ganchiku@gmail.com>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  Release: @package_version@
 * @link     http://pear.php.net/Services_YouTube
 */
class Services_YouTube
{
    // {{{ class const
    /**
     * Version of this package
     */
    const VERSION = '@package_version@';

    /**
     * URL of the YouTube Server
     */
    const URL = 'www.youtube.com';


    /**
     * Max number of the movie list per page
     */
    const VIDEO_PER_PAGE_MAX = 100;
    // }}}

    // {{{ class vars
    /**
     * Developer ID
     *
     * @var string
     * @access public
     */
    protected $developerId = null;

    /**
     * Use cache
     * @var boolean
     * @access protected
     */
    protected $useCache = false;
    /**
     * cache_lite options
     *
     * @var array
     * @access protected
     */
    protected $cacheOptions = array();
    /**
     * format of the xml response
     *
     * @var string
     * @access protected
     */
    protected $responseFormat = 'object';

    protected $adapter;
    // }}}

    // {{{ constructor
    /**
     * Constructor
     *
     * @param string  $developerId Developer ID
     * @param Services_YouTube_Adapter  One of Services_YouTube_Adapter_REST, Services_YouTube_Adapter_XML_RPC
     * @param mixed[] $options     useCache, cacheOptions, responseFormat
     *
     * @access public
     * @return void
     */
    public function __construct($developerId, Services_YouTube_Adapter $adapter = null, $options = array())
    {
        $this->developerId = $developerId;

        $availableOptions = array('useCache', 'cacheOptions',
                                  'responseFormat');

        foreach ($options as $key => $value) {
            if (in_array($key, $availableOptions)) {
                $this->$key = $value;
            }
        }

        if (empty($adapter)) {
            require_once 'Services/YouTube/Adapter/REST.php';
            $adapter = new Services_YouTube_Adapter_REST();
        }

        $this->setAdapter($adapter);
    }
    // }}}
    // {{{ getter methods

    public function getAdapter() {
        return $this->adapter;
    }

    /**
     * Return array of available time range
     *
     * @access public
     * @return void
     */
    public function getAvailableTimeRanges()
    {
        return array('day', 'week', 'month', 'all');
    }

    /**
     * Return array of available category array which key is category id
     * and value is category name.
     *
     * @access public
     * @return void
     */
    public function getAvailableCategories()
    {
        return array('1' => 'Arts & Animation',
            '2' => 'Autos & Vehicles',
            '23' => 'Comedy',
            '24' => 'Entertainment',
            '10' => 'Music',
            '25' => 'News & Blogs',
            '22' => 'People',
            '15' => 'Pets & Animals',
            '26' => 'Science & Technology',
            '17' => 'Sports',
            '19' => 'Travel & Places',
            '20' => 'Video Games');
    }
    // }}}

    // {{{ setter methods
    public function setAdapter(Services_YouTube_Adapter $adapter) {
        $this->adapter = $adapter;
    }

    /**
     * Choose which Response Fomat to use
     *
     * @param string $responseFormat One of 'object', 'array'
     *
     * @access public
     * @return void
     * @throws Services_YouTube_Exception
     */
    public function setResponseFormat($responseFormat)
    {
        if ($responseFormat == 'object' or $responseFormat == 'array') {
            $this->responseFormat = $responseFormat;
        } else {
            $msg = 'ResponseFormat has to be "object" or "array"';
            throw new Services_YouTube_Exception($msg);
        }
    }

    /**
     * Choose if this uses Cache_Lite.
     * If this uses Cache_Lite, then set the cacheOptions for Cache_Lite
     *
     * @param mixed $useCache     Use cache?
     * @param array $cacheOptions Cache options
     *
     * @access public
     * @return void
     */
    public function setUseCache($useCache = false, $cacheOptions = array())
    {
        $this->useCache = $useCache;
        if ($useCache) {
            $this->cacheOptions = $cacheOptions;
        }
    }
    // }}}
    // {{{ users
    /**
     * Retrieves the public parts of a user profile.
     *
     * @param string $user The user to retrieve the profile for.
     *                     This is the same as the name that shows up
     *                     on the YouTube website.
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function getProfile($user)
    {
        $parameters = array('dev_id' => $this->developerId,
            'user'   => $user);
        return $this->sendRequest('youtube.users.', 'get_profile', $parameters);
    }

    /**
     * Lists a user's favorite videos.
     *
     * @param string $user The user to retrieve the favorite videos for.
     *                     This is the same as the name that shows up
     *                     on the YouTube website
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listFavoriteVideos($user)
    {
        $parameters = array('dev_id' => $this->developerId,
            'user'   => $user);
        return $this->sendRequest('youtube.users.', 'list_favorite_videos',
                                  $parameters);
    }
    /**
     * Lists a user's friends.
     *
     * @param string $user The user to retrieve the favorite videos for.
     *                     This is the same as the name that shows up
     *                     on the YouTube website
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listFriends($user)
    {
        $parameters = array('dev_id' => $this->developerId,
            'user'   => $user);
        return $this->sendRequest('youtube.users.', 'list_friends', $parameters);
    }
    // }}}
    // {{{ videos
    /**
     * Displays the details for a video.
     *
     * @param string $videoId The ID of the video to get details for.
     *                        This is the ID that's returned by the list
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function getDetails($videoId)
    {
        $parameters = array('dev_id'   => $this->developerId,
            'video_id' => $videoId);
        return $this->sendRequest('youtube.videos.', 'get_details', $parameters);
    }

    /**
     * Lists all videos that have the specified tag.
     *
     * @param string $tag     the tag to search for
     * @param int    $page    the "page" of results you want to retrieve 
     *                        (e.g. 1, 2, 3) (default 1)
     * @param int    $perPage the number of results you want to retrieve
     *                        per page (default 20, maximum 100)
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listByTag($tag, $page = 1, $perPage = 20)
    {
        if ($perPage > self::VIDEO_PER_PAGE_MAX) {
            throw new Services_YouTube_Exception('The Maximum of the perPage is '
                                                 . self::VIDEO_PER_PAGE_MAX);
        }
        $parameters = array('dev_id' => $this->developerId,
            'tag'    => $tag,
            'page' => $page,
            'per_page' => $perPage);
        return $this->sendRequest('youtube.videos.', 'list_by_tag', $parameters);
    }

    /**
     * Lists all videos that match any of the specified tags.
     *
     * @param string $tag     the tag to search for
     * @param int    $page    the "page" of results you want to retrieve 
     *                        (e.g. 1, 2, 3) (default 1)
     * @param int    $perPage the number of results you want to retrieve
     *                        per page (default 20, maximum 100)
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listByRelated($tag, $page = 1, $perPage = 20)
    {
        if ($perPage > self::VIDEO_PER_PAGE_MAX) {
            throw new Services_YouTube_Exception('The Maximum of the perPage is '
                                                 . self::VIDEO_PER_PAGE_MAX);
        }
        $parameters = array('dev_id' => $this->developerId,
            'tag'    => $tag,
            'page' => $page,
            'per_page' => $perPage);
        return $this->sendRequest('youtube.videos.', 'list_by_related', $parameters);
    }

    /**
     * Lists all videos in the specified playlist.
     *
     * @param string $id      the id of the playlist
     * @param int    $page    the "page" of results you want to retrieve 
     *                        (e.g. 1, 2, 3) (default 1)
     * @param int    $perPage the number of results you want to retrieve
     *                        per page (default 20, maximum 100)
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listByPlaylist($id, $page = 1, $perPage = 20)
    {
        if ($perPage > self::VIDEO_PER_PAGE_MAX) {
            throw new Services_YouTube_Exception('The Maximum of the perPage is '
                                                 . self::VIDEO_PER_PAGE_MAX);
        }
        $parameters = array('dev_id' => $this->developerId,
            'id'    => $tag,
            'page' => $page,
            'per_page' => $perPage);
        return $this->sendRequest('youtube.videos.', 'list_by_playlist',
                                  $parameters);
    }

    /**
     * Lists all videos in the specified time_range.
     *
     * @param string $timeRange the time_range to list by 
     *                          (e.g. 'day', 'week', 'month', 'all')
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listByPopular($timeRange = 'all')
    {
        $availables = $this->getAvailableTimeRanges();
        // TODO Message
        if (!in_array($timeRange, $availables)) {
            $msg = '$timeRange must be either "day" "week", "month", "all".';
            throw new Services_YouTube_Exception($msg);
        }

        $parameters = array('dev_id' => $this->developerId,
            'time_range'    => $timeRange);
        return $this->sendRequest('youtube.videos.', 'list_by_popular', $parameters);
    }

    /**
     * Lists all videos that have the specified category id and tag.
     *
     * @param string $categoryId the category id to search in
     * @param string $tag        the tag to search for
     * @param int    $page       the "page" of results you want to retrieve
     * @param int    $perPage    the number of results you want to retrieve per page
     *                           (default 20, maximum 100)
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listByCategoryAndTag($categoryId, $tag, $page = 1,
                                         $perPage = 20)
    {
        $availables = array_keys($this->getAvailableCategories());
        // TODO Message
        if (!in_array($categoryId, $availables)) {
            throw new Services_YouTube_Exception('');
        }

        if ($perPage > self::VIDEO_PER_PAGE_MAX) {
            throw new Services_YouTube_Exception('The Maximum of the perPage is '
                                                 . self::VIDEO_PER_PAGE_MAX);
        }

        $parameters = array('dev_id' => $this->developerId,
            'categoryId' => $categoryId,
            'tag'    => $tag,
            'page' => $page,
            'per_page' => $perPage);
        return $this->sendRequest('youtube.videos.', 'list_by_category_and_tag',
                                  $parameters);
    }

    /**
     * Lists all videos that were uploaded by the specified user
     *
     * @param string $user user whose videos you want to list
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listByUser($user)
    {
        $parameters = array('dev_id' => $this->developerId,
            'user'   => $user);
        return $this->sendRequest('youtube.videos.', 'list_by_user', $parameters);
    }

    /**
     * Lists the most recent 25 videos that have been featured
     * on the front page of the YouTube site.
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    public function listFeatured()
    {
        $parameters = array('dev_id' => $this->developerId);
        return $this->sendRequest('youtube.videos.', 'list_featured', $parameters);
    }
    // }}}

    // {{{ protected
    /**
     * Send Request either rest or xmlrpc approach, and return 
     * simplexml_element of the response.
     * When $this->usesCaches is true, use Cache_Lite the response xml.
     * If $this->responseFormat is "array", return array, instead simplexml_element.
     *
     * @param string $prefix     Unknown
     * @param string $method     Method
     * @param array  $parameters Arguments
     *
     * @access protected
     * @return SimpleXMLObject or Array on success, error object on failure
     * @throws Services_YouTube_Exception
     */
    protected function sendRequest($prefix, $method, $parameters)
    {
        // Use Cache_Lite
        if ($this->useCache) {
            include_once 'Cache/Lite.php';
            $cacheID = md5($prefix . $method . serialize($parameters));

            $cache = new Cache_Lite($this->cacheOptions);

            if ($response = $cache->get($cacheID)) {
                return $this->parseResponse($response);
            }
        }

        $response = $this->adapter->execute($prefix, $method, $parameters);
        $data = $this->parseResponse($response);

        // Use Cache_Lite
        if ($this->useCache and isset($cache)) {
            if (!$cache->save($response, $cacheID)) {
                throw new Services_YouTube_Exception('Can not write cache');
            }
        }
        return $data;
    }

    /**
     * parseResponse
     *
     * @param string $response Response xml
     *
     * @access protected
     * @return SimpleXMLElement or array of the response data.
     * @throws Services_YouTube_Exception
     */
    protected function parseResponse($response)
    {
        $handler = array('Services_YouTube_Exception', 'errorHandlerCallback');
        set_error_handler($handler, E_ALL);

        try {
            if (!$data = simplexml_load_string($response)) {
                $msg = 'Parsing Failed. Response string is invalid';
                throw new Services_YouTube_Exception($msg);
            }
            if ($this->responseFormat == 'array') {
                $data = $this->forArray($data);
            }
            restore_error_handler();
        } catch (Services_YouTube_Exception $e) {
            restore_error_handler();
            throw $e;
        }
        return $data;
    }

    /**
     * Parse all SimpleXMLElement to array
     *
     * @param mixed $object SimpleXMLElement or array
     *
     * @access protected
     * @return array
     */
    protected function forArray($object)
    {
        $return = array();

        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $return[$key] = $this->forArray($value);
            }
        } else {
            $vars = get_object_vars($object);
            if (is_array($vars)) {
                foreach ($vars as $key => $value) {
                    if ($key && !$value) {
                        $return[$key] = null;
                    } else {
                        $return[$key] = $this->forArray($value);
                    }
                }
            } else {
                return $object;
            }
        }
        return $return;
    }
    // }}}
}

?>
