<?php if ( $result ) : ?>

	<style>
	#weekly-commit-count rect { fill: #999; }
	#weekly-commit-count text { fill: #fff; font: 9px sans-serif; text-anchor: middle; }
	</style>
	<svg id="weekly-commit-count"></svg>

<?php else : ?>

	<?php _e( 'Unable to retrieve user information', 'github-api' ); ?>

<?php endif; ?>
