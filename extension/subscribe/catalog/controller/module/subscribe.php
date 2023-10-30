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
        if ((oc_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = $this->language->get('error_email');
        }
        $customer_info = $this->model_extension_subscribe_module_subscribe->getUserByEmail($this->request->post['email']);
        if ($customer_info) {
            $json['error']['warning'] = $this->language->get('error_exists');
        }
        if (!$json) {
            $this->model_extension_subscribe_module_subscribe->addUser($this->request->post['email']);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_THROW_ON_ERROR));
    }
}