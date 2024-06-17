<?php

include_once "inc/function.inc.php";
include_once "inc/db.inc.php";

$db = new PDO(DB_INFO, DB_USER, DB_PASS);

if (isset($_GET['page'])) {
    $page = htmlentities(strip_tags($_GET['page']));
} else {
    $page = 'blog';
}
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    if ($_POST['submit'] == 'Yes') {
        $url = htmlentities(strip_tags($_POST['url']));
        if (deleteEntry($db, $url)) {
            header("Location: /Simple_Blog");
            exit;
        }
    } else {
        header("Location: /Simple_Blog/blog/$url");
        exit;
    }
}
if (isset($_GET['url'])) {
    $url = htmlentities(strip_tags($_GET['url']));

    if ($page == 'delete') {
        $comfirm = confirmDelete($db, $url);
    }

    $legend = "Edit This Entry";
    $e = retrivesEntries($db, $page, $url);

    $id = $e['id'];
    $title = $e['title'];
    $entry = $e['entry'];
} else {
    $legend = "New Entry Submission";
    $id = Null;
    $title = Null;
    $entry = Null;
}
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
    <?php if ($page == 'delete') : {
            echo $comfirm;
        }
    else : ?>
        <form action="/Simple_Blog/inc/update.inc.php" method="POST" enctype="multipart/form-data">
            <fieldset>
                <Legend><?php echo $legend ?></Legend>
                <label for="">Title <input type="text" name="title" maxlength="150" value="<?php echo htmlentities($title) ?>"></label>
                <label for="">Image <input type="file" name="image"></label>
                <label for="">Entry <textarea type="text" name="entry" cols="45" rows="10"><?php echo sanitizeData($entry) ?></textarea></label>
                <input type="hidden" name="id" value="<?php echo $id ?>">
                <input type="hidden" name="page" value="<?php echo $page ?>">
                <input type="submit" name="submit" value="Save Entry">
                <input type="submit" name="submit" value="Cancel">
            </fieldset>
        </form>
    <?php endif; ?>
</body>

</html>