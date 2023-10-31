<?php
namespace Opencart\Admin\Controller\Extension\Subscribe\Module;

class Subscribe extends \Opencart\System\Engine\Controller {

	public function index(): void
	{
		$this->load->language('extension/subscribe/module/subscribe');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/subscribe/module/subscribe', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['save'] = $this->url->link('extension/subscribe/module/subscribe.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['module_subscribe_status'] = $this->config->get('module_subscribe_status');

		$this->response->setOutput($this->load->view('extension/subscribe/module/subscribe', $data));
	}

    public function save(): void {
        $this->load->language('extension/subscribe/module/subscribe');
        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/subscribe/module/subscribe')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('module_subscribe', $this->request->post);

            $json['success'] = $this->language->get('text_success');

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
	
	public function install():void
	{
        $this->load->model('extension/subscribe/module/subscribe');
        $this->model_extension_subscribe_module_subscribe->install();
	}

}