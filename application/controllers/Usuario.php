<?php
defined('BASEPATH') or exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

class Usuario extends RestController{

    function __construct(){
        // Construct the parent class
        parent::__construct();
        $this->input_user = json_decode(trim(file_get_contents('php://input')));
        include('./system/helpers/string_helper.php');
        $this->load->model('Usuario_model');
    }

    public function desativarusuario_put(){
        //pega token do header
        $token = $this->input->get_request_header('Token');

        $senha = trim($this->input_user->senha);
        //verifica se os campos foram preenchidos
        if (!empty($token) || !empty($senha)) {
            $user = $this->Usuario_model->get_token(array('token' => $token));

            $update = $this->Usuario_model->desativar_usuario(array('ativo' => 0), $user[0]->user_id);
            if($update > 0){
                http_response_code(200);
            } else{
                http_response_code(400);
                echo json_encode(array('error' => true, 'msg' => 'Não foi possivel desativar usuario, entre em contato com o suporte'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preenchidos'));
        }
    }
}
