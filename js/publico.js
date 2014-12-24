Ext.Loader.setConfig({
    enabled: true
});
Ext.Loader.setPath('Ext.ux', 'extjs-docs-5.0.0/extjs-build/build/examples/ux');
var idRoute = '';
var tabPanelMapa;
var bandera = true;
var color;
var bandera1 = true;
var vehiculo = false;
var spot = Ext.create('Ext.ux.Spotlight', {
    easing: 'easeOut',
    duration: 500
});

    Ext.onReady(function () {
        Ext.apply(Ext.form.field.VTypes, {
            cedulaValida: function (val, field) {
                if (val.length !== 10) {
                    return false;
                }
                if (val.length === 10) {
                    if (check_cedula(val)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return true;
            },
            cedulaValidaText: 'Numero de Cedula Invalida',
            numeroTelefono: function (val, field) {
                var partes = val.split("");
                if (partes.length === 10) {
                    //para celular
                    if (!/^[0]{1}[9]{1}[0-9]{8}$/.test(val)) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    //para telefono
                    if (!/^[0]{1}[7]{1}[0-9]{7}$/.test(val)) {
                        return false;
                    } else {
                        return true;
                    }
                }
            },
            numeroTelefonoText: 'Ingresar solo caracteres numéricos válidos <br>que empiezen con [09] movil tamaño de (10)dígitos<br> 0 [072] convencional tamaño de (9)dígitos ',
            emailNuevo: function (val, field) {
                if (!/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/.test(val)) {
                    return false;
                }
                return true;
            },
            emailNuevoText: 'Dede ingresar segun elz formato kradac@kradac.com <br>sin caracteres especiales',
        });
        Ext.tip.QuickTipManager.init();

        var menuRoutePrincipal = Ext.create('Ext.menu.Menu', {
            items: menuRoute,
            listeners: {
                click: function (menu, item, e, eOpts) {
                    clearLienzoLineRoute();
                    clearVehiclesByRoute();
                    color = item.color;
                    idRoute = item.getItemId();
                    setRoute();
                }
            }
        });
//    for (var i = 1; i < datos.length; i++) {
//        menuRoute.add({itemId: datos[i].idRuta, text: datos[i].ruta, color: datos[i].color});
//    }

        var panelMenu = Ext.create('Ext.form.Panel', {
            region: 'north',
            items: [{
                    xtype: 'toolbar',
                    padding: '5 0 0 60',
                    style: {
                        background: '#3A8144',
                        borderColor: 'white',
                        borderStyle: 'solid'
                    },
                    items: [{
                            xtype: 'label',
                            html: '<a href="http://www.kradac.com" target="_blank"><img src="img/principal1.png" width="200" height="60"></a>'
                        }, {
                            xtype: 'label',
                            padding: '15 5 5 5',
                            html: '<section id="panelNorte">' +
                                    '</section>'
                        }]
                },
            ]
        });

        var menuUsuario = Ext.create('Ext.Button', {
            style: {
                background: '#3A8144',
            },
            padding: '6 6 6 4',
            text: '<span id="titulo">Opciones</span>',
            menu: [
                {text: 'Consultas', handler: function () {
                        showWinConsultas();
                    }},
                {text: 'Sugerencias', handler: function () {
                        showWinAdminSugerencias();
                    }, },
                {text: 'Denuncias', handler: function () {
                        showWinAdminDenuncias();
                    }}
            ]
        });
        var barMap = Ext.create('Ext.toolbar.Toolbar', {
            region: 'north',
            border: true,
            style: {
                background: 'white',
                borderStyle: 'solid',
                borderBottomColor: '#FFBF00',
                borderBottomWidth: '5px'
            },
            items: [
//                menuUsuario, '-',

                {
                    xtype: 'button',
                    arrowAlign: 'bottom',
                    style: {
                        background: 'white',
                        borderStyle: 'none',
                    },
                    tooltip: 'Dar click para visualizar',
                    text: '<img src="img/parada1.png" width="100" height="40">',
                    handler: function () {
                    },
                    listeners: {
                        click: function () {
                            if (bandera) {
                                this.setText('<span style="color:#003F72"><img src="img/parada1.png" width="100" height="40"></span>');
                                clearLienzoPointsRoute();
                                bandera = false;
                            } else {
                                this.setText('<span style="color:#003F72"><img src="img/parada2.png" width="100" height="40"></span>');
                                setEstation();
                                bandera = true;
                            }
                        }

                    }
                }, '-',
                {
                    xtype: 'button',
                    tooltip: 'Dar click para visualizar',
                    style: {
                        background: 'white',
                        borderStyle: 'none',
                    },
                    text: '<img src="img/ruta1.png" width="100" height="40">',
                    handler: function () {
                    },
                    listeners: {
                        click: function () {
                            if (idRoute !== '') {

                                if (bandera1) {
                                    bandera1 = false;
                                    this.setText('<span style="color:#003F72"><img src="img/ruta1.png" width="100" height="40"></span>');
                                    clearLienzoLineRoute();
                                    clearMarks();
                                    vehiculo = true;
                                    setVehicle();
                                } else {
                                    this.setText('<span style="color:#003F72"><img src="img/ruta2.png" width="100" height="40"></span>');
                                    setRoute();
                                    clearVehiclesByRoute();
                                    bandera1 = true;
                                    vehiculo = false;

                                }
                            } else {
                                Ext.example.msg("Alerta", 'Seleccione una Ruta');
                            }
                        }

                    }
                }, '-',
                {
                    xtype: 'combobox',
                    width: '30%',
                    labelWidth: 20,
                    store: storeAuxRoute,
                    fieldLabel: '<img src="img/buscar3.png"/>',
                    labelSeparator: '',
                    emptyText: 'Sin Ruta',
                    displayField: 'ruta',
                    valueField: 'idRuta',
                    forceselection: true,
                    editable: true,
                    listeners: {
                        select: function (thisObject, record, eOpts) {
                            idRoute = record[0].data.idRuta;
                            color = record[0].data.color;
                            clearLienzoLineRoute();
                            if (bandera) {
                                setEstation();
                            }
                            if (bandera1) {
                                setRoute();
                            } else {
                                clearLienzoLineRoute();
                            }
                            clearVehiclesByRoute();
                        }
                    },
                    pageSize: 10
                }
                ,
//                {
//                    text: '<b><span id="titulo">Ver Tarifas</span></b>',
//                    tooltip: 'Ver Tarifas',
//                    style: {
//                        background: '#3A8144'
//                    },
//                    handler: function () {
//                        showWinPrecios();
                //                    }
//                },
                '->',
//                {
//                    icon: 'img/localizame.png',
//                    tooltip: 'Ubicar mi Posición',
//                    handler: function () {
//                        getLocation();
//                    }
//                }
            ]
        });
        console.log(storeAuxRoute);
        console.log(storeAuxRoute);
        tabPanelMapa = Ext.create('Ext.panel.Panel', {
            region: 'center',
            style: {
                borderStyle: 'solid',
                borderTopColor: '#FFBF00',
                borderTopWidth: '6px'
            },
            id: 'panel-map',
            html: '<div id="map"></div>'
        });

        var panelCentral = Ext.create('Ext.form.Panel', {
            region: 'center',
            layout: 'border',
            items: [
                barMap,
                tabPanelMapa
            ]
        });

        Ext.create('Ext.container.Viewport', {
            layout: 'border',
            items: [panelMenu, panelCentral]
        });

        loadMap();

        setTimeout(function () {
            if (vehiculo) {
                setVehicle();
            }
        }, 5 * 1000);


    });

;
function setEstation() {
    if (connectionMap()) {
        var resultadoEstaciones;
        $.ajax({
            type: 'GET',
            url: 'http://190.12.61.30:5801/K-Bus/webresources/com.kradac.kbus.rest.entities.estaciones', dataType: 'json',
            dataType:'json',
                    dataType:'text',
                    success: recuperarDatos1,
            error: function () {
                Ext.example.msg("Alerta", 'Problemas con el servidor');
            }
        });

        function recuperarDatos1(ajaxResponse, textStatus)
        {
            resultadoEstaciones = Ext.JSON.decode(ajaxResponse);
            drawPointsRoute(resultadoEstaciones, idRoute);
        }
    }

}
function setRoute() {
    if (connectionMap()) {
        var resultado;
        $.ajax({
            type: 'GET',
            url: 'http://190.12.61.30:5801//K-Bus/webresources/com.kradac.kbus.rest.entities.linearutas/ruta=' + idRoute, dataType: 'json',
            dataType:'json',
                    dataType:'text',
                    success: recuperarDatos,
            error: function () {
                Ext.example.msg("Alerta", 'Problemas con el servidor');

            }
        });

        function recuperarDatos(ajaxResponse, textStatus)
        {
            resultado = Ext.JSON.decode(ajaxResponse);
            drawLineRoute(resultado, idRoute, color);

        }
    }
}


function setVehicle() {
    if (idRoute !== '') {
        if (vehiculo) {
            if (connectionMap()) {
                var vehiculos;
                $.ajax({
                    type: 'GET',
                    url: 'http://190.12.61.30:5801/K-Bus/webresources/com.kradac.kbus.rest.entities.ultimodatofastracks/ruta=' + idRoute, dataType: 'json',
                    dataType:'json',
                            dataType:'text',
                            success: recuperarDatos,
                    error: function () {
                        Ext.example.msg("Alerta", 'Problemas con el servidor');
                    }
                });

                function recuperarDatos(ajaxResponse, textStatus)
                {
                    vehiculos = Ext.JSON.decode(ajaxResponse);
                    if (connectionMap()) {
                        addVehiculosToCanvas(vehiculos);
                    }
                }

                var vehiculos;
                $.ajax({
                    type: 'GET',
                    url: 'http://190.12.61.30:5801/K-Bus/webresources/com.kradac.kbus.rest.entities.ultimodatoskps/ruta=' + idRoute, dataType: 'json',
                    dataType:'json',
                            dataType:'text',
                            success: recuperarDatos,
                    error: function () {
                        Ext.example.msg("Alerta", 'Problemas con el servidor');
                    }
                });

                function recuperarDatos(ajaxResponse, textStatus)
                {
                    vehiculos = Ext.JSON.decode(ajaxResponse);
                    addVehiculosToCanvas(vehiculos);
                }
            }
        }
    }

}

