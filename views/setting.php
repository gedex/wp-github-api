<div class="wrap">
	<?php screen_icon( 'tools' ) ?>
	<h2><?php echo esc_html( $title ); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( $plugin ); ?>
		<?php do_settings_sections( $plugin ); ?>
		<?php submit_button(); ?>
	</form>
</div>
