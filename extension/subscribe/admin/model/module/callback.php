<?php


namespace Opencart\Admin\Model\Extension\Subscribe\module;


class Callback extends \Opencart\System\Engine\Model
{
    /**
     * @param array $data
     *
     * @return array
     */

    public function getResults(array $data = []): array
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "callback_contact`";
        $sort_data = [
            'name',
            'age',
            'email',
            'phone',
            'date_added',
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

    /**
     * @return int
     */
    public function getTotalCallbacks(): int
    {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "callback_contact`");

        return (int)$query->row['total'];
    }

    public function deleteEntry(int $id): void
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "callback_contact` WHERE `id` = '" . (int) $id . "'");
    }
}