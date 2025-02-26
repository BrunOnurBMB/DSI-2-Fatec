<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

class Sala extends CI_Controller{

    //Atributos privados da classe
    private $codigo;
    private $descricao;
    private $andar;
    private $capacidade;
    private $estatus;

    // Getters dis atributos
    public function getCodigo(){
        return $this->codigo;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getAndar(){
        return $this->andar;
    }

    public function getCapacidade(){
        return $this->capacidade;
    }

    public function getEstatus(){
        return $this->estatus;
    }

    // Setters dos atributos
    public function setCodigo(){
        $this->codigo = $codigoFront;
    }

    public function setDescricao(){
        $this->descricao = $descricaoFront;
    }

    public function setAndar(){
        $this->andar = $andarFront;
    }

    public function setCapacidade(){
        $this->capacidade = $capacidadeFront;
    }

    public function setEstatus(){
        $this->estatus = $estatusFront;
    }

    public function inserir(){
        /* codigo, descrição e andar
           Recebidos via JSON e colocados em variáveis
           Retornos possíveis:
            1 - Sala cadastrada corretamente (Banco)
            2 - Faltou informar o codigo da sala (Front)
            3 - Faltou informar a descrição (Front)
            4 - Faltou informar o andar (Front)
            5 - Sala já cadastrada no sistema
            6 - Houve algum problema no insert da tabela (Banco)
            7 - Sala já cadastrada no Sistema
        */
        try {
            // dados recabidos via JSON e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do front
            $lista = array(
                "codigo" -> '0',
                "descricao" -> '0',
                "andar" -> '0',
                "capacidade" -> '0'
            );

            if(verificarParam($resultado, $lista) == 1){
                //fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setDescricao($resultado->descricao);
                $this->setAndar($resultado->andar);
                $this->setCapacidade($resultado->capacidade);

                // validação para verificar se todos os dados foram enviados
                if(trim($this->getCodigo()) == '' || $this->getCodigo() == 0){
                    $retorno = array('codigo' = > 2,
                                     'msg' => 'Código não informado.');
                }elseif(trim($this->getDescricao()) == ''){
                    $retorno = array('codigo' => 3
                                     'msg' => 'Descrição não informada.');
                }elseif(trim($this->getAndar()) == '' || trim($this->getAndar()) == 0){
                    $retorno = array('codigo' => 4
                                     'msg' => 'Andar não informado.');
                }elseif (trim($this->getCapacidade()) == '' || trim($this->getCapacidade()) == 0){
                    $retorno = array('codigo' => 5
                                     'msg' => 'Capacidade não informada.');
                }else{
                    // Realizando a instância da Model
                    $this->load->model('M_sala');

                    // atributo $retorno recebe array com informações da validação de acesso
                    $retorno = $this->M_sala->inserir($this->getCodigo(), $this->getDescricao()
                                                      $this->getAndar(), $this->getCapacidade());
                }
            }else{
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'os campos vindos do FrontEnd não representam o médotodo de Inersção, verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array('codigo' => 0,
                             'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                                        $e->getMessage())
        }

        //Retorno no dromato JSON
        echo json_encode($retorno);
    }

    public function consultar(){
        //Codigo, descrição e tipi (Adm ou comum)
        //Recebidos via JSON e colocados em variáveis.
        //Retornos possíveis:
        //1 - Dados consultados corretamente (Banco)
        //2 - Dados não encontrados (Banco)
        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "descricao" => '0',
                "andar" => '0',
                "capacidade" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setDescricao($resultado->descricao);
                $this->setAndar($resultado->andar);
                $this->setCapacidade($resultado->capacidade);

                //Realizando a instância da Model
                $this->load->model('M_sala');

                //Atributo $retorno recebe array com informações da consulta dos dados
                $retorno = $this->M_sala->consultar($this->getCodigo(),
                                                    $this->getDescricao(),
                                                    $this->getAndar(),
                                                    $this->getCapacidade());
            }else{
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de Consulta. Verifique.'
                )
            }
        } catch (Exception $e) {
            $retorno = array('codigo' => 0
                             'msg' => 'Atenção o seguinte erro aconteceu ->'
                                        $e=>getMessage());
        }
        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function alterar(){
        /* Codigo descrição e andar
           Recebidos via jSON e colocados em variáveis
           Retornos possiveis:
           1 - Dado(s) alterado(s) corretamente (Banco)
           2 - Código da sala não informado ou zerado
           3 - Descrição não informada. 
        */

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do front
            $lista = array(
                "codigo" -> '0',
                "descricao" -> '0',
                "andar" -> '0',
                "capacidade" -> '0'
            );

            if(verificarParam($resultado, $lista) == 1){
                //fazendo setters
                $this->setCodigo($resultado->codigo);
                $this->setDescricao($resultado->descricao);
                $this->setAndar($resultado->andar);
                $this->setCapacidade($resultado->capacidade);

                //Validação para tipo de usuário que deverá ser ADMINISTRADOR, COMUM ou VAZIO
                if (trim($this->getCodigo() == '')) {
                    $retorno = array('codigo' => 2
                                     'msg' => 'Código não informado');
                // Descrição, andar ou capacidade, pelo menos 1 deles precisa ser infromado.
                }elseif (trim($this->getDescricao()) == '' && trim($this->getAndar()) == '' && 
                trim($this->getCapacidade()) == '') {
                    $retorno = array('codigo' = 3,
                                     'msg' => 'Pelo menos um parâmetro precisa ser passado para atualização');
                }else {
                    //Realizandoa a instãncia da Model
                    $this->load->model('M_sala');

                    //Atributo $retorno recebe array com inftomações da alteração dos dados
                    $retorno = $this->M_sala->alterar($this->getCodigo(), $this->getDescricao()
                                                      $this->getAndar(), $this->getCapacidade());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de alteração. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array('codigo' -> 0,
                             'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                                        $e->getMessege());
        }
        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        /*  Usuário recebido via JSON e colocado em variável
            Retornos possíveis:
            1 - Sala desativada corretamente (Banco)
            2 - Código da sala não informado
            5 - Houve algum problema na desativação da sala
            6 - Dados não encontrados (Banco) 
        */
        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do front
            $lista = array(
                "codigo" -> '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                // Fazendo o setter
                $this->setCodigo($resultado->codigo);

                //Validação das informações
                if (trim($this->getCodigo()) == '') {
                    $retorno = array('codigo' => 2,
                                     'msg' => 'Código não informado');
                }else {
                    //Realiza a instância da Model
                    $this->load->model('M_sala');

                    //Atributo $retorno recebe array com informações
                    $retorno = $this->M_sala->desativar($this->getCodigo());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de login. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array('codigo' => 0,
                             'msg' => 'ATENÇÃO: o seguinte erro aconteceu -> ',
                                       $e->getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }
}
?>