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
    dataType: 'text',
    success: recuperar,
    error: function () {
        Ext.example.msg("Alerta", 'Problemas con el servidor');

    }
});

function recuperar(ajaxResponse, textStatus)
{
    datos = Ext.JSON.decode(ajaxResponse);
    cargar();
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
    }


