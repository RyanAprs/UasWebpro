<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require_once FCPATH . 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
class Laporan extends REST_Controller
{
    function __construct($config = 'rest'){
        parent::__construct($config);
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Headers:X-API-KEY,Origin,X-Requested-With,Content-Type,Accept,Access-Control-Request-Method,Authorization");
        header("Access-Control-Allow-Methods:GET,POST,OPTIONS,PUT,DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->database();
        $this->load->model('M_Laporan');
        $this->load->library('form_validation');
        $this->load->library('jwt');
    }

    public function options_get() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        exit();
    }

    function validate(){
        $input_data = file_get_contents("php://input");
        parse_str($input_data, $put_data);

        $this->form_validation->set_data($put_data);
        
        $this->form_validation->set_rules('nama_mobil', 'Nama Mobil', 'required|trim');
        $this->form_validation->set_rules('warna', 'Warna Mobil','required|trim');
        $this->form_validation->set_rules('no_polisi', 'Nomor Polisi', 'required|trim');
        $this->form_validation->set_rules('jumlah_kursi', 'Jumlah Kursi', 'required|trim|numeric');
        $this->form_validation->set_rules('harga_sewa', 'Harga Sewa', 'required|trim');
    }

    function is_login() {
        $authorizationHeader = $this->input->get_request_header('Authorization', true);

        if (empty($authorizationHeader) || $this->jwt->decode($authorizationHeader) === false) {
            $this->response(
                array(
                    'kode' => '401',
                    'pesan' => 'signature tidak sesuai',
                    'data' => []
                ), '401'
            );
            return false;
        }

        return true;
    }

    function index_get() {
        if (!$this->is_login()) {
            return;
        }

        $id = $this->get('id');
        if($id == '') {
            $data = $this->M_Laporan->get_all();
        } else {
            $data = $this->M_Laporan->get_by_id($id);
        }
        $this->response($data);
    }

    function index_post() {
        $tanggal_mulai = $this->input->post('tanggal_mulai');
        $tanggal_selesai = $this->input->post('tanggal_selesai');

        $data = $this->M_Laporan->getLaporanByDateRange($tanggal_mulai, $tanggal_selesai);

        if(!$data) {
            $response = array(
                'status_code' => 502,
                'message' => "Tidak ada transaksi pada range tanggal ini"
            );
            return $this->response($response);
        }

        $this->response($data);
    }
    
}

?>