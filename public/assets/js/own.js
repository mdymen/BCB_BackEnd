
function info_msg(msg) {
    $("#info_msg").html(msg);
    $("#append_info").html($("#info").html());
    $(".alert-info").show();  
    $(".alert-success").hide();
    fechar_info();
}

function hide_info() {
    
}

function success_msg(msg) {
    $("#sucesso_msg").html(msg);
    $("#append_success").html($("#success").html());
    $(".alert-success").show();
    $(".alert-info").hide();
    fechar_sucesso();
}

function hide_success() {
    
}

function fechar_sucesso() {
    $(".close-success").bind("click", function() {
        $(".alert-success").hide();
    });
}

function fechar_info() {
    $(".close-info").bind("click", function() {
        $(".alert-info").hide();
    });
}

function fechar_alert() {
    $(".close-danger").bind("click", function() {
        $(".alert-danger").hide();
    });
}

function excluir(url) {
    $(".excluir").bind('click', function() {
        var rs = $(this).attr("result");
        var match = $(this).attr("match");
        $.post(url,{ result : rs }, function(response) {
//           $("#info_msg").html("Palpite excluido!");
//           $("#append_info").html($("#info").html());
//           $(".alert-info").show();
//            fechar_info();

            console.log(response);

            $('#result1pf_'+match).val(0);
            $('#result2pf_'+match).val(0);

            $('#rs_dados_'+match).attr("style","display:none"); 
            $('#fila_'+match).attr("style","display:yes");
        }); 
    });
}

function box_mais_info() {
    $(".box_palpite").bind("click", function() {

        var box_opcoes = $(this).attr("id_opcoes");

        if ($("#"+box_opcoes).is(":visible")) {
             $("#"+box_opcoes).hide();    
        } else {
             $("#"+box_opcoes).show();
         }
    }) 
}
