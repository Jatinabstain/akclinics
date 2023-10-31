<?php


namespace Opencart\Catalog\Model\Extension\HairTest\module;


class HairTest extends \Opencart\System\Engine\Model
{

    public function getCategories(): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hair_test_category` where `status` = 1 order by sort_order ");

        return $query->rows;
    }
}