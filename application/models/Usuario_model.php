<?php

class Usuario_model extends CI_Model
{

    const table = 'usuario';
    const tableAPI = 'api_key';

    public function get($where = null)
    {
        $this->db->select('usuario.*');
        $this->db->where($where);
        $this->db->limit(1);
        return $this->db->get('usuario')->result();
    }
    public function getAll()
    {
        $this->db->select('usuario.*');
        return $this->db->get('usuario')->result();
    }

    public function insert($post)
    {
        $this->db->insert(self::table, $post);
        return $this->db->insert_id();
    }

    public function insertAPI($insert)
    {
        $this->db->insert(self::tableAPI, $insert);
        return $this->db->insert_id();
    }
}