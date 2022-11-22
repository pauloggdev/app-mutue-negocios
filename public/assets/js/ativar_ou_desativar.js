
function esconderCampoSinal(valor){
    var referencia_sinal=document.getElementById("referencia_sinal").value;
    
    if((valor === "Marca") || ((referencia_sinal  === "Marca"))){
        $('#reproducaoMarca').removeClass('hide');

        $('#tipoSinal').removeClass('hide');
        $('#classe_produto').removeClass('hide');

        $('#averbamento_marca').removeClass('hide');
        $('#averbamento_insigNomes').addClass('hide');
        
        document.getElementById("taxa_sem_mandatario").removeAttribute("required");
        document.getElementById("taxa_sem_mandatario").removeAttribute("data-parsley-group");
        document.getElementById("taxa_sem_mandatario").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_com_mandatario").removeAttribute("required");
        document.getElementById("taxa_com_mandatario").removeAttribute("data-parsley-group");
        document.getElementById("taxa_com_mandatario").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_marcas_normal").removeAttribute("required");
        document.getElementById("taxa_marcas_normal").removeAttribute("data-parsley-group");
        document.getElementById("taxa_marcas_normal").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_marcas_mandatario").removeAttribute("required");
        document.getElementById("taxa_marcas_mandatario").removeAttribute("data-parsley-group");
        document.getElementById("taxa_marcas_mandatario").removeAttribute("data-parsley-required");
        
        document.getElementById("validar_talao_pagamento_insignia").removeAttribute("required");
        document.getElementById("validar_talao_pagamento_insignia").removeAttribute("data-parsley-group");
        document.getElementById("validar_talao_pagamento_insignia").removeAttribute("data-parsley-required");
        
        document.getElementById("numero_talao_insignia").removeAttribute("required");
        document.getElementById("numero_talao_insignia").removeAttribute("data-parsley-group");
        document.getElementById("numero_talao_insignia").removeAttribute("data-parsley-required");
    }
    
    if((valor === "Insígnia") || ((referencia_sinal  === "Insígnia"))){
        $('#reproducaoMarca').removeClass('hide');

        $('#tipoSinal').addClass('hide');
        $('#classe_produto').addClass('hide');
         
        $('#averbamento_marca').addClass('hide');
        $('#averbamento_insigNomes').removeClass('hide');
        
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_normal_marcas").removeAttribute("required");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("required");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-group");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("numero_talao_marca").removeAttribute("required");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("data_talao_marca").removeAttribute("required");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-required");
    }
    
    if( (valor === "Nome de Estabelecimento") || (referencia_sinal === "Nome de Estabelecimento")){
        $('#reproducaoMarca').addClass('hide');
        $('#reproducaoLabel').addClass('hide'); 
        $('#tipoSinal').addClass('hide');
        $('#classe_produto').addClass('hide');
        
        $('#averbamento_marca').addClass('hide');
        $('#averbamento_insigNomes').removeClass('hide');
        
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_normal_marcas").removeAttribute("required");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("required");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-group");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("numero_talao_marca").removeAttribute("required");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("data_talao_marca").removeAttribute("required");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-required");
    }
} 

function mostrarTipoSinal_vs_classe(valor){
    var referencia_sinal=document.getElementById("referencia_sinal").value;
    if( ((valor === "Marca") || (referencia_sinal === "Marca")) ) {
        $('#tipoSinal_vs_Classes').removeClass('hide');
    }else {
        $('#tipoSinal_vs_Classes').addClass('hide');
    }
}

function esconderProduto(valor){
    var Classe_id = document.getElementById("classe_id").value;
    if((valor) || (valor >= 1) || (Classe_id >= 1) ) {
        $('#produto_classe').removeClass('hide');
    }else {
        $('#produto_classe').addClass('hide');
    }
}


function mostrarData_resolucao(valor){
    var Resolucao_processo = document.getElementById("resolucao_processo").value;
    if((valor !== "Em Análise") || (Resolucao_processo !== "Em Análise") ) {
        $('#data_resolucao').removeClass('hide');
    }else {
        $('#data_resolucao').addClass('hide');
    }
}

function esconderCampoProvincia(valor){
    var Paise_id = document.getElementById("paise_id").value;
    if((valor === "7") || (Paise_id === "7") ) {
        $('#tProvincia_id').removeClass('hide');
    }else if((valor !== "7") || (Paise_id !== "7")) {
        $('#tProvincia_id').addClass('hide');
        document.getElementById("provincia_id").removeAttribute("required");
        document.getElementById("provincia_id").removeAttribute("data-parsley-group");
        document.getElementById("provincia_id").removeAttribute("data-parsley-required");
    }
}


