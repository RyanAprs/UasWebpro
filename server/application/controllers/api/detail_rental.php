<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require_once FCPATH . 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
class Detail_Rental extends REST_Controller
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
        $this->load->model('M_Detail_Rental');
        $this->load->library('form_validation');
        $this->load->library('jwt');
    }

    function validate(){
        $input_data = file_get_contents("php://input");
        parse_str($input_data, $put_data);

        $this->form_validation->set_data($put_data);
        
        $this->form_validation->set_rules('nama', 'Nama Pelanggan', 'required|trim');
        $this->form_validation->set_rules('alamat', 'Alamat Pelanggan','required|trim');
        $this->form_validation->set_rules('no_hp', 'Nomor Hp Pelanggan', 'required|trim');
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
            $data = $this->M_Detail_Rental->get_all();
        } else {
            $data = $this->M_Detail_Rental->get_by_id($id);
        }
        $this->response($data, 200);
    }
    
    public function index_post() {
        if (!$this->is_login()) {
            return;
        }

        $this->validate();
    
        if ($this->form_validation->run() === FALSE) {
            $error = $this->form_validation->error_array();
            $response = array(
                'status_code' => 502,
                'message' => $error
            );
            return $this->response($response);
    
        $data = [
            'id_pelanggan' => $this->req->post('id_pelanggan'),
            'id_mobil' => $this->req->post('id_mobil'),
            'harga' => $this->req->post('harga'),
            'tgl_pinjam' => $this->req->post('tgl_pinjam'),
            'tgl_kembali' => $this->req->post('tgl_kembali'),
        ];
    
        if ($this->pesanan->insert($data)) {
            $response = array(
                'status_code' => 201,
                'message' => 'success',
                'data' => $data,
            );

            return $this->response($response);
        } else {
            $error = array(
                'status_code' => 400,
                'message' => 'gagal menambahkan data',
            );

            return $this->response($error);
        }
    }
}
    
    public function index_put(){
        if (!$this->is_login()) {
            return;
        }

        $id = $this->put('id');
        $check = $this->M_Detail_Rental->check_data($id);

        if (!$check) {
            $error = array(
                'status' => 'fail',
                'field' => 'id',
                'message' => 'ID is not found',
                'status_code' => 502
            );

            return $this->response($error, 502);
        }

        $this->validate();
        if ($this->form_validation->run() === FALSE) {
            $error = $this->form_validation->error_array();
            $response = array(
                'status_code' => 502,
                'message' => $error
            );
            return $this->response($response);
        }

        $data = array(
            'nama' => $this->put('nama'),
            'alamat' => $this->put('alamat'),
            'no_hp' => $this->put('no_hp'),
        );

        $this->M_Detail_Rental->update($id, $data);
        $newData = $this->M_Detail_Rental->get_by_id($id);
        $response = array(
            'status' => 'success',
            'data' => $newData,
            'status_code' => 200
        );

        return $this->response($response, 200);
    }
    
    function index_delete() {
        if (!$this->is_login()) {
            return;
        }

        $id = $this->delete('id');
        $check = $this->M_Detail_Rental->check_data($id);
        if($check == false) {
            $error = array(
                'status' => 'fail',
                'field' => 'id',
                'message' => 'id is not found',
                'status_code' => 502
            );

            return $this->response($error);
        }
        $delete = $this->M_Detail_Rental->delete($id);
        $response = array(
            'status' => 'success',
            'data' => $delete,
            'status_code' => 200
        );
        return $this->response($response);
    }

}

?>