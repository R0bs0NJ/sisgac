<?php

session_start();
require_once $_SESSION['diretorio_base'] . '/controller/sessaoController.php';
require_once $_SESSION['diretorio_base'].'/model/usuarioModel.php';
require_once $_SESSION['diretorio_base'].'/model/perfilModel.php';
require_once $_SESSION['diretorio_base'].'/model/cursoModel.php';

class usuarioController {
    
    private $usuarioM;
    private $perfilM;
    private $cursoM;
    private $msg;
    
    public function __construct() {        
        
        // Objeto da classe Model
        $this->usuarioM = new usuarioModel();
        $this->perfilM = new perfilModel();
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
            $parametros = array('nome'=>$_POST['filtro'],
                                'matricula'=>$_POST['filtro'],
                                'email'=>$_POST['filtro'],
                                'perfil'=>$_POST['filtro'],
                                'curso'=>$_POST['filtro'],
                                
                                );
        } else {
            $parametros=array();
        }
        
        $ordenacao = array('usuario.nome'=>'ASC');
        
        $inicio = ($pagina-1) * $registros;
        $limit = array('inicio'=>$inicio,'quantidade'=>$registros);
        
        $tabela = '';
        
        // Chama a classe model para fazer a consulta no banco
        $result = $this->usuarioM->listar($parametros,$ordenacao,$limit);
        
        // Se o número de registros for maior que zero, percorre o resultado 
        // para gerar a tabela
        $total_linhas = mysqli_num_rows($result);
        if ($total_linhas > 0) {       
            
            // Título da tabela
            $tabela .= '<table class="table table-striped table-hover table-responsive">';
            $tabela .= '<thead>';
            $tabela .= '<tr>';
            $tabela .= '<th scope="col">Perfil</th>';
            $tabela .= '<th scope="col">Curso</th>';
            $tabela .= '<th scope="col">Nome</th>';
            $tabela .= '<th scope="col">Matrícula</th>';
            $tabela .= '<th scope="col">Email</th>';
            $tabela .= '<th scope="col"></th>';
            $tabela .= '<th scope="col"></th>';
            $tabela .= '</tr>';
            $tabela .= '</thead>';
            

            
            // Registros da tabela
            $tabela .= '<tbody>';
            while ($linha = mysqli_fetch_assoc($result)) {
                $tabela .= '<tr>';
                $tabela .= '<td>'.$linha['perfil'].'</td>';
                $tabela .= '<td>'.$linha['curso'].'</td>';
                $tabela .= '<td>'.$linha['nome'].'</td>';
                $tabela .= '<td>'.$linha['matricula'].'</td>';
                $tabela .= '<td>'.$linha['email'].'</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_formulario','atualizar'," . $linha['id_usuario'] . ')" style="color:green">';
                $tabela .= '<span class="glyphicon glyphicon-edit"></span>';
                $tabela .= '</a>';
                $tabela .= '</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_confirmacao','deletar'," . $linha['id_usuario'] . ')" style="color:red">';
                $tabela .= '<span class="glyphicon glyphicon-remove"></span>';
                $tabela .= '</a>';

                $tabela .= '</td>';
                $tabela .= '</tr>';
            }
            $tabela .= '</tbody>';
            $tabela .= '</table>';
            
        } 
        
