<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class usuarioModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
    
    public function getUsuarioId($id_usuario) {
        $sql = "SELECT  
                    *
                FROM 
                    usuario
                WHERE 
                     id_usuario = $id_usuario";
 
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    }      
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                usuario(id_perfil,id_curso,nome,matricula,senha,email) 
                VALUES (?,?,?,?,?,?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("iissss", 
                $campos['id_perfil'], 
                $campos['id_curso'], 
                $campos['nome'], 
                $campos['matricula'],
                $campos['senha'],
                $campos['email'],
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
        
        $sql = "UPDATE usuario SET 
                    id_perfil = {$campos['id_perfil']}, 
                    id_curso = {$campos['id_curso']}, 
                    nome = '{$campos['nome']}', 
                    matricula = '{$campos['matricula']}', 
                    senha = '{$campos['senha']}', 
                    email = '{$campos['email']}'
                WHERE 
                    id_usuario = {$campos['id_usuario']}";
           
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar($parametros = array(), $ordenacao = array(), $limit = array()) {
        $sql = "SELECT 
                    usuario.id_usuario,
                    usuario.nome,
                    usuario.matricula,
                    usuario.email,
                    perfil.descricao AS perfil,
                    curso.nome AS curso
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

    public function deletar($id_usuario) {
        $sql = "DELETE FROM usuario WHERE id_usuario = $id_usuario";
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }   
}