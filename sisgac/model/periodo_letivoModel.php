<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class periodo_letivoModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                periodo_letivo(ano,semestre,data_inicio,data_fim) 
                VALUES (?,?,?,?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("iiss", 
                $campos['ano'], 
                $campos['semestre'],
                $campos['data_inicio'],
                $campos['data_fim']
                );
        $result = $stmt->execute() or die($this->bd->error);
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }
    
    public function existePerioLetivo($ano,$semestre) {
        $sql = "SELECT * FROM periodo_letivo WHERE ano = $ano AND semestre = $semestre";
        
        $result = mysqli_query($this->bd, $sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function atualizar($campos) {
        $sql = "UPDATE periodo_letivo SET 
                    ano = {$campos['ano']}, 
                    semestre = {$campos['semestre']},
                    data_inicio = '{$campos['data_inicio']}',
                    data_fim = '{$campos['data_fim']}'
                WHERE 
                    id_periodo_letivo = '{$campos['id_periodo_letivo']}'";
            
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar($parametros = array(), $ordenacao = array(), $limit = array()) {
        $sql = "SELECT 
                    periodo_letivo.id_periodo_letivo,
                    periodo_letivo.ano,
                    periodo_letivo.semestre,
                    DATE_FORMAT(periodo_letivo.data_inicio,'%d/%m/%Y') as data_inicio,
                    DATE_FORMAT(periodo_letivo.data_fim,'%d/%m/%Y') as data_fim
                FROM 
                    periodo_letivo" 
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

    public function getPeriodo_letivoId($id_periodo_letivo) {
        $sql = "SELECT  
                    *
                FROM 
                    periodo_letivo
                WHERE 
                     id_periodo_letivo = $id_periodo_letivo";
      
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    }
    
    public function deletar($id_periodo_letivo) {
        $sql = "DELETE FROM periodo_letivo WHERE id_periodo_letivo = $id_periodo_letivo";
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }  
}