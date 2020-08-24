<h2 id="saved-cards" style="margin-top:40px;"><?php esc_html_e( 'Saved cards', 'wc-eway' ); ?></h2>
<table class="shop_table">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Card', 'wc-eway' ); ?></th>
			<th><?php esc_html_e( 'Expires', 'wc-eway' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( (array) $cards as $card ) : ?>
		<tr>
			<td><?php echo esc_html( $card['number'] ); ?></td>
			<td><?php printf( esc_html__( 'Expires %1$s/%2$s', 'wc-eway' ), esc_html( $card['exp_month'] ), esc_html( $card['exp_year'] ) ); ?></td>
			<td>
				<form action="" method="POST">
					<?php wp_nonce_field( 'eway_del_card' ); ?>
					<input type="hidden" name="eway_delete_card" value="<?php echo esc_attr( $card['id'] ); ?>">
					<input type="submit" class="button" value="<?php esc_attr_e( 'Delete card', 'wc-eway' ); ?>">
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
