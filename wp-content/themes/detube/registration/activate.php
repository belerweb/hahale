<?php get_header( 'buddypress' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_activation_page' ); ?>

		<div class="page" id="activate-page">

			<?php if ( bp_account_was_activated() ) : ?>

				<h2 class="widgettitle"><?php _e( 'Account Activated', 'dp' ); ?></h2>

				<?php do_action( 'bp_before_activate_content' ); ?>

				<?php if ( isset( $_GET['e'] ) ) : ?>
					<p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'dp' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up.', 'dp' ); ?></p>
				<?php endif; ?>

			<?php else : ?>

				<h3><?php _e( 'Activate your Account', 'dp' ); ?></h3>

				<?php do_action( 'bp_before_activate_content' ); ?>

				<p><?php _e( 'Please provide a valid activation key.', 'dp' ); ?></p>

				<form action="" method="get" class="standard-form" id="activation-form">

					<label for="key"><?php _e( 'Activation Key:', 'dp' ); ?></label>
					<input type="text" name="key" id="key" value="" />

					<p class="submit">
						<input type="submit" class="btn-black" name="submit" value="<?php _e( 'Activate', 'dp' ); ?>" />
					</p>

				</form>

			<?php endif; ?>

			<?php do_action( 'bp_after_activate_content' ); ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_activation_page' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'buddypress' ); ?>

<?php get_footer( 'buddypress' ); ?>
