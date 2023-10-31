<?php


namespace Opencart\Admin\Model\Extension\HairTest\module;


class questions extends \Opencart\System\Engine\Model
{
    public function getQuestions(array $data = []): array
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "hair_test_question` where `category_id`='".(int) $data['category_id']."'";
        $sort_data = [
            'status',
            'question',
            'question_type',
            'sort_order',
            'date_added',
        ];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data, true)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY `sort_order`";
        }

        if (isset($data['order']) && ($data['order'] === 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalQuestions(int $category_id): int
    {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "hair_test_question` where `category_id`='".$category_id."'");
        return (int)$query->row['total'];
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function addQuestion(array $data): int
    {
        $this->db->query("INSERT INTO  `" . DB_PREFIX . "hair_test_question` SET `question`= '" . $this->db->escape((string) $data['question']) . "', `question_type`= '" . $this->db->escape((string) $data['question_type']) . "', `category_id`= '" .  (int) $data['category_id'] . "', `sort_order`= '" . $this->db->escape((int) $data['sort_order']) . "', `status`= '" . $this->db->escape((int) $data['status']) . "', `date_modified` = NOW(), `date_added` = NOW(), `parent_id`= '" .  (int) $data['parent_id'] . "', `answer_id`= '" .  (int) $data['answer_id'] . "', `description`= '" . $this->db->escape((string) $data['description']) . "'");
        $question_id =  $this->db->getLastId();
        $answers = $data['answers'] ?? [];
        if ($answers) {
            foreach ($answers as $answer) {
                $this->db->query("INSERT INTO  `" . DB_PREFIX . "hair_test_question_answer` SET `question_id`= '" .  (int) $question_id . "', `name`= '" . $this->db->escape((string) $answer['name']) . "', `image`= '" . $this->db->escape((string) $answer['image']) . "', `sort_order`= '" . (int) $answer['sort_order'] . "'");
            }
        }
        return $question_id;
    }

    /**
     * @param int $question_id
     * @param array $data
     *
     * @return int
     */
    public function editQuestion(int $question_id, array $data): int
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "hair_test_question` SET `question`= '" . $this->db->escape((string) $data['question']) . "', `question_type`= '" . $this->db->escape((string) $data['question_type']) . "', `category_id`= '" .  (int) $data['category_id'] . "', `sort_order`= '" . $this->db->escape((int) $data['sort_order']) . "', `status`= '" . $this->db->escape((int) $data['status']) . "', `date_modified` = NOW(), `parent_id`= '" .  (int) $data['parent_id'] . "', `answer_id`= '" .  (int) $data['answer_id'] . "', `description`= '" . $this->db->escape((string) $data['description']) . "' where `question_id`=".$question_id);

        $this->db->query("DELETE FROM `" . DB_PREFIX . "hair_test_question_answer` where `question_id`=".$question_id);
        $answers = $data['answers'] ?? [];
        if ($answers) {
            foreach ($answers as $answer) {
                $this->db->query("INSERT INTO  `" . DB_PREFIX . "hair_test_question_answer` SET `question_id`= '" .  $question_id . "', `name`= '" . $this->db->escape((string) $answer['name']) . "', `image`= '" . $this->db->escape((string) $answer['image']) . "', `sort_order`= '" . (int) $answer['sort_order'] . "'");
            }
        }
        return $this->db->getLastId();
    }


    public function getQuestion(int $question_id)
    {
        $query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "hair_test_question` where `question_id` = '" . $question_id . "'");
        return $query->row;
    }

    public function deleteQuestion(int $question_id): void
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "hair_test_question` WHERE `question_id` = '" . $question_id . "' ");
    }

    public function getAnswers(int $question_id)
    {
        $query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "hair_test_question_answer` where `question_id` = '" . $question_id . "'");
        return $query->rows;
    }

    public function getCategoryQuestions(int $category_id=null) {
        if ($category_id) {
            $query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "hair_test_question` where `category_id` = '" . $category_id . "'");
        } else {
            $query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "hair_test_question`");
        }

        return $query->rows;
    }

    public function getAnswer(int $answer_id)
    {
        $query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "hair_test_question_answer` where `answer_id` = '" . $answer_id . "'");
        return $query->row;
    }

}