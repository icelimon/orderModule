<?php
include_once 'includes/header.php';
?>
<title>Sign in | Oracle</title>
</head>
<body>
<?php
if(isset($_SESSION['loggedin'])){
    header("Location: profile.php");
}
$_SESSION['rand_str'] = randomString(17);
include_once 'includes/public_navbar.php';

//CREATE CSRF TOKEN RANDOM VALUE
function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <h1 class="text-center login-title">Sign in to continue to Oracle</h1>
            <div class="account-wall">
<?php if (isset($_SESSION['msg'])){ ?>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="alert alert-danger" role="alert"><?php echo $_SESSION['msg']; ?></div>
  </div>
</div>
                
<?php } ?>
                <img class="profile-img" src="https://lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=120"
                    alt="">
                <form action="signin.php" method="post" class="form-signin">
                <input type="text" class="form-control" name="emp-id" placeholder="Employee id" required autofocus>
                <input type="text" class="form-control" name="branch-id" placeholder="Branch id" required autofocus>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <input type="hidden" value="<?php echo $_SESSION['rand_str']; ?>" name="rand">
                <button class="btn btn-lg btn-primary btn-block" name="signin" type="submit">
                    Sign in</button>
                <label class="checkbox pull-left">
                    <input type="checkbox" name="remember" value="remember-me">
                    Remember me
                </label>
                <a href="#" class="pull-right need-help">Need help? </a><span class="clearfix"></span>
                </form>
            </div>
            <a href="signup.php" class="text-center new-account">Create an account </a>
        </div>
    </div>
</div>

<footer style="position: fixed;bottom: 0px">
        <p>© 2018<a style="color:#0a93a6; text-decoration:none;" href="#"> PAGE FOOTER</a>, All rights reserved 2018-2019.</p>
</footer>