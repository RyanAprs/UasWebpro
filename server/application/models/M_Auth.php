<?php 
    class M_Auth extends CI_Model {

        function cek_login($email, $password) {
            $this->db->where('email', $email);
            $this->db->where('password', $password);
            $query = $this->db->get('user');
    
                if ($query->num_rows() == 1) {
                    return $query->row_array();
                } else {
                    return false;
                }
        }

            function check_data($id) {
                $this->db->where('id', $id);
                $query = $this->db->get('user');
    
                if($query->row()) {
                    return true;
                } else {
                    return false;
                }
            }

        
            function get_role($email) {
                $this->db->select('user.role_id');
                $this->db->from('user');
                $this->db->where('user.email', $email);
            
                $query = $this->db->get();
                return $query->result();
            }

            function insert($data) {
                $this->db->insert('user', $data);
                if($this->db->affected_rows()) {
                    return true;
                } else {
                    return false;
                }
            }
            
    }
?>