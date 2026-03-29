<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package shopstore
 */

?>

		<?php if ( is_active_sidebar( 'footer' ) ) { ?>
		<footer>
			<div class="container">
            	<?php if ( is_active_sidebar( 'footer' ) ) { ?>
                    <div class="row">
                        <?php dynamic_sidebar( 'footer' ); ?>
                    </div>
                <?php }?>
			</div><!-- /.container -->
		</footer><!-- /footer -->
 		<?php }?>
		<section class="footer-bottom">
			<div class="container">
				<div class="row">
					<div class="col-md-7">
						<p class="copyright"> 
						<?php
						$l = substr(strtolower(get_locale()),0,2);
						$dev_url = ($l==='de')?'https://de.athemeart.com/':(($l==='es')?'https://es.athemeart.com/':(($l==='pl')?'https://pl.athemeart.com/':(($l==='jp'||$l==='ja')?'https://jp.athemeart.com/':'https://athemeart.com/')));
						printf(
							wp_kses_post(__('Copyright &copy; %1$s %2$s, All Rights Reserved.<br/> %3$s by %4$s - Proudly powered by WordPress','shopstore')),
							esc_html(date('Y')),
							esc_html(get_bloginfo('name')),
							'<a href="https://wordpress.org/themes/shopstore/" target="_blank" rel="noopener">ShopStore</a>',
							'<a href="'.esc_url($dev_url).'" target="_blank" rel="noopener">aThemeArt</a>'
						);
						?>							
						</p>
					</div><!-- /.col-md-12 -->
                    <div class="col-md-5 text-right">
                    
                        <ul class="social-list">
                         <?php if ( get_theme_mod('shopstore_social_profile_link') != "" && count (  get_theme_mod('shopstore_social_profile_link') ) > 0 ) :?>	
                         <?php $social_link = get_theme_mod('shopstore_social_profile_link');?>
                   
						<?php 
						foreach ($social_link['social'] as $key => $link): 
							if( !empty( $link )):
							?>
							<li><a href="<?php echo esc_url( $link );?>" class="fa <?php echo esc_attr($key);?>" target="_blank"></a></li>
							<?php endif; 
                        endforeach;
						?>
                   		 <?php endif;?>
                          
                        </ul>

					</div><!-- /.col-md-12 -->
				</div><!-- /.row -->
			</div><!-- /.container -->
		</section><!-- /.footer-bottom -->
</div><!-- /.boxed -->

<a href="javascript:void(0)" id="backToTop" class="ui-to-top"><?php echo esc_html__( 'BACK TO TOP', 'shopstore' );?><i class="fa fa-long-arrow-up"></i></a>

<?php wp_footer(); ?>
</body>
</html>
