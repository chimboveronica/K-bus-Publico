Ext.Loader.setConfig({
    enabled: true
});
Ext.Loader.setPath('Ext.ux', 'extjs-docs-5.0.0/extjs-build/build/examples/ux');
var idRoute = '';
var tabPanelMapa;
var bandera = true;
var color;
var bandera1 = true;
var spot = Ext.create('Ext.ux.Spotlight', {
    easing: 'easeOut',
    duration: 500
});
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    menuRoute = Ext.create('Ext.menu.Menu', {
        items: [],
        listeners: {
            click: function (menu, item, e, eOpts) {
                if (item.checked) {
                    var idRoute = item.getItemId();
                    var form = Ext.create('Ext.form.Panel');
                    form.getForm().submit({
                        url: 'php/gui/draw/getRoute.php',
                        params: {
                            idRoute: idRoute
                        },
                        failure: function (form, action) {
                            Ext.MessageBox.show({
                                title: 'Mensaje',
                                msg: action.result.message,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.INFO
                            });
                        },
                        success: function (form, action) {
                            var resultado = action.result;
                            if (connectionMap()) {
                                drawLineRoute(resultado.dataLine, idRoute);
                                drawPointsRoute(resultado.dataPoint, idRoute);
                            }
                        }
                    });
                } else {
                    if (connectionMap()) {
                        clearLienzoRouteByItems(item);
                    }
                }
            }
        }
    });

    var panelMenu = Ext.create('Ext.form.Panel', {
        region: 'north',
        items: [{
                xtype: 'toolbar',
                padding: '5 0 0 60',
                border: '0 0 3 0',
                style: {
                    background: '#3A8144',
                    borderColor: '#FFBF00',
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
            background: 'white',
        },
        padding: '6 6 6 4',
        text: '<img src="img/opciones.png" width="120" height="30"/>',
        menu: [
            {text: 'Información', handler: function () {
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
        items: [menuUsuario, '-',
            {
                xtype: 'button',
                arrowAlign: 'bottom',
                tooltip: 'Dar click para visualizar',
                text: '<span style="color:#003F72"><img src="img/parada1.png" width="100" height="50"></span>',
                handler: function () {
                },
                listeners: {
                    click: function () {
                        if (bandera) {
                            this.setText('<span style="color:#003F72"><img src="img/parada2.png" width="100" height="50"></span>');
                            setEstation();
                            bandera = false;
                        } else {
                            this.setText('<span style="color:#003F72"><img src="img/parada1.png" width="100" height="50"></span>');
                            clearLienzoPointsRoute();
                            bandera = true;
                        }
                    }

                }
            }, '-',
            {
                xtype: 'button',
                tooltip: 'Dar click para visualizar',
                text: '<span style="color:#003F72"><img src="img/ruta1.png" width="100" height="50"></span>',
                handler: function () {
                },
                listeners: {
                    click: function () {
                        if (bandera1) {
                            this.setText('<span style="color:#003F72"><img src="img/ruta2.png" width="100" height="50"></span>');
                            bandera1 = false;
                            setRoute();
                            clearVehiclesByRoute();

                        } else {
                            this.setText('<span style="color:#003F72"><img src="img/ruta1.png" width="100" height="50"></span>');
                            bandera1 = true;
                            clearLienzoLineRoute();
                            clearMarks();
                            setVehicle();
                        }
                    }

                }
            },
            {
                xtype: 'label',
                html: '<b><span class="btn-menu">Rutas:</span></b>'
            }, '-',
            {
                xtype: 'combo',
                width: '50%',
                labelWidth: 20,
                store: storeAuxRoute,
                fieldLabel: '<img src="img/buscar3.png"/>',
                displayField: 'todo',
                labelSeparator: '',
                typeAhead: false,
                hideTrigger: true,
                emptyText: 'Ruta',
                listConfig: {
                    loadingText: 'Buscando...',
                    emptyText: 'No ha encontrado resultados parecidos.',
                    // Custom rendering template for each item
                    getInnerTpl: function () {
                        return '<b>{ruta}';
                    }
                },
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
            }, '-',
            {
                iconCls: 'icon-localizame',
                text: '<img src="img/marker.png"/><b><span class="btn-menu">Ubicar mi Posición</span></b>',
                tooltip: 'Ubicar mi Posición',
                style: {
                    background: '#E0E0E0',
                },
                handler: function () {
                    getLocation();
                }
            }

        ]
    });

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

    storeAuxRoute.load();
    loadMap();

});
function setEstation() {
    if (connectionMap()) {
        if (idRoute !== '') {
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