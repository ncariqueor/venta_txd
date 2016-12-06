var page = require('webpage').create();
page.injectJs("jquery-1.12.0.min.js");
$.ajax({
    type: 'POST',
    url: 'ajax.php',
    data: 'id=testdata',
    dataType: 'json',
    cache: false,
    success: function (result) {
        var cant_tiendas = result.length - 1;

        var fecha_ant = result[cant_tiendas];

        var f    = new Date();
        var dia  = f.getDate();
        var dia  = ""+dia+"";
        var mes  = f.getMonth()+1;
        var mes  = ""+mes+"";
        var anio = f.getFullYear();

        if(mes.length < 2)
            mes = "0"+mes;

        if(dia.length < 2)
            dia = "0"+dia;

        var fecha = anio+""+mes+""+dia;

        var hora = f.getHours();
        hora = ""+hora+"";
        var min  = f.getMinutes();
        min = ""+min+"";

        if(hora.length < 2)
            hora = "0"+hora;

        if(min.length<2)
            min = "0"+min;

        var hour = hora+""+min;

        var fs = require('fs');

        var dest = 'C:/Users/Administrator/SharePoint/Venta Online TxD - Documentos 1/Paris/'+result[0]+'/'+fecha;

        if(fs.makeDirectory(dest))
            console.log('"'+dest+'" was created.');
        else
            console.log('"'+dest+'" is NOT created.');

        var cad = "C:/Users/Administrator/SharePoint/Venta Online TxD - Documentos 1/Paris/"+result[0]+"/"+fecha+"/"+result[0]+"_"+hour+".png";

        console.log(cad);

        var page = require('webpage').create();

        page.viewportSize = {width: 1366, height: 2050};

        var system = require('system');

        page.open("http://localhost/ventas_txd/txd.php?tienda="+result[0]+"&fecha="+fecha+"&anterior="+fecha_ant, function (status){
            page.render(cad);
            phantom.exit();
        });
    }
});


/*var f    = new Date();
var dia  = f.getDate();
var dia  = ""+dia+"";
var mes  = f.getMonth()+1;
var mes  = ""+mes+"";
var anio = f.getFullYear();

if(mes.length < 2)
    mes = "0"+mes;

if(dia.length < 2)
    dia = "0"+dia;

var fecha = anio+""+mes+""+dia;

var hora = f.getHours();
hora = ""+hora+"";
var min  = f.getMinutes();
min = ""+min+"";

if(hora.length < 2)
    hora = "0"+hora;

if(min.length<2)
    min = "0"+min;

var hour = hora+""+min;

var fs = require('fs');

var dest = 'C:/Users/Administrator/SharePoint/Venta Online TxD - Documentos 1/Paris/Total_Paris/'+fecha;

if(fs.makeDirectory(dest))
    console.log('"'+dest+'" was created.');
else
    console.log('"'+dest+'" is NOT created.');

var cad = "C:/Users/Administrator/SharePoint/Venta Online TxD - Documentos 1/Paris/Total_Paris/"+fecha+"/Total_Paris"+hour+".png";

console.log(cad);

var page = require('webpage').create();

page.viewportSize = {width: 1366, height: 2050};

var system = require('system');

page.open("http://localhost/ventas_txd/index.php", function (status){
    page.render(cad);
    phantom.exit();
});*/