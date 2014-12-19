/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var showCoopMap = new Array();
var showRouteMap = new Array();
var storeAuxRoute;
var datos;
var menuRoute = [];
$.ajax({
    type: 'GET',
    url: 'http://190.12.61.30:5801//K-Bus/webresources/com.kradac.kbus.rest.entities.rutas',
    dataType:'text',
            success: recuperar,
    error: function() {
        Ext.example.msg("Alerta", 'Problemas con el servidor');

    }
});

function recuperar(ajaxResponse, textStatus)
{
    datos = Ext.JSON.decode(ajaxResponse);
    
    if (datos.length > 0) {
//        for (var i = 0; i < datos.length; i++) {
//            console.log(datos[i].idRuta);
//            menuRoute.push({itemId: datos[i+1].idRuta, text: datos[i+1].ruta, color: datos[i+1].color});
//            //Xq el +1??
//            showRouteMap[i] = [datos[i].idRuta, datos[i].ruta, false];
//        }
        cargar();
    } else {
        Ext.example.msg("Alerta", 'Problemas con el servidor');

    }

}
;
function cargar() {

    storeAuxRoute = Ext.create('Ext.data.Store', {
        data: datos,
        reader: {
            type: 'json',
            root: 'data'
        },
        proxy: {
            type: 'memory',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        fields: [
            'codRuta', 'color', 'distancia', 'fechaHoraRegistro', 'icono', 'idRuta', 'ruta', 'tiempoSancion', 'velocidadComercial', 'velocidadOperacion'
        ]
    });
    console.log(datos);
    console.log(storeAuxRoute);
    cargarPrincipal();
}


