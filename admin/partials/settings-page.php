<div class="wrap geot-settings">
	<form name="geot-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">

			<?php do_action( 'geot/settings_partial/before', $opts ); ?>

			<tr valign="top" class="geot-settings-title">
				<th colspan="3"><h3><?php _e( 'GeotargetingWP : ', 'geot' ); ?></h3></th>
			</tr>

			<tr valign="top" class="">
				<th><label for="menu_integration"><?php _e( 'Disable Menu integration', 'geot' ); ?></label></th>
				<td colspan="3">
					<input type="checkbox" id="menu_integration" name="geot_settings[disable_menu_integration]"
					       value="1" <?php checked( $opts['disable_menu_integration'], '1' ); ?>/>
					<p class="help"><?php _e( 'Check this to remove geotargeting options from menus', 'geot' ); ?></p>
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="widget_integration"><?php _e( 'Disable Widget Integration', 'geot' ); ?></label></th>
				<td colspan="3">
					<input type="checkbox" id="widget_integration" name="geot_settings[disable_widget_integration]"
					       value="1" <?php checked( $opts['disable_widget_integration'], '1' ); ?>/>
					<p class="help"><?php _e( 'Check this to remove geotargeting options from widgets', 'geot' ); ?></p>
				</td>
			</tr>
			<?php
			$settings = geot_settings();
			if( isset( $settings['ajax_mode'] ) && '1' == $settings['ajax_mode'] ) { ?>
			<tr valign="top" class="">
				<th><label for="widget_integration"><?php _e( 'Disable remove post globally', 'geot' ); ?></label></th>
				<td colspan="3">
					<input type="checkbox" id="widget_integration" name="geot_settings[disable_remove_post]"
					       value="1" <?php checked( $opts['disable_remove_post'], '1' ); ?>/>
					<p class="help"><?php _e( 'When ajax mode it\'s enabled checking a post to remove from listing and search will consume one request on every page. Check this option to disable globally', 'geot' ); ?></p>
				</td>
			</tr>
			<?php } ?>
			<tr valign="top" class="">
				<th><label for="taxonomy_integration"><?php _e( 'Enable on taxonomies', 'geot' ); ?></label></th>
				<td colspan="3">

					<?php foreach($taxs as $tax_key => $tax_value) : ?>
						<input
							type="checkbox"
							id="widget_integration"
							name="geot_settings[enable_taxonomies][]"
							value="<?php echo $tax_key; ?>"
							<?php checked( true, in_array( $tax_key, $opts['enable_taxonomies'] ) ); ?>
						/> <span><?php echo $tax_value; ?></span>
						<br />
					<?php endforeach; ?>

					
				</td>
			</tr>

			<tr valign="top" class="">
				<th><label for="widget_integration"><?php _e( 'Radius in miles or km?', 'geot' ); ?></label></th>
				<td colspan="3">
					<select name="geot_settings[radius_unit]">
						<option value="km" <?php selected( $opts['radius_unit'], 'km' ); ?>>km</option>
						<option value="miles" <?php selected( $opts['radius_unit'], 'miles' ); ?>>miles</option>
					</select>
					<p class="help"><?php _e( 'Choose the unit to apply when using radius functionality', 'geot' ); ?></p>
				</td>
			</tr>
			<?php do_action( 'geot/settings_partial/after', $opts ); ?>

			<tr>
				<td><input type="submit" name="geot_settings[button]" class="button-primary"
				           value="<?php _e( 'Save settings', 'geot' ); ?>"/></td>
				<input type="hidden" name="geot_return" value="<?php echo $return; ?>"/>
				<?php wp_nonce_field( 'geot_pro_save_settings', 'geot_nonce' ); ?>
		</table>
	</form>
</div>
