<?php
// REDIRECT USERS WHO ARE NOT LOGGED IN TO THE HOME PAGE
if(empty($_COOKIE['username'])){
    header('Location: /');
}

// SET USERNAME
$username = $_COOKIE['username'];

// REQUIRE FILES FOR COMMENTS PAGE
require_once("includes/header.php");
require_once("includes/vars.php");

// SET DATABASE VARS
$host = HOST;
$user = USER;
$password = PASSWORD;
$dbname = DB_NAME;

// SET DSN
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

// CREATE A PDO INSTANCE
$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

// GET USER ID
$sql = 'SELECT * FROM users WHERE username = :username';
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$data = $stmt->fetchAll();

foreach($data as $user){
$user_id = $user->id;
}

// SAVE USER COMMENT TO DATABASE
if(isset($_POST['submit'])) {
    // GET DATA FROM USER SUBMISSION
    $comment = trim($_POST['comment']);
    
    // INSERT COMMENT INTO DATABASE
    $sql = 'INSERT INTO comments(comment, user_id) VALUES(:comment, :user_id)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['comment' => $comment, 'user_id' => $user_id]);
    $comment_feedback = 'Comment Submitted Successfully';
}

?>

<div class="top-header d-flex align-items-center p-3 my-3 text-white-50 bg-white rounded shadow-sm">
    <div class="lh-100">
        <h6 class="mb-0 text-gray-dark lh-100">Comments App</h6>
    </div>
</div>

<?php
if(!empty($comment_feedback)) {
    echo '<div class="p-3 bg-white rounded shadow-sm"><div class="lh-100">';
    echo '<div class="mb-0 lh-100 alert alert-success" role="alert">'.$comment_feedback.'</div></div></div>';
}
?>

<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Recent Comments</h6>
    <?php
    // QUERY THE DATABASE FOR COMMENTS, INNER JOIN USERS TABLE ON USER_ID, LIMIT COMMENTS TO 5
    $sql = "SELECT *,
                comments.comment as userComment,
                users.username as userName,
                users.color as userColor
                FROM comments
                INNER JOIN users
                ON comments.user_id = users.id
                ORDER BY comments.id DESC
                LIMIT 5
                ";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $data = $stmt->fetchAll();

      foreach($data as $commentData){
          $dataComment = $commentData->userComment;
          $dataColor = $commentData->userColor;
          $dataUserName = $commentData->userName;
          
          echo '<div class="media text-muted pt-3">';

          echo '<svg class="bd-placeholder-img mr-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32">';

          echo '<rect width="100%" height="100%" fill='.$dataColor.' />';

          echo '<text x="50%" y="50%" fill="#007bff" dy=".3em"></text></svg>';

          echo '<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">';

          echo '<strong class="d-block text-gray-dark">@'.$dataUserName.'</strong>';

          echo ''.$dataComment.'</p></div>';
      }
    ?>
</div>

<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6>Leave a Comment</h6>

    <form action="comments.php" method="POST">
        <div class="form-group">
            <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
require_once("includes/footer.php");
?>