<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 24/05/17
 * Time: 1:11 AM
 */
error_reporting(E_ERROR);
require_once dirname(__FILE__) . '/../bootstrap.php';

if (isset($_GET['submit']) && $_GET['submit'] == 'submit'){
    $config['host'] = $_GET['host'];
    $config['database'] = $_GET['database'];
    $config['user'] = $_GET['user'];
    $config['pass'] = $_GET['password'];

    $installer = new restApi\core\ConfigMaker($config);
    try{
        if ($installer->exec()) {
            if (unlink(__FILE__))
                header("Location: index.php");
            else
                die ('Installation complete. Please delete the installer.php file');
        }

    }
    catch (Exception $e){
        echo "<h3> Error: ".$e->getMessage() ."</h3>";
    }
}

?>

<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    </head>
    <body>
        <div class="container">

         <form class="form-horizontal" method="GET" action = "<?= $_SERVER['PHP_SELF']; ?>">
            <fieldset>
                <!-- Form Name -->
                <legend>Database Configuration</legend>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="database">Database Name</label>
                    <div class="col-md-5">
                        <input id="database" name="database" type="text" placeholder="" class="form-control input-md" required="">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="host">Hostname</label>
                    <div class="col-md-5">
                        <input id="host" name="host" type="text" placeholder="" required class="form-control input-md">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="user">UserName</label>
                    <div class="col-md-5">
                        <input id="user" name="user" type="text" placeholder="" required class="form-control input-md">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="password">Password</label>
                    <div class="col-md-5">
                        <input id="password" name="password" type="text" placeholder="" required class="form-control input-md">

                    </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="submit"></label>
                    <div class="col-md-4">
                        <button id="submit" name="submit" value = "submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </fieldset>
        </form>
        </div>

    </body>
</html>
