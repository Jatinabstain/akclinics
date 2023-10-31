<?php


namespace Opencart\Admin\Controller\Extension\Subscribe\Module;


class Subscribers extends \Opencart\System\Engine\Controller {

    public function index(): void
    {
        $this->load->language('extension/subscribe/module/subscribers');
        $data['list'] = $this->subscribersList();
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/subscribe/module/subscribers', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['delete'] = $this->url->link('extension/subscribe/module/subscribers.delete', 'user_token=' . $this->session->data['user_token']);

        $this->response->setOutput($this->load->view('extension/subscribe/module/subscribers', $data));
    }


    protected function subscribersList(): string
    {
        if (isset($this->request->get['sort'])) {
            $sort = (string)$this->request->get['sort'];
        } else {
            $sort = 'date_added';
        }

        if (isset($this->request->get['order'])) {
            $order = (string)$this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
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

        $data['action'] = $this->url->link('extension/subscribe/module/subscribers.list', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['subscribers'] = [];

        $filter_data = [
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin')
        ];

        $this->load->model('extension/subscribe/module/subscribe');
        $results = $this->model_extension_subscribe_module_subscribe->getSubscribers($filter_data);
        foreach ($results as $result) {
            $data['subscribers'][] = [
                'subscriber_id' => $result['subscriber_id'],
                'email'       => $result['email'],
                'status'      => $result['status'],
                'date_added'  => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
            ];
        }
        $url = '';

        if ($order === 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_email'] = $this->url->link('extension/subscribe/module/subscribers.list', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url);
        $data['sort_date_added'] = $this->url->link('extension/subscribe/module/subscribers.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);
        $data['sort_status'] = $this->url->link('extension/subscribe/module/subscribers.list', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $subscribers_total = $this->model_extension_subscribe_module_subscribe->getTotalSubscribers();
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $subscribers_total,
            'page'  => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url'   => $this->url->link('extension/subscribe/module/subscribers.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
        ]);
        $data['results'] = sprintf($this->language->get('text_pagination'), ($subscribers_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($subscribers_total - $this->config->get('config_pagination_admin'))) ? $subscribers_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $subscribers_total, ceil($subscribers_total / $this->config->get('config_pagination_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('extension/subscribe/module/subscribers_list', $data);
    }

    /**
     * @return void
     */
    public function list(): void {
        $this->load->language('extension/subscribe/module/subscribers');

        $this->response->setOutput($this->subscribersList());
    }

    public function delete(): void
    {
        $this->load->language('extension/subscribe/module/subscribers');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } else {
            $selected = [];
        }

        if (!$this->user->hasPermission('modify', 'extension/subscribe/module/subscribers')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('extension/subscribe/module/subscribe');

            foreach ($selected as $subscriber_id) {
                $this->model_extension_subscribe_module_subscribe->deleteSubscriber($subscriber_id);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}