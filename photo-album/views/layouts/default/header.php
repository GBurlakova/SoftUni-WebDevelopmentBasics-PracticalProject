<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        <?php if (isset($this->title)) $this->renderText($this->title) ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="/content/styles/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="/content/styles/bootswatch.min.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="../bower_components/html5shiv/dist/html5shiv.js"></script>
    <script src="../bower_components/respond/dest/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/content/lib/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/content/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/content/js/bootwatch.js"></script>
    <script type="text/javascript" src="/content/lib/jquery.noty.packaged.min.js"></script>
    <script type="text/javascript" src="/content/js/notifier.js"></script>
    <script type="text/javascript" src="/content/lib/q.js"></script>
</head>

<body>
    <div id="wrapper">
        <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
                <div class="navbar-header">
                    <a href="/" class="navbar-brand">Photo Album</a>
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse" id="navbar-main">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="/home/publicAlbums">Public albums</a>
                        </li>
                        <?php if($this->isLoggedIn): ?>
                        <li>
                            <a href="/">My albums</a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a href="http://news.bootswatch.com">About</a>
                        </li>
                    </ul>
                    <?php if($this->isLoggedIn): ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="btn btn-primary">Hello, <?php $this->renderText($_SESSION['username'])?></li>
                        <li>
                            <form action="/account/logout" method="post">
                                <button type="submit" class="btn btn-default">Logout</button>
                            </form>
                        </li>
                    </ul>
                    <?php endif; ?>
                    <?php if(!$this->isLoggedIn): ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="label-primary"><a href="/account/login">Login</a></li>
                        <li><a href="/account/register">Register</a></li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
    </div>
    <?php include('messages.php'); ?>