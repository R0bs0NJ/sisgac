<?php

session_start();
require_once $_SESSION['diretorio_base'] . '/controller/sessaoController.php';
require_once $_SESSION['diretorio_base'].'/model/tipo_atividadeModel.php';
require_once $_SESSION['diretorio_base'].'/model/categoriaModel.php';


class tipo_atividadeController {
    
    private $tipo_atividadeM;
    private $categoriaM;
    private $msg;
    
    public function __construct() {        
        
        // Objeto da classe Model
        $this->tipo_atividadeM = new tipo_atividadeModel();
        $this->categoriaM = new categoriaModel();
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
            $parametros = array('descricao'=>$_POST['filtro'],
                                'carga horaria maxima'=>$_POST['filtro'],
                                'carga horária minima'=>$_POST['filtro']                                
                                );
        } else {
            $parametros=array();
        }
        
        $ordenacao = array('tipo_atividade.descricao'=>'ASC');
        
        $inicio = ($pagina-1) * $registros;
        $limit = array('inicio'=>$inicio,'quantidade'=>$registros);
        
        $tabela = '';
        
        // Chama a classe model para fazer a consulta no banco
        $result = $this->tipo_atividadeM->listar($parametros,$ordenacao,$limit);
        
        // Se o número de registros for maior que zero, percorre o resultado 
        // para gerar a tabela
        $total_linhas = mysqli_num_rows($result);
        if ($total_linhas > 0) {       
            
            // Título da tabela
            $tabela .= '<table class="table table-striped table-hover table-responsive">';
            $tabela .= '<thead>';
            $tabela .= '<tr>';
            $tabela .= '<th scope="col">Categoria</th>';
            $tabela .= '<th scope="col">Descrição</th>';
            $tabela .= '<th scope="col">Carga Horária Máxima</th>';
            $tabela .= '<th scope="col">Carga Horária Mínima</th>';
            $tabela .= '</tr>';
            $tabela .= '</thead>';
            

            
            // Registros da tabela
            $tabela .= '<tbody>';
            while ($linha = mysqli_fetch_assoc($result)) {
                $tabela .= '<tr>';
                $tabela .= '<td>'.$linha['categoria'].'</td>';
                $tabela .= '<td>'.$linha['descricao'].'</td>';
                $tabela .= '<td>'.$linha['ch_maxima'].'</td>';
                $tabela .= '<td>'.$linha['ch_minima'].'</td>';
              

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_formulario','atualizar'," . $linha['id_tipo_atividade'] . ')" style="color:green">';
                $tabela .= '<span class="glyphicon glyphicon-edit"></span>';
                $tabela .= '</a>';
                $tabela .= '</td>';

                $tabela .= '<td>';
                $tabela .= '<a href="#void" onclick="abrirModal(' . "'modal_confirmacao','deletar'," . $linha['id_tipo_atividade'] . ')" style="color:red">';
                $tabela .= '<span class="glyphicon glyphicon-remove"></span>';
                $tabela .= '</a>';

                $tabela .= '</td>';
                $tabela .= '</tr>';
            }
            $tabela .= '</tbody>';
            $tabela .= '</table>';
            
        } 
        
        $resultado = $this->tipo_atividadeM->listar($parametros,$ordenacao);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros/$registros);
      
        $resposta = array('tabela'=>$tabela,'total_paginas'=>$total_paginas,'pagina'=>$pagina,'registros'=>$registros,'filtro'=>$_POST['filtro']);

        return json_encode($resposta);           
    }
    
    public function getTipo_atividadeId() {
        $res = $this->tipo_atividadeM->getTipo_atividadeId($_POST['id_tipo_atividade']);
        $linha = mysqli_fetch_assoc($res);
        return json_encode($linha);
    }

    /*
     * Método que deleta o registro com o id enviado por POST
     */
    public function deletar() {       
    
        $resultado = false;
        
        if (isset($_POST['id_tipo_atividade'])) {
            $res = $this->tipo_atividadeM->deletar($_POST['id_tipo_atividade']);
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

        if (trim($_POST['id_categoria']) == '') {
            $this->msg = 'O preenchimento do campo Categoria é obrigatório!';
            $valido = false;
        } else if (trim($_POST['descricao']) == '') {
            $this->msg = 'O preenchimento do campo Descrição é obrigatório!';
            $valido = false;
        } else if (trim($_POST['ch_maxima']) == '') {
            $this->msg = 'O preenchimento do campo Carga horária máxima é obrigatório!';
            $valido = false;
        } 
 
        

        if (!$valido) {
            $this->msg = '<div class="alert alert-danger">' . $this->msg . '</div>';
        }
        return $valido;
    }    

    public function inserir() {
        $resultado = false;
        $id_tipo_atividade = 0;

        if ($this->formularioValido()) {
                                    
            $res = $this->tipo_atividadeM->inserir($_POST);
            if ($res) {
                $this->msg .= '<div class="alert alert-success">';
                $this->msg .= 'Registro cadastrado com sucesso!';
                $this->msg .= '</div>';
                $id_tipo_atividade = $res;
                $resultado = true;
            } else {
                $this->msg .= '<div class="alert alert-danger">';
                $this->msg .= 'Erro ao inserir - Contactar o administrador do sistema';
                $this->msg .= '</div>';
            }
        }
        $resposta = array('resultado' => $resultado, 'msg' => $this->msg, 'id_tipo_atividade' => $id_tipo_atividade);
        return json_encode($resposta);
    }    
    
    public function atualizar() {
        $resultado = false;

        if ($this->formularioValido()) {
                                    
            $res = $this->tipo_atividadeM->atualizar($_POST);
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

    public function carregarCategoria() {
        $select = '<label for="id_categoria">Categoria:</label>';
        $select .= '<select id="id_categoria" name="id_categoria" class="form-control">';
        $select .= "<option value=''></option>";
        
        $this->categoriaM = new categoriaModel();
        $result = $this->categoriaM->listar();
        while ($linha = mysqli_fetch_assoc($result)) {
            $select .= "<option value='{$linha['id_categoria']}'>";
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
    $objeto = new tipo_atividadeController();
    echo $objeto->$metodo();
} 