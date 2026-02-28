<?php

session_start();
require_once $_SESSION['diretorio_base'] . '/controller/sessaoController.php';
require_once $_SESSION['diretorio_base'].'/model/cursoModel.php';

class cursoController {
    
    private $cursoM;
    private $msg;
    
    public function __construct() {        
        
        // Objeto da classe Model
        $this->cursoM = new cursoModel();
    }
    
    public function getSessao() {

        // Perfis de acesso na página
        $perfis = array('Administrador');
        
        // Valida e configura vetor com valores da sessão
        $sessaoC = new sessaoController();
        $erro = $sessaoC->validar($perfis);
        $sessao = array('erro'=>$erro,'email'=>$_SESSION['email'],'nome'=>$_SESSION['nome'],'perfil'=>$_SESSION['perfil'],'topo'=>$_SESSION['topo']);
        
        return json_encode($sessao);         
    }
    
    
    /*
     * Método que lista os registros cadastrados na tabela
     */
    public function listar() {        
        
        $registros = '';
        $pagina = '';
        
        if (trim($_POST['registros']) == '') {
            $registros = 10;
        } else {
            $registros = $_POST['registros'];
        }        
            
        if (trim($_POST['pagina']) == '') {
            $pagina = 1;
        } else {
            $pagina = $_POST['pagina'];
        }        
        
        if (trim($_POST['filtro']) != '') {
            $parametros = array('nome'=>$_POST['filtro']);
        } else {
            $parametros=array();
        }
        $ordenacao = array('curso.nome'=>'ASC');
        
        $inicio = ($pagina-1) * $registros;
        $limit = array('inicio'=>$inicio,'quantidade'=>$registros);
        
        $tabela = '';
        
        // Chama a classe model para fazer a consulta no banco
        $result = $this->cursoM->listar($parametros,$ordenacao,$limit);
        
        // Se o número de registros for maior que zero, percorre o resultado 
        // para gerar a tabela
        $total_linhas = mysqli_num_rows($result);
        if ($total_linhas > 0) {       
            
            // Título da tabela
            $tabela .= '<table class="table table-striped table-hover table-responsive">';
            $tabela .= '<thead>';
            $tabela .= '<tr>';
            $tabela .= '<th width="45%">Nome</th>';
            $tabela .= '<th width="45%">Carga horária</th>';
            $tabela .= '<th width="5%"></th>';
            $tabela .= '<th width="5%"></th>';
            $tabela .= '</tr>';
            $tabela .= '</thead>';
            

            
            // Registros da tabela
            $tabela .= '<tbody>';
            while ($linha = mysqli_fetch_assoc($result)) {
                $tabela .= '<tr>';
                $tabela .= '<td>'.$linha['nome'].'</td>';
                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_formulario','atualizar'," . $linha['id_curso'] . ')" style="color:green">';
                $tabela .= '<span class="glyphicon glyphicon-edit"></span>';
                $tabela .= '</a>';
                $tabela .= '</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_confirmacao','deletar'," . $linha['id_curso'] . ')" style="color:red">';
                $tabela .= '<span class="glyphicon glyphicon-remove"></span>';
                $tabela .= '</a>';

                $tabela .= '</td>';
                $tabela .= '</tr>';
            }
            $tabela .= '</tbody>';
            $tabela .= '</table>';
            
        } 
        
        $resultado = $this->cursoM->listar($parametros,$ordenacao);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros/$registros);
      
        $resposta = array('tabela'=>$tabela,'total_paginas'=>$total_paginas,'pagina'=>$pagina,'registros'=>$registros,'filtro'=>$_POST['filtro']);

        return json_encode($resposta);           
    }
    
    public function getCursoId() {
        $res = $this->cursoM->getCursoId($_POST['id_curso']);
        $linha = mysqli_fetch_assoc($res);
        return json_encode($linha);
    }

    /*
     * Método que deleta o registro com o id enviado por POST
     */
    public function deletar() {       
    
        $resultado = false;
        
        if (isset($_POST['id_curso'])) {
            $res = $this->cursoM->deletar($_POST['id_curso']);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro deletado com sucesso!';
                $this->msg .= '</div>';
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao deletar - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        } else {
            $this->msg .= '<div class="alert alert-danger">';
            $this->msg .= 'O ID do registro não foi enviado - Contactar o administrador do sistema!';
            $this->msg .= '</div>';
        }
        
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg);
        return json_encode($resposta);    
    }
    
    public function formularioValido() {

        $valido = true;

        if (trim($_POST['nome']) == '') {
            $this->msg = 'O preenchimento do campo Nome é obrigatório!';
            $valido = false;
        } 

        if (!$valido) {
            $this->msg = '<div class="alert alert-danger">' . $this->msg . '</div>';
        }
        return $valido;
    }    

    public function inserir() {
        $resultado = false;
        $id_curso = 0;

        if ($this->formularioValido()) {

            $res = $this->cursoM->inserir($_POST);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro cadastrado com sucesso!';
                $this->msg .= '</div>';
                $id_categoria = $res;
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao inserir - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        }
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg, 'id_categoria' => $id_curso);
        return json_encode($resposta);
    }    
    
    public function atualizar() {
        $resultado = false;

        if ($this->formularioValido()) {
            
            $res = $this->cursoM->atualizar($_POST);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro atualizado com sucesso!';
                $this->msg .= '</div>';
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao atualizar - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        }
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg);
        return json_encode($resposta);
    }
  
}

// Callback
if (isset($_POST['metodo'])) {
    $metodo = $_POST['metodo'];
    $objeto = new cursoController();
    echo $objeto->$metodo();
} 