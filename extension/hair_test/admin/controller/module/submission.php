<?php


namespace Opencart\Admin\Controller\Extension\HairTest\Module;


class Submission extends \Opencart\System\Engine\Controller
{

    public function index(): void
    {
        $this->load->language('extension/hair_test/module/submission');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/hair_test/module/submission', 'user_token=' . $this->session->data['user_token']),
        ];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['delete'] = $this->url->link('extension/hair_test/module/submission.delete', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->submissionList();
        $this->response->setOutput($this->load->view('extension/hair_test/module/submission', $data));
    }

    protected function submissionList(): string
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

        $data['action'] = $this->url->link('extension/hair_test/module/submission.list', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['submissions'] = [];
        $filter_data = [
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin'),
        ];
        $this->load->model('extension/hair_test/module/submission');

        $results = $this->model_extension_hair_test_module_submission->getSubmissions($filter_data);
        foreach ($results as $result) {
            $data['submissions'][] = [
                'submission_id' => $result['survey_id'],
                'name' => $result['name'],
                'phone_number' => $result['phone_number'],
                'email' => $result['email'],
                'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
                'view' => $this->url->link('extension/hair_test/module/submission.view', 'user_token=' . $this->session->data['user_token'] . '&submission_id=' . $result['survey_id'] . $url),
            ];
        }

        $url = '';

        if ($order === 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_name'] = $this->url->link('extension/hair_test/module/submission.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
        $data['sort_phone_number'] = $this->url->link('extension/hair_test/module/submission.list', 'user_token=' . $this->session->data['user_token'] . '&sort=phone_number' . $url);
        $data['sort_email'] = $this->url->link('extension/hair_test/module/submission.list', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url);
        $data['sort_date_added'] = $this->url->link('extension/hair_test/module/submission.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        $submission_total = $this->model_extension_hair_test_module_submission->getTotalSubmissions();
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $submission_total,
            'page' => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url' => $this->url->link('extension/hair_test/module/category.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}'),
        ]);
        $data['results'] = sprintf($this->language->get('text_pagination'), ($submission_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($submission_total - $this->config->get('config_pagination_admin'))) ? $submission_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $submission_total, ceil($submission_total / $this->config->get('config_pagination_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;

        return $this->load->view('extension/hair_test/module/submission_list', $data);
    }
    /**
     * @return void
     */
    public function list(): void
    {
        $this->load->language('extension/hair_test/module/submission');

        $this->response->setOutput($this->submissionList());
    }

    public function delete(): void
    {
        $this->load->language('extension/hair_test/module/submission');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } else {
            $selected = [];
        }

        if (!$this->user->hasPermission('modify', 'extension/hair_test/module/submission')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('extension/hair_test/module/submission');

            foreach ($selected as $submission_id) {
                $this->model_extension_hair_test_module_submission->deleteSubmission($submission_id);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function view(): void
    {
        $data = [];
        $submission_id = $this->request->get['submission_id'] ?? 0;
        $this->load->language('extension/hair_test/module/submission');
        $this->load->model('extension/hair_test/module/submission');
        $this->document->setTitle($this->language->get('heading_view'));
        $submission = $this->model_extension_hair_test_module_submission->getSubmission($submission_id);
        if ($submission) {
            $data['breadcrumbs'] = [];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
            ];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/hair_test/module/submission', 'user_token=' . $this->session->data['user_token']),
            ];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_view'),
                'href' => $this->url->link('extension/hair_test/module/submission.view', 'user_token=' . $this->session->data['user_token'].'&submission_id='.$submission_id),
            ];
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['back'] = $this->url->link('extension/hair_test/module/submission', 'user_token=' . $this->session->data['user_token']);
            $data['answers'] = [];
            $answers = $this->model_extension_hair_test_module_submission->getAnswers($submission_id);
            if ($answers) {
                foreach ($answers as $answer) {
                    $answer['answers'] = unserialize($answer['answers']);
                    $answer['download'] = '';
                    if ($answer['question'] === 'Upload Your Scalp Photo') {
                        $answer['download'] = $this->url->link('tool/upload.download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $answer['answer']);
                    }

                    $data['answers'][] = $answer;
                }
            }
            $this->response->setOutput($this->load->view('extension/hair_test/module/submission_view', $data));
        }
    }
}