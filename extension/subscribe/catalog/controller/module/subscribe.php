<?php


namespace Opencart\Catalog\Controller\Extension\Subscribe\Module;

class Subscribe extends \Opencart\System\Engine\Controller
{
    public function index(): string  {
       $status = $this->config->get('module_subscribe_status');
       if ($status) {
            $data['action'] =  $this->url->link('extension/subscribe/module/subscribe.save', 'language=' . $this->config->get('config_language'));
            return $this->load->view('extension/subscribe/module/subscribe', $data);
       }
       return '';
    }

    /**
     * Save Subscriber Email.
     * @throws \JsonException
     */
    public function save(): void
    {
        $this->load->language('extension/subscribe/module/subscribe');
        $this->load->model('extension/subscribe/module/subscribe');
        $json = [];
        if ((oc_strlen($this->request->post['subscribe_email']) > 96) || !filter_var($this->request->post['subscribe_email'], FILTER_VALIDATE_EMAIL)) {
            $json['error']['subscribe_email'] = $this->language->get('error_email');
        }
        $customer_info = $this->model_extension_subscribe_module_subscribe->getUserByEmail($this->request->post['subscribe_email']);
        if ($customer_info) {
            $json['error']['warning'] = $this->language->get('error_exists');
        }
        if (!$json) {
            $this->model_extension_subscribe_module_subscribe->addUser($this->request->post['subscribe_email']);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_THROW_ON_ERROR));
    }

    public function callback(): string{
        $status = $this->config->get('module_subscribe_status');
        if ($status) {
            $data['action'] =  $this->url->link('extension/subscribe/module/subscribe.save_callback', 'language=' . $this->config->get('config_language'));
            return $this->load->view('extension/subscribe/module/callback', $data);
        }
        return '';
    }

    public function save_callback(): void
    {
        $this->load->language('extension/subscribe/module/callback');
        $this->load->model('extension/subscribe/module/subscribe');
        $json = [];
        if ((oc_strlen($this->request->post['name']) < 4) || (oc_strlen($this->request->post['name']) > 32)) {
            $json['error']['name'] = $this->language->get('error_name');
        }
        if ((oc_strlen($this->request->post['call_email']) > 96) || !filter_var($this->request->post['call_email'], FILTER_VALIDATE_EMAIL)) {
            $json['error']['call_email'] = $this->language->get('error_email');
        }
        if ((oc_strlen($this->request->post['phone']) < 10) || (oc_strlen($this->request->post['phone']) > 12)) {
            $json['error']['phone'] = $this->language->get('error_phone');
        }

        if (!$json) {
            $this->model_extension_subscribe_module_subscribe->addCallback($this->request->post);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_THROW_ON_ERROR));
    }
}