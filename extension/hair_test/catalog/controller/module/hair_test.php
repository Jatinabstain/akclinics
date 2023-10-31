<?php


namespace Opencart\Catalog\Controller\Extension\HairTest\Module;


class HairTest extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        $this->load->language('extension/hair_test/module/hair_test');
        $this->load->model('extension/hair_test/module/hair_test');
        $categories = $this->model_extension_hair_test_module_hair_test->getCategories();

        $data['categories'] = [];
        if ($categories) {
            foreach ($categories as $category) {
                $data['categories'][] = [
                    'category_id' => $category['category_id'],
                    'name' => $category['name'],
                ];
            }
        }
        $this->document->setTitle($this->language->get('heading_title'));

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['question'] = $this->url->link('extension/hair_test/module/hair_test.question');
        $data['save'] = $this->url->link('extension/hair_test/module/hair_test.save');

        $this->response->setOutput($this->load->view('extension/hair_test/module/hair_test', $data));
    }

    public function question(): void
    {
        $data = [];
        $parent_id = (int) $this->request->post['parent_id'];
        $this->load->model('extension/hair_test/module/question');

        $data['question'] = $data['answers'] = [];
        $question = $this->model_extension_hair_test_module_question->getQuestion($parent_id);

        if ($question) {
            $question_data = $this->model_extension_hair_test_module_question->getQuestionById($question['parent_id']);
            $data['question'] = [
                'name' => $question['question'],
                'category_id' => $question['category_id'],
                'question_id' => $question['question_id'],
                'question_type' => $question['question_type'],
                'description' => $question['description'],
                'answer_id' => $question['answer_id'],
                'parent_id' => $question_data['parent_id'] ?? '',
                'value' => $this->session->data['hair_test_answers'][$question['question_id']]['value'] ?? '',
                'show_prev' => $question_data ? true : false
            ];
            $data['answers'] = $this->model_extension_hair_test_module_question->getAnswers($question['question_id']);
        }
        $this->load->model('tool/image');

        $data['input'] = ['text', 'number', 'email'];
        $data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));
        $data['config_file_max_size'] = ((int)$this->config->get('config_file_max_size') * 1024 * 1024);
        $data['upload'] = $this->url->link('tool/upload', 'language=' . $this->config->get('config_language'));
        $data['scalp_photo'] = $this->model_tool_image->resize('/catalog/questions/head.jpeg', 241, 209);
        $save_data = false;

        if (!$data['question'] && $this->session->data['hair_test_answers']) {
            $this->load->model('extension/hair_test/module/question');
            $this->model_extension_hair_test_module_question->saveAnswers();
            $save_data = true;
        }
        if ($save_data) {
            $this->response->setOutput($this->load->view('extension/hair_test/module/thanks', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/hair_test/module/question', $data));
        }

    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function save(): void
    {
        $json = [];
        $this->load->language('extension/hair_test/module/question');
        $this->load->model('extension/hair_test/module/question');
        $question = $this->request->post['question'] ?? [];
        $question_id = $this->request->post['question_id'] ?? 0;
        $question_data = $this->model_extension_hair_test_module_question->getQuestionById($question_id);
        if (!$question_id || !isset($question[$question_id]) || !$question_data) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $value = $question[$question_id];
            if (!$value) {
                $json['error']['warning'] = sprintf($this->language->get('error_empty'), $question_data['question']);
            }
        }
        if (!$json) {
            $this->session->data['hair_test_answers'][$question_id] = [
                'question_data' => $question_data,
                'value' => $value
            ];
            $json['next_question'] = $question_id;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_THROW_ON_ERROR));
    }
}