<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class tipo_atividadeModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                tipo_atividade(id_categoria,descricao,ch_maxima,ch_minima) 
                VALUES (?,?,?,?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("isii", 
                $campos['id_categoria'], 
                $campos['descricao'],
                $campos['ch_maxima'],
                $campos['ch_minima']
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
        $sql = "UPDATE tipo_atividade SET 
                    id_categoria = '{$campos['id_categoria']}', 
                    id_descricao = {$campos['id_descricao']},
                    ch_maxima = {$campos['ch_maxima']},
                    ch_minima = {$campos['ch_minima']}
                WHERE 
                    id_tipo_atividade = '{$campos['id_tipo_atividade']}'";
            
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar($parametros = array(), $ordenacao = array(), $limit = array()){
        $sql = "SELECT 
                    tipo_atividade.id_tipo_atividade,
                    categoria.id_categoria,
                    tipo_atividade.descricao,
                    tipo_atividade.ch_maxima,
                    tipo_atividade.ch_minima
                    
                FROM 
                    tipo_atividade" 
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

    public function getTipo_atividadeId($id_tipo_atividade) {
        $sql = "SELECT  
                    *
                FROM 
                    tipo_atividade
                WHERE 
                     id_tipo_atividade = $id_tipo_atividade";
      
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    } 

    public function deletar($id_tipo_atividade) {
        $sql = "DELETE FROM tipo_atividade WHERE id_tipo_atividade = $id_tipo_atividade";
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }  
}