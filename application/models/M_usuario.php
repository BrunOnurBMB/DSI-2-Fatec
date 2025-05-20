<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_usuario extends CI_Model {
    public function inserir($nome, $email, $usuario, $senha){
        try {
            //Verificar o status do usuáio antes de fazer o insert
            $retornoUsuario = $this->validaUsuario($usuario);

            if ($retornoUsuario['codigo'] == 4) {
                //Query de inserção dos dados
                $this->db->query("insert into tbl_usuario (nome, email, usuario, senha)
                                  values ('$nome', '$email', '$usuario', md5('$senha'))");

                //verificando se a inserção ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuário cadastrado corretamente');
                }else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Houve algum problema na inserção na tabela de usuário.');
                }
            }else {
                $dados = array(
                    'codigo' => $retornoUsuario['codigo'],
                    'msg' => $retornoUsuario['msg']);
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function consultar($nome, $email, $usuario) {
        /*
        Função que servira para três tipos de consulta
            para todos os usuário
            para um determinado usuário
            para nomes de usuários
        */

        try {
            //Query para consultar dados de acordo com parâmentros passados
            $sql = "select id_usuario, nome, usuario, email
                    from tbl_usuario
                    where estatus != 'D'";

            if (trim($nome) != '') {
                $sql = $sql . "and nome like '%$nome%' ";
            }

            if (trim($email) != '') {
                $sql = $sql . "and email = '$email' ";
            }

            if (trim($usuario) != '') {
                $sql = $sql . "and usuario like '%$usuario%' ";
            }

            $retorno = $this->db->query($sql);

            //Verificando se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.',
                    'dados' => $retorno->result());
            }else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Dados não encontratods.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÂO: O seguinte erro aconteceu -> ',
                        $e->getMessage());
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function alterar($idUsuario, $nome, $email, $senha) {
        try {
            //Vefificar o status do usuário antes de fazer o update
            $retornoUsuario = $this->validaIdUsuario($idUsuario);

            if ($retornoUsuario['codigo'] == 1) {
                // Iniciando a query pata update
                $query = "update tbl_usuario set ";

                //Comparandos os items
                if ($nome !== '') {
                    $query .= "nome = '$nome', ";
                }

                if ($email !== '') {
                    $query .= "email = '$email', ";
                }

                if ($senha !== '') {
                    $query .= "senha = md5('$senha'), ";
                }

                //Terminando a concatenação da query
                $queryFinal = rtrim($query, ", ") . "where id_usuario = $idUsuario ";

                //Executando a Query de update dos dados
                $this->db->query($queryFinal);

                //Verificando se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuário atualizado corretamente');
                }else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Houve algum problema na atualização da tabela usuários');
                }
            } else {
                $dados = array(
                    'codigo' => $retornoUsuario['codigo'],
                    'msg' => $retornoUsuario['msg']);
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                                    $e->getMessage());
        }
        //Enviando o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function desativar($idUsuario) {
        try {
            //Verificando o estatus do usuário antes de realizar a desativação
            $retornoUsuario = $this->validaIdUsuario($idUsuario);

            if ($retornoUsuario['codigo'] == 1) {
                //Query de atualização dos dados
                $this->db->query("update tbl_usuario set estatus = 'D'
                                  where id_usuario = $idUsuario");
                //Verificando se a atualização ocorreu com sucesso
                if ($this->db-> affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuário DESATIVADO corretamente.');
                }else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO do usuário.');
                }
            }else {
                $dados = array(
                    'codigo' => $retornoUsuario['codigo'],
                    'msg' => $retornoUsuario['msg']);
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }
        //Enviando o array $dados com as informações tratadas acimea pela estrutura de decisão if
        return $dados;
    }

    private function validaUsuario($usuario) {
        try {
            /*
            Atributo retorno recebe o resultado do SELECT sem status pois teremos que validar para verificar se está deletando virtualmente ou não
            */
            $retorno = $this->db->query("select * from tbl_usuario
                                         where usuario = '$usuario'");
            /*
            Verificando se a quantidade de linhas trazidas na consulta é superior a 0
            Vinculando o resultado da query para trtar o resultado do status
            */
            $linha = $retorno->row();

            if ($retorno->num_rows() == 0) {
                $dados = array(
                    'codigo' => 4,
                    'msg' => 'Usuário não existe na base de dados.');
            }else {
                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Usuário DESATIVADO NA BASE DE DADOS, não pder ser utilizado!');
                }else {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuário correto');
                }
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }

        return $dados;
    }

    private function validaIdUsuario($idUsuario) {
        try {
            /*
            Atributo retorno recebe o resultado do SELCT sem statuspois teremos que validar
            para verificar se está deletado virtualmente ou não
            */
            $retorno = $this->db->query("select * from tbl_usuario
                                         where id_usuario = $idUsuario");
            /*
            Verificando se a quantidade de linhas trazidas na consulta é superior a 0
            Vinculando o resultado da query para tratar o resultado do status
            */
            $linha = $retorno->row();

            if ($retorno->num_rows() == 0) {
                $dados = array(
                    'codigo' => 4,
                    'msg' => 'Usuário não existe na base dados.');
            }else {
                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Usuário JÁ DESATIVADO NA BASE DE DADOS!');
                }else {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuário correto');
                }
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }

        return $dados;
    }

    public function validaLogin($usuario, $senha) {
        try {
            /*
            Atributo retorno recebe o resultado do SELECT realizado na tabela de usuários lembrando da função MD5() por causa da criptografia, e sem status pois teremos que validar para verificar se aestá deltado virtualmente ou não.
            */
            $retorno = $this->db->query("select * from tbl_usuario
                                         where usuario = '$usuario' 
                                         and senha = md5('$senha')");
            /*
            Verificando se a quantidade de linhas trazidas na consulta é superior a 0
            Vinculando o resultado da query para tratarmos o resultado do status
            */
            $linha = $retorno->row();

            if ($retorno->num_rows() == 0) {
                $dados = array(
                    'codigo' => 4,
                    'msg' => 'Usuário ou senha inválidos.');
            }else {
                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Usuário DESATIVADO NA BASE DE DADOS!');
                }else {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuário correto.');
                }
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ', $e->getMessage());
        }

        return $dados;
    }
}
?>