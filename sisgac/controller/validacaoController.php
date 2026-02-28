<?php

session_start();
require_once $_SESSION['diretorio_base'] . '/controller/sessaoController.php';
require_once $_SESSION['diretorio_base'].'/model/validacaoModel.php';
require_once $_SESSION['diretorio_base'].'/model/atividadesModel.php';
require_once $_SESSION['diretorio_base'].'/model/situacaoModel.php';

class validacaoController {
    
    private $validacaoM;
    private $atividadesM;
    private $situacaoM;
    private $msg;
    
    public function __construct() {        
        
        // Objeto da classe Model
        $this->validacaoM = new validacaoModel();
        $this->atividadesM = new atividadesModel();
        $this->situacaoM = new situacaoModel();
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
            $parametros = array('data_situacao'=>$_POST['filtro'],
                                'parecer'=>$_POST['filtro'],
                                'atividades'=>$_POST['filtro'],
                                'situacao'=>$_POST['filtro']
                                );
        } else {
            $parametros=array();
        }
        
        $ordenacao = array('validacao.nome'=>'ASC');
        
        $inicio = ($pagina-1) * $registros;
        $limit = array('inicio'=>$inicio,'quantidade'=>$registros);
        
        $tabela = '';
        
        // Chama a classe model para fazer a consulta no banco
        $result = $this->validacaoM->listar($parametros,$ordenacao,$limit);
        
        // Se o número de registros for maior que zero, percorre o resultado 
        // para gerar a tabela
        $total_linhas = mysqli_num_rows($result);
        if ($total_linhas > 0) {       
            
            // Título da tabela
            $tabela .= '<table class="table table-striped table-hover table-responsive">';
            $tabela .= '<thead>';
            $tabela .= '<tr>';
            $tabela .= '<th scope="col">Atividades</th>';
            $tabela .= '<th scope="col">Situação</th>';
            $tabela .= '<th scope="col">Data</th>';
            $tabela .= '<th scope="col">Parecer</th>';
            $tabela .= '<th scope="col"></th>';
            $tabela .= '<th scope="col"></th>';
            $tabela .= '<th scope="col"></th>';
            $tabela .= '</tr>';
            $tabela .= '</thead>';
            

            
            // Registros da tabela
            $tabela .= '<tbody>';
            while ($linha = mysqli_fetch_assoc($result)) {
                $tabela .= '<tr>';
                $tabela .= '<td>'.$linha['atividades'].'</td>';
                $tabela .= '<td>'.$linha['situacao'].'</td>';
                $tabela .= '<td>'.$linha['data'].'</td>';
                $tabela .= '<td>'.$linha['parecer'].'</td>';
                $tabela .= '<td>'.$linha[''].'</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_formulario','atualizar'," . $linha['id_validacao'] . ')" style="color:green">';
                $tabela .= '<span class="glyphicon glyphicon-edit"></span>';
                $tabela .= '</a>';
                $tabela .= '</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_confirmacao','deletar'," . $linha['id_validacao'] . ')" style="color:red">';
                $tabela .= '<span class="glyphicon glyphicon-remove"></span>';
                $tabela .= '</a>';

                $tabela .= '</td>';
                $tabela .= '</tr>';
            }
            $tabela .= '</tbody>';
            $tabela .= '</table>';
            
        } 
        
        $resultado = $this->validacaoM->listar($parametros,$ordenacao);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros/$registros);
      
        $resposta = array('tabela'=>$tabela,'total_paginas'=>$total_paginas,'pagina'=>$pagina,'registros'=>$registros,'filtro'=>$_POST['filtro']);

        return json_encode($resposta);           
    }
    
    public function getValidacaoId() {
        $res = $this->validacaoM->getValidacaoId($_POST['id_validacao']);
        $linha = mysqli_fetch_assoc($res);
        return json_encode($linha);
    }

    /*
     * Método que deleta o registro com o id enviado por POST
     */
    public function deletar() {       
    
        $resultado = false;
        
        if (isset($_POST['id_validacao'])) {
            $res = $this->validacaoM->deletar($_POST['id_validacao']);
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

        if (trim($_POST['id_atividades']) == '') {
            $this->msg = 'O preenchimento do campo Atividades é obrigatório!';
            $valido = false;
        } else if (trim($_POST['situacao']) == '') {
            $this->msg = 'O preenchimento do campo Situação é obrigatório!';
            $valido = false;
        } else if (trim($_POST['data']) == '') {
            $this->msg = 'O preenchimento do campo Data é obrigatório!';
            $valido = false;
        } else if (trim($_POST['parecer']) == '') {
            $this->msg = 'O preenchimento do campo Parecer é obrigatório!';
            $valido = false;
        } 
      

        if (!$valido) {
            $this->msg = '<div class="alert alert-danger">' . $this->msg . '</div>';
        }
        return $valido;
    }    

    public function inserir() {
        $resultado = false;
        $id_validacao = 0;

        if ($this->formularioValido()) {

                       
            
            $res = $this->validacaoM->inserir($_POST);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro cadastrado com sucesso!';
                $this->msg .= '</div>';
                $id_validacao = $res;
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao inserir - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        }
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg, 'id_validacao' => $id_validacao);
        return json_encode($resposta);
    }    
    
    public function atualizar() {
        $resultado = false;

        if ($this->formularioValido()) {
                      
            
            
            $res = $this->validacaoM->atualizar($_POST);
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

    public function carregarAtividades() {
        $select = '<label for="id_atividades">Atividades:</label>';
        $select .= '<select id="id_atividades" name="id_atividades" class="form-control">';
        $select .= "<option value=''></option>";
        
        $this->validacaoM = new perfilModel();
        $result = $this->validacaoM->listar();
        while ($linha = mysqli_fetch_assoc($result)) {
            $select .= "<option value='{$linha['id_validacao']}'>";
            $select .= $linha['descricao'];
            $select .= $linha['descricao'];
            $select .= '</option>';
        }
        $select .= '</select>';
        $resposta = array('select' => $select);
        return json_encode($resposta);
    }     
    
    public function carregarSituacao() {
        $select = '<label for="id_situacao">Situação:</label>';
        $select .= '<select id="id_situacao" name="id_situacao" class="form-control">';
        $select .= "<option value=''></option>";
        
        $this->situacaoM = new situacaoModel();
        $result = $this->situacaoM->listar();
        while ($linha = mysqli_fetch_assoc($result)) {
            $select .= "<option value='{$linha['id_situacao']}'>";
            $select .= $linha['descricao'];
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
    $objeto = new validacaoController();
    echo $objeto->$metodo();
} 