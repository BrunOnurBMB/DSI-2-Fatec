<?php
defined('BASEPATH') or exit('No direct script acess allowed');

//incluir a classe que precisaremos intânciar
include_once("M_sala.php");
include_once("M_horario.php");
include_once("M_turma.php");
include_once("M_professor.php");

class M_mapa extends CI_Model{

    public function inserir($dataReserva, $codSala, $codHorario, $codTurma, $codProfessor){
        try {
            //Verificando se o professor já está cadastrado
            $retornoConsulta = $this->consultaReservaTotal($dataReserva, $codSala, $codHorario,
                                                           $codTurma,  $codProfessor);
            
            if ($retornoConsulta['codigo'] == 6 || $retornoConsulta['codigo'] == 7) {
                
                //Chamando o objeto sala para validação
                $salaObj = new M_sala();

                //Chamando o método de verificação
                $retornoConsultaSala = $salaObj->consultar($codSala, '', '', '');

                if ($retornoConsultaSala['codigo'] == 1) {
                    //chamndo o objeto sala para validação
                    $horarioObj = new M_horario();

                    //chamando o método de verificação
                    $retornoConsultaHorario = $horarioObj->consultaHorarioCod($codHorario);

                    if ($retornoConsultaHorario['codigo'] == 1) {
                        //chamando o objeto sala para validação
                        $turmaObj = new M_turma();

                        //Chamando o método de verificação
                        $retornoConsultaTurma = $turmaObj->consultaTurmaCod($codTurma);

                        if ($retornoConsultaTurma['codigo'] == 1) {
                            //Chamando o objeto sala para validação
                            $professorObj = new M_professor();

                            //Chamando o método de verificação
                            $retornoConsultaProfessor = $professorObj->consultaProfessorCod($codProfessor);

                            if ($retornoConsultaProfessor['codigo'] == 1) {
                                //Query de inserção dos dados
                                $this->db->query("insert into tbl_mapa (datareserva, sala, codigo_horario,
                                                 codigo_turma, codigo_professor)
                                                 values ('" . $dataReserva . "', $codSala, $codHorario, $codTurma, $codProfessor)");
                            
                             //verificando se a inseerção ocorreu com sucesso
                                if ($this->db->affected_rows() > 0) {
                                    $dados = array(
                                        'codigo' => 1,
                                        'msg' => 'Agendamento cadastrado corretamente.'
                                    );
                                } else {
                                    $dados = array(
                                        'codigo' => 8,
                                        'msg' => 'Houve algum problema na inserção da tabela de agendamento.'
                                    );
                                }
                            } else {
                                $dados = array(
                                    'codigo' => $retornoConsultaProfessor['codigo'],
                                    'msg' => $retornoConsultaProfessor['msg']
                                );
                            }
                        } else {
                            $dados = array(
                                'codigo' => $retornoConsultaTurma['codigo'],
                                'msg' => $retornoConsultaTurma['msg']
                            );
                        }                     
                    } else {
                        $dados = array(
                            'codigo' => $$retornoConsultaHorario['codigo'],
                            'msg' => $$retornoConsultaHorario['msg']
                        );
                    }
                } else {
                    $dados = array(
                        'cdoigo' => $retornoConsultaSala['codigo'],
                        'msg' => $retornoConsultaSala['msg']
                    );
                }
            } else {
                $dados = array(
                'cdoigo' => 7,
                'msg' => 'Agendamento já cadastradono sistema.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n"
            );
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    private function consultaReservaTotal($dataReserva, $codSala, $codHorario){
        try {
            //Query oara verificar a hora inicial e final daquele determinado horário
            $sql = "select * from tbl_horario 
                    where codigo = $codHorario";

            $retornoHorario = $this->db->query($sql);

            if ($retornoHorario->num_rows() > 0) {
                $linhaHr = $retornoHorario->row();
                $horaInicial = $linhaHr->hora_ini;
                $horaFinal = $linhaHr->hora_fim;

                //Query para consultar dados de acordo com oarâmetros passados
                $sql = "select * from tbl_mapa m, tbl_horario h 
                        where m.datareserva = '" . $dataReserva . "'
                            and m.sala = $codSala 
                            and m.codigo_horario = h.codigo 
                            and (h.hora_fim >= '" . $horaInicial . "' 
                            and h.hora_ini <= '" . $horaFinal . "')";
            $retornoMapa = $this->db->query($sql);

                //Verificando se a consulta ocorreu com sucesso
                if ($retornoMapa->num_rows() > 0) {
                    $linha = $retornoMapa->row();
                    
                    if (trim($linha->estatus) == "D") {
                        $dados = array(
                            'cdoigo' => 7,
                            'msg' => 'Agendamento desativado no sistema.'
                        );
                    } else {
                        $dados = array(
                            'codigo' => 1,
                            'msg' => 'A data de ' . $dataReserva . ' está ocupada para esta sala'
                        );
                    }
                } else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Reserva não encontrada.'
                    );
                }
            }
        } catch (Exception $e) {
            $dadoos = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu  -> ',
                $e->getMessage(),
                "\n"
            );
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisãi if
        return $dados;
    }

    public function consultar($codigo, $dataReserva, $codSala, $codHorario, $codTurma, $codProfessor){
        try {
            //Query para consultar dados de acrodo com parâmetros passados
            $sql = "select m.codigo, date_format(m.datareserva, '%d-%m-%Y') datareservabra, datareserva,
                    m.sala, s.descricao descsala, m.codigo_horario,
                    h.descricao deshorario, m.codigo_turma, t.descricao descturma, m.codigo_professor,
                    p.nome nome_professor 
                    from tbl_mapa m, tbl_professor p, tbl_horario h, tbl_turma t, tbl_sala s 
                    where m.estatus = '' 
                        and m.codigo_professor = p.codigo 
                        and m.codigo_horario   = h.codigo 
                        and m.codigo_turma     = t.codigo 
                        and m.sala              = s.codigo";

            if (trim($codigo) != '') {
                $sql = $sql . " and m.codigo = $codigo ";
            }

            if (trim($dataReserva) != '') {
                $sql = $sql . " and m.datareserva = '" . $dataReserva . "' ";
            }

            if (trim($codSala) != '') {
                $sql = $sql . " and m.sala = $codSala ";
            }

            if (trim($codHorario) != '') {
                $sql = $sql . " and m.codigo_horario = $codHorario ";
            }

            if (trim($codTurma) != '') {
                $sql = $sql . " and m.codigo_turma = $codTurma ";
            }

            if (trim($codProfessor) != '') {
                $sql = $sql . " and m.codigo_professor = $codProfessor ";
            }

            $sql = $sql . " order by m.datareserva, h.hora_ini, m.codigo_horario, m.sala ";

            $retorno = $this->db->query($sql);

            //verificando se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso',
                    'dados' => $retorno->result()
                );
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Agendamento não encontrado.'
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENSÇÃO: O seguinte erro aconteceu -> ', 
                $e->getMessage(), "\n"
            );
        }
        //Enviando o array $dados com as informações tratadas pela estrutura de decisão if
        return $dados;
    }

    public function alterar($codigo, $dataReserva, $codSala, $codHorario, $codTurma, $codProfessor){
        try {
            //Varificando se o professor já está cadastrado
            $retornoConsultaCodigo = $this->consultar($codigo, "", "", "", "", "");

            if ($retornoConsultaCodigo['codigo'] == 1) {
                //Iniciando a query para atualização
                $query = "update tbl_mapa set ";

                if ($dataReserva != "") {
                    $query .= "datareserva = '$dataReserva', ";
                }

                if ($codSala != "") {
                    //Chamando o objeto sala para validação
                    $salaObj = new M_sala();

                    //Chamando o método de verificação
                    $retornoConsultaSala = $salaObj->consultar($codSala, '', '', '');

                    if ($retornoConsultaSala['codigo'] == 1) {
                        $query .= "sala = $codSala, ";
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaSala['codigo'],
                            'msg' => $retornoConsultaSala['msg']
                        );
                    }
                }

                if ($codHorario != "") {
                    //Chamando o objeto horario para validação
                    $horarioObj = new M_horario();

                    //chamando o método para verificação
                    $retornoConsultaHorario = $horarioObj->consultaHorarioCod($codHorario);

                    if ($retornoConsultaHorario['codigo'] == 1) {
                        $query .= "codigo_horario = $codHorario, ";
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaHorario['codigo'],
                            'msg' => $retornoConsultaHorario['msg']
                        );
                    }
                }

                if ($codTurma != "") {
                    //chamando o objeto turma para validação
                    $turmaObj = new M_turma();

                    //Chamando o método para verificação
                    $retornoConsultaTurma = $turmaObj->consultaTurmaCod($codTurma);

                    if ($retornoConsultaTurma['codigo'] == 1) {
                        $query .= "codigo_turma = $codTurma, ";
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaTurma['codigo'],
                            'msg' => $retornoConsultaTurma['msg']
                        );
                    }
                }
                if ($codProfessor != "") {
                    //chamando o objeto professor para validação
                    $professorObj = new M_professor();

                    //chamando o método de verificação
                    $retornoConsultaProfessor = $professorObj->consultaProfessorCod($codProfessor);

                    if ($retornoConsultaProfessor['codigo'] == 1) {
                        $query .= "codigo_professor = $codProfessor, ";

                        //Terminando a concatenação da query
                        $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                        //executando a query de atualização dos dados
                        $this->db->query($queryFinal);

                        //Verificando se a atualização ocorreu com sucesso
                        if ($this->db->affected_rows() > 0) {
                            $dados = array(
                                'codigo' => 1,
                                'msg' => 'Agendamento alterado corretamente.'
                            );
                        } else {
                            $dados = array(
                                'codigo' => 8,
                                'msg' => 'Houve algum problema de alteração na tabela agendamento.'
                            );
                        }
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaProfessor['codigo'],
                            'msg' => $retornoConsultaProfessor['msg']
                        );
                    }
                }
            } else {
                $dados = array(
                    'codigo' => 8,
                    'msg' => 'Agendamento não cadastrado no sistema.'
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÂO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n"
            );
        }
        //Enviando o array $dados com as informações tratados pela estrutura de decisão if
        return $dados;
    }

    public function desativar($codigo){
        try {
            //Verificando se o agendamento já está cadastrado
            $retornoConsulta = $this->consultar($codigo, "", "", "", "", "");

            if ($retornoConsulta['codigo'] == 1) {
                
                //Query de atualização dos dados
                $this->db->query("delete from tbl_mapa 
                                 where codigo = $codigo");

                //Verificando se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Agendamento DEASTIVADO corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Houve algum problema na DESATIVAÇÂO do Agendamento.'
                    );
                }
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Agendamento não cadastrado no Sistema, não pode excluir.'
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÂO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n"
            );
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }
}
?>