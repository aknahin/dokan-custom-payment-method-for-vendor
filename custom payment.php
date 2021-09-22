<?php
#-- Custom Payment Method --#
function new_withdraw_methods($methods){
  $methods['custom'] = [
    'title'     => __( 'Coinbase', 'dokan-lite' ),
    'callback'  =>  'dokan_withdraw_method_custom'
  ];
  return $methods;
}
add_filter('dokan_withdraw_methods', 'new_withdraw_methods');

#-- Custom Withdraw Method Callback function --#
function dokan_withdraw_method_custom( $store_settings ) {
    global $current_user;
	$wallet = isset( $store_settings['payment']['custom']['wallet'] ) ? esc_attr( $store_settings['payment']['custom']['wallet'] ) : '';
    $address = isset( $store_settings['payment']['custom']['address'] ) ? esc_attr( $store_settings['payment']['custom']['address'] ) : '' ?>
    <div class="dokan-form-group">
        <div class="dokan-w8">
			 <div class="dokan-input-group">
                <span class="dokan-input-group-addon"><?php esc_html_e( 'Wallet', 'dokan-lite' ); ?></span>
                <input value="<?php echo esc_attr( $wallet ); ?>" name="settings[custom][wallet]" class="dokan-form-control wallet" placeholder="BTC/USD" type="text">
            </div>
            <div class="dokan-input-group">
                <span class="dokan-input-group-addon"><?php esc_html_e( 'Address', 'dokan-lite' ); ?></span>
                <input value="<?php echo esc_attr( $address ); ?>" name="settings[custom][address]" class="dokan-form-control address" placeholder="3HBP1RKe5yAqZ2L9xqc94kBZ6NrGYTFb2M" type="text">
            </div>
        </div>
    </div>
    <?php
}
#-- Save --#
function save_custom_payment_method( $store_id, $dokan_settings ) {
  $post_data = wp_unslash( $_POST );

  if ( isset( $post_data['settings']['custom'] ) ) {
      $dokan_settings['payment']['custom'] = array(
		  'wallet' => $post_data['settings']['custom']['wallet'] ,
          'address' => $post_data['settings']['custom']['address'] ,
      );
  }

  update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );
}
add_action( 'dokan_store_profile_saved', 'save_custom_payment_method', 10, 2 );
#-- Dropdown--#
function seller_active_withdraw_methods($active_payment_methods, $vendor_id){
  $store_info = dokan_get_store_info($vendor_id);
	$custom = isset( $store_info['payment']['custom']['wallet'] ) && $store_info['payment']['custom']['wallet'] !== false ? 'custom' : '';
  $custom = isset( $store_info['payment']['custom']['address'] ) && $store_info['payment']['custom']['address'] !== false ? 'custom' : '';
  $active_payment_methods[] = $custom;
  return $active_payment_methods;
}
add_filter('dokan_get_seller_active_withdraw_methods', 'seller_active_withdraw_methods', 10, 2);
#-- Details --#
function vue_admin_withdraw(){
  ?>
  <script>
    var hooks;
    function getCustomPaymentDetails(details, method, data){
      if (data[method] !== undefined) {
        if ('custom' === method) {
			a = data[method].wallet+' > '+data[method].address;
			details = a || '';
        }
      }

      return details;
    }
    dokan.hooks.addFilter('dokan_get_payment_details', 'getCustomPaymentDetails', getCustomPaymentDetails, 33, 3);
  </script>
  <?php
}
add_action('admin_print_footer_scripts', 'vue_admin_withdraw', 99);
