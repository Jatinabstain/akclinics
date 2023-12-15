<?php
namespace Opencart\Catalog\Controller\Common;
/**
 * Class Footer
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Footer extends \Opencart\System\Engine\Controller {
	/**
	 * @return string
	 */
	public function index(): string {
		$this->load->language('common/footer');

		$data['informations'] = [];
		$this->load->model('catalog/information');

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
                $external = false;
                $href = $this->url->link('information/information', 'language=' . $this->config->get('config_language') . '&information_id=' . $result['information_id']);
                if ($result['external_url']) {
                    $href = $result['external_url'];
                    $external = true;
                }
                if ((int) $result['information_id'] === 5 ) {
                    $external = false;
                }
				$data['informations'][] = [
					'title' => $result['title'],
                    'href'  => $href,
                    'external'  => $external,
				];
			}
		}

		$data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));
		$data['return'] = $this->url->link('account/returns.add', 'language=' . $this->config->get('config_language'));

		// Who's Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['HTTP_X_REAL_IP'])) {
				$ip = $this->request->server['HTTP_X_REAL_IP'];
			} elseif (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['bootstrap'] = 'catalog/view/javascript/bootstrap/js/bootstrap.bundle.min.js';

		$data['scripts'] = $this->document->getScripts('footer');

		$data['cookie'] = $this->load->controller('common/cookie');

		$data['telephone'] = $this->config->get('config_telephone');
		$data['email'] = $this->config->get('config_email');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['FB_LINK'] = FB_LINK;
		$data['LI_LINK'] = LI_LINK;
		$data['IG_LINK'] = IG_LINK;
		$data['YT_LINK'] = YT_LINK;
        $data['subscribe'] = $this->load->controller('extension/subscribe/module/subscribe');
        $data['callback'] = $this->load->controller('extension/subscribe/module/subscribe.callback');
		return $this->load->view('common/footer', $data);
	}
}
