<?php


class Tarefa_model extends CI_Model
{

    const table = 'tarefas';

    public function get($where = null){
        $this->db->select(self::table.'.*');
        $this->db->where($where);
        $this->db->limit(1);
        return $this->db->get(self::table)->result();
    }

    public function insert($post){
        $this->db->insert(self::table, $post);
        return $this->db->insert_id();
    }

    public function update($data, $id) {
        $this->db->update(self::table, $data, array('id'=>$id));
        return $this->db->affected_rows();
    }

    public function delete($id) {
        $this->db->delete(self::table, array('id'=>$id));
        return $this->db->affected_rows();
    }
}
