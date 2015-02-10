<!DOCTYPE html>
<html>
<head>
<title>Sh@rkPHP :: FW Logs viewer</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="x-ua-compatible" content="IE=10">
<link rel="icon" type="image/png" href="{$config.url}themes/sharkphp/images/sharkphp.png" />
<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="{$config.url}{$config.url_dir}themes/sharkphp/images/sharkphp.ico" /><![endif]-->
<link rel="stylesheet" href="{$config.url}themes/bt3/css/bootstrap.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$config.url}themes/font-awesome/css/font-awesome.css" type="text/css" media="screen" />
<link rel="stylesheet" href="{$config.url}themes/squid/css/theme.css" type="text/css" media="screen" />
{if !empty($css_add)}
{foreach $css_add as $k => $v}
<link rel="stylesheet" href="{$config.url}web/css/{$v}" type="text/css" media="screen" />
{/foreach}
{foreach registry::$css_lib as $k => $v}
<link rel="stylesheet" href="{$config.url}web/lib/{$v}" type="text/css" media="screen" />
{/foreach}
{/if}
<script type="text/javascript" src="{$config.url}web/js/javascript.js"></script>
{if !empty($js_add)}
{foreach $js_add as $k => $v}
<script type="text/javascript" src="{$config.url}web/js/{$v}"></script>
{/foreach}
{/if}
{foreach registry::$js_lib as $k => $v}
<script type="text/javascript" src="{$config.url}web/lib/{$v}"></script>
{/foreach}
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript">var base_url = '{$config.url}';</script>

</head>
<body data-spy="scroll" data-target=".navbar">
{strip}
	<!-- NAVBAR -->
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
			
				<a class="btn btn-navbar" data-toggle="collapse" date-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="navbar-brand" href="{$Helper->getLink("index")}" title="Retour au site">Sh@rkPHP :: FW Logs viewer</a>
				<div class="nav-collapse">
					<ul class="nav">
					<!--
						<li><a href="{$Helper->getLinkAdm("index")}"><i class="icon-home icon-white"></i></a></li>
					//-->
					</ul>
					<ul class="nav pull-right">
						{if $smarty.session.utilisateur.id != 'Visiteur'}
						<li><a href="{$Helper->getLink("utilisateur")}" title=""><i class="icon-user icon-white"></i></a>
						<li><a href="{$Helper->getLink("connexion/logout")}" title=""><i class="icon-off icon-white"></i></a>
						{/if}

						{if $smarty.session.utilisateur.isAdmin > 0}
						<li><a href="{$config.url}{$config.url_dir}adm/" title="Administration"><i class="icon-wrench icon-white"></i></a></li>
						{/if}
					</ul>
					
				</div>
			</div>
		</div>
	</div><!-- /navbar -->
	<!-- Header -->
	<div id="header" style="padding-top:50px;"></div>

	<!-- Conteneur centrale -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-2 col-sm-3 sidebar">
				<div class="well nav-collapse sidebar-nav">
					<ul class="nav nav-tabs nav-stacked main-menu">
						<li class="nav-header hidden-tablet">Reports</li>
						<li><a href="{$Helper->getLink("index/index")}" title="Accueil">Accueil</a></li>
					</ul><!-- /nav -->
				</div><!-- /well -->
			</div><!-- /span2 -->
			<div class="col-md-10 col-sm-9">
				<div id="fwlogsviewer_content">
				{$content}
				</div>
			</div><!-- /span10 -->
		</div><!-- /row-fluid -->
	</div><!-- /container-fluid -->

	<!-- Footer -->
	<footer class="footer_site">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-8 col-sm-8"></div>
				<div class="col-md-4 col-sm-4"></div>
			</div>
		</div>
		<div class="container">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-8 col-sm-8"></div>
					<div class="col-md-4 col-sm-4"></div>
				</div>
			</div>
			<hr/>
			<div class="pull-left"></div>
			<div class="pull-right">
				Réaliser avec <a href="http://www.sharkphp.com" title="Another CMS/FRAMEWORK">Sharkphp <img src="{$config.url}web/images/sharkphp.png" alt="" style="width:20px;" /></a>
			</div>
			<div class="clearfix"></div>
		</div>
	</footer>
{/strip}

{* APPEL JS EN FOOTER *}
{if isset($FlashMessage) && !empty($FlashMessage)}<script type="text/javascript">var flash_message = '{$FlashMessage}'</script>{/if}
{if isset($pnotify) && !empty($pnotify)}<script type="text/javascript">var notify =  {$pnotify|json_encode}</script>{/if}
{foreach registry::$js_lib_footer as $k => $v}
<script type="text/javascript" src="{$config.url}web/lib/{$v}"></script>
{/foreach}
<script type="text/javascript" src="{$config.url}themes/bt3/js/bootstrap.min.js"></script>

{if $smarty.const.IN_PRODUCTION === false}
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 main" id="content-central">
			<div class="pull-right">
				<a href="#dvlpModal" role="button" class="btn btn-primary" data-toggle="modal">Infos dev</a>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div style="size:9px; margin:auto; width:1000px;">
				<div>
				Page generee en : {$dvlp_tps_generation} sec | 
				Requete SQL : {$dvlp_nb_queries}| 
				Utilisation memoire : {$dvlp_memory} mo
				</div>
			</div>
			{$infosdev}
		</div>
	</div>
</div>
{/if}

</body>
</html>