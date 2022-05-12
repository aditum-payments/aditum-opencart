<?php
class ControllerExtensionPaymentAditumPix extends Controller {
	private $error = array();
	
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "aditum` (
		  `order_id` int(11),
		  `date_added` datetime NOT NULL DEFAULT current_timestamp,
		  `data` longtext NOT NULL
		)");
	}

	public function index() {
		$this->load->language('extension/payment/aditum_pix');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_aditum_pix', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->load->model('customer/custom_field');
		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/aditum_pix', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/aditum_pix', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_aditum_pix_total'])) {
			$data['payment_aditum_pix_total'] = $this->request->post['payment_aditum_pix_total'];
		} else {
			$data['payment_aditum_pix_total'] = ($c=$this->config->get('payment_aditum_pix_total')) ? $c : 0;
		}

		if (isset($this->request->post['payment_aditum_pix_order_status_id'])) {
			$data['payment_aditum_pix_order_status_id'] = $this->request->post['payment_aditum_pix_order_status_id'];
		} else {
			$data['payment_aditum_pix_order_status_id'] = $this->config->get('payment_aditum_pix_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_aditum_pix_geo_zone_id'])) {
			$data['payment_aditum_pix_geo_zone_id'] = $this->request->post['payment_aditum_pix_geo_zone_id'];
		} else {
			$data['payment_aditum_pix_geo_zone_id'] = $this->config->get('payment_aditum_pix_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_aditum_pix_status'])) {
			$data['payment_aditum_pix_status'] = $this->request->post['payment_aditum_pix_status'];
		} else {
			$data['payment_aditum_pix_status'] = $this->config->get('payment_aditum_pix_status');
		}

		if (isset($this->request->post['payment_aditum_pix_sort_order'])) {
			$data['payment_aditum_pix_sort_order'] = $this->request->post['payment_aditum_pix_sort_order'];
		} else {
			$data['payment_aditum_pix_sort_order'] = $this->config->get('payment_aditum_pix_sort_order');
		}

		if (isset($this->request->post['payment_aditum_pix_modo'])) {
			$data['payment_aditum_pix_modo'] = $this->request->post['payment_aditum_pix_modo'];
		} else {
			$data['payment_aditum_pix_modo'] = $this->config->get('payment_aditum_pix_modo');
		}

		if (isset($this->request->post['payment_aditum_pix_titulo_gateway'])) {
			$data['payment_aditum_pix_titulo_gateway'] = $this->request->post['payment_aditum_pix_titulo_gateway'];
		} else {
			$data['payment_aditum_pix_titulo_gateway'] = ($c=$this->config->get('payment_aditum_pix_titulo_gateway')) ? $c : 'Aditum Pix Gateway';
		}

		if (isset($this->request->post['payment_aditum_pix_descricao_gateway'])) {
			$data['payment_aditum_pix_descricao_gateway'] = $this->request->post['payment_aditum_pix_descricao_gateway'];
		} else {
			$data['payment_aditum_pix_descricao_gateway'] = ($c=$this->config->get('payment_aditum_pix_descricao_gateway')) ? $c : 'Pague com total segurança através de pix';
		}

		if (isset($this->request->post['payment_aditum_pix_cnpj'])) {
			$data['payment_aditum_pix_cnpj'] = $this->request->post['payment_aditum_pix_cnpj'];
		} else {
			$data['payment_aditum_pix_cnpj'] = $this->config->get('payment_aditum_pix_cnpj');
		}

		if (isset($this->request->post['payment_aditum_pix_merchant_token'])) {
			$data['payment_aditum_pix_merchant_token'] = $this->request->post['payment_aditum_pix_merchant_token'];
		} else {
			$data['payment_aditum_pix_merchant_token'] = $this->config->get('payment_aditum_pix_merchant_token');
		}

		if (isset($this->request->post['payment_aditum_pix_campo_documento'])) {
			$data['payment_aditum_pix_campo_documento'] = $this->request->post['payment_aditum_pix_campo_documento'];
		} else {
			$data['payment_aditum_pix_campo_documento'] = $this->config->get('payment_aditum_pix_campo_documento');
		}

		if (isset($this->request->post['payment_aditum_pix_campo_numero'])) {
			$data['payment_aditum_pix_campo_numero'] = $this->request->post['payment_aditum_pix_campo_numero'];
		} else {
			$data['payment_aditum_pix_campo_numero'] = $this->config->get('payment_aditum_pix_campo_numero');
		}

		if (isset($this->request->post['payment_aditum_pix_campo_complemento'])) {
			$data['payment_aditum_pix_campo_complemento'] = $this->request->post['payment_aditum_pix_campo_complemento'];
		} else {
			$data['payment_aditum_pix_campo_complemento'] = $this->config->get('payment_aditum_pix_campo_complemento');
		}

		if (isset($this->request->post['payment_aditum_pix_tipo_antifraude'])) {
			$data['payment_aditum_pix_tipo_antifraude'] = $this->request->post['payment_aditum_pix_tipo_antifraude'];
		} else {
			$data['payment_aditum_pix_tipo_antifraude'] = $this->config->get('payment_aditum_pix_tipo_antifraude');
		}

		if (isset($this->request->post['payment_aditum_pix_token_antifraude'])) {
			$data['payment_aditum_pix_token_antifraude'] = $this->request->post['payment_aditum_pix_token_antifraude'];
		} else {
			$data['payment_aditum_pix_token_antifraude'] = $this->config->get('payment_aditum_pix_token_antifraude');
		}

		if (isset($this->request->post['payment_aditum_pix_debug'])) {
			$data['payment_aditum_pix_debug'] = $this->request->post['payment_aditum_pix_debug'];
		} else {
			$data['payment_aditum_pix_debug'] = $this->config->get('payment_aditum_pix_debug');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/aditum_pix', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/aditum_pix')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}