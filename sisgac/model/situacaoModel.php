<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class situacaoModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                situacao(descricao) 
                VALUES (?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("s", 
                $campos['descricao']
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
                    descricao = '{$campos['decricao']}'
                WHERE 
                    id_situacao = '{$campos['id_situcao']}'";
            
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar($parametros = array(), $ordenacao = array(), $limit = array()) {
        $sql = "SELECT 
                    situacao.id_situacao,
                    situacao.descricao
                FROM 
                    situacao" 
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
    
    public function getSituacaoId($id_situacao) {
        $sql = "SELECT  
                    *
                FROM 
                    situacao
                WHERE 
                     id_situacao = $id_situacao";
      
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    } 

    public function deletar($id_situacao) {
        $sql = "DELETE FROM situacao WHERE id_situacao = $id_situacao";
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    } 
}