        $resultado = $this->usuarioM->listar($parametros,$ordenacao);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros/$registros);
      
        $resposta = array('tabela'=>$tabela,'total_paginas'=>$total_paginas,'pagina'=>$pagina,'registros'=>$registros,'filtro'=>$_POST['filtro']);

        return json_encode($resposta);           
    }
    
    public function getUsuarioId() {
        $res = $this->usuarioM->getUsuarioId($_POST['id_usuario']);
        $linha = mysqli_fetch_assoc($res);
        return json_encode($linha);
    }

    /*
     * Método que deleta o registro com o id enviado por POST
     */
    public function deletar() {       
    
        $resultado = false;
        
        if (isset($_POST['id_usuario'])) {
            $res = $this->usuarioM->deletar($_POST['id_usuario']);
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

        if (trim($_POST['id_perfil']) == '') {
            $this->msg = 'O preenchimento do campo Perfil é obrigatório!';
            $valido = false;
        } else if (trim($_POST['nome']) == '') {
            $this->msg = 'O preenchimento do campo Nome é obrigatório!';
            $valido = false;
        } else if (
                    (trim($_POST['id_curso']) == '') &&
                    ((trim($_POST['id_perfil']) == 2) || (trim($_POST['id_perfil']) == 3))
                    )
                    {
            $this->msg = 'O preenchimento do campo Curso é obrigatório!';
            $valido = false;
        } else if (trim($_POST['matricula']) == '') {
            $this->msg = 'O preenchimento do campo Matrícula é obrigatório!';
            $valido = false;
        } else if (trim($_POST['email']) == '') {
            $this->msg = 'O preenchimento do campo Email é obrigatório!';
            $valido = false;
        } else if (trim($_POST['senha']) == '') {
            $this->msg = 'O preenchimento do campo Senha é obrigatório!';
            $valido = false;
        } else if (trim($_POST['confirmar']) == '') {
            $this->msg = 'O preenchimento do campo Confirmar Senha é obrigatório!';
            $valido = false;
        } else if (trim($_POST['senha']) != trim($_POST['confirmar'])) {
            $this->msg = 'A confirmação da senha não bate com a senha digitada!';
            $valido = false;
        } 
 
        

        if (!$valido) {
            $this->msg = '<div class="alert alert-danger">' . $this->msg . '</div>';
        }
        return $valido;
    }    

    public function inserir() {
        $resultado = false;
        $id_usuario = 0;

        if ($this->formularioValido()) {

            if ($_POST['id_curso'] == '') {
                $_POST['id_curso'] = null;
            }
            $_POST['senha'] = md5($_POST['senha']);
            
            $res = $this->usuarioM->inserir($_POST);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro cadastrado com sucesso!';
                $this->msg .= '</div>';
                $id_usuario = $res;
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao inserir - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        }
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg, 'id_usuario' => $id_usuario);
        return json_encode($resposta);
    }    
    
    public function atualizar() {
        $resultado = false;

        if ($this->formularioValido()) {
            
            if ($_POST['id_curso'] == '') {
                $_POST['id_curso'] = 'null';
            }            
            
            $_POST['senha'] = md5($_POST['senha']);
            
            $res = $this->usuarioM->atualizar($_POST);
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

    public function carregarPerfil() {
        $select = '<label for="id_perfil">Perfil:</label>';
        $select .= '<select id="id_perfil" name="id_perfil" class="form-control" onChange="esconder_curso()">';
        $select .= "<option value=''></option>";
        
        $this->perfilM = new perfilModel();
        $result = $this->perfilM->listar();
        while ($linha = mysqli_fetch_assoc($result)) {
            $select .= "<option value='{$linha['id_perfil']}'>";
            $select .= $linha['descricao'];
            $select .= '</option>';
        }
        $select .= '</select>';
        $resposta = array('select' => $select);
        return json_encode($resposta);
    }     
    
    public function carregarCurso() {
        $select = '<label for="id_curso">Curso:</label>';
        $select .= '<select id="id_curso" name="id_curso" class="form-control">';
        $select .= "<option value=''></option>";
        
        $this->cursoM = new cursoModel();
        $result = $this->cursoM->listar();
        while ($linha = mysqli_fetch_assoc($result)) {
            $select .= "<option value='{$linha['id_curso']}'>";
            $select .= $linha['nome'];
            $select .= '</option>';
        }
        $select .= '</select>';
        $resposta = array('select' => $select);
        return json_encode($resposta);
    }     
}

// Callback
if (isset($_POST['metodo'])) {
    $metodo = $_POST['metodo'];
    $objeto = new usuarioController();
    echo $objeto->$metodo();
} 