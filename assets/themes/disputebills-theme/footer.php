<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package some_like_it_neat
 */
?>
		<?php tha_content_bottom(); ?>
		</main><!-- #main -->
		<?php tha_content_after(); ?>
		<?php tha_footer_before(); ?>
		<footer class="page-footer" role="contentinfo" itemscope="itemscope" itemtype="http://schema.org/WPFooter">

		<?php tha_footer_top(); ?>
			<div class="footer-block" style="padding: 36px 20px 36px; padding: 2rem 1.33333rem 2.4rem 2rem;">
				<div class="footer-logo">
					<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img src="http://res.cloudinary.com/candidbusiness/image/upload/v1455406791/dispute-bills-logo-white.png" width="200" height="32" alt="Medical Dispute">
					</a></p>
					<p><strong>410 N. May Street Chicago, Illinois 60642 | <a href="tel:1-888-622-2809">(888) 622-2809</a></strong></p>
					<p class="copyright">©<?php echo date('Y'); ?> Dispute Bills. All Rights Reserved.</p>
				</div>
				<ul class="social-media">
					<li><a href="http://www.bbb.org/chicago/business-reviews/medical-billing-services/disputebills-com-in-chicago-il-90002872/" alt="Better Business Bureau" style="width: 115px; opacity: .8;"><img src="http://disputebills.com/assets/uploads/2016/05/bbb-horizontal-ab-seal.png" style="width: 115px;"></a></li>
					<li><a href="https://www.facebook.com/disputebills"><img width="45" height="45" src="http://res.cloudinary.com/candidbusiness/image/upload/v1456556634/facebook.png" alt='facebook' /></a></li>
					<li><a href="https://twitter.com/DisputeBills"><img width="45" height="45" src="http://res.cloudinary.com/candidbusiness/image/upload/v1456556533/twitter.png" alt='twitter' /></a></li>
					<li><a href="https://plus.google.com/103378801284776045769"><img width="45" height="45" src="http://res.cloudinary.com/candidbusiness/image/upload/v1456556634/google-plus.png" alt='google plus' /></a></li>
					<li><a href="https://www.linkedin.com/company/dispute"><img width="45" height="45" src="http://res.cloudinary.com/candidbusiness/image/upload/v1456556634/linkedin.png" alt='linkedin' /></a></li>
				</ul>
			</div>			
		</footer>
		<?php tha_footer_after(); ?>
	</div>
<?php wp_footer(); ?>
<?php do_action('bw_after_wp_footer'); ?>
<?php tha_body_bottom(); ?>
</body>
</html>