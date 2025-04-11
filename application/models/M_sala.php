<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_sala extends CI_Model
{

    public function inserir($codigo, $descricao, $andar, $capacidade)
    {
        try {
            // Verifica se a sala já entá cadastrada
            $retornoConsulta = $this->consultaSala($codigo);

            if ($retornoConsulta['codigo'] !== 1 && $retornoConsulta['codigo'] !== 7) {
                //Querry de inserção de dados
                $this->db->query("insert into tbl_sala (codigo, descricao, andar, capacidade)
                                 values ($codigo, '$descricao', $andar, $capacidade)");

                //Verificar se a inserção ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Sala cadastrada corretamente');
                }
                else{
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Houve algum problema na inserção na tabela salas');
                } 
            } else {
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg' => $retornoConsulta['msg']);
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e->getMessage(), "\n");
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    private function consultaSala($codigo)
    {
        try {
            //Querry para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_sala where codigo = $codigo ";

            $retornoSala = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso // perguntar para o professor // erro ao alterar
            if ($retornoSala->num_rows() > 0) {
                $linha = $retornoSala->row();

                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 7,
                        'msg' => 'Sala desativada no sistema, caso precise reativar a mesma, fale com o administrador.');
                } else {

                    $dados = array(
                        'codigo' => 8,
                        'msg' => 'Sala já cadastrada no sistema.');
                }
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Sala não encontrada.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n");
        }
        // Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function consultar($codigo, $descricao, $andar, $capacidade)
    {
        try {
            //Querry para consultar dados de acordo com prâmetros passados
            $sql = "select * from tbl_sala where estatus = '' ";

            if (trim($codigo) != '') {
                $sql = $sql . " and codigo = $codigo ";
            }

            if (trim($andar) != '') {
                $sql = $sql . " and andar = '$andar' ";
            }

            if (trim($descricao) != '') {
                $sql = $sql . " and descricao = '%$descricao%' ";
            }

            if (trim($capacidade) != '') {
                $sql = $sql . "and capacidade = '$capacidade' ";
            }

            $sql = $sql . "order by codigo ";
            $retorno = $this->db->query($sql);

            //verificar se  a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.',
                    'dados' => $retorno->result());
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Sala não encontrada.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n");
        }
        //Enviar o array $dados com as infromações tratadas acimapela estrutura de decisão if
        return $dados;
    }

    public function alterar($codigo, $descricao, $andar, $capacidade)
    {
        try {
            //Verifica se a sala já está cadastrda
            $retornoConsulta = $this->consultaSala($codigo);

            if ($retornoConsulta['codigo'] == 8) {
                //Iniciando a querry para atualização
                $query = 'update tbl_sala set ';

                //Comparando os items
                if ($descricao !== '') {
                    $query .= " descricao = '$descricao', ";
                }

                if ($andar !== '') {
                    $query .= " andar = $andar, ";
                }

                if ($capacidade !== '') {
                    $query .= " capacidade = $capacidade, ";
                }

                //Terminando a concatenação da querry
                $queryFinal = trim($query, ", ") . " where codigo = $codigo ";

                //Executando a query de atualização dos dados
                $this->db->query($queryFinal);

                //Verificando se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Sala atualizada corretamente.');
                } else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Houve algum problema na atualização da sala.');
                }
            } else {
                $dados = array(
                    'codigo' => 5,
                    'msg' => 'Sala não cadastrada no sistema.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n");
        }
        //Envia o array $dados com as infromações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function desativar($codigo)
    {
        try {
            // Verificando se a sala já está cadastrada
            $retornoConsulta = $this->consultaSala($codigo);

            if ($retornoConsulta['codigo'] == 8) {

                //Query de atualização dos dados
                $this->db->query("update tbl_sala set estatus = 'D' where codigo = $codigo");

                //Verificar se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Sala DESATIVADA corretamente');
                } else {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO da Sala.');
                }
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Sala não cadastrada no Sistema, não pode excluir.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n");
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }
}
