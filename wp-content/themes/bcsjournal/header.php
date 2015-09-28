<?php
/**
 * The header for our theme.
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<div class="site-inner">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'twentysixteen' ); ?></a>
<div class="headernavi">  

<?php if ( is_front_page() && is_home() ) : ?>
    <div class="logo" style="float: left;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="http://blog.black-crows.com/wp-content/uploads/2015/09/blackcrows_logo.png" title="<?php bloginfo( 'name' ); ?>"></a>
    </div>  
<div class="headernavi">  	
      <ul class="navul">
        <li class="navli"><a href="http://store-blackcrows.herokuapp.com/" class="navli">shop</a></li>
        <li class="navli"><a href="/" class="navli">journal</a></li>
        <li class="navli"><a href="#" class="navli">about us</a></li>
         <li class="navli"><a href="#" class="navli">shop finder</a></li>
      </ul>    
</div>
<?php else : ?>
 <div class="logo" style="float: left;">
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="http://blog.black-crows.com/wp-content/uploads/2015/09/blackcrows_logo.png" title="<?php bloginfo( 'name' ); ?>"></a>
  </div>
<div class="headernavi">  	
      <ul class="navul">
        <li class="navli"><a href="http://store-blackcrows.herokuapp.com/" class="navli">shop</a></li>
        <li class="navli"><a href="/" class="navli">journal</a></li>
        <li class="navli"><a href="#" class="navli">about us</a></li>
         <li class="navli"><a href="#" class="navli">shop finder</a></li>
      </ul>    
</div>
					<?php endif;


					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo $description; ?></p>
					<?php endif; ?>



		

    

				</div><!-- .site-branding -->


				<?php if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) ) : ?>
					<button id="menu-toggle" class="menu-toggle"><?php esc_html_e( 'Menu', 'twentysixteen' ); ?></button>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) ) : ?>
					<div id="site-header-menu" class="site-header-menu">
						<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<nav id="site-navigation" class="main-navigation" role="navigation">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'primary',
										'menu_class'     => 'primary-menu'
									 ) );
								?>
							</nav><!-- .main-navigation -->
						<?php endif; ?>

						<?php if ( has_nav_menu( 'social' ) ) : ?>
							<nav id="social-navigation" class="social-navigation" role="navigation">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'social',
										'menu_class'     => 'social-links-menu',
										'depth'          => 1,
										'link_before'    => '<span class="screen-reader-text">',
										'link_after'     => '</span>'
									) );
								?>
							</nav><!-- .social-navigation -->
						<?php endif; ?>
					</div><!-- .site-header-menu -->
				<?php endif; ?>
			</div><!-- .site-header-main -->

			<?php if ( get_header_image() ) : ?>
				<div class="header-image">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img src="<?php header_image(); ?>" width="<?php echo absint( get_custom_header()->width ); ?>" height="<?php echo absint( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
					</a>
				</div>
			<?php endif; // End header image check. ?>
		</header><!-- .site-header -->

		<div id="content" class="site-content">
