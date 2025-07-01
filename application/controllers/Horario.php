<?php
defined('BASEPATH') or exit('No direct script acess alowed');

class Horario extends CI_Controller
{
    //Atributos privados da classe
    private $codigo;
    private $descricao;
    private $horaInicial;
    private $horaFinal;
    private $estatus;

    //Getters dos Atributos
    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getHoraInicial()
    {
        return $this->horaInicial;
    }

    public function getHoraFinal()
    {
        return $this->horaFinal;
    }

    public function getEstatus()
    {
        return $this->estatus;
    }

    //Setters dos atributos
    public function setCodigo($codigoFront)
    {
        $this->codigo = $codigoFront;
    }

    public function setDescricao($descricaoFront)
    {
        $this->descricao = $descricaoFront;
    }

    public function setHoraInicial($horaInicialFront)
    {
        $this->horaInicial = $horaInicialFront;
    }

    public function setHoraFinal($horaFinalFront)
    {
        $this->horaFinal = $horaFinalFront;
    }

    public function setEstatus($estatusFront)
    {
        $this->estatus = $estatusFront;
    }

    public function inserir()
    {
        /* 
        Horário inicial e Horário Final recebidos via JSON e colocados em variáveis
        Retorno Possiveis:
        1 - Horario cadastrado corretamente (Banco)
        2 - Faltou informar a descrição (FrontEnd)
        3 - Faltou informar o Horário Inicial (FrontEnd)
        4 - Faltou informar o Horário Final (FrontEnd)
        5 - Horário já cadastrado no sistema
        6 - Houve algum problema no insert da tabela (Banco)
        */

        try {
            //Dados recebidos via JSON e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "descricao" => '0',
                "horaInicial" => '0',
                "horaFinal" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //fazendo os setters
                $this->setDescricao($resultado->descricao);
                $this->setHoraInicial($resultado->horaInicial);
                $this->setHoraFinal($resultado->horaFinal);

                // Fazendo validação para dabermos se todos os dados foram enviados
                if (trim($this->getDescricao()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Descrição não informada.');
                } elseif (trim($this->getHoraInicial()) == '') {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'Hora inicial não informada.');
                } elseif (trim($this->getHoraFinal()) == '') {
                    $retorno = array(
                        'codigo' => 4,
                        'msg' => 'Hora final não foi informada.');
                }else{
                    //Realizando a intância da Model
                    $this->load->model('M_horario');

                    //Atributo $retorno recebe array com informações da validação do acesso
                    $retorno = $this->M_horario->inserir($this->getDescricao(),
                                                         $this->getHoraInicial(),
                                                         $this->getHoraFinal());
                }
            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de loguin. Verifique'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function consultar()
    {
        /*
        Código recebido via Json e colocados em váriaveis.
        Possiveis Retornos:
        1 - Dados consultados corretamente (Banco)
        6 - Dados não encontrados (Banco)
        */

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array copm os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "descricao" => '0',
                "horaInicial" => '0',
                "horaFinal" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setDescricao($resultado->descricao);
                $this->setHoraInicial($resultado->horaInicial);
                $this->setHoraFinal($resultado->horaFinal);

                //Realizando instância da Model
                $this->load->model('M_horario');

                //Atributo $retorno recebe array com informações da consulta dos dados
                $retorno = $this->M_horario->consultar(
                    $this->getCodigo(),
                    $this->getDescricao(),
                    $this->getHoraInicial(),
                    $this->getHoraFinal()
                );
            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de loguin. Verifique'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e->getMessage()
            );
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function alterar() {
        /* 
        Codigo, hora inicial e hora final recebidos via JSOn e colocados em variáveis
        Retornos possiveis:
        1 - Dado(s) alterado(s) corretamente (banco)
        2 - Código da sala não informado ou Zerado
        3 - Pelo menos um prâmetro precisa ser informado (descricao,hora inicial ou hora final)
        4 - Horário não cadastrado no sistema
        5 - Houve algum problema no salvamento dos dados 
        */

        try {
            $json = file_get_contents('php://input');
            $json = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "descricao" => '0',
                "horaInicial" => '0',
                "horaFinal" => '0'
            );

            //Definindo a váriavel $resultado com os dados decodificados do JSON
            $resultado = (object) array(
                "codigo" => isset($json->codigo) ? $json->codigo : '0',
                "descricao" => isset($json->descricao) ? $json->descricao : '0',
                "horaInicial" => isset($json->horaInicial) ? $json->horaInicial : '0',
                "horaFinal" => isset($json->horaFinal) ? $json->horaFinal : '0'
            );
            
            if (verificarParam($resultado, $lista) == 1) {
                //fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setDescricao($resultado->descricao);
                $this->setHoraInicial($resultado->horaInicial);
                $this->setHoraFinal($resultado->horaFinal);

                //código é obrigatório
                if (trim($this->getCodigo() == '')) {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Código não informado.');
                    //Descrição, hora inicial e hora final, pelo menos 1 deve ser infromado.

                } elseif (trim($this->getDescricao() == '' && $this->getHoraInicial() == '' && $this->getHoraFinal() == '')) {
                    $retorno = array(
                        'codigo' => 3,
                        'msg' => 'Pelo menos um parâmetro precisa ser passado para atualização');
                } else {
                    //realizando a intanância da Model
                    $this->load->model('M_horario');

                    //Atributo $retorno recebe array com infromações da alteração dos dados
                    $retorno = $this->M_horario->alterar(
                        $this->getCodigo(),
                        $this->getDescricao(),
                        $this->getHoraInicial(),
                        $this->getHoraFinal());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de loguin. Verifique.');
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => '0',
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e->getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        /* 
        Usuário recebido via JSON e colocado em variável
        Retornos possíveis:
        1 - Horário desativado corretamente (Banco)
        2 - Código do horário não infomado
        3 - Horário não cadastrado no sistema
        4 - Houve algum problema na desativação do horário
        */

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do FrontEnd
            $lista = array(
                "codigo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                // Fazendo os setters
                $this->setCodigo($resultado->codigo);

                //Codigo é obrigatório
                if (trim($this->getCodigo() == '')) {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Código não informado.');
                }else{
                    //Realizando a instância da Model
                    $this->load->model('M_horario');

                    //Atributo $retorno recebe array com infromações
                    $retorno = $this->M_horario->desativar($this->getCodigo());
                }
            }else {
                $retorno = array (
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de loguin. Verifique');
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e->getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function listar(){
        // Função para listar os horários no Front sem necessidade de parâmentros
        try {
            $this->load->model('M_horario');
            
            //Chama o método para buscar todas as turmas
            $retorno = $this->M_horario->listarTodos();
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'Erro ao listar os horarios: ' . $e.getMessage());
        }
        echo json_encode($retorno);
    }
}
?>