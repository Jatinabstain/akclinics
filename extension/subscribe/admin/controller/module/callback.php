<?php


namespace Opencart\Admin\Controller\Extension\Subscribe\Module;


class Callback extends \Opencart\System\Engine\Controller
{

    public function index(): void
    {
        $this->load->language('extension/subscribe/module/callback');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/subscribe/module/callback', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['delete'] = $this->url->link('extension/subscribe/module/callback.delete', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->callbackList();

        $this->response->setOutput($this->load->view('extension/subscribe/module/callback', $data));
    }

    protected function callbackList(): string
    {
        if (isset($this->request->get['sort'])) {
            $sort = (string) $this->request->get['sort'];
        } else {
            $sort = 'date_added';
        }

        if (isset($this->request->get['order'])) {
            $order = (string) $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['action'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . $url);

        $data['callback'] = [];
        $filter_data = [
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin')
        ];
        $this->load->model('extension/subscribe/module/callback');
        $results = $this->model_extension_subscribe_module_callback->getResults($filter_data);
        foreach ($results as $result) {
            $data['callback'][] = [
                'id'            => $result['id'],
                'name'          => $result['name'],
                'age'           => $result['age'],
                'email'         => $result['email'],
                'phone'         => $result['phone'],
                'ip_address'    => $result['ip_address'],
                'date_added'    => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
            ];
        }
        $url = '';

        if ($order === 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_name'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
        $data['sort_age'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . '&sort=age' . $url);
        $data['sort_email'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url);
        $data['sort_phone'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url);
        $data['sort_ip_address'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . '&sort=ip_address' . $url);
        $data['sort_date_added'] = $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $callback_total = $this->model_extension_subscribe_module_callback->getTotalCallbacks();
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $callback_total,
            'page'  => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url'   => $this->url->link('extension/subscribe/module/callback.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
        ]);
        $data['results'] = sprintf($this->language->get('text_pagination'), ($callback_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($callback_total - $this->config->get('config_pagination_admin'))) ? $callback_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $callback_total, ceil($callback_total / $this->config->get('config_pagination_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('extension/subscribe/module/callback_list', $data);

    }

    /**
     * @return void
     */
    public function list(): void {
        $this->load->language('extension/subscribe/module/callback');

        $this->response->setOutput($this->callbackList());
    }

    public function delete(): void
    {
        $this->load->language('extension/subscribe/module/callback');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } else {
            $selected = [];
        }

        if (!$this->user->hasPermission('modify', 'extension/subscribe/module/callback')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('extension/subscribe/module/callback');

            foreach ($selected as $id) {
                $this->model_extension_subscribe_module_callback->deleteEntry($id);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}