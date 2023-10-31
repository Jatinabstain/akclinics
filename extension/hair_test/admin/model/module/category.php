<?php


namespace Opencart\Admin\Model\Extension\HairTest\module;


class Category extends \Opencart\System\Engine\Model
{
    public function getCategories(array $data = []): array
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "hair_test_category`";
        $sort_data = [
            'name',
            'status',
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

    public function getTotalCategories()
    {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "hair_test_category`");

        return (int)$query->row['total'];
    }

    /**
     * @param int $category_id
     *
     * @return array
     */
    public function getCategory(int $category_id): array
    {
        $query = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "hair_test_category` where `category_id` = '" . $category_id . "'");
        return $query->row;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function addCategory(array $data): int
    {
        $this->db->query("INSERT INTO  `" . DB_PREFIX . "hair_test_category` SET `name`= '" . $this->db->escape((string) $data['name']) . "', `sort_order`= '" . $this->db->escape((int) $data['sort_order']) . "', `status`= '" . $this->db->escape((int) $data['status']) . "', `date_modified` = NOW(), `date_added` = NOW()");
        return $this->db->getLastId();
    }

    public function editCategory(int $category_id, array $data): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "hair_test_category` SET `name`= '" . $this->db->escape((string) $data['name']) . "', `sort_order`= '" . $this->db->escape((int) $data['sort_order']) . "', `status`= '" . $this->db->escape((int) $data['status']) . "', `date_modified` = NOW() WHERE `category_id` = '" . (int)$category_id . "'");
    }

    public function deleteCategory(int $category_id): void
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "hair_test_category` WHERE `category_id` = '" . $category_id . "' ");
    }
}