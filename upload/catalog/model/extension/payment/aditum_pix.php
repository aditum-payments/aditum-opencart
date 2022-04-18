<?php
class ModelExtensionPaymentAditumPix extends Model {

	public function getMethod($address, $total) {
		$this->load->language('extension/payment/aditum_pix');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_aditum_pix_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($total < $this->config->get('payment_aditum_pix_total')) {
			$status = false;
		} elseif (!$this->cart->hasShipping()) {
			$status = false;
		} elseif (!$this->config->get('payment_aditum_pix_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}


		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'aditum_pix',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_aditum_pix_sort_order')
			);
		}

		return $method_data;
	}
}