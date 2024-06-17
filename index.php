<?php
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
</head>

<body>
    <h1>Simple Blog Application</h1>

    <ul id="menu">
        <li><a href="/simple_blog/blog/">Blog</a></li>
        <li><a href="/simple_blog/about/">About the Author</a></li>
    </ul>
    <div id="entries">
        <?php
        if ($fulldisp == 1) {
            $url = (isset($url)) ? $url : $e['url'];

            // build the admin Links
            $admin = adminLinks($page, $url);

            // Format the image if one exits
            $img = formatImage($e['image'], $e['title']);
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
            <?php } ?>
            <?php } else {
            foreach ($e as $entry) { ?>
                <p>
                    <a href="/Simple_Blog/<?php echo $entry['page'] ?>/<?php echo $entry['url'] ?>">
                        <?php echo $entry['title'] ?></a>
                </p>
        <?php }
        } ?>

        <p class="backlink">
            <a href="/Simple_Blog/admin/<?php echo $page ?>">Post A new Entry</a>
        </p>
    </div>
</body>

</html>