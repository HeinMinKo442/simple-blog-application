<?php


// 

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

    // if (isset($_FILES['image']['tmp_name'])) {
    //     try {
    //         // instantiate the class and set a save path
    //         $img = new ImageHandler("/Simple_Blog/images/");
    //         $iamge_path = $img->processUploadedImage($_FILES['image']);

    //         // output the uploaded image as it was save
    //         echo '<img src="', $iamge_path, '"/><br/>';
    //     } catch (Exception $e) {
    //         die($e->getMessage());
    //     }
    // } else {
    //     $iamge_path = NULL;
    // }

    // echo "<pre>";
    // print_r($_FILES);
    // echo "<br>";

    // exit;

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
} else {
    header('Location: ../');
    exit;
}
