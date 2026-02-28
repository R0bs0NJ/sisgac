<?php

require_once $_SESSION['diretorio_base'] . '/model/conexaoModel.php';

class cursoModel {

    private $bd;

    public function __construct() {
        $conexao = new conexaoModel();
        $this->bd = $conexao->getConexao();
    }
	
    public function inserir($campos) {
        $sql = "INSERT INTO 
                curso(nome,ch_ac) 
                VALUES (?,?)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bind_param("si", 
                $campos['nome'], 
                $campos['ch_ac'] 
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
        $sql = "UPDATE curso SET 
                    nome = '{$campos['nome']}', 
                    ch_ac = {$campos['ch_ac']}
                WHERE 
                    id_curso = '{$campos['id_curso']}'";
            
        $stmt = $this->bd->prepare($sql);
        $result = $stmt->execute() or die($this->bd->error);
        return $result;
    }    
    
    public function listar() {
        $sql = "SELECT 
                    curso.id_curso,
                    curso.nome,
                    curso.ch_ac
                FROM 
                    curso" 
                ;
        $stmt = $this->bd->prepare($sql);
        $stmt->execute() or die($this->bd->error);
        $result = $stmt->get_result();
        return $result;
    }    
}