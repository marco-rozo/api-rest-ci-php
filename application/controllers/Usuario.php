<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Usuario extends RestController {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->input_user = json_decode(trim(file_get_contents('php://input')));
        include('./system/helpers/string_helper.php');
        $this->load->model('Usuario_model');
    }

    public function index_get(){
        //echo "Hello word";
        //exit;
        $emailcadastrado = $this->Usuario_model->get(array('email'=>$email, 'senha'=>$senha));
        echo json_encode($emailcadastrado);
    }

    public function cadastro_post(){

        $email = strtolower(trim($this->input_user->email));
        $nome = ucwords(strtolower(trim($this->input_user->nome)));
        $senha = trim($this->input_user->senha);
        $cidade = strtolower(trim($this->input_user->cidade));
        //$telefone = trim($this->input_user->telefone);

         //verifica os campos obrigatorios
         if (!empty($email) /*&& !empty($senha) && !empty($nome) /*&& !empty($ruc)*/) {
             //verifica se o email ja esta cadastrado no banco de dados
            $emailcadastrado = $this->Usuario_model->get(array('email'=>$email));
             if (!$emailcadastrado) {
                 //criptografa a senha
                 $senhacrip = MD5($senha . $this->config->item('encryption_key'));
                 $data = array('nome' => $nome, 'senha' => $senhacrip, 'email' => $email,'cidade' => $cidade,/*'telefone' =>$telefone*/);

                 //realizaa a inserção no banco de dados
                 $id = $this->Usuario_model->insert($data);

                 //verifica se ocorreu a inserção no banco de dados
                 if ($id) {
                     //cria o token (key) e insere na tabela motoristajson_api_key
                     $api['token'] = md5(random_string('alnum', 6).date('YmdHis') . $this->config->item('encryption_key'));
                     $api['usuario_id'] = $id;
                     $api['date_created'] = date('Y-m-d');;
                     $this->Usuario_model->insertAPI($api);

                     $resposta = $this->Usuario_model->get(array('usuario.id'=>$id));

                     http_response_code(200);
                     $resposta[0]->error=false;
                     echo json_encode($resposta[0]);
                    echo json_encode(array('status' => true, 'error' => 'Sucesso ao cadastrar'));
                 } else {
                     http_response_code(400);
                     echo json_encode(array('error' => true, 'msg' => 'Erro ao realizar cadastro'));
                 }
             } else {
                 http_response_code(400);
                 echo json_encode(array('error' => true, 'msg' => 'Email já existente na base de dados'));
             }
         } else {
             http_response_code(400);
             echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preenchidos'));

         }


    }

    public function buscalogin_get()
    {
        $email = strtolower(trim($this->input_user->email));
        $senha = trim($this->input_user->senha);
        //verifica se os campos foram preenchidos
        if (!empty($email) && !empty($senha)) {
            //criptográfa a senha com a "encryption_key"
            $senhacrip = MD5($senha . $this->config->item('encryption_key'));
            //realiza a busca no banco de dados
            $retorno = $this->Usuario_model->get(array('email'=>$email, 'senha'=>$senhacrip));
            //verifica a existencia do login no banco de dados
            if ($retorno) {
                $retorno[0]->error=false;
                echo json_encode($retorno[0]);
            } else {
                http_response_code(406);
                echo json_encode(array('error' => true, 'msg' => 'Login Inválido'));
            }

        } else {
            http_response_code(400);
            echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preenchidos'));
        }

    }
}