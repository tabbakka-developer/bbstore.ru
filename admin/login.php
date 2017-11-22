<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 12:57
 */

session_start();
if(isset($_SESSION['token'])){
    header("Location: main.php");
}
else {
    session_destroy();
}

?>


<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Вход</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

</head>
<body>
    <div class="container">

        <form class="form-signin" action="app/post_handler.php" method="post">
            <h2 class="form-signin-heading">Войдите в систему</h2>
            <label for="inputEmail" class="sr-only">Логин</label>
            <input type="text" id="inputEmail" name="login" class="form-control" placeholder="Логин" required autofocus>
            <label for="inputPassword" class="sr-only">Пароль</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required>
            <input type="hidden" id="method" name="method" value="auth">
<!--            <div class="checkbox">-->
<!--                <label>-->
<!--                    <input type="checkbox" value="remember-me"> Remember me-->
<!--                </label>-->
<!--            </div>-->
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>

    </div> <!-- /container -->

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>
</html>
