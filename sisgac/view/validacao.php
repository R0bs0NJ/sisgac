<!DOCTYPE html>
<html lang="pt-BR">
    <head>    
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Sistema de Gerenciamento de Atividades Complementares">
        <meta name="author" content="Robson">

        <title>SISGAC</title>

        <!-- Bootstrap CSS file -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

        <!-- Jquery and Bootstrap Script files -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>  

        <!-- JS da página -->
        <script src="js/validacao.js"></script>
    </head>
    <body>

        <!-- Topo contendo o Menu -->
        <header class="navbar navbar-default bs-docs-nav" role="banner" id="topo"></header>

        <!-- Painel do corpo da página -->
        <div class="container">
            <form id="formulario">
                <div class="col-md-12">

                    <div class="panel panel-default">

                        <div class="panel-heading text-center">
                            <span style="color:green; font-weight: bold; text-transform: uppercase;">Validação</span>
                        </div>

                        <div class="panel-body">

                            <br>
                            <div id="msg"></div>                            

                            <nav class="nav navbar-form" style="padding-left: 0px">
                                <span class="navbar-left">
                                    <button type="button" class="btn btn-success form-control" id="btn_adicionar" >
                                        <span class="glyphicon glyphicon-plus"></span> Adicionar
                                    </button>
                                </span>
                                <span class="navbar-right">
                                    <div class="form-group" id="perfil_busca"></div> 
                                    <input type="text" name="filtro" id="filtro" class="form-control input-sm" size="35" placeholder="Buscar">
                                    <button type="button" class="btn btn-default form-control input-sm" id="btn_buscar" >
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>                                                                         
                                </span>
                            </nav>                            

                            <!-- Campos para envio de informações do sistema -->
                            <input type="hidden" name="metodo" id="metodo">
                            <input type="hidden" name="id_valiacao" id="id_validacao">

                            <!-- Tabela de registros -->
                            <div id="tabela"></div>                            

                            <nav class="nav navbar-form" style="padding-left: 0px">
                                <div class="navbar-left">
                                    <label for="registros">Registros por Página</label>
                                    <input class="form-control input-sm" type="text" name="registros" id="registros" size="1">
                                </div>
                                <div class="navbar-right">
                                    <button type="button" class="form-control input-sm" id="btn_anterior">Anterior</button>
                                    <input class="form-control input-sm" type="text" name="pagina" id="pagina" size="1">
                                    DE
                                    <input class="form-control input-sm" type="text" name="total_paginas" id="total_paginas" size="1" disabled>
                                    <button type="button" class="form-control input-sm" id="btn_proximo">Próximo</button>                              
                                </div>
                            </nav>                            

                        </div>
                    </div>
                </div>

                <!--
                Modal para inserir e atualizar 
                -->
                <div id="modal_formulario" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Informações da categoria</h4>
                                <div id="modal_formulario_msg"></div>
                            </div>
                            <div class="modal-body">                                                             
                                <div class="form-group">
                                    <label for="nome">Atividades:</label>
                                    <input type="text" class="form-control" name="atividades" id="atividades">
                                </div>
                                <div class="form-group">
                                    <label for="nome">Situação:</label>
                                    <input type="text" class="form-control" name="situacao" id="situação">
                                </div>
                                <div class="form-group">
                                    <label for="nome">Data:</label>
                                    <input type="text" class="form-control" name="data_situacao" id="data_situacao">
                                </div>
                                <div class="form-group">
                                    <label for="nome">Parecer:</label>
                                    <input type="text" class="form-control" name="parecer" id="parecer">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success" id="btn_gravar">Gravar</button>
                            </div>
                            </div>
                        </div>   
                    </div>
                </div>


                <!--
                 Modal para confirmação de exclusão
                -->
                <div id="modal_confirmacao" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Exclusão</h4>
                                <div id="modal_confirmacao_msg"></div>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    Deseja realmente excluir este registro?
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success" data-dismiss="modal" id="btn_sim">Sim</button>
                            </div>
                        </div>
                    </div>   
                </div>                  

            </form>
        </div>
    </body>
</html>