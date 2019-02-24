<?php
// REDIRECT LOGGED IN USERS TO THE COMMENTS PAGE
if(!empty($_COOKIE['username'])){
    header('Location: comments.php');
}

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

if(isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $sql = 'SELECT * FROM users WHERE username = :username AND password = :password';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'password' => $password]);
    $data = $stmt->fetchAll();

    $count = $stmt->rowCount();

    if($count > 0){
        // USERNAME AND PASSWORD MATCH
        // SET COOKIES FOR LOGGED IN USER
        setcookie('username', $username, time() + (60*60*24*30));

        // REDIRECT TO THE COMMENTS PAGE
        header('Location: comments.php');
    } else {
        $feedback = 'Invalid username or password';
    }
}

?>

<div class="top-header d-flex align-items-center p-3 my-3 text-white-50 bg-white rounded shadow-sm">
    <div class="lh-100">
        <h6 class="mb-0 text-gray-dark lh-100">Please Log in to Comment</h6>
    </div>
</div>

<?php
if(!empty($feedback)) {
    echo '<div class="p-3 bg-white rounded shadow-sm"><div class="lh-100">';
    echo '<div class="mb-0 lh-100 alert alert-danger" role="alert">'.$feedback.'</div></div></div>';
}
?>

<div class="my-3 p-3 bg-white rounded shadow-sm">

    <form action="index.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control <?php echo(!empty($username_err)) ? 'is-invalid' : ''; ?>" id="username" aria-describedby="emailHelp" placeholder="Enter username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control <?php echo(!empty($password_err)) ? 'is-invalid' : ''; ?>" id="password" placeholder="Password" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<?php
require_once("includes/footer.php");
?>