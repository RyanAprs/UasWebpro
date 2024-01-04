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

    function getLaporanByDateRange($tanggal_mulai, $tanggal_selesai) {
        $this->db->select('transaksi.id, transaksi.nama as nama, mobil.nama_mobil as nama_mobil,transaksi.tgl_pinjam as tanggal_pinjam, transaksi.tgl_kembali as tanggal_kembali, DATEDIFF(transaksi.tgl_kembali, transaksi.tgl_pinjam) as lama_pinjam, mobil.harga_sewa * DATEDIFF(transaksi.tgl_kembali, transaksi.tgl_pinjam) as total_biaya');
        $this->db->from('transaksi');
        $this->db->join('mobil', 'transaksi.id_mobil = mobil.id');
    
        $this->db->where("transaksi.status_transaksi = 2 AND transaksi.tgl_pinjam >= '$tanggal_mulai' AND transaksi.tgl_kembali <= '$tanggal_selesai'");
    
        $query = $this->db->get();
        return $query->result_array();
    }
    

}

?>