<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require_once FCPATH . 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
class Transaksi extends REST_Controller
{
    function __construct($config = 'rest'){
        parent::__construct($config);
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, ");
            exit;
        }
        $this->load->database();
        $this->load->model('M_Transaksi');
        $this->load->library('form_validation');
        $this->load->library('jwt');
    }

    function validate(){
        $input_data = file_get_contents("php://input");
        parse_str($input_data, $put_data);

        $this->form_validation->set_data($put_data);
        
        $this->form_validation->set_rules('harga', 'Harga Mobil', 'required|numeric');
        $this->form_validation->set_rules('tgl_pinjam', 'Tanggal Pinjam','required');
        $this->form_validation->set_rules('tgl_kembali', 'Tanggal Kembali', 'required');
    }

    public function options_get() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        exit();
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
            $data = $this->M_Transaksi->get_all();
        } else {
            $data = $this->M_Transaksi->get_by_id($id);
        }
        $this->response($data, 200);
    }
    
    function index_delete() {
        if (!$this->is_login()) {
            return;
        }
    
        $id = $this->delete('id');
        $check = $this->M_Transaksi->check_data($id);
    
        if ($check == false) {
            $error = array(
                'status' => 'fail',
                'field' => 'id',
                'message' => 'id is not found',
                'status_code' => 502
            );
    
            return $this->response($error);
        }
    
        $delete = $this->M_Transaksi->customerMengembalikanMobil($id);
    
        if ($delete) {
            $response = array(
                'status' => 'success',
                'data' => $delete,
                'status_code' => 200
            );
        } else {
            $response = array(
                'status' => 'fail',
                'message' => 'Failed to delete the record',
                'status_code' => 502
            );
        }
    
        return $this->response($response);
    }

}

?>