function esconderNumeroProcesso(valor){
    var referencia=document.getElementById("referencia").value;

    if((valor === "Marca") || ((referencia  === "Marca"))){

        $('#processoMarca').removeClass('hide');

        $('#processoInsignia').addClass('hide');
        $('#processoNomeEstab').addClass('hide');
        document.getElementById("insignia_id").removeAttribute("required");
        document.getElementById("insignia_id").removeAttribute("data-parsley-group");
        document.getElementById("insignia_id").removeAttribute("data-parsley-required");
        
        document.getElementById("nomes_estabelecimento_id").removeAttribute("required");
        document.getElementById("nomes_estabelecimento_id").removeAttribute("data-parsley-group");
        document.getElementById("nomes_estabelecimento_id").removeAttribute("data-parsley-required");

        $('#reproducaoMarca').removeClass('hide');
        $('#reproducaoLabel').removeClass('hide');
        $('#averbamento_marca').removeClass('hide');
        $('#averbamento_insigNomes').addClass('hide');
        
        document.getElementById("taxa_sem_mandatario").removeAttribute("required");
        document.getElementById("taxa_sem_mandatario").removeAttribute("data-parsley-group");
        document.getElementById("taxa_sem_mandatario").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_com_mandatario").removeAttribute("required");
        document.getElementById("taxa_com_mandatario").removeAttribute("data-parsley-group");
        document.getElementById("taxa_com_mandatario").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_marcas_normal").removeAttribute("required");
        document.getElementById("taxa_marcas_normal").removeAttribute("data-parsley-group");
        document.getElementById("taxa_marcas_normal").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_marcas_mandatario").removeAttribute("required");
        document.getElementById("taxa_marcas_mandatario").removeAttribute("data-parsley-group");
        document.getElementById("taxa_marcas_mandatario").removeAttribute("data-parsley-required");
        
        document.getElementById("validar_talao_pagamento_insignia").removeAttribute("required");
        document.getElementById("validar_talao_pagamento_insignia").removeAttribute("data-parsley-group");
        document.getElementById("validar_talao_pagamento_insignia").removeAttribute("data-parsley-required");
        
        document.getElementById("numero_talao_insignia").removeAttribute("required");
        document.getElementById("numero_talao_insignia").removeAttribute("data-parsley-group");
        document.getElementById("numero_talao_insignia").removeAttribute("data-parsley-required");
        
        document.getElementById("data_talao_insignia").removeAttribute("required");
        document.getElementById("data_talao_insignia").removeAttribute("data-parsley-group");
        document.getElementById("data_talao_insignia").removeAttribute("data-parsley-required");
    }

    if((valor === "Insígnia") || ((referencia  === "Insígnia"))){
        $('#processoInsignia').removeClass('hide');

        $('#processoMarca').addClass('hide');
        $('#processoNomeEstab').addClass('hide');
        
        document.getElementById("marca_id").removeAttribute("required");
        document.getElementById("marca_id").removeAttribute("data-parsley-group");
        document.getElementById("marca_id").removeAttribute("data-parsley-required");
        
        document.getElementById("nomes_estabelecimento_id").removeAttribute("required");
        document.getElementById("nomes_estabelecimento_id").removeAttribute("data-parsley-group");
        document.getElementById("nomes_estabelecimento_id").removeAttribute("data-parsley-required");

        $('#reproducaoMarca').removeClass('hide');
        $('#reproducaoLabel').removeClass('hide');

        $('#averbamento_insigNomes').removeClass('hide');
        $('#averbamento_marca').addClass('hide');
        $('#averbamento_marca').addClass('hide');
        $('#averbamento_insigNomes').removeClass('hide');
        
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_normal_marcas").removeAttribute("required");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("required");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-group");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("numero_talao_marca").removeAttribute("required");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("data_talao_marca").removeAttribute("required");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-required");
    }

    if( (valor === "Nomes de Estabelecimento") || (referencia === "Nomes de Estabelecimento")){
        $('#processoNomeEstab').removeClass('hide');

        $('#processoInsignia').addClass('hide');
        $('#processoMarca').addClass('hide');
        
        document.getElementById("marca_id").removeAttribute("required");
        document.getElementById("marca_id").removeAttribute("data-parsley-group");
        document.getElementById("marca_id").removeAttribute("data-parsley-required");
        
        document.getElementById("insignia_id").removeAttribute("required");
        document.getElementById("insignia_id").removeAttribute("data-parsley-group");
        document.getElementById("insignia_id").removeAttribute("data-parsley-required");

        $('#reproducaoMarca').addClass('hide');
        $('#reproducaoLabel').addClass('hide');

        $('#averbamento_insigNomes').removeClass('hide');
        $('#averbamento_marca').addClass('hide');
        $('#averbamento_marca').addClass('hide');
        $('#averbamento_insigNomes').removeClass('hide');
        
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_sem_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_com_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_normal_marcas").removeAttribute("required");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_normal_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("taxa_mandatario_marcas").removeAttribute("required");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-group");
        document.getElementById("taxa_mandatario_marcas").removeAttribute("data-parsley-required");
        
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("required");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-group");
        document.getElementById("validar_talao_pagamento_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("numero_talao_marca").removeAttribute("required");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("numero_talao_marca").removeAttribute("data-parsley-required");
        
        document.getElementById("data_talao_marca").removeAttribute("required");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-group");
        document.getElementById("data_talao_marca").removeAttribute("data-parsley-required");
    }
}

function mostrarQuantidadeNormal(valor){
    var taxa1 = document.getElementById("taxa_marcas_normal").value;
    if((valor >=4 && valor <=5) || (taxa1 >=4 && taxa1 <=5) || valor ===5) {
        $('#quantidade_extra').removeClass('hide');
    }else {
        document.getElementById("quantidade_extra").style.display = "block";
        $('#quantidade_extra').addClass('hide');
        document.getElementById("quantidades_extra").removeAttribute("required");
        document.getElementById("quantidades_extra").removeAttribute("data-parsley-group");
        document.getElementById("quantidades_extra").removeAttribute("data-parsley-required");
    }
}

function mostrarQuantidadeExtra(valor){
    var taxa2 = document.getElementById("taxa_marcas_mandatario").value;
    if((valor >=1 && valor <=2) || (taxa2 >=1 && taxa2 <=2) || valor ===2) {
        $('#quantidade_extra').removeClass('hide');
    }else {
        document.getElementById("quantidade_extra").style.display = "block";
        $('#quantidade_extra').addClass('hide');
        document.getElementById("quantidades_extra").removeAttribute("required");
        document.getElementById("quantidades_extra").removeAttribute("data-parsley-group");
        document.getElementById("quantidades_extra").removeAttribute("data-parsley-required");
    }
}  