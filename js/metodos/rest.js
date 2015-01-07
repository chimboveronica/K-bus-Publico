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
storeAuxRoute = Ext.create('Ext.data.Store', {
        data: [],
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
    console.log(datos);
    cargar();
}
;
function cargar() {
var data = [];
    for (var i = 0; i < datos.length; i++) {
        data.push({
            codRuta: datos[i].codRuta, color: datos[i].color, 
            distancia: datos[i].distancia, fechaHoraRegistro: datos[i].fechaHoraRegistro, icono: datos[i].icono,
            idRuta: datos[i].idRuta, ruta: datos[i].ruta, tiempoSancion: datos[i].tiempoSancion, 
            velocidadComercial: datos[i].velocidadComercial, velocidadOperacion: datos[i].velocidadOperacion
        });
    }
    storeAuxRoute.setData(data);
    
  
   

}


