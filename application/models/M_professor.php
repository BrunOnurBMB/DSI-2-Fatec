<?php
defined('BASEPATH') or exit('No direct script allowed');

class M_professor extends CI_Model{
    public function inserir($nome, $tipo, $cpf){
        try {
            //verificando se o professor já está cadastrado
            $retornoConsulta = $this->consultaProfessorCpf($cpf);

            if ($retornoConsulta['codigo'] != 1) {
                //Query de inserção dos dados
                $this->db->query("insert into tbl_professor (nome, tipo, cpf) 
                                                                        values ('$nome', '$tipo', '$cpf')");

                //Verificar se a inserção ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Professor cadastrado corretamente.');
                }else{
                    $dados = array('codigo' => 6,
                                    'msg' => 'Houve algum problema na inserção na tabela de professor.');
                }
            }else{
                $dados = array('codigo' => 5,
                                'msg' => 'Professor já cadastrado no sistema.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage(), "\n");
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    private function consultaProfessorCpf($cpf){
        try {
            //Query para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_professor where cpf = '$cpf'";

            $retornoSala = $this->db->query($sql);

            //Verificando se a consulta ocorreu com sucesso
            if ($retornoSala->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.');
            }else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Professor não encontrado.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÂO: O seguinte erro aconteceu -> ', $e->getMessage(), "\n");
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function consultaProfessorCod($codigo){
        try {
            //Query para consulta dados de acordo com parâmetros passados
            $sql = "select * from tbl_professor where codigo = '$codigo' and estatus = ''";

            $retornoSala = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if ($retornoSala->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.');
            }else{
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Professor não encontrado.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage(), "\n");
        }
        //Envia o array $dados com as informações tratadas acimea pela estrutura de decisão if
        return $dados;
    }

    public function consultar($codigo, $nome, $cpf, $tipo){
        try {
            //Query para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_professor where estatus = '' ";

            if (trim($codigo) != '') {
                $sql = $sql . "and codigo = $codigo ";
            }

            if (trim($cpf) != '') {
                $sql = $sql . "and cpf = '$cpf' ";
            }

            if (trim($nome) != '') {
                $sql = $sql . "and nome like '%$nome%' ";
            }

            if (trim($tipo) != '') {
                $sql = $sql . "and tipo = '$tipo' ";
            }

            $sql = $sql . " order by nome ";
            $retorno = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.', 
                    'dados' => $retorno->result());
            }else{
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Professor não encontrado.');
            }
        } catch (Exception $e) {
            $dados =  array(
                'codigo' => 00,
                'mdg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage(),"\n");
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function alterar($codigo, $nome, $cpf, $tipo)
    {
        try {
            //Verifica se o professor já está cadastrado
            $retornoConsulta = $this->consultaProfessorCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                //Iniciando a querry para atualização
                $query = 'update tbl_professor set ';

                //Comparando os items
                if ($nome !== '') {
                    $query .= "nome = '$nome', ";
                }

                if ($cpf !== '') {
                    $query .= "cpf = '$cpf', ";
                }

                if ($tipo !== '') {
                    $query .= "tipo = '$tipo' ";
                }

                //Terminando a concatenação da querry
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";
                //Executando a query de atualização dos dados
                $this->db->query($queryFinal);
                
                //Verificando se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Professor atualizado corretamente.');
                } else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Houve algum problema na atualização da tabela de professor.');
                }
            } else {
                $dados = array(
                    'codigo' => 5,
                    'msg' => 'Professor não cadastrado no sistema.');   
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

    public function desativar($codigo){
        try {
            //verificando se o professor já está cadastrado
            $retornoConsulta = $this->consultaProfessorCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                
                //Querry de atualização dos dados
                $this->db->query("update tbl_professor set estatus = 'D' where codigo = $codigo");

                //Verificar se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Professor Desativado corretamente.');
                }else{
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO do professor.');
                }
            }else{
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Professor não cadastrado no Sistema, não pode excluir.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage(), "\n");
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function listarTodos(){
        try {
            //Query para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_professor where estatus = ''
                    order by codigo";
            $retorno = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.',
                    'dados' => $retorno->result()
                );
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Professor(a) não encontrado(a)'
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        return $dados; // Retorna um array de objetos
    }
}
?>