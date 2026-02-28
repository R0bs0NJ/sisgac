<!DOCTYPE html>
<html lang="pt-BR">
<head>    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SISGAC">
    <meta name="author" content="Robson">
    
    <title>Perfil do Egresso</title>
    
    <!-- Bootstrap CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- Jquery and Bootstrap Script files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>  
    
    <!-- JS da página -->
    <script src="js/principal.js"></script>
</head>
<body>
    
    <!-- Topo contendo o Menu -->
    <header class="navbar navbar-default bs-docs-nav" role="banner" id="topo"></header>
    
    <!-- Painel do corpo da página -->
    <div class="container">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <span style="color:green; font-weight: bold; text-transform: uppercase;">BEM VINDO SISTEMA DE GERENCIAMENTO DE ATIVIDADES COMPLEMENTARES</span>
                </div>
                <div class="panel-body">
                    
                    <form name="formulario" id="formulario">
                        
                        <!-- Armazena o nome do método que será chamado na classe controler -->
                        <input type="hidden" name="metodo" id="metodo">
                        
                        <h5 style="color:green"> Informações do usuário </h5>
                        <p><strong>Nome:</strong>Robson <span id="nome"></span>
                        <p><strong>Email:</strong>juniorrobson2021@gmail.com <span id="email"></span>
                        <p><strong>Perfil:</strong>Administrador<span id="perfil"></span>
                    </form>
                </div>
            </div>
        </div>       
    </div>
    
</body>
</html>