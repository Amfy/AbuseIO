<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>AbuseIO 4.0</title>

	<link href="{{ asset('/css/bootstrap.min.css')          }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap-theme.min.css')    }}" rel="stylesheet">
    <link href="{{ asset('/css/style.css')                  }}" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="https://abuse.io" target="_blank">AbuseIO</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <?php
                        $nav_items = array('Home','Contacts','Netblocks','Domains','Reports','Search','Analytics');
                        foreach($nav_items as $nav_item) {
                            $nav_link = strtolower($nav_item);
                        ?>
                            <li class="{{ Request::path() == $nav_link ? 'active' : '' }}"><a href="{{ url('/'.$nav_link) }}">{{ $nav_item }}</a></li>
                        <?php
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

	@yield('content')

	<!-- Scripts -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
