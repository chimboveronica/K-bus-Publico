/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var menuRoute;
var showCoopMap = new Array();
var showRouteMap = new Array();
var storeAuxRoute;
var datos;
$.ajax({
    type: 'GET',
    url: 'http://190.12.61.30:5801//K-Bus/webresources/com.kradac.kbus.rest.entities.rutas', dataType: 'json',
    dataType:'json',
            dataType:'text',
            success: recuperar,
    error: function () {
        console.log('Uh Oh!');
    }
});

function recuperar(ajaxResponse, textStatus)
{
    console.log('entro');
    datos = Ext.JSON.decode(ajaxResponse);
    cargar();
}
;
function cargar() {

    for (var i = 0; i < datos.length; i++) {
        showRouteMap[i] = [datos[i].idRuta, datos[i].ruta, false];
//        menuRoute.add({itemId: datos[i].id, text: datos[i].ruta, checked: false});
    }

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
        ],
    });
}


