<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Usuario extends RestController
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->input_user = json_decode(trim(file_get_contents('php://input')));
        include('./system/helpers/string_helper.php');
        $this->load->model('Usuario_model');
    }

    public function cadastro(){
        $email = strtolower(trim($this->input_user->email));
        $nome = ucwords(strtolower(trim($this->input_user->nome)));
        $senha = trim($this->input_user->senha);

        //verifica os campos obrigatorios
        if (!empty($email) && !empty($senha) && !empty($nome) ) {
            //verifica se o email ja esta cadastrado no banco de dados
            $emailcadastrado = $this->Usuario_model->get(array('email' => $email));
            if (!$emailcadastrado) {
                //criptografa a senha
                $senhacrip = MD5($senha . $this->config->item('encryption_key'));
                $data = array('nome' => $nome, 'senha' => $senhacrip, 'email' => $email, 'data_registro' => date('Y-m-d'),/*'telefone' =>$telefone*/);

                //realiza a inserção no banco de dados
                $id = $this->Usuario_model->insert($data);

                //verifica se ocorreu a inserção no banco de dados
                if ($id) {
                    //cria o token (key) e insere na tabela motoristajson_api_key
                    $api['token'] = md5(random_string('alnum', 6) . date('YmdHis') . $this->config->item('encryption_key'));
                    $api['user_id'] = $id;
                    $api['date_created'] = date('Y-m-d');
                    $this->Usuario_model->insertAPI($api);

                    $resposta = $this->Usuario_model->get(array('usuario.id' => $id));

                    http_response_code(200);
                    $resposta[0]->error = false;
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

    public function buscalogin_get(){
        $email = strtolower(trim($this->input_user->email));
//        $telefone = strtolower(trim($this->input_user->telefone));
        $senha = trim($this->input_user->senha);
        //verifica se os campos foram preenchidos
        if (!empty($telefone) || !empty($email)) {
            if (!empty($senha)) {
                //criptográfa a senha com a "encryption_key"
                $senhacrip = MD5($senha . $this->config->item('encryption_key'));
                //realiza a busca no banco de dados
                $retorno = $this->Usuario_model->get(array('email' => $email, 'senha' => $senhacrip));
                //verifica a existencia do login no banco de dados
                if ($retorno) {
                    http_response_code(200);
                    $retorno[0]->error = false;
                    echo json_encode($retorno[0]);
                } else {
                    http_response_code(406);
                    echo json_encode(array('error' => true, 'msg' => 'Login Inválido'));
                }

            } else {
                http_response_code(400);
                echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preenchidos'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preenchidos'));
        }
    }

    public function allusers_get(){
        $retorno = $this->Usuario_model->getAll();
        if ($retorno){
            http_response_code(200);
            $retorno[0]->error = false;
            echo json_encode($retorno);
        } else{
            http_response_code(406);
            echo json_encode(array('error' => true, 'msg' => 'Algo Deu Errado'));
        }
    }

    public function recuperarsenha_post()
    {
        $email = strtolower(trim($this->input_user->email));
        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $retorno = $this->Login_model->get(array('email' => $email, ));
                if ($retorno) {
                    $novasenha = random_string('alnum', 6);
                    $senhacrip = md5($novasenha . $this->config->item('encryption_key'));

                    $this->load->library('email'); // Note: no $config param needed
                    $this->email->from('no-reply-cde@testemarcorozo.com.py','TESTE MARCO DEV');
                    $this->email->to(/*$email*/'tmarco2702@gmail.com');
                    $this->email->subject('Apenas um teste marco');
                    $this->email->message('lalalalalalalalalalalalalalalalala');
                    if ($this->email->send()) {
                        //atualiza a senha do motorista
                        $this->Login_model->update(array('senha'=>$senhacrip), array('email' => $email));
                        $this->User_model->updateToken($retorno[0]->id,array('key'=>md5(random_string('alnum', 6).date('YmdHis') . $this->config->item('encryption_key'))));
                        http_response_code(200);
                        echo json_encode(array('error' => false, 'msg' => $this->lang->line('login_email_envio')));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array('error' => true, 'msg' => 'Email não encontrado na base de dados'));
                }
            } else {
                http_response_code(400);
                echo json_encode(array('error' => true, 'msg' => 'Email inválido'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => true, 'msg' => 'Dados necessários não preencidos'));

        }
    }

    public function eviodeemail_post(){
        $email = strtolower(trim($this->input_user->email));

        $this->load->library('email'); // Note: no $config param needed
        $this->email->from('no-reply-cde@testemarcorozo.com.py', 'TESTE MARCO DEV');
        $this->email->to(/*$email*/ 'tmarco2702@gmail.com');
        $this->email->subject('Apenas um teste marco');
        $this->email->message('lalalalalalalalalalalalalalalalala');
        if ($this->email->send()) {
            http_response_code(200);
            echo json_encode(array('error' => false, 'msg' => 'Email enviado'));
        }
    }
}