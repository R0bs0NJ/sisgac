/*
 * Função executada após todo o documento ser carregado.
 */
$(document).ready(function () {
    // Valida sessão e recuperação as informações armazenadas
    getSessao();
});

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
                $('#nome').html(json.nome);
                $('#email').html(json.email);
                $('#perfil').html(json.perfil);
            });
        } else {
            location.href = '../index.php?erro='+json.erro;
        }
    });        
}