<?php

class M_Laporan extends CI_Model {

    function get_all() {
        $this->db->select('transaksi.id, transaksi.nama, mobil.nama_mobil, mobil.harga_sewa, transaksi.tgl_pinjam, transaksi.tgl_kembali ');
        $this->db->from('transaksi');
        $this->db->join('mobil', 'transaksi.id_mobil = mobil.id');
        $this->db->where('transaksi.status_transaksi = 2');

        $query = $this->db->get();
        return $query->result_array();

    }   


}

?>