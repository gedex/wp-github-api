<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>
	<h3><?php echo esc_html( $tool['title'] ); ?></h3>

	<p><?php echo esc_html( $tool['desc'] ); ?></p>

	<?php
	$result = call_user_func( $tool['callback'] );

	require $tool['view'];
	?>
</div>
