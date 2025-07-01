<?php 
defined('BASEPATH') OR exit('No direct script acess allowed');

class M_horario extends CI_Model {
   
    public function inserir($descricao, $horaInicial, $horaFinal){
        
        try {
            // Verificando se o horário já está cadastrado
            $retornoConsulta = $this->consultaHorario($descricao, $horaInicial, $horaFinal);

            if ($retornoConsulta['codigo'] != 1) {
                //Query de inserção dos dados
                $this->db->query("insert into tbl_horario (descricao, hora_ini, hora_fim)
                                 values ('$descricao', '$horaInicial', '$horaFinal')");
            
            // Verificando se ainserção ocorreu com sucesso
            if ($this->db->affected_rows() > 0){
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Horário cadastrado corretamente.'
                );
            }else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Houve algum problema na inserção na tabela de horário.'
                );
            }
            }else {
                $dados = array(
                    'codigo' => 5,
                    'msg' => 'Horário já cadastrado no sistema.'
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n"
            );
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    private function consultaHorario($descricao, $horaInicial, $horaFinal){

        try {
            //Query para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_horario
                    where descricao = '$descricao'
                        and hora_ini = '$horaInicial'
                        and hora_fim = '$horaFinal'
                        and estatus = ''";

        $retornoHorario = $this->db->query($sql);

        //verificar se a consulta ocorreu com sucesso
        if ($retornoHorario->num_rows() > 0) {
            $dados = array(
                'codigo' => 1,
                'msg' => 'Consulta efetuada com sucesso.'
            );
        }else {
            $dados = array(
                'codigo' => 4,
                'msg' => 'Horário não encontrado.'
            );
        }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n"
            );
        }
        //Envia o array $dados com as infromações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function consultaHorarioCod($codigo){

        try {
            //Query para consulta dados de acordo com o parâmetro passado
            $sql = "select * from tbl_horario where codigo = $codigo and estatus = ''";

            $retornoHorario = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if ($retornoHorario->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com scesso.');
            }else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Horário não encontrato.');
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

    public function consultar($codigo, $descricao, $horaInicial, $horaFinal){

        try {
            //Query para consultar dados de acordo com parâmetros passado
            $sql = "select * from tbl_horario where estatus != 'D' ";

            if (trim($codigo) !== '') {
                $sql = $sql . "and codigo = $codigo ";
            }

            if (trim($horaInicial) !== '') {
                $sql = $sql . " and hora inicial = '$horaInicial' ";
            }

            if (trim($descricao) !== '') {
                $sql = $sql . " and descricao like = '%$descricao%' ";
            }

            if (trim($horaFinal) !== '') {
                $sql = $sql . " and hora final = '$horaFinal' ";
            }

            $retorno = $this->db->query($sql);

            //vetificar se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $linha = $retorno->row();

                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 7,
                        'msg' => 'Horário desativado no sistema.');
                }else {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Consulta efetuada com sucesso.',
                        'dados' => $retorno->result());
                }
            }else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Horário não encontrado');
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

    public function alterar($codigo, $descricao, $horaInicial, $horaFinal){

        try {
            //Verifica se a sala já está cadastrada
            $retornoConsulta = $this->consultaHorarioCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                // Iniciando a query para atualização
                $query = "update tbl_horario set ";

                //Comparando os items
                if ($descricao !== '') {
                    $query .= "descricao = '$descricao', ";
                }

                if ($horaInicial !== '') {
                    $query .= "hora_ini = '$horaInicial', ";
                }

                if ($horaFinal !== '') {
                    $query .= "hora_fim = '$horaFinal', ";
                }

                //Terminando a concatenação da query
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                //Executando a query de atualização dos dados
                $this->db->query($queryFinal);

                //verificando se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Horário atualizado corretamente.');
                }else {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Houve algum problema na atualização na tavela de horário.');
                }
            }else {
                $dados = array(
                    'codigo' => 4,
                    'msg' => 'Horário não cadastrado no sistema.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n");
        }
        //Envia o array $dados com as informações tratadas acima pela estrutuda de decisão if
        return $dados;
    }

    public function desativar($codigo){

        try {
            //Verificando se o horário já está cadastrado
            $retornoConsulta = $this->consultaHorarioCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                
                //Query de atualização dos dados
                $this->db->query("update tbl_horario set estatus = 'D'
                                  where codigo = $codigo");

                //Verificando se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Horário DESATIVADO corretamente.');
                }else {
                    $dados = array(
                        'codigo' => 4,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO do Horário.');
                }
            }else {
                $dados = array(
                    'codigo' => 3,
                    'msg' => 'Horário não cadastrado no Sistema, não pode ser excluido.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                $e->getMessage(), "\n");
        }
        //Enviando o array com as informações tratadas acima pela estrutuda de decisão if
        return $dados;
    }

    public function listarTodos(){
        try {
            //Query para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_horario where estatus = ''
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
                    'msg' => 'Horário não encontrado'
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