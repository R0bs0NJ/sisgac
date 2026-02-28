<?php
    session_start();
   
    $msg = '';
    if (isset($_GET['erro'])) {
        switch ($_GET['erro']) {
            case 1: 
                $msg = 'Usuário não autenticado!'; 
                break;
            case 2:
                $msg = 'Permissão de acesso negada!';
                break;
            default: 
                $msg = 'Erro inesperado!';
                break;
        }
    } else {
        $_SESSION['ativo'] = true;
        $_SESSION['diretorio_base'] = str_replace('\\', '/', realpath(NULL));
        $_SESSION['topo'] = 'topo_administrador.php';
        $_SESSION['perfil'] = 'Administrador';
        $_SESSION['nome'] = 'Robson';
        $_SESSION['email'] = 'juniorrobson2021@gmail.com';
        header('Location: view/principal.php');
    }
    echo $msg;