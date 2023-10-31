<?php
namespace Opencart\Admin\Controller\Extension\HairTest\Module;

class HairTest extends \Opencart\System\Engine\Controller {

	public function index(): void
	{
		$this->load->language('extension/hair_test/module/hair_test');
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
			'href' => $this->url->link('extension/hair_test/module/hair_test', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['save'] = $this->url->link('extension/hair_test/module/hair_test.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['module_hair_test_status'] = $this->config->get('module_hair_test_status');

		$this->response->setOutput($this->load->view('extension/hair_test/module/hair_test', $data));
	}

    public function save(): void {
        $this->load->language('extension/hair_test/module/hair_test');
        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/hair_test/module/hair_test')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('module_hair_test', $this->request->post);

            $json['success'] = $this->language->get('text_success');

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
	
	public function install():void
	{
        $this->load->model('extension/hair_test/module/hair_test');
        $this->model_extension_hair_test_module_hair_test->install();
	}

}