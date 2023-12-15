<?php


namespace Opencart\Catalog\Model\Extension\HairTest\module;


class Question extends \Opencart\System\Engine\Model
{

    public function getQuestions(): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_question` WHERE `status`=1 order by category_id,sort_order");
        return $query->rows;
    }

    public function getQuestion(int $parent_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_question` WHERE `status`=1 and `parent_id`=".$parent_id.' order by sort_order');
        return $query->row;
    }

    public function getQuestionById(int $question_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_question` WHERE `status`=1 and `question_id`=".$question_id.' order by sort_order');
        return $query->row;
    }

    public function getAnswers(int $question_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_question_answer` WHERE `question_id`=".$question_id.' order by sort_order');
        return $query->rows;
    }

    public function saveAnswers(): void
    {
        $hair_test_answers = $this->session->data['hair_test_answers'] ?? [];
        $name = $phone_number = $email = '';
        if ($hair_test_answers) {
            foreach ($hair_test_answers as $hair_test_answer) {
                if ($hair_test_answer['question_data']['question'] === 'Your Name') {
                    $name = $hair_test_answer['value'];
                }
                if ($hair_test_answer['question_data']['question'] === 'Phone Number') {
                    $phone_number = $hair_test_answer['value'];
                }
                if ($hair_test_answer['question_data']['question'] === 'Email') {
                    $email = $hair_test_answer['value'];
                }
            }
            $ip_address = $this->request->server['REMOTE_ADDR'];
            $user_agent = $this->request->server['HTTP_USER_AGENT'];
            $customer_id = $this->customer->getId();
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hair_test_surveys` SET `customer_id`=".(int) $customer_id.", `name`='".$this->db->escape((string) $name)."', `phone_number`='".$this->db->escape((string) $phone_number)."', `email`='".$this->db->escape((string) $email)."', `ip_address`='".$this->db->escape($ip_address)."',  `user_agent`='".$this->db->escape($user_agent)."', `date_added`=now(), `date_modifiled`=now()");
            $survey_id = $this->db->getLastId();
            foreach ($hair_test_answers as $hair_test_answer) {
                if (is_array($hair_test_answer['value'])) {
                    $hair_test_answer['value'] = implode('<br/> ',$hair_test_answer['value']);
                }
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hair_test_survey_answers` SET `survey_id`=".(int) $survey_id.", `question_id`=".(int) $hair_test_answer['question_data']['question_id'].",  `question`='".$this->db->escape($hair_test_answer['question_data']['question'])."', `answer`='".$this->db->escape($hair_test_answer['value'])."', `answers`='".$this->db->escape(serialize($hair_test_answer['answers']))."'");
            }

        }
        $this->session->data['hair_test_answers'] = null;
    }
}