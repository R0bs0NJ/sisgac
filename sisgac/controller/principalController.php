<?php
session_start();
require_once $_SESSION['diretorio_base'] . '/controller/sessaoController.php';

class principalController {
              
    public function getSessao() {
        
        // Perfis de acesso na página
        $perfis = array('Administrador','Registro Acadêmico','Aluno','Colegiado');
        
        // Valida e configura vetor com valores da sessão
        $sessaoC = new sessaoController();
        $erro = $sessaoC->validar($perfis);
        $sessao = array('erro'=>$erro,'email'=>$_SESSION['email'],'nome'=>$_SESSION['nome'],'perfil'=>$_SESSION['perfil'],'topo'=>$_SESSION['topo']);

        return json_encode($sessao);         
    }
}

// Callback
if (isset($_POST['metodo'])) {    
    $metodo = $_POST['metodo'];
    $objeto = new principalController();
    echo $objeto->$metodo();
}