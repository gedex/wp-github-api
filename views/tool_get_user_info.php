<?php if ( $result ) : ?>
	<table>
		<tbody>
			<tr>
				<td>
					<img width="230" height="230" src="<?php echo esc_url( $result->avatar_url ); ?>" alt="<?php echo esc_attr( $result->name ); ?>">
				</td>
				<td>
					<table>
						<tr>
							<td><?php _e( 'Name', 'github-api' ); ?></td>
							<td><strong><?php echo esc_html( $result->name ); ?></strong></td>
						</tr>
						<tr>
							<td><?php _e( 'Company', 'github-api' ); ?></td>
							<td><strong><?php echo esc_html( $result->company ); ?></strong></td>
						</tr>
						<tr>
							<td><?php _e( 'Blog', 'github-api' ); ?></td>
							<td><a href="<?php echo esc_url( $result->blog ); ?>" target="_blank"><?php echo esc_html( $result->blog ); ?></a></td>
						</tr>
						<tr>
							<td><?php _e( 'GitHub URL', 'github-api' ); ?></td>
							<td><a href="<?php echo esc_url( $result->html_url ); ?>" target="_blank"><?php echo esc_html( $result->html_url ); ?></a></td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
<?php else : ?>

	<?php _e( 'Unable to retrieve user information', 'github-api' ); ?>

<?php endif; ?>
