<?php

namespace Opencart\Admin\Controller\Extension\HairTest\Module;

class Category extends \Opencart\System\Engine\Controller
{

    public function index(): void
    {
        $this->load->language('extension/hair_test/module/category');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/hair_test/module/category', 'user_token=' . $this->session->data['user_token']),
        ];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['delete'] = $this->url->link('extension/hair_test/module/category.delete', 'user_token=' . $this->session->data['user_token']);
        $data['add'] = $this->url->link('extension/hair_test/module/category.form', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->categoryList();
        $this->response->setOutput($this->load->view('extension/hair_test/module/category', $data));
    }

    protected function categoryList(): string
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

        $data['action'] = $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['categories'] = [];

        $filter_data = [
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin'),
        ];
        $this->load->model('extension/hair_test/module/category');
        $results = $this->model_extension_hair_test_module_category->getCategories($filter_data);
        foreach ($results as $result) {
            $data['categories'][] = [
                'category_id' => $result['category_id'],
                'name' => $result['name'],
                'status' => $result['status'],
                'sort_order' => $result['sort_order'],
                'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
                'edit' => $this->url->link('extension/hair_test/module/category.form', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url),
                'questions' => $this->url->link('extension/hair_test/module/questions', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url),
            ];
        }

        $url = '';

        if ($order === 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_name'] = $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
        $data['sort_sort_order'] = $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);
        $data['sort_date_added'] = $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);
        $data['sort_status'] = $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $categories_total = $this->model_extension_hair_test_module_category->getTotalCategories();
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $categories_total,
            'page' => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url' => $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}'),
        ]);

        $data['results'] = sprintf($this->language->get('text_pagination'), ($categories_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($categories_total - $this->config->get('config_pagination_admin'))) ? $categories_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $categories_total, ceil($categories_total / $this->config->get('config_pagination_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('extension/hair_test/module/category_list', $data);
    }

    /**
     * @return void
     */
    public function list(): void
    {
        $this->load->language('extension/hair_test/module/category');

        $this->response->setOutput($this->categoryList());
    }

    public function form()
    {
        $this->load->language('extension/hair_test/module/category');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['text_form'] = !isset($this->request->get['category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/hair_test/module/category', 'user_token=' . $this->session->data['user_token'] . $url),
        ];

        $data['save'] = $this->url->link('extension/hair_test/module/category.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('extension/hair_test/module/category', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['category_id'] = 0;
        $data['name'] = $data['status'] = NULL;
        $data['sort_order'] = 1;
        if (isset($this->request->get['category_id'])) {
            $data['category_id'] = $this->request->get['category_id'];
            $this->load->model('extension/hair_test/module/category');
            $category_info = $this->model_extension_hair_test_module_category->getCategory($this->request->get['category_id']);
            if ($category_info) {
                $data['name'] = $category_info['name'];
                $data['status'] = $category_info['status'];
                $data['sort_order'] = $category_info['sort_order'];
            }
        }
        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/hair_test/module/category_form', $data));
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->load->language('extension/hair_test/module/category');

        $json = [];
        if (!$this->user->hasPermission('modify', 'extension/hair_test/module/category')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }
        if ((oc_strlen(trim($this->request->post['name'])) < 1) || (oc_strlen($this->request->post['name']) > 255)) {
            $json['error']['name'] = $this->language->get('error_name');
        }
        if (!$this->request->post['sort_order']) {
            $json['error']['sort_order'] = $this->language->get('error_sort_order');
        }

        if (!$json) {
            $this->load->model('extension/hair_test/module/category');
            if (!$this->request->post['category_id']) {
                $json['category_id'] = $this->model_extension_hair_test_module_category->addCategory($this->request->post);
            } else {
                $this->model_extension_hair_test_module_category->editCategory($this->request->post['category_id'], $this->request->post);
            }

            $json['success'] = $this->language->get('text_success');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete(): void
    {
        $this->load->language('extension/hair_test/module/category');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } else {
            $selected = [];
        }

        if (!$this->user->hasPermission('modify', 'extension/hair_test/module/category')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('extension/hair_test/module/category');

            foreach ($selected as $category_id) {
                $this->model_extension_hair_test_module_category->deleteCategory($category_id);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}