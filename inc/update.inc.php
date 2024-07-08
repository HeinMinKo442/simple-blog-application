<?php


session_start();

include_once 'images.inc.php';
if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && $_POST['submit'] == 'Save Entry'
    && !empty($_POST['page'])
    && !empty($_POST['title'])
    && !empty($_POST['entry'])

) {
    //    connect to the database 
    include_once "./db.inc.php";
    $db = new PDO(DB_INFO, DB_USER, DB_PASS);

    include_once "function.inc.php";

    $url = makeUrl($_POST['title']);

    $image_path = NULL;

    if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK  && isset($_FILES['image']['tmp_name'])) {
            try {
                // Instantiate the class and set a save path
                $img = new ImageHandler("/Simple_Blog/images/");
                $image_path = $img->processUploadedImage($_FILES['image']);

                // Output the uploaded image as it was saved
                // echo '<img src="' . $image_path . '"/><br/>';
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } else {
            // $error_message = fileUploadErrorMessage($_FILES['image']['error']);
            die('An error occurred with the upload: ');
        }
    }

    if (!empty($_POST['id'])) {
        $sql = "UPDATE entries SET title=?,image=?,entry=?,url=? WHERE id=? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($_POST['title'], $image_path, $_POST['entry'], $url, $_POST['id']));
        $stmt->closeCursor();
    } else {
        // Store Enrty input data
        $sql = "INSERT INTO entries (page,title,image,entry,url) VALUES (?,?,?,?,?) ";
        $stmt = $db->prepare($sql);
        $stmt->execute(
            array($_POST['page'], $_POST['title'], $image_path, $_POST['entry'], $url)
        );
        $stmt->closeCursor();
    }
    $page = htmlentities(strip_tags($_POST['page']));

    // Get last Entry Id
    $id_obj = $db->query("SELECT LAST_INSERT_ID()");
    $id = $id_obj->fetch();
    $id_obj->closeCursor();

    header('Location: /Simple_Blog/' . $page . '/' . $url);
    exit();
} else if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    $_POST['submit'] == 'Post Comment'
) {
    include_once 'comments.inc.php';
    $comments = new Comments();
    $comments->saveComment($_POST);

    if (isset($_SERVER['HTTP_REFERER'])) {
        $loc = $_SERVER['HTTP_REFERER'];
    } else {
        $loc = '../';
    }
    header('Location:' . $loc);
    exit;
} else if ($_GET['action'] == 'comment_delete') {

    include_once "comments.inc.php";
    $comments = new Comments();
    echo $comments->comfirmDelete($_GET['id']);
    exit;
} else if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && $_POST['action'] == 'comment_delete'
) {
    // If set, store the entry from which we came
    $loc = isset($_POST['url']) ? $_POST['url'] : '../';


    // If the user clicked "Yes", continue with deletion
    if ($_POST['comfirm'] == "Yes") {
        // Include and instantiate the Comments class
        include_once "comments.inc.php";
        $comments = new Comments();
        // Delete the comment and return to the entry
        if ($comments->deleteComment($_POST['id'])) {

            header('Location: ' . $loc);
            exit;
        }
        // If deleting fails, output an error message
        else {
            exit('Could not delete the comment.');
        }
    }
    // If the user clicked "No", do nothing and return to the entry
    else {
        header('Location: ' . $loc);
        exit;
    }
}

// If a user is trying to log in, check it here
else if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && $_POST['action'] == 'login'
    && !empty($_POST['username'])
    && !empty($_POST['password'])
) {
    // Include database credentials and connect to the database
    include_once 'db.inc.php';
    $db = new PDO(DB_INFO, DB_USER, DB_PASS);
    $sql = "SELECT COUNT(*) AS num_users FROM admin WHERE username=? AND password=SHA1(?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['username'], $_POST['password']));
    $response = $stmt->fetch();
    if ($response['num_users'] > 0) {
        $_SESSION['loggedin'] = 1;
    } else {
        $_SESSION['loggedin'] = NULL;
    }
    header('Location: /Simple_Blog/');
    exit;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'createuser' && !empty($_POST['username']) && !empty($_POST['password'])) {

    include_once 'db.inc.php';
    $db = new PDO(DB_INFO, DB_USER, DB_PASS);
    $sql = "INSERT INTO admin (username,password) VALUES (?,SHA1(?))";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['username'], $_POST['password']));
    header('Location: /Simple_Blog/');
    exit;
} else if ($_GET['action'] == 'logout') {
    session_destroy();
    header('Location: ../');
    exit;
} else {

    unset($_SESSION['c_name'], $_SESSION['c_email'], $_SESSION['c_comment'], $_SESSION['error']);
    header('Location: ../');
    exit;
}
