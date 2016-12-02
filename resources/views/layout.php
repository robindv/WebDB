<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Webprogrammeren en Databases <?php if(isset($title)) echo ' - '.$title ; ?></title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo asset('css/style.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo asset('tablesorter/css/theme.default.min.css'); ?>" />

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>

    <script src="<?php echo asset('tablesorter/js/jquery.tablesorter.min.js'); ?>"></script>
    <script src="<?php echo asset('tablesorter/js/jquery.tablesorter.widgets.min.js'); ?>"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php if(isset($_GET['s']) && is_numeric($_GET['s']))
        echo '<script type="text/javascript">$(function() {$(document).scrollTop('.$_GET['s'].');});</script>';
    ?>


</head>
<body>
<div id="header"><div id="logo"><div>Webprogrammeren en Databases</div></div></div>
<div id="nav">
    <ul id="menu">
        <li><?php echo link_to('/','Home'); ?></li>
        <?php

        if(Auth::id() != 1)
            echo '<li>'.link_to('voorbeeldcode','Voorbeeldcode').'</li>';
        if (Auth::check())
        {
            if(!Auth::user()->is_student())
            {
                echo '<li>'.link_to('staff/students','Studenten').'</li>';
                echo '<li>'.link_to('staff/groups','Groepen').'</li>';
                echo '<li>'.link_to('staff/servers','Servers').'</li>';
            }
            else
            {
                echo '<li>'.link_to('student/project','Project').'</li>';
                echo '<li>'.link_to('student/server','Server').'</li>';
            }

            if(Auth::id() == 1)
            {
                echo '<li>'.link_to('admin/config', 'Configuratie').'</li>';
            }

            echo '<li>'.link_to('profile',Auth::user()->firstname).'</li>';
            echo '<li>'.link_to('logout','Uitloggen').'<li>';
        }
        else
            echo '<li>'.link_to('login','Inloggen').'</li>';
        ?>
    </ul>
</div>
<div id="main">
    <div class="modal fade" id="modal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:1000px;">
            <div class="modal-content" id="modal-content">
                <div class="modal-body" id="modal-body">
                    Laden...
                </div>
            </div>
        </div>
    </div>

    <?php echo $page; ?>
</div>


</body>
</html>
