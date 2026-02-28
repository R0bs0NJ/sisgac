/*
 * Função executada após todo o documento ser carregado.
 */
$(document).ready(function () {
    // Valida sessão e recuperação as informações armazenadas
    getSessao();
    preencheTabela();
    
    $('#pagina').blur(function () {
        $('#msg').html('');
        $('#modal_formulario_msg').html('');        
        var valor = parseInt($('#pagina').val());
        var total_paginas = parseInt($('#total_paginas').val());        
        if (
                (valor < 1) || 
                (valor > total_paginas) ||
                (isNaN($('#pagina').val()))
            ){
            alert('Valor de página inválido!');
            $('#pagina').val('');
        } else {
            preencheTabela();
        }       
    });

    $('#registros').blur(function () {
        $('#msg').html('');
        $('#modal_formulario_msg').html('');           
        var valor = parseInt($('#pagina').val());
        if (
                (valor < 1) ||
                (isNaN($('#registros').val()))
                ) {
            alert('Valor de registro inválido!');
            $('#registros').val('');
        } else {
            $('#pagina').val('1');
            preencheTabela();
        }
    });

    $('#btn_buscar').click(function () {
        $('#msg').html('');
        $('#modal_formulario_msg').html('');           
        preencheTabela();
    });

    $('#btn_anterior').click(function () {
        $('#msg').html('');
        $('#modal_formulario_msg').html('');           
        var valor = parseInt($('#pagina').val());
        var total_paginas = parseInt($('#total_paginas').val());
        var novo_valor = (valor - 1);
        if ((novo_valor >= 1) && (novo_valor <= total_paginas)) {
            $('#pagina').val(novo_valor);
        } else {
            alert('Página inexistente: '+valor+' - '+total_paginas);
        }
        preencheTabela();
    });

    $('#btn_proximo').click(function () {
        $('#msg').html('');
        $('#modal_formulario_msg').html('');           
        var valor = parseInt($('#pagina').val());
        var total_paginas = parseInt($('#total_paginas').val());
        var novo_valor = (valor + 1);
        if ((novo_valor >= 1) && (novo_valor <= total_paginas)) {
            $('#pagina').val(novo_valor);
        } else {
            alert('Página inexistente: '+valor+' - '+total_paginas);
        }
        preencheTabela();
    });
    
    $('#modal_formulario').on('shown.bs.modal', function () {
        // 2 - Aqui deve ser colocado o campos que terá o foco ao abrir o formulario
        $('#descricao').focus();
    });       
    
    $('#btn_adicionar').click(function () {
        abrirModal('modal_formulario', 'inserir', 0);
    });    
    
    $('#btn_gravar').click(function () {
        enviar('modal_formulario');
    });
    
    $('#btn_sim').click(function () {
        enviar('modal_confirmacao');
    });     

});

function enviar(modal) {
    var dados = $('#formulario').serialize();
    $.ajax({
        url: '../controller/' + arquivo(),
        type: 'post',
        dataType: 'html',
        data: dados
    }).done(function (resposta) {
        var json = JSON.parse(resposta);
        if (modal == 'modal_formulario') {
            if (json.resultado) {
                $('#modal_formulario').modal('toggle');
                $('#msg').html(json.msg);
                preencheTabela();
            } else {
                $('#modal_formulario_msg').html(json.msg);
            }
        } else {
            if (json.resultado) {
                preencheTabela();
            }
            $('#msg').html(json.msg);
        }
    });
}

function abrirModal(modal, metodo, id) {

    $('#msg').html('');
    $('#modal_formulario_msg').html('');

    $('#id_validacao').val(id);
    if (metodo == 'atualizar') {
        $('#metodo').val('getValidacaoId');
        carregar(id);
    } else {
        // 3 - Aqui deve ser colocado os campos que serão limpos no formulario de 
        $("#id_atividades").val('');
        $("#id_situacao").val('');
        $("#data_situacao").val('');
        $("#parecer").val('');
    }
    $('#metodo').val(metodo);

    $('#' + modal).modal();
}

// Função que retorna o nome do arquivo na URL
function arquivo() {
    var url = window.location.href;
    var url_partes = url.split("/");
    var nome_arquivo = url_partes[url_partes.length - 1].split(".");
    var nome = nome_arquivo[0];
    var arquivo = nome+'Controller.php';
    return arquivo;
}

function getSessao() {
    $('#metodo').val('getSessao');
    var dados = $('#formulario').serialize();
    $.ajax({
        url: '../controller/' + arquivo(),
        type: 'post',
        dataType: 'html',
        data: dados
    }).done(function (resposta) {
        var json = JSON.parse(resposta);
        if (json.erro == 0) {
            $('#topo').load(json.topo, function() {
                $('#user').html(json.email);
            });
        } else {
            location.href = '../index.php?erro='+json.erro;
        }
    });        
}

function preencheTabela() {      
    $('#metodo').val('listar');
    var dados = $('#formulario').serialize();    
    $.ajax({
        url: '../controller/' + arquivo(),
        type: 'post',
        dataType: 'html',
        data: dados
    }).done(function (resposta) {
        var json = JSON.parse(resposta);
        $('#tabela').html(json.tabela);
        $('#registros').val(json.registros);
        $('#total_paginas').val(json.total_paginas);
        $('#pagina').val(json.pagina);
        $('#filtro').val(json.filtro);        
    });    
}

function carregar() {
    var dados = $('#formulario').serialize();
    $.ajax({
        url: '../controller/' + arquivo(),
        type: 'post',
        dataType: 'html',
        data: dados
    }).done(function (resposta) {
        var json = JSON.parse(resposta);
        // 4 - Aqui deve ser colocado os campos que serão carregados para edição
        $("#id_atividades").val(json.descricao);
        $("#id_situacao").val(json.descricao);
        $("#data_situacao").val(json.descricao);
        $("#parecer").val(json.descricao);
    });
}