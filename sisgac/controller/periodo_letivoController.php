<?php

session_start();
require_once $_SESSION['diretorio_base'] . '/controller/sessaoController.php';
require_once $_SESSION['diretorio_base'].'/model/periodo_letivoModel.php';

class periodo_letivoController {
    
    private $periodo_letivoM;
    private $msg;
    
    public function __construct() {        
        
        // Objeto da classe Model
        $this->periodo_letivoM = new periodo_letivoModel();
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
            $parametros = array('ano'=>$_POST['filtro'],
                                'semestre'=>$_POST['filtro'],
                                'data_inicio'=>$_POST['filtro'],
                                'data_fim'=>$_POST['filtro'],
                                    
                                );
        } else {
            $parametros=array();
        }
        
        $ordenacao = array('periodo_letivo.ano'=>'ASC');
        
        $inicio = ($pagina-1) * $registros;
        $limit = array('inicio'=>$inicio,'quantidade'=>$registros);
        
        $tabela = '';
        
        // Chama a classe model para fazer a consulta no banco
        $result = $this->periodo_letivoM->listar($parametros,$ordenacao,$limit);
        
        // Se o número de registros for maior que zero, percorre o resultado 
        // para gerar a tabela
        $total_linhas = mysqli_num_rows($result);
        if ($total_linhas > 0) {       
            
            // Título da tabela
            $tabela .= '<table class="table table-striped table-hover table-responsive">';
            $tabela .= '<thead>';
            $tabela .= '<tr>';
            $tabela .= '<th width="20%">Ano</th>';
            $tabela .= '<th width="20%">Semestre</th>';
            $tabela .= '<th width="25%">Data Início</th>';
            $tabela .= '<th width="25%">Data Fim</th>';
            $tabela .= '<th width="5%"></th>';
            $tabela .= '<th width="5%"></th>';
            $tabela .= '</tr>';
            $tabela .= '</thead>';
            

            
            // Registros da tabela
            $tabela .= '<tbody>';
            while ($linha = mysqli_fetch_assoc($result)) {
                $tabela .= '<tr>';
                $tabela .= '<td>'.$linha['ano'].'</td>';
                $tabela .= '<td>'.$linha['semestre'].'</td>';
                $tabela .= '<td>'.$linha['data_inicio'].'</td>';
                $tabela .= '<td>'.$linha['data_fim'].'</td>';
                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_formulario','atualizar'," . $linha['id_periodo_letivo'] . ')" style="color:green">';
                $tabela .= '<span class="glyphicon glyphicon-edit"></span>';
                $tabela .= '</a>';
                $tabela .= '</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_confirmacao','deletar'," . $linha['id_periodo_letivo'] . ')" style="color:red">';
                $tabela .= '<span class="glyphicon glyphicon-remove"></span>';
                $tabela .= '</a>';

                $tabela .= '</td>';
                $tabela .= '</tr>';
            }
            $tabela .= '</tbody>';
            $tabela .= '</table>';
            
        } 
        
        $resultado = $this->periodo_letivoM->listar($parametros,$ordenacao);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros/$registros);
      
        $resposta = array('tabela'=>$tabela,'total_paginas'=>$total_paginas,'pagina'=>$pagina,'registros'=>$registros,'filtro'=>$_POST['filtro']);

        return json_encode($resposta);           
    }
    
    public function getPeriodo_letivoId() {
        $res = $this->periodo_letivoM->getPeriodo_letivoId($_POST['id_periodo_letivo']);
        $linha = mysqli_fetch_assoc($res);
        return json_encode($linha);
    }

    /*
     * Método que deleta o registro com o id enviado por POST
     */
    public function deletar() {       
    
        $resultado = false;
        
        if (isset($_POST['id_periodo_letivo'])) {
            $res = $this->periodo_letivoM->deletar($_POST['id_periodo_letivo']);
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
                
        $data_inicio = explode("-",$_POST['data_inicio']);
        $data_fim = explode("-",$_POST['data_fim']);
        
        

        if (trim($_POST['ano']) == '') {
            $this->msg = 'O preenchimento do campo Ano é obrigatório!';
            $valido = false;
        } else if (trim($_POST['semestre']) == '') {
            $this->msg = 'O preenchimento do campo Semestre é obrigatório!';
            $valido = false;
        } else if (trim($_POST['data_inicio']) == '') {
            $this->msg = 'O preenchimento do campo Data início é obrigatório!';
            $valido = false;
        } else if (trim($_POST['data_fim']) == '') {
            $this->msg = 'O preenchimento do campo Data final é obrigatório!';
            $valido = false;
        } else if (!checkdate($data_inicio[1], $data_inicio[2], $data_inicio[0])) {
            $this->msg = 'Data de início inválida!';
            $valido = false; 
        } else if (!checkdate($data_fim[1], $data_fim[2], $data_fim[0])) {
            $this->msg = 'Data de fim inválida!';
            $valido = false; 
        } else if(($data_inicio > $data_fim)){
            $this->$msg = 'Data inicial deve ser maior que a data final';
            $valido = false;
        }

        if (!$valido) {
            $this->msg = '<div class="alert alert-danger">' . $this->msg . '</div>';
        } 
        
        return $valido;
    }    

    public function inserir() {
        $resultado = false;
        $id_periodo_letivo = 0;

        if ($this->formularioValido()) {

            $res = $this->periodo_letivoM->inserir($_POST);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro cadastrado com sucesso!';
                $this->msg .= '</div>';
                $id_periodo_letivo = $res;
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao inserir - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        }
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg, 'id_periodo_letivo' => $id_periodo_letivo);
        return json_encode($resposta);
    }    
    
    public function atualizar() {
        $resultado = false;

        if ($this->formularioValido()) {
            
            $res = $this->periodo_letivoM->atualizar($_POST);
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
    $objeto = new periodo_letivoController();
    echo $objeto->$metodo();
} 