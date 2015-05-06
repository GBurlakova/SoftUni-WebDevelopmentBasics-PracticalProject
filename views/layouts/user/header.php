<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        <?php if (isset($this->title)) $this->renderText($this->title) ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="/photo-album/content/styles/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="/photo-album/content/styles/bootswatch.min.css">
    <link rel="stylesheet" href="/photo-album/content/styles/photo-album-style.css">
    <script type="text/javascript" src="/photo-album/content/lib/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/photo-album/content/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/photo-album/content/js/bootwatch.js"></script>
    <script type="text/javascript" src="/photo-album/content/lib/jquery.noty.packaged.min.js"></script>
    <script type="text/javascript" src="/photo-album/content/js/notifier.js"></script>
    <script type="text/javascript" src="/photo-album/content/lib/q.js"></script>
</head>

<body>
<div id="wrapper">
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a href="/photo-album/" class="navbar-brand">Photo album</a>
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="/photo-album/albums/publicAlbums">Public albums</a>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download" aria-expanded="true">My albums<span class="caret"></span></a>
                        <ul class="dropdown-menu" aria-labelledby="download">
                            <li>
                                <a href="/photo-album/albums/index">My albums</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="/photo-album/albums/newAlbum">New album</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="/photo-album/albums/uploadPhoto">Upload photo</a>
                            </li>
                        </ul>
                    </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download" aria-expanded="true">Profile <span class="caret"></span></a>
                            <ul class="dropdown-menu" aria-labelledby="download">
                                <li><a href="./bootstrap.min.css">Profile</a></li>
                                <li class="divider"></li>
                                <li><a href="./bootstrap.min.css">Edit profile</a></li>
                            </ul>
                        </li>
                    <li>
                        <a href="http://news.bootswatch.com">About</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="btn btn-primary">Hello, <?php $this->renderText($_SESSION['username'])?></li>
                    <li>
                        <form action="/photo-album/account/logout" method="post">
                            <button type="submit" class="btn btn-default">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php include('./views/layouts/messages.php'); ?>