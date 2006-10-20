<?php
error_reporting(E_ALL|E_STRICT);
require_once 'Services/YouTube.php';
define('MAX_COLS', 5);

define('DEV_ID', 'YOUR_DEV_ID');

function getFeaturedVideos()
{
    $youtube = new Services_YouTube(DEV_ID);
    $response = $youtube->listFeatured();
    if (isset($response->error)) {
        throw new Services_YouTube_Exception($response->error->description);
    }
    return $response;
}

function getTaggedVideos($tag, $page)
{
    if (is_array($tag)) {
        $tag = implode(" ", $tag);
    }
    $youtube = new Services_YouTube(DEV_ID);
    $response = $youtube->listByTag($tag, $page, 25);
    if (isset($response->error)) {
        throw new Services_YouTube_Exception($response->error->description);
    }
    return $response;
}

function getVideoTable($response)
{
    $table= "<table border='1'>\n";
    foreach ($response->xpath('//video') as $i => $video) {
        if ($i % MAX_COLS == 0) {
            $table .= "<tr>";
        }

        $table .= <<<EOF
<td>
<a href='javascript:openVideo("{$video->id}");'>
<img src='{$video->thumbnail_url}' alt='{$video->title}' /></a>
<br/>
<small><a href='javascript:openVideo("{$video->id}");'>{$video->title}</a></small>
</a>
<small>(<a href='{$video->url}'>Details</a>)</small>
</td>
EOF;

        if ($i % MAX_COLS == MAX_COLS -1) {
            $table .= "</tr>";
        }
    }
    $table .= "</table>";
    return $table;
}

function output($table)
{
    print <<<EOF
<html>
<head>
<title>Example</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="javascripts/prototype.js"> </script>
<script type="text/javascript" src="javascripts/effects.js"> </script>
<script type="text/javascript" src="javascripts/window.js"> </script>
<link href="themes/default.css" rel="stylesheet" type="text/css"></link>

<script type="text/javascript">
var win;
function openVideo(video_id) {
    if (win == undefined) {
        win = new Window('window_id', {className: "dialog",width:445,height:370,zIndex: 100,resizable: true,title: "Sample window",showEffect:Effect.BlindDown,hideEffect: Effect.SwitchOff,draggable:true});
    }

    win.getContent().innerHTML= '<div style="padding:10px"><object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' + video_id + '"></param><embed src="http://www.youtube.com/v/' + video_id + '" type="application/x-shockwave-flash" width="425" height="350"></embed></object></div>';

    win.showCenter();
}
</script>

</head>
<body>
<h1>This is sample</h1>
<div>Here is the sample using my dev id.</div>
<a href="http://www.unchiku.com/example.php">Featured List</a>
<hr />
<div>
<form action="example.php" method="get">
<input type="text" name="tag" size="16" maxlength="255">
<input type="submit" value="GET TAGGED VIDEOS!">
</form>
</div>
<div>
$table
</div>
</body>
</html>
EOF;
}

function main()
{
    try {
        if (isset($_REQUEST['tag'])) {
            $videos = getTaggedVideos($_REQUEST['tag'], 1);
        } else {
            $videos = getFeaturedVideos();
        }
    } catch (Services_YouTube_Exception $e) {
        print $e;
        print '<hr>';
        print '<div>Here is the sample using my dev id.</div>';
        print '<a href="http://www.unchiku.com/example.php">Featured List</a>';
        print "error";
        exit;
    }
    $table = getVideoTable($videos);
    output($table);
}

main();

?>

