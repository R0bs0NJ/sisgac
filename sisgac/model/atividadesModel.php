<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class atividadesModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
    
    public function getAtividadesId($id_atividades) {
        $sql = "SELECT  
                    *
                FROM 
                    atividades
                WHERE 
                     id_atividades = $id_atividades";
 
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    }      
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                atividades(id_usuario,id_tipo_atividade,id_periodo_letivo,descricao,ch_atividade,ch_aproveitada) 
                VALUES (?,?,?,?,?,?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("iiisii", 
                $campos['id_usuario'], 
                $campos['id_tipo_atividade'], 
                $campos['id_tipo_letivo'], 
                $campos['descricao'],
                $campos['ch_atividade'],
                $campos['ch_aproveitada'],
                );
        $result = $stmt->execute() or die($this->bd->error);
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }
    
    public function existeMatricula($matricula) {
        $sql = "SELECT matricula FROM usuario WHERE matricula = '$matricula'";
        
        $result = mysqli_query($this->bd, $sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function atualizar($campos) {
        
        $sql = "UPDATE atividades SET 
                    id_usuario = {$campos['id_usuario']}, 
                    id_tipo_atividade = {$campos['id_tipo_atividade']}, 
                    id_periodo_letivo = '{$campos['periodo_letivo']}', 
                    descricao = '{$campos['descricao']}', 
                    ch_atividade = '{$campos['ch_atividade']}', 
                    ch_aproveitada = '{$campos['ch_aproveitada']}'
                WHERE 
                    id_atividades = {$campos['id_atividades']}";
           
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar($parametros = array(), $ordenacao = array(), $limit = array()) {
        $sql = "SELECT 
                    atividades.id_atividades,
                    atividades.descricao,
                    atividades.ch_atividade,
                    atividades.ch_aproveitada,
                    usuario.nome AS usuario,
                    tipo_atividade.descricao AS tipo_atividade,
                    periodo_letivo.semestre AS periodo_letivo
                FROM 
                    usuario INNER JOIN perfil 
                        ON usuario.id_perfil = perfil.id_perfil
                    LEFT JOIN curso
                        ON usuario.id_curso = curso.id_curso" 
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

    public function deletar($id_atividades) {
        $sql = "DELETE FROM atividades WHERE id_atividades = $id_atividades";
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }   
}