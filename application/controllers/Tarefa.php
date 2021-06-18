<?php

defined('BASEPATH') or exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

class Tarefa extends RestController{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->input_user = json_decode(trim(file_get_contents('php://input')));
        include('./system/helpers/string_helper.php');
        $this->load->model('Tarefa_model');
        $this->load->model('Usuario_model');
    }

    public function cadastrotarefa_post(){
        $token = $this->input->get_request_header('Token');

        $titulo = trim($this->input_user->titulo);
        $descricao = trim($this->input_user->descricao);
        $horario = $this->input_user->horario != null ? $this->input_user->horario : '';
        $data = $this->input_user->data != null ? $this->input_user->data : '';
        $lembrete = $this->input_user->lembrete != null ? $this->input_user->lembrete : 0;
        $importante = $this->input_user->importante != null ? $this->input_user->importante : 0;
        $status = $this->input_user->status != null ? $this->input_user->status : 0;
//        $categoria_id = $this->input_user->categoria;
        $categoria_id = 1;

        if (!empty($titulo) || !empty($descricao) || !empty($status)) {
            //retorna o id do usuario
            $user_id = $this->Usuario_model->get_token(array('token' => $token));

            if ($user_id){
                //adiciona todos os dados em um array para passar para o insert
                $data = array('data_registro' => date('Y-m-d H:i:s'), 'titulo' => $titulo, 'descricao' => $descricao, 'horario' => $horario,
                    'data' => $data, 'lembrete' => $lembrete, 'importante' => $importante, 'status' => $status, 'categoria' => $categoria_id,
                    'usuario_id' => intval($user_id[0]->user_id));

                //realiza a inserção no banco de dados
                $idTarefa = $this->Tarefa_model->insert($data);

                if ($idTarefa){
                    //busca a tarefa
                    $tarefa = $this->Tarefa_model->get(array('id' => $idTarefa));

                    http_response_code(200);
                    $tarefa[0]->error = false;
                    echo json_encode($tarefa[0]);
                    echo json_encode(array('status' => true, 'error' => 'Sucesso ao cadastrar'));
                }
            } else{
                http_response_code(400);
                echo json_encode(array('error' => true, 'msg' => 'Erro ao cadastrar tarefa, entre em contato com o suporte'));
            }

        } else{
            http_response_code(400);
            echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preenchidos'));
        }
    }
}