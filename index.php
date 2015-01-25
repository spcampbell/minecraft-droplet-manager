<?php
/* 

Minecraft Droplet Manager

http://gnonai.github.io/minecraft-droplet-manager/

Single click control of a minecraft server droplet
hosted on Digital Ocean. Starts it up when ready to play
and tears it down when done. Automatically creates a latest
snapshot before destroying droplet.

Let the kids play without needing your help and costing you a fortune.

Go here for more information: http://github.com/gnonai/minecraft-droplet-manager

Released under MIT License - 2015 - gnonai

Inspired by the work of S-rc-C-d-
http://hi.srccd.com/post/hosting-minecraft-on-digitalocean

*/

session_start();


// ****** Change this to a unique password for the page ******
$secretpasswordA = 'password123';
// ***********************************************************


$logout = isset($_GET['logout']) ? $_GET['logout'] : '';
$getready = isset($_GET['ready']) ? $_GET['ready'] : '';
$hma = isset($_SESSION['hmauthenticated']) ? $_SESSION['hmauthenticated'] : false;

if ($logout=="yes") {
	$_SESSION = array();
	session_destroy();
	header( 'Location: index.php' );
}

if ($hma == true) {
	$goahead = 'yes';
}else{
	$goahead = 'no';
	$error = null;
	if (!empty($_POST)) {
		$password = empty($_POST['psswrdab']) ? null : $_POST['psswrdab'];
		if ($password == $secretpasswordA) {
			$_SESSION['hmauthenticated'] = true;
			$_SESSION['hmauthlvl'] = "A";
			if ($getready != "yes") header( 'Location: index_minecraft.php?ready=yes' );
			$goahead = 'yes';
		} else {
			$error = 'Incorrect password';
		}
	}
}

if ($goahead != 'yes') {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Minecraft Droplet Manager</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">
    <script src="js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

    <body>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-signin" action="index.php" method="POST">
                        <h2 class="form-signin-heading">Please sign in</h2>
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input name="psswrdab" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                        <button class="btn btn-lg btn-danger btn-block" type="submit">Sign in</button>
                    </form>
                </div>
            </div>          
            <div class="row">
                <div class="col-md-4">&nbsp;</div>
                <div class="col-md-4">
                    <p style="text-align: center;"><?php echo $error;?></p>
                </div>
                <div class="col-md-4">&nbsp;</div>
            </div>
 
        </div> <!-- /container -->


        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>

<?php
    
    exit;
}

$whatlevel = isset($_SESSION['hmauthlvl']) ? $_SESSION['hmauthlvl'] : '';
if ($whatlevel=="A") header( 'Location: index_minecraft.php?ready=yes' );
?>