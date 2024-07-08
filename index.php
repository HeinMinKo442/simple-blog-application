<?php

session_start();

include_once "./inc/db.inc.php";
include_once "./inc/function.inc.php";

// connect to datase
$db = new PDO(DB_INFO, DB_USER, DB_PASS);

// catch id
// $id = (isset($_GET['id'])) ? $_GET['id'] : NULL;


// catch url 
$url = (isset($_GET['url'])) ? $_GET['url'] : NULL;

if (isset($_GET['page'])) {
    $page = htmlentities(strip_tags($_GET['page']));
} else {
    $page = 'blog';
}

$e = retrivesEntries($db, $page, $url);

$fulldisp = array_pop($e);

$e = sanitizeData($e);
// echo '<pre>';
// echo "<br>";
// print_r($url);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Blog</title>
    <link rel="stylesheet" href="/Simple_Blog/css/default.css">
    <link rel="alternate" type="application/rss+xml" title="My Simple Blog -RSS 2.0" href="/Simple_Blog/feeds/rss.php" />
</head>

<body>
    <h1>Simple Blog Application</h1>

    <ul id="menu">
        <li><a href="/Simple_Blog/blog/">Blog</a></li>
        <li><a href="/Simple_Blog/about/">About the Author</a></li>
    </ul>
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) : ?>
        <p id="control_panel">
            You are Logged in!
            <a href="/Simple_Blog/inc/update.inc.php?action=logout">Log Out</a>
        </p>
    <?php endif; ?>
    <div id="entries">
        <?php
        if ($fulldisp == 1) {
            $url = (isset($url)) ? $url : $e['url'];
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) {
                // build the adminlinks
                $admin = adminLinks($page, $url);
            } else {
                $admin = array('edit' => NULL, 'delete' => NULL);
            }
            // build the admin Links
            $admin = adminLinks($page, $url);

            // Format the image if one exits
            $img = formatImage($e['image'], $e['title']);

            if ($page === 'blog') {
                // load the comment object
                include_once "inc/comments.inc.php";
                $comments = new Comments();
                $comment_disp = $comments->showComments(($e['id']));
                $comment_form = $comments->showCommentForm($e['id']);
            } else {
                $comment_form = NULL;
                
            }
        ?>
            <h2><?php echo $e['title'] ?></h2>
            <?php if (isset($img)) { ?>
                <p> <?php echo $img ?></p>
            <?php } ?>
            <p> <?php echo $e['entry'] ?></p>
            <p>
                <?php echo $admin['edit'] ?>
                <?php if ($page == 'blog')
                    echo $admin['delete'] ?>
            </p>
            <?php if ($page == 'blog') { ?>
                <p class="backlink">
                    <a href="../">Back to Last Entry</a>
                </p>
                <h3>Comments for This Entry</h3>
                <?php echo $comment_disp, $comment_form; ?>

            <?php } ?>
            <?php } else {
            foreach ($e as $entry) { ?>
                <p>
                    <a href="/Simple_Blog/<?php echo $entry['page'] ?>/<?php echo $entry['url'] ?>">
                        <?php echo $entry['title'] ?></a>
                </p>
        <?php }
        }
        ?>
        <?php if ($page == 'blog' && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) :  ?>
            <p class="backlink">
                <?php if ($page == 'blog') ?>
                <a href="/Simple_Blog/admin/<?php echo $page ?>">Post A new Entry</a>
            </p>
        <?php else : ?>
            <p class="backlink">
                <a href="/Simple_Blog/admin/<?php echo $page ?>">Create a New Adminstrator</a>
            </p>
        <?php endif; ?>
        <p>
            <a href="/Simple_Blog/feeds/rss.php">Subscribe Via RSS!</a>
        </p>
    </div>
</body>

</html>