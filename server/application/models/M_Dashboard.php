<?php
class M_Dashboard extends CI_Model {
    function getCountUser()
    {
        try {
            return $this->db->count_all('user');
        } catch (Exception $e) {
            return 0; 
        }
    }

    
    function getCountMobil()
    {
        try {
            return $this->db->count_all('mobil');
        } catch (Exception $e) {
            return 0; 
        }
    }

    function getCountTransaksi()
    {
        try {
            $this->db->where('status_transaksi', 1);
            $transaksi = $this->db->get('transaksi');

            return $transaksi->num_rows();
        } catch (Exception $e) {
            return 0; 
        }
    }

    function getCountLaporan()
    {
        try {
            $this->db->where('status_transaksi', 2);
            $transaksi = $this->db->get('transaksi');

            return $transaksi->num_rows();
        } catch (Exception $e) {
            return 0; 
        }
    }

}
?>
