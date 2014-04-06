<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>

	<p><?php _e( 'Tools contain example usage of <code>github-api</code> plugin API. Please check the code in <code>includes/tools.php</code>.', 'github-api' ); ?></p>

	<table class="widefat importers" cellspacing="0">
	<?php $alt = ''; ?>
	<?php foreach ( $tools as $key => $tool ) : ?>
		<?php
		$alt  = $alt ? '' : ' class="alternate"';
		$link = add_query_arg(
			array(
				'page' => $page,
				'tool' => $key,
			),
			admin_url( 'tools.php' )
		);

		printf(
			'
			<tr%s>
				<td class="import-system row-title"><a href="%s">%s</a></td>
				<td class="desc">%s</td>
			</tr>
			',
			$alt,
			esc_url( $link ),
			esc_html( $tool['title'] ),
			esc_html( $tool['desc'] )
		);
		?>
	<?php endforeach; ?>
	</table>
</div>
