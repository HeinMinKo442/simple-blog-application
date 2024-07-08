<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Include necessary files
include_once '../inc/function.inc.php';
include_once '../inc/db.inc.php';
// Open a database connection
$db = new PDO(DB_INFO, DB_USER, DB_PASS);

// Load all blog entries
$e = retrivesEntries($db, 'blog');

// Remove the fulldisp flag
array_pop($e);
// Perform basic data sanitization
$e = sanitizeData($e);


// Add a content type header to ensure proper execution
header('Content-Type: application/rss+xml');
// Output the XML declaration
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">
    <channel>
        <title>My Simple Blog</title>
        <link>http://localhost/Simple_Blog/</link>
        <description>This blog is awesome.</description>
        <language>en-us</language>

        <?php
        // Loop through the entries and generate RSS items
        foreach ($e as $e) :
            // Escape HTML to avoid errors
            $entry = htmlentities($e['entry']);
            // Build the full URL to the entry
            $url = 'http://localhost/Simple_Blog/blog/' . $e['url'];
            $date = date(DATE_RSS, strtotime($e['created']));
        ?>
            <item>
                <title><?php echo $e['title']; ?></title>
                <description><?php echo $entry; ?></description>
                <link><?php echo $url; ?></link>
                <guid><?php echo $url; ?></guid>
                <pubDate><?php echo $date ?></pubDate>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>