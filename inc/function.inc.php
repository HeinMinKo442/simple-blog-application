<?php
function retrivesEntries($db, $page, $url = Null)
{

    if (isset($url)) {
        $sql = "SELECT id, page, title,image, entry,created
    FROM entries
    WHERE url=?
    LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($url));

        $e = $stmt->fetch();
        // Set the fulldisp flag for a single entry
        $fulldisp = 1;
    } else {
        // if no entry id get all entry title 
        $sql = "SELECT id,page,title,image,entry,url,created FROM entries WHERE page=? ORDER BY created DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$page]);

        // foreach ($db->query($sql) as $row) {
        //     $e[] = ['id' => $row['id'], 'title' => $row['title']];
        // }
        $e = Null;

        // while ($row = $stmt->fetch()) {
        //     $e[] = $row;
        //     $fulldisp = 0;
        // }

        while ($row = $stmt->fetch()) {
            if ($page == 'blog') {
                $e[] = $row;
                $fulldisp = 0;
            } else {
                $e = $row;
                $fulldisp = 1;
            }
        };


        if (!is_array($e)) {
            $fulldisp = 1;
            $e = array(
                'title' => 'No Entry Yet',
                'entry' => 'This page does not have an entry yet!'
            );
        }
    }
    // if no entry id empty default entry 

    array_push($e, $fulldisp);
    return $e;
}
function sanitizeData($data)
{
    if (!is_array($data)) {
        return strip_tags($data, '<a>');
    } else {
        return array_map('sanitizeData', $data);
    }
}


function makeUrl($title)
{
    $patterns = array(
        '/\s+/',
        '/(?!-)\W+/'
    );
    $replacements = array('-', '');
    return preg_replace($patterns, $replacements, strtolower($title));
}

function adminLinks($page, $url)
{
    $editURL = "/Simple_Blog/admin/$page/$url";
    $deleteURL = "/Simple_Blog/admin/delete/$url";

    // Make a hyperlink and add it to an array
    $admin['edit'] = "<a href=\"$editURL\">edit</a>";
    $admin['delete'] = "<a href=\"$deleteURL\">delete</a>";

    return $admin;
}

function confirmDelete($db, $url)
{
    $e = retrivesEntries($db, '', $url);
    return <<<FORM
    <form action="/Simple_Blog/admin.php" method="post">
    <fieldset>
    <legend>Are You Sure?</legend>
    <p>Are you sure you want to delete the entry "$e[title]"?</p>
    <input type="submit" name="submit" value="Yes" />
    <input type="submit" name="submit" value="No" />
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="url" value="$url" />
    </fieldset>
    </form>
    FORM;
}
function deleteEntry($db, $url)
{
    $sql = "DELETE FROM entries WHERE url=? LIMIT 1";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array($url));
}

function formatImage($img = NULL, $alt = NULL)
{
    if (isset($img)) {
        return '<img src="' . $img . '" alt="' . $alt . '" />';
    } else {
        return NULL;
    }
}

function createUserForm()
{
    return <<<FORM
        <form action="/Simple_Blog/inc/update.inc.php" method="POST">
            <fieldset>
            <legend>Create a New Adminstrator</legend>
            <label for="">Username
                <input type="text" name="username" maxlength="75">
            </label>
            <label for="">Password
                <input type="password" name="password">
            </label>
            <input type="submit" name="submit" value="Create">
            <input type="submit" name="submit" value="Cancel">
            <input type="hidden" name="action" value="createuser">
            </fieldset>
    </form>
FORM;
}
