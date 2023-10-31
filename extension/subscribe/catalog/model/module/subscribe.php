<?php
namespace Opencart\Catalog\Model\Extension\Subscribe\module;
class Subscribe extends \Opencart\System\Engine\Model
{
    /**
     * @param string $email
     *
     * @return array
     */
    public function getUserByEmail(string $email): array
    {
        $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "subscribers` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "' and `status` =1");
        return $query->row;
    }

    public function addUser(string $email): void
    {
        $user_info = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "subscribers` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "' and `status` =0")->row;
        if ($user_info) {

            $this->db->query("UPDATE `" . DB_PREFIX . "subscribers` set status = 1, date_modified=now() WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
        } else {
            $ip = $this->request->server['REMOTE_ADDR'];
            $user_agent = $this->request->server['HTTP_USER_AGENT'];
            $this->db->query("INSERT INTO `" . DB_PREFIX . "subscribers` set status = 1, `email`= '" . $this->db->escape(oc_strtolower($email)) . "', `ip_address` = '" . $this->db->escape($ip) . "', `user_agent`='" . $this->db->escape($user_agent) . "', `date_added`=now(), `date_modified`=now()");
        }
    }

    public function addCallback(array $data): void
    {
        $ip = $this->request->server['REMOTE_ADDR'];
        $user_agent = $this->request->server['HTTP_USER_AGENT'];
        $this->db->query("INSERT INTO `" . DB_PREFIX . "callback_contact` set `email`='" . $this->db->escape(oc_strtolower($data['call_email'])) . "', `name`='" . $this->db->escape($data['name']) . "', `age`='" . $this->db->escape($data['age']) . "', `phone`='" . $this->db->escape($data['phone']) . "', `ip_address` = '" . $this->db->escape($ip) . "', `user_agent`='" . $this->db->escape($user_agent) . "', `date_added`=now(), `date_modified`=now()");
    }
}
