<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package sparkling
 */
?>
			</div><!-- close .*-inner (main-content or sidebar, depending if sidebar is used) -->
		</div><!-- close .row -->
	</div><!-- close .container -->
</div><!-- close .site-content -->

<div class="container-fluid footer-top">
	<div class="container text-center">
        <div class="row">
            <div class="col-md-4 contact-details">
                <h4><i class="fa fa-phone"></i> Call</h4>
                <p>320.285.2200</p>
            </div>
            <div class="col-md-4 contact-details">
                <h4><i class="fa fa-map-marker"></i> Visit</h4>
                <p>315 Maple Str. N<br>P.O. Box 201<br>Grey Eagle, MN 56336</p>
            </div>
            <div class="col-md-4 contact-details">
                <h4><i class="fa fa-envelope"></i> Email</h4>
                <p><a href="mailto:kboffice@arvig.net">kboffice@arvig.net</a>
                </p>
            </div>
        </div>
        <div class="row social">
            <div class="col-lg-12">
                <ul class="list-inline">
                    <li><a target="_blank" href="https://www.facebook.com/Kbsspecialties"><i class="fa fa-facebook fa-fw fa-2x"></i></a>
                    </li>
                    <!--<li><a href="#"><i class="fa fa-twitter fa-fw fa-2x"></i></a>
                    </li>
                    <li><a href="#"><i class="fa fa-linkedin fa-fw fa-2x"></i></a>
                    </li>-->
                </ul>
            </div>
        </div>
        <div class="row kbs">
            <div class="col-lg-12">
                <p class="small">&copy; 2014 KB's Specialties LLC</p>
            </div>
        </div>
    </div>
</div><!--.footer-top-->

	<div id="footer-area">
		<div class="container footer-inner">
			<div class="row">
				<?php get_sidebar( 'footer' ); ?>
			</div>
		</div>

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info container">
				<div class="row">
					<?php sparkling_social(); ?>
					<nav role="navigation" class="col-md-6">
						<?php sparkling_footer_links(); ?>
					</nav>
					<div class="copyright col-md-6">
						Developed by <a href="http://greydenmedia.com" target="_blank"> Greyden Media</a>
					</div>
				</div>
			</div><!-- .site-info -->
			<div class="scroll-to-top"><i class="fa fa-angle-up"></i></div><!-- .scroll-to-top -->
		</footer><!-- #colophon -->
	</div>


</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>