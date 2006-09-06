<?php
require_once 'Services/YouTube.php';


class YouTubeTutorial
{
    protected $page     = 1;
    protected $perPage  = 25;
    protected $tag      = 'guitar';

    private $devId      = null;
    private $script     = null;
    private $useDetails = null;

    // {{{public
    public function __construct($devId, $useDetails = false)
    {
        if (array_key_exists('page', $_REQUEST) and is_numeric($_REQUEST['page'])) {
            $this->page = (int)$_REQUEST['page'];
        }
        if (array_key_exists('per_page', $_REQUEST) and is_numeric($_REQUEST['per_page'])) {
            $this->perPage = (int)$_REQUEST['per_page'];
        }
        if (array_key_exists('tag', $_REQUEST)) {
            $this->tag = strip_tags($_REQUEST['tag']);
        }

        $this->devId      = $devId;
        $this->useDetails = $useDetails;
        $this->script     = basename($_SERVER['SCRIPT_NAME']);
    }

    public function execute()
    {
        try {
            $youtube = new Services_YouTube($this->devId, array('useCache' => true));
            $xml     = $youtube->listByTag($this->tag, $this->page, $this->perPage + 1);
            $videos  = $xml->xpath('//video');

            $pager   = $this->createPager($videos);

            echo $this->createHeader($pager);
            echo $this->createBody($videos);
            echo $this->createFooter($pager);

        } catch (Services_YouTube_Exception $e) {
            // internal server error. service is not available.
            echo $e;
        }
    }
    // }}}
    // {{{protected
    protected function createPager(&$videos)
    {
        $page    = (int)$this->page;
        $perPage = (int)$this->perPage;
        $tag     = htmlspecialchars(urlencode($this->tag));
        $script  = htmlspecialchars($this->script);

        $prev    = '';
        $next    = '';

        if ($page > 1) {
            $prev = sprintf('<a href="%s?page=%s&per_page=%s&tag=%s">prev page</a>|',
                $script, $page -1, $perPage, $tag);
        }

        // XXX youtube does not return the result number! it is hard to implement pagenator
        if (count($videos) == $perPage + 1) {
            // delete the last array data. it is used only for checking existance of the next page.
            array_pop($videos);
            $next = sprintf('<a href="%s?page=%s&per_page=%s&tag=%s">next page</a>',
                $script, $page + 1, $perPage, $tag);
        }

        return '<div style="float:right">' . $prev.$next . '</div>';
    }

    protected function createHeader($pager)
    {
        $page     = (int)$this->page;
        $perPage  = (int)$this->perPage;
        $tag      = htmlspecialchars(urlencode($this->tag));
        $script   = htmlspecialchars($this->script);

        $lastNum  = $page * $perPage;
        $firstNum = $lastNum - $perPage + 1;

        // mendokusa
        $form     = sprintf('
            <form method="get" action="%s">
            Results per page:
            <select name="per_page">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="25">25</option>
            </select>
            <input type="text" name="tag" value="%s">
            <input type="submit" value="Search">
            </form>', $script, $tag);

        $search  = sprintf('Searching Tags: "%s"', $tag);
        $current = sprintf('<div style="float:right"><h4>Displaying Videos %d - %d</h4></div>', $firstNum, $lastNum);

        return $form.$pager.$search.$current;
    }

    protected function createFooter($pager)
    {
        // mendokusa
        return $pager;
    }

    protected function createBody($videos)
    {
        $html = '';
        foreach ($videos as $video) {
            $html .= $this->getTitle($video);
            $html .= $this->getThumbnail($video);
            $html .= $this->getUploadTime($video);
            $html .= $this->getAuthor($video);
            if ($this->useDetails) {
                $html .= $this->getChannel($video);
            }
            $html .= $this->getLength($video);
            $html .= $this->getDescription($video);
            $html .= $this->getCommentCount($video);
            $html .= $this->getTags($video);
            $html .= $this->getViewCount($video);
            $html .= $this->getRating($video);
        }
        return $html;
    }

    // {{{ UI, override them if you want to. or maybe different class is better.
    protected function getThumbnail($video)
    {
        $title         = htmlspecialchars($video->title);
        $url           = htmlspecialchars($video->url);
        $thumbnail_url = htmlspecialchars($video->thumbnail_url);

        return '<a href="'.$url.'"><img src="'.$thumbnail_url.'" alt="'.$title.'"></a>';
    }
    protected function getTitle($video)
    {
        $title = htmlspecialchars($video->title);

        return '<h2>'.$title.'</h2>';
    }
    protected function getUploadTime($video)
    {
        $uploadTime = date("F j, Y, g:i a", (int)$video->upload_time);

        return '<b>added:</b> '.$uploadTime;
    }
    protected function getAuthor($video)
    {
        $author = htmlspecialchars($video->author);

        // xxx youtube does not return author data...
        return '<b>by:</b> '.$author;
    }

    // i don't think it is a very good idea to use youtube.videos.get_details call.
    // too many requests...
    protected function getChannel($video)
    {
        static $youtube = null;

        $devId = $this->devId;
        $id    = htmlspecialchars($video->id);

        $youtube       = new Services_YouTube($devId, array('useCache' => true));
        $detailsXml    = $youtube->getDetails($id);

        $details       = array_pop($detailsXml->xpath('//video_details'));
        $channelsArray = array_map(array('self', 'callbackChannels'), $details->xpath('//channel'));
        $channels      = implode("\n", $channelsArray);

        return $channels.'<br />';
    }
    protected function getLength($video)
    {
        $lengthSeconds = gmdate("i:s", (int)$video->length_seconds);

        return '<b>Length:</b>'.$lengthSeconds.'<br />';
    }
    protected function getDescription($video)
    {
        $description = htmlspecialchars($video->description);

        return '<i>'.$description.'</i><br />';
    }
    protected function getCommentCount($video)
    {
        $commentCount = (int)$video->comment_count;

        return '<b>Comments:</b>'.$commentCount;
    }
    protected function getTags($video)
    {
        $tags      = htmlspecialchars($video->tags);

        $tagsArray = explode(" ", $tags);
        $tagsArray =  array_map(array('self', 'callbackTags'), $tagsArray);

        $tags      = implode("\n", $tagsArray);

        return '<b>tags:</b> '.$tags.'<br />';
    }
    protected function getViewCount($video)
    {
        $viewCount = (int)$video->view_count;

        return '<b>Views:</b>'.$viewCount.' | ';
    }

    protected function getRating($video)
    {
        $ratingAvg   = $video->rating_avg;
        $ratingCount = $video->rating_count;

        return '<b>rating:</b>'.$ratingAvg.' with '.$ratingCount.' total votes';
    }

    // {{{ callback functions
    protected function callbackTags($tag)
    {
        $tag     = htmlspecialchars($tag);
        $script  = htmlspecialchars($this->script);
        $perPage = (int)$this->perPage;

        return '<a href="'.$script.'?per_page='.$perPage.'&page=1&tag='.$tag.'">'.$tag.'</a>&nbsp;';
    }

    protected function callbackChannels($channel)
    {
        $channel    = htmlspecialchars((string)$channel);
        $youtubeUrl = "http://youtube.com/result?search_type=search_videos";

        return '<a href="'.$youtubeUrl.'&search_query='.$channel.'">'.$channel.'</a>';
    }
    // }}}
    // }}}
    // }}}

}


$t = new YouTubeTutorial('E88fqlcDtlM');
$t->execute();

?>
