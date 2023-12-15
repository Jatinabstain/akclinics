<?php


namespace Opencart\Admin\Controller\Extension\HairTest\Module;


class Questions extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        $category_info = $this->checkCategory();
        if ($category_info) {
            $category_id = $category_info['category_id'];
            $data['category_info'] = $category_info;
            $this->load->language('extension/hair_test/module/questions');
            $this->document->setTitle($this->language->get('heading_title'));

            $data['breadcrumbs'] = [];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
            ];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_category'),
                'href' => $this->url->link('extension/hair_test/module/category', 'user_token=' . $this->session->data['user_token']),
            ];
            $data['breadcrumbs'][] = [
                'text' => $category_info['name'],
                'href' => $this->url->link('extension/hair_test/module/category.form', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id),
            ];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/hair_test/module/questions', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id),
            ];

            $data['delete'] = $this->url->link('extension/hair_test/module/questions.delete', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id);
            $data['add'] = $this->url->link('extension/hair_test/module/questions.form', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id);
            $data['back'] = $this->url->link('extension/hair_test/module/category', 'user_token=' . $this->session->data['user_token']);

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['list'] = $this->questionsList($category_id);
            $this->response->setOutput($this->load->view('extension/hair_test/module/questions', $data));
        }
    }

    protected function checkCategory()
    {
        $category_id = $this->request->get['category_id'] ?? 0;
        $this->load->model('extension/hair_test/module/category');
        $category_info = $this->model_extension_hair_test_module_category->getCategory($category_id);
        if ($category_info) {
            return $category_info;
        }
        return [];
    }

    protected function questionsList(int $category_id): string
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

        $data['action'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . $url . '&category_id=' . $category_id);
        $data['questions'] = [];

        $filter_data = [
            'sort' => $sort,
            'order' => $order,
            'category_id' => $category_id,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin'),
        ];
        $this->load->model('extension/hair_test/module/questions');
        $results = $this->model_extension_hair_test_module_questions->getQuestions($filter_data);
        foreach ($results as $result) {
            $question_data = $this->model_extension_hair_test_module_questions->getQuestion($result['parent_id'] ?? 0);
            $answer_data = $this->model_extension_hair_test_module_questions->getAnswer($result['answer_id'] ?? 0);
            $data['questions'][] = [
                'status' => $result['status'],
                'parent_id' => $question_data['question'] ?? '',
                'answer_id' => $answer_data['name'] ?? '',
                'question' => $result['question'],
                'sort_order' => $result['sort_order'],
                'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
                'question_id' => $result['question_id'],
                'question_type' => $result['question_type'],
                'edit' => $this->url->link('extension/hair_test/module/questions.form', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id . $url . '&question_id=' . $result['question_id']),
            ];
        }
        $url = '';

        if ($order === 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        $data['sort_status'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url . '&category_id=' . $category_id);
        $data['sort_question'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . '&sort=question' . $url . '&category_id=' . $category_id);
        $data['sort_sort_order'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url . '&category_id=' . $category_id);
        $data['sort_date_added'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url . '&category_id=' . $category_id);
        $data['sort_question_id'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . '&sort=question_id' . $url . '&category_id=' . $category_id);
        $data['sort_question_type'] = $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . '&sort=question_type' . $url . '&category_id=' . $category_id);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $questions_total = $this->model_extension_hair_test_module_questions->getTotalQuestions($category_id);
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $questions_total,
            'page' => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url' => $this->url->link('extension/hair_test/module/questions.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}&category_id=' . $category_id),
        ]);

        $data['results'] = sprintf($this->language->get('text_pagination'), ($questions_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($questions_total - $this->config->get('config_pagination_admin'))) ? $questions_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $questions_total, ceil($questions_total / $this->config->get('config_pagination_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('extension/hair_test/module/questions_list', $data);
    }

    /**
     * @return void
     */
    public function list(): void
    {
        $category_info = $this->checkCategory();
        if ($category_info) {
            $this->load->language('extension/hair_test/module/questions');

            $this->response->setOutput($this->questionsList($category_info['category_id']));
        }
    }

    public function form()
    {
        $category_info = $this->checkCategory();
        if ($category_info) {
            $category_id = $category_info['category_id'];
            $this->load->language('extension/hair_test/module/questions');
            $this->document->setTitle($this->language->get('heading_title'));

            $data['text_form'] = !isset($this->request->get['question_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
                'text' => $this->language->get('heading_category'),
                'href' => $this->url->link('extension/hair_test/module/category', 'user_token=' . $this->session->data['user_token']),
            ];
            $data['breadcrumbs'][] = [
                'text' => $category_info['name'],
                'href' => $this->url->link('extension/hair_test/module/category.form', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id),
            ];
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/hair_test/module/questions', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id),
            ];

            $data['save'] = $this->url->link('extension/hair_test/module/questions.save', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $category_id);
            $data['back'] = $this->url->link('extension/hair_test/module/questions', 'user_token=' . $this->session->data['user_token'] . $url . '&category_id=' . $category_id);

            $data['user_token'] = $this->session->data['user_token'];
            $data['category_id'] = $category_id;

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['placeholder'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

            $data['question'] = $data['question_type'] = $data['parent_id'] = $data['status'] = $data['question_id'] = $data['answer_id'] = $data['description'] = '';
            $data['sort_order'] = 1;
            $data['answers'] = [];
            $this->load->model('extension/hair_test/module/questions');
            if (isset($this->request->get['question_id']) && $this->request->get['question_id']) {

                $result = $this->model_extension_hair_test_module_questions->getQuestion($this->request->get['question_id']);
                if ($result) {
                    $data['question'] = $result['question'];
                    $data['question_type'] = $result['question_type'];
                    $data['parent_id'] = $result['parent_id'];
                    $data['status'] = $result['status'];
                    $data['sort_order'] = $result['sort_order'];
                    $data['question_id'] = $result['question_id'];
                    $data['answer_id'] = $result['answer_id'];
                    $data['description'] = $result['description'];
                }
            }
            if (isset($this->request->get['question_id'])) {
                $question_answers = $this->model_extension_hair_test_module_questions->getAnswers($this->request->get['question_id']);
                if ($question_answers) {
                    foreach ($question_answers as $answer) {
                        $image = '';
                        $thumb = 'no_image.png';
                        if (is_file(DIR_IMAGE . html_entity_decode($answer['image'], ENT_QUOTES, 'UTF-8'))) {
                            $image = $answer['image'];
                            $thumb = $answer['image'];
                        }
                        $data['answers'][] = [
                            'answer_id' => $answer['answer_id'],
                            'name' => $answer['name'],
                            'image' => $image,
                            'thumb' => $this->model_tool_image->resize(html_entity_decode($thumb, ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height')),
                            'sort_order' => $answer['sort_order'],
                        ];
                    }
                }
            }
            $data['questions'] = [];
            $questions = $this->model_extension_hair_test_module_questions->getCategoryQuestions();
            if ($questions) {
                foreach ($questions as $question) {
                    $data['questions'][] = [
                        'question_id' => $question['question_id'],
                        'question' => $question['question'],
                    ];
                }
            }
            $this->response->setOutput($this->load->view('extension/hair_test/module/question_form', $data));
        }
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $this->load->language('extension/hair_test/module/questions');
        $json = [];
        $category_info = $this->checkCategory();
        if ($category_info) {
            if (!$this->user->hasPermission('modify', 'extension/hair_test/module/questions')) {
                $json['error']['warning'] = $this->language->get('error_permission');
            }

            if ((oc_strlen(trim($this->request->post['question'])) < 3) || (oc_strlen($this->request->post['question']) > 255)) {
                $json['error']['question'] = $this->language->get('error_question');
            }
            if (!$this->request->post['sort_order']) {
                $json['error']['sort_order'] = $this->language->get('error_sort_order');
            }
            if (!$this->request->post['question_type']) {
                $json['error']['question_type'] = $this->language->get('error_question_type');
            }
            $answers = $this->request->post['answers'] ?? [];
            if ($answers && in_array($this->request->post['question_type'], ['select', 'radio', 'checkbox'])) {
                foreach ($answers as $id => $answer) {
                    if ((oc_strlen(trim($answer['name'])) < 1) || (oc_strlen($answer['name']) > 128)) {
                        $json['error']['answers_' . $id] = $this->language->get('error_option_value');
                    }
                }
            }
            if (!$json) {
                $this->request->post['category_id'] = $category_info['category_id'];
                $this->load->model('extension/hair_test/module/questions');
                if (!$this->request->post['question_id']) {
                    $json['question_id'] = $this->model_extension_hair_test_module_questions->addQuestion($this->request->post);
                } else {
                    $this->model_extension_hair_test_module_questions->editQuestion($this->request->post['question_id'], $this->request->post);
                }
                $json['success'] = $this->language->get('text_success');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete(): void
    {
        $this->load->language('extension/hair_test/module/questions');
        $json = [];
        $category_info = $this->checkCategory();
        if ($category_info) {
            if (isset($this->request->post['selected'])) {
                $selected = $this->request->post['selected'];
            } else {
                $selected = [];
            }

            if (!$this->user->hasPermission('modify', 'extension/hair_test/module/questions')) {
                $json['error'] = $this->language->get('error_permission');
            }

            if (!$json) {
                $this->load->model('extension/hair_test/module/questions');

                foreach ($selected as $question_id) {
                    $this->model_extension_hair_test_module_questions->deleteQuestion($question_id);
                }
                $json['success'] = $this->language->get('text_success');
            }
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function answers()
    {
        $this->load->model('extension/hair_test/module/questions');
        $html = '<option>--Please Select--</option>';
        $json['html'] = $html;
        $category_info = $this->checkCategory();

        if ($category_info && isset($this->request->post['question_id'])) {
            $answers = $this->model_extension_hair_test_module_questions->getAnswers($this->request->post['question_id']);
            if ($answers) {
                $selected = $this->request->post['selected'];
                foreach ($answers as $answer) {
                    if ($selected === $answer['answer_id']) {
                        $html .= '<option selected value="' . $answer['answer_id'] . '">' . $answer['name'] . '</option>';
                    } else {
                        $html .= '<option value="' . $answer['answer_id'] . '">' . $answer['name'] . '</option>';
                    }

                }
                $json['html'] = $html;
            }

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}