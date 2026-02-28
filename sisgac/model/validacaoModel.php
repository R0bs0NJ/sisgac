<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class validacaoModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                validacao(id_atividades,id_situcao,data_situacao,parecer) 
                VALUES (?,?,?,?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("iiis", 
                $campos['id_atividades'], 
                $campos['id_situcao'],
                $campos['data_situacao'],
                $campos['parecer']
                );
        $result = $stmt->execute() or die($this->bd->error);
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }
    
    public function existeNome($nome) {
        $sql = "SELECT nome FROM curso WHERE nome = '$nome'";
        
        $result = mysqli_query($this->bd, $sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function atualizar($campos) {
        $sql = "UPDATE situacao SET 
                    id_atividades = '{$campos['id_atividades']}', 
                    id_situacao = {$campos['id_situacao']}
                    data_situacao = {$campos['data_situacao']}
                    parecer = {$campos['parecer']}
                        
                WHERE 
                    id_validacao = '{$campos['id_validacao']}'";
            
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar($parametros = array(), $ordenacao = array(), $limit = array()) {
        $sql = "SELECT 
                    validacao.id_validacao,
                    atividades.id_atividades,
                    situacao.id_situacao
                    validacao.data_situacao,
                    parecer.parecer
                FROM 
                    validacao" 
                ;
        if (count($parametros) > 0) {
            $i = 0;
            $sql .= ' WHERE ';
            foreach ($parametros as $key => $value) {               
                if ($i > 0) $sql .= " OR ";
                $sql .= "$key like '%$value%'";                
                $i++;
            }  
        }        
        
        if (count($ordenacao) > 0) {
            $i = 0;
            $sql .= ' ORDER BY ';
            foreach ($ordenacao as $key => $value) {
                if ($i > 0) $sql .= ", ";
                $sql .= "$key $value";               
                $i++;
            }          
        }
        
        if (count($limit) > 0) {
            $sql .= " LIMIT {$limit['inicio']},{$limit['quantidade']}";      
        }          
        
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;    
    } 
    public function getValidacaoId($id_validacao) {
        $sql = "SELECT  
                    *
                FROM 
                    validacao
                WHERE 
                     id_validacao = $id_validacao";
      
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    }
    public function deletar($id_validacao) {
        $sql = "DELETE FROM validacao WHERE id_validacao = $id_validacao";
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }   
}