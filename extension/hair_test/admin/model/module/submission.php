<?php


namespace Opencart\Admin\Model\Extension\HairTest\module;


class Submission extends \Opencart\System\Engine\Model
{
    public function getSubmissions(): array
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "hair_test_surveys`";
        $sort_data = [
            'name',
            'phone_number',
            'email',
            'date_added',
        ];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data, true)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY `date_added`";
        }

        if (isset($data['order']) && ($data['order'] === 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
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

    public function getTotalSubmissions(): int
    {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "hair_test_surveys`");

        return (int)$query->row['total'];
    }

    public function deleteSubmission(int $submission_id): void
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "hair_test_surveys` WHERE `survey_id` = '" . $submission_id . "' ");
    }

    public function getSubmission(int $submission_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_surveys` WHERE `survey_id` = '" . $submission_id . "' ");
        return $query->row;
    }

    public function getAnswers(int $submission_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_survey_answers` WHERE `survey_id` = '" . $submission_id . "' ");
        return $query->rows;
    }

}