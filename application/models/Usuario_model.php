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

    public function get_all()
    {
        $this->db->select('usuario.*');
        return $this->db->get('usuario')->result();
    }

    public function desativar_usuario($data,$id) {
        $this->db->update(self::table, $data ,array('id'=>$id));
        return $this->db->affected_rows();
    }

//    public function update($where, $post) {
//        $this->db->where($where);
//        $this->db->update('alocacao', $post);
////        echo $this->db->last_query();
//        return $this->db->affected_rows();
//    }

    public function insert($post)
    {
        $this->db->insert(self::table, $post);
        return $this->db->insert_id();
    }

    public function insert_API($insert)
    {
        $this->db->insert(self::tableAPI, $insert);
        return $this->db->insert_id();
    }

    public function get_token($where)
    {
        $this->db->select(self::tableAPI.'.*');
        $this->db->where($where);
        $this->db->limit(1);
        return $this->db->get(self::tableAPI)->result();
    }
}