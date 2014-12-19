var winConsultas;
var formConsultas;
var val = 0;
var labelDatos = Ext.create('Ext.form.Label', {
    html: '<center><b>INFORMACIÓN:</b></center>',
    style: {
        color: '#000000',
    }
});
Ext.onReady(function () {
    formConsultas = Ext.create('Ext.form.Panel', {
        region: 'center',
        width: '100%', bodyPadding: '15 15 15 15',
        items: [
            {xtype: 'fieldset',
                columnWidth: 0.5,
                title: 'Información Vehículo',
                collapsible: true,
                defaults: {anchor: '100%'},
                layout: 'anchor',
                items: [
                    {
                        xtype: 'label',
                        text: 'Parametro de Búsqueda',
                    },
                    {
                        xtype: 'radiogroup',
                        columns: 2,
                        items: [
                            {boxLabel: 'Registro Municipal', name: 'rb', inputValue: '1'},
                            {boxLabel: 'Placa', name: 'rb', inputValue: '2', checked: true}, ],
                        listeners: {
                            change: function (field, newValue, oldValue) {
                                if (parseInt(newValue['rb']) === 1) {
                                    val = 1;
                                } else {
                                    val = 2;

                                }
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        name: 'parametro',
                        id: 'parametro',
                        fieldLabel: 'Name',
                        allowBlank: false  // requires a non-empty value
                    },
                    {
                        xtype: 'button',
                        text: 'Buscar',
                        style: {
                            background: 'red',
                        },
                        handler: function () {
                            var datos;
                            var parametro = Ext.getCmp('parametro').getValue();
                            if (parametro !== '') {
                                if (val === 1) {
                                    $.ajax({
                                        type: 'GET',
                                        url: 'http://190.12.61.30:5801/K-Bus/webresources/com.kradac.kbus.rest.entities.vehiculos/regmuni=' + parametro, dataType: 'json',
                                        dataType:'json',
                                                dataType:'text',
                                                success: recuperar,
                                        error: function () {
                                            Ext.example.msg("Alerta", 'Problemas con el servidor');
                                        }
                                    });

                                    function recuperar(ajaxResponse, textStatus)
                                    {
                                        console.log('entro');
                                        datos = Ext.JSON.decode(ajaxResponse);
                                        cargar();
                                    }
                                    function cargar() {
                                        if (datos.length > 0) {
                                            var mensaje = '<center><b>Datos del Vehículo</b></center></br><table>'
                                            for (var i = 0; i < datos.length; i++) {
                                                mensaje = mensaje + '<tr><td>Propietario</td><td>' + datos[i].idConductor.apellidos + ' ' + datos[i].idConductor.nombres + '</td></tr>'
                                                        + '<tr><td>Operadora</td><td>' + datos[i].idEmpresa.empresa + '</td></tr>'
                                                        + '<tr><td>Placa</td><td>' + datos[i].placa + '</td></tr>'
                                                        + '<tr><td>Registro Municipal</td><td>' + datos[i].regMunicipal + '</td></tr>';
                                            }
                                            mensaje = mensaje + '</table></br><center>Denuncia a:</br>Unidad Municipal de Tránsito</br>Teléfono: 2587621</center>';
                                            labelDatos.setHtml(mensaje);
                                        } else {
                                            labelDatos.setHtml('<center><b>INFORMACIÓN:</b></center>');
                                            Ext.example.msg("Alerta", 'Datos Incorrectos');

                                        }
                                    }
                                } else {
                                    $.ajax({
                                        type: 'GET',
                                        url: 'http://190.12.61.30:5801/K-Bus/webresources/com.kradac.kbus.rest.entities.vehiculos/placa=' + parametro, dataType: 'json',
                                        dataType:'json',
                                                dataType:'text',
                                                success: recuperar,
                                        error: function () {
                                            Ext.example.msg("Alerta", 'Problemas con el servidor');
                                        }
                                    });

                                    function recuperar(ajaxResponse, textStatus)
                                    {
                                        console.log('entro');
                                        datos = Ext.JSON.decode(ajaxResponse);
                                        console.log(datos);
                                        cargar();
                                    }
                                    function cargar() {
                                        if (datos.length > 0) {
                                            var mensaje = '<center><b>Datos del Vehículo</b></center></br><table>'
                                            for (var i = 0; i < datos.length; i++) {
                                                mensaje = mensaje + '<tr><td>Propietario</td><td>' + datos[i].idConductor.apellidos + ' ' + datos[i].idConductor.nombres + '</td></tr>'
                                                        + '<tr><td>Operadora</td><td>' + datos[i].idEmpresa.empresa + '</td></tr>'
                                                        + '<tr><td>Placa</td><td>' + datos[i].placa + '</td></tr>'
                                                        + '<tr><td>Registro Municipal</td><td>' + datos[i].regMunicipal + '</td></tr>';
                                            }
                                            mensaje = mensaje + '</table></br><center>Denuncia a:</br>Unidad Municipal de Tránsito</br>Teléfono: 2587621</center>';
                                            labelDatos.setHtml(mensaje);
                                        } else {
                                            labelDatos.setHtml('<center><b>INFORMACIÓN:</b></center>');
                                            Ext.example.msg("Alerta", 'Datos Incorrectos');

                                        }
                                    }

                                }
                            } else {
                                Ext.example.msg("Alerta", 'Debe de ingresar datos');

                            }
                        }
                    },
                    {
                        xtype: 'fieldset',
                        columnWidth: 0.5,
                        title: 'Resultado',
                        collapsible: true,
                        defaults: {anchor: '100%'},
                        layout: 'anchor',
                        items: [
                            labelDatos
                        ]
                    }]}
        ],
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'bottom', ui: 'footer',
                items: ['->', {
                        iconCls: 'icon-cancel',
                        text: '<span class="btn-menu">Cancelar</span>',
                        tooltip: '<span class="tooltip">Cancelar</span>',
                        scope: this,
                        handler: function () {
                            winConsultas.hide();
                        }}
                ]
            }]
    });
});
function showWinConsultas() {
    if (!winConsultas) {
        winConsultas = Ext.create('Ext.window.Window', {
            layout: 'fit',
            title: '<div id="titulosForm">Consultas</div>',
            resizable: false,
            width: 400,
            height: 450,
            closeAction: 'hide',
            plain: false,
            items: formConsultas
        });
    }

    winConsultas.show();
}

