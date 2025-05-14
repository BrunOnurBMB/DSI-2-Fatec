<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mapa extends CI_Controller{

    //Atributos privados da classe
    private $codigo;
    private $dataReserva;
    private $codigo_sala;
    private $codigo_horario;
    private $codigo_turma;
    private $codigo_professor;
    private $estatus;

    //Getters dos atributos
    public function getCodigo(){
        return $this->codigo;
    }

    public function getDataReserva(){
        return $this->dataReserva;
    }

    public function getCodigoSala(){
        return $this->codigo_sala;
    }

    public function getCodigoHorario(){
        return $this->codigo_horario;
    }

    public function getCodigoTurma(){
        return $this->codigo_turma;
    }

    public function getProfessor(){
        return $this->codigo_professor;
    }

    public function getEstatus(){
        return $this->estatus;
    }

    //Setters dos atributos
    public function setCodigo($codigoFront){
        $this->codigo = $codigoFront;
    }

    public function setDataReserva($dataReservaFront){
        $this->dataReserva = $dataReservaFront;
    }

    public function setCodigoSala($codigo_salaFront){
        $this->codigo_sala = $codigo_salaFront;
    }

    public function setCodigoHorario($codigo_horarioFront){
        $this->codigo_horario = $codigo_horarioFront;
    }

    public function setCodigoTurma($codigo_turmaFront){
        $this->codigo_turma = $codigo_turmaFront;
    }

    public function setProfessor($professorFront){
        $this->codigo_professor = $professorFront;
    }

    public function setEstatus($estatusFront){
        $this->estatus = $estatusFront;
    }

    public function inserir(){
        /*
        Data de reserva, codigo da sala, codigo do horario, codiggo da turma recebidos via JSON e colocados em variáveis.
        Retorno possíveis:
        1 - Reserva cadastrada corretamente (Banco)
        2 - Faltou informar a Data (FrontEnd)
        3 - Faltou informar a Sala (FrontEnd)
        4 - Faltou informar o Horário (FrontEnd)
        5 - Faltou informar a Turma (FrontEnd)
        6 - Faltou informar o Professro (FrontEnd)
        7 - Agendamento já cadastrado no sistema
        8 - Agendamento desativado no sistema
        9 - Houve algum problema no insert da tabela (Banco)
        */

        try {
            //Dadis recebidos via JSON e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do front
            $lista = array(
                "dataReserva" => '0',
                "codSala" => '0',
                "codHorario" => '0',
                "codTurma" => '0',
                "codProfessor" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setDataReserva($resultado->dataReserva);
                $this->setCodigoSala($resultado->codSala);
                $this->setCodigoHorario($resultado->codHorario);
                $this->setCodigoTurma($resultado->codTurma);
                $this->setProfessor($resultado->codProfessor);

                //Realizando validação para saber se todos os dados foram enviados
                if (trim($this->getDataReserva()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Data não informada.'
                    );
                }elseif (trim($this->getCodigoSala()) == '') {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'Sala Não informada.'
                    );
                }elseif (trim($this->getCodigoHorario()) == '') {
                    $retorno = array(
                        'codigo' => 4,
                        'msg' => 'Horário não informado.'
                    );
                }elseif (trim($this->getCodigoTurma()) == '') {
                    $retorno = array(
                        'codigo' => 5,
                        'msg' => 'Turma não infromada.'
                    );
                }elseif (trim($this->getProfessor()) == '') {
                    $retorno = array(
                        'codigo' => 6,
                        'msg' => 'Professor não informado.'
                    );
                }else{
                    //Realizando a intância da Model
                    $this->load->model('M_mapa');

                    //Atribuindo $retorno recebe array com informações da validação do acesso
                    $retorno = $this->M_mapa->inserir(
                        $this->getDataReserva(),
                        $this->getCodigoSala(),
                        $this->getCodigoHorario(),
                        $this->getCodigoTurma(),
                        $this->getProfessor()
                    );
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os camopos vindos do FrontEnd não representam o método de login. verifique'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATEÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage()
            );
        }

        //retorno no formato JSON
        echo json_encode($retorno);
    }

    public function consultar(){
        /*
        Código, Data de resrva, código da sala, código do horário e codigo da turma recebidos via JSON e colocados em variáveis
        Retornos possiveis:
        1 - Dados consultados corretamente (Banco)
        6 - Dados não encontrados (Banco) 
        */
        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "dataReserva" => '0',
                "codSala" => '0',
                "codHorario" => '0',
                "codTurma" => '0',
                "codProfessor" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setDataReserva($resultado->dataReserva);
                $this->setCodigoSala($resultado->codSala);
                $this->setCodigoHorario($resultado->codHorario);
                $this->setCodigoTurma($resultado->codTurma);
                $this->setProfessor($resultado->codProfessor);

                //Realizando a intância da Model
                $this->load->model('M_mapa');

                //Atributo $retorno recebe array com informações da consulta dos dados
                $retorno = $this->M_mapa->consultar(
                    $this->getCodigo(),
                    $this->getDataReserva(),
                    $this->getCodigoSala(),
                    $this->getCodigoHorario(),
                    $this->getCodigoTurma(),
                    $this->getProfessor()
                );
            } else{
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de login. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', 
                $e->getMessage()
            );
        }
        echo json_encode($retorno);
    }

    public function alterar(){
        /*
        Código, Data de reserva, código da sala, código do horário e código da turma recebidos via JSON e colocados em variáveis
        Retorno possíveis:
        1 - Reserva altera corretamente (Banco)
        2 - Faltou indorma o código da reserva (FrontEnd)
        3 - Faltou informar a Data (FrontEnd)
        4 - Faltou informar a Sala (FrontEnd)
        5 - Faltou informar o Horário (FrontEnd)
        6 - Faltou informar a Turma (FrontEnd)
        7 - Faltou informar o Professor (FrontEnd)
        8 - Agendamento não cadastrado no sistema
        9 - Houve algum problema no insert da tabela (banco)
        */

        try {
            //Dados recebidos via JSON e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "dataReserva" => '0',
                "codSala" => '0',
                "codHorario" => '0',
                "codTurma" => '0',
                "codProfessor" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Realizando os setters
                $this->setCodigo($resultado->codigo);
                $this->setDataReserva($resultado->dataReserva);
                $this->setCodigoSala($resultado->codSala);
                $this->setCodigoHorario($resultado->codHorario);
                $this->setCodigoTurma($resultado->codTurma);
                $this->setProfessor($resultado->codProfessor);

                //Realizando validação dos dados para saber se todos foram enviados
                if (trim($this->getCodigo()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Código não informado.'
                    );
                }elseif (trim($this->getDataReserva()) == '') {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'Data não informada.'
                    );
                }elseif (trim($this->getCodigoSala()) == '') {
                    $retorno = array(
                        'codigo' => 4,
                        'msg' => 'Sala Não informada.'
                    );
                }elseif (trim($this->getCodigoHorario()) == '') {
                    $retorno = array(
                        'codigo' => 5,
                        'msg' => 'Horário não informado.'
                    );
                }elseif (trim($this->getCodigoTurma()) == '') {
                    $retorno = array(
                        'codigo' => 6,
                        'msg' => 'Turma não infromada.'
                    );
                }elseif (trim($this->getProfessor()) == '') {
                    $retorno = array(
                        'codigo' => 7,
                        'msg' => 'Professor não informado.'
                    );
                } else {
                    //Realizando a intância da Model
                    $this->load->model('M_mapa');

                    $retorno = $this->M_mapa->alterar(
                        $this->getCodigo(),
                        $this->getDataReserva(),
                        $this->getCodigoSala(),
                        $this->getCodigoHorario(),
                        $this->getCodigoTurma(),
                        $this->getProfessor()
                    );
                }
            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os cmapos vindos do FrontEnd não representam o método de login. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage()
            );
        }
        //Retorno no forma JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        /*
        Usuário recebido via JSON e colocado em variável
        Retornos possíveis:
        1 - Agendamento desativado corretament (Banco)
        2 - Código do curso não informado
        5 - Houve algum problema na desativação do horário
        6 - Dados não encontrados (Banco)
        */
        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                //Realizando os setters
                $this->setCodigo($resultado->codigo);
                //Validando o dados recebido que não deverá ser em branco
                if (trim($this->getCodigo()) == '') {
                    $retorno = array(
                        'codigo' =>  2,
                        'msg' => 'Código do agendamento não informado.'
                    );
                } else {
                    //Realizando a intância da Model
                    $this->load->model('M_mapa');

                    //Atributo $retorno recebe o array com informações
                    $retorno = $this->M_mapa->desativar($this->getCodigo());
                }
            } else {
              $retorno = array(
                'codigo' => 99,
                'msg' => 'Os campos vindos do FrontEnd não representam o método de login. Verifique'
              );  
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage()
            );
        }

        //Retornando os dados no formato JSON
        echo json_encode($retorno);
    }
}
?>