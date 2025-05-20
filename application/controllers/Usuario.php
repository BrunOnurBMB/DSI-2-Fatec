<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {
    //Atributos privados da classe
    private $idUsuario;
    private $nome;
    private $email;
    private $usuario;
    private $senha;

    //Getters dos atributos
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getSenha() {
        return $this->senha;
    }

    //Setters dos atributos
    public function setIdUsuario($idUsuarioFront) {
        $this->idUsuario = $idUsuarioFront;
    }

    public function setNome($nomeFront) {
        $this->nome = $nomeFront;
    }

    public function setEmail($emailFront) {
        $this->email = $emailFront;
    }

    public function setUsuario($usuarioFront) {
        $this->usuario = $usuarioFront;
    }

    public function setSenha($senhaFront) {
        $this->senha = $senhaFront;
    }

    public function inserir() {
        /*
        Nome, Usuário e Senha recebidos via JSON e colocados em variáveis
        Retornos possíveis:
        1 - Usuário cadastrado corretamente (Banco)
        2 - Faltou informar o nome (FrontEnd)
        2 - Faltou informar o email (FrontEnd)
        2 - Faltou informar o usuário (FrontEnd)
        2 - Faltou informar a senha (FrontEnd)
        */

        try {
            //Usuário e senha recebidos via JSON e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "nome" => '0',
                "email" => '0',
                "usuario" => '0',
                "senha" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Realizando os setters
                $this->setNome($resultado->nome);
                $this->setEmail($resultado->email);
                $this->setUsuario($resultado->usuario);
                $this->setSenha($resultado->senha);

                //Realizando validação para sabermos se todos os dados foram enviados corretamente
                if (trim($this->getNome()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Nome não informado.');
                }elseif (trim($this->getEmail()) == '') {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'E-mail não informado.');
                }elseif (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $retorno = array(
                        'codigo' => 6,
                        'msg' => 'E-mail em formato inválido.');
                }elseif (trim($this->getUsuario()) == '') {
                    $retorno = array(
                        'codigo' => 4,
                        'msg' => 'Usuário não informado.');
                }elseif (trim($this->getSenha()) == '') {
                    $retorno = array(
                        'codigo' => 5,
                        'msg' => 'Senha não informada.');
                }else {
                    //Realizando a instância da Model
                    $this->load->model('M_usuario');

                    //Atributo $retorno recebe array com informações da validação feita acima
                    $retorno = $this->M_usuario->inserir($this->getNome(),
                                                         $this->getEmail(),
                                                         $this->getUsuario(),
                                                         $this->getSenha());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de inserir. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function consultar() {
        /*
        Nome, E-mail e Usuário recebidos via JSON e colocados em variáveis
        Retornos possíveis:
        1 - Dados consultados corretamente (Banco)
        6 - Dados não encontrados  (Banco)
        */

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "nome" => '0',
                "email" => '0',
                "usuario" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                
                //realizando os setter
                $this->setNome($resultado->nome);
                $this->setEmail($resultado->email);
                $this->setUsuario($resultado->usuario);

                //Realizando a instância da model
                $this->load->model('M_usuario');

                //Atributo $retorno recebe array com informações da consulta dos dados
                $retorno = $this->M_usuario->consultar($this->getNome(),
                                                       $this->getEmail(),
                                                       $this->getUsuario());
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o métododo de consultar. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }

        //Retornando no formato JSON
        echo json_encode($retorno);
    }

    public function alterar() {
        /*
        IdUsuario, Nome, Email, Senha recebidos via JSON se colocados em variáveis
        Retornos possíveis:
        1 - Dado(s) alterado(s) corretamente (Banco)
        2 - IdUsuario em Branco ou Zerado
        3 - Nenhum parâmetro de alteração informado.
        5 - Dados não enconstrados (Banco)
        */

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com dados que deverão vir do Front
            $lista = array(
                "idUsuario" => '0',
                "nome" => '0',
                "email" => '0',
                "senha" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Realizando os setters
                $this->setIdUsuario($resultado->idUsuario);
                $this->setNome($resultado->nome);
                $this->setEmail($resultado->email);
                $this->setSenha($resultado->senha);

                if (trim($this->getIdUsuario()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'ID do Usuário não informado.');
                //Nome, Senha ou Email, pelos menos um deles deve ser informado.
                }elseif (trim($this->getNome()) == '' &&
                         trim($this->getSenha()) == '' &&
                         trim($this->getEmail()) == '') {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'Pelo menos um parâmetro precisa ser passado para atualização.');
                //verificando se e-mail é válido
                } elseif (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $retorno = array(
                        'codigo' => 6,
                        'msg' => 'E-mail em formato inválido.');
                }else {
                    //Realizando a instância da Model
                    $this->load->model('M_usuario');

                    //atributo retorno recebe array com informações da alteração dos dados
                    $retorno = $this->M_usuario->alterar($this->getIdUsuario(),
                                                         $this->getNome(),
                                                         $this->getEmail(),
                                                         $this->getSenha());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }
        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar() {
        /*
        Usuário recebido via JSON e colocado em variável
        Retornos possíveis
        1 - Usuário desativado corretamente (Banco)
        2 - Usuário em Branco
        3 - Usuário inexistente na base de dados
        4 - Usuário já desativado na base da dados
        */

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //array com os dados que deverão vir do Front
            $lista = array(
                "idUsuario" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                //Realizando os setters
                $this->setIdUsuario($resultado->idUsuario);

                //Validando informações vindas do FRONT
                if (trim($this->getIdUsuario()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'ID do usuário não informado.');
                }else {
                    //Realizando a instância da Model
                    $this->load->model('M_usuario');

                    //Atributo $retorno recebe array com as informações
                    $retorno = $this->M_usuario->desativar($this->getIdUsuario());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método. Verifique'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage());
        }
        //Retornando ao formarto JSON
        echo json_encode($retorno);
    }

    public function logar() {
        /*
        Recebimento via JSON o usuaário e senha
        Retornos possiveis:
        1 - Usuário e senha validados corretamente (Banco)
        2 - Faltou informar o usuário (FrontEnd)
        3 - Faltou informar a senha (FrontEnd)
        4 - Usuário ou senha inválidos (Banco)
        5 - Usuário deletado - Status (Banco)
        99 -  Os campos vindos do FrontEnd não representam o método de login
        */

        try {
            //Usuário e senha recebidos via JSON e colocado em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "usuario" => '0', 
                "senha" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Realizando os setters
                $this->setUsuario($resultado->usuario);
                $this->setSenha($resultado->senha);

                if (trim($this->getUsuario()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Usuário não informado.');
                }elseif (trim($this->getSenha()) == '') {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'Senha não informada.');
                }else {

                    //Realizando a instância da Model
                    $this->load->model('M_usuario');

                    //Atributos $retorno recebe array com informações da validação de acesso tratada acima
                    $retorno = $this->M_usuario->validaLogin($this->getUsuario(),
                                                             $this->getSenha());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Oscampos vindos do FrontEnd não representam o método. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }
        //Retornando para o formato JSON
        echo json_encode($retorno);
    }
}
?>