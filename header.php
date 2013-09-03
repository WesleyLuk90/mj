<html>
<head>
	<?php wp_head() ?>
</head>
<body>
	<div class="page container">
		<section role="menu" class="navbar navbar-default">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?php home_url() ?>"><?php bloginfo( "name" ) ?></a>
			</div>
			<?php
			wp_nav_menu( array(
				'menu'            => 'primary', 
				'container'       => 'div', 
				'container_class' => 'navbar-collapse collapse', 
				'menu_class'      => 'menu',
				'items_wrap'      => '<ul id="%1$s" class="%2$s nav navbar-nav">%3$s</ul>',
				) )
			?>
		</section>
		<section role="widgets">
			<?php
			dynamic_sidebar('top-widget-bar');
			?>
		</section>
		<section role="content">
