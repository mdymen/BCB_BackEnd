
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