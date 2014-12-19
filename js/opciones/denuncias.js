var winDenuncias;
var formDenuncias;
var value;
var idVehiculo;
var latitud;
var longitud;
var labelDatosVehicle = Ext.create('Ext.form.Label', {
    html: '<center><b>INFORMACIÓN:</b></center>',
    style: {
        color: '#000000',
    }
});
Ext.onReady(function () {
    var motivos = Ext.create('Ext.data.Store', {
        fields: ['id', 'mes'],
        data: [{id: 1, data: 'Exceso de Velocidad'},
            {id: 2, data: 'Mal trato'},
            {id: 3, data: 'Irrespeto de Parada'},
            {id: 4, data: 'Correteo entre buses'},
            {id: 5, data: 'Otro'}
        ]
    });
    formDenuncias = Ext.create('Ext.form.Panel', {
        region: 'center',
        autoScroll: true,
        width: '100%',
        bodyPadding: '15 15 15 15',
        items: [
            {
                xtype: 'fieldset',
                columnWidth: 0.5,
                title: 'Información Denunciante',
                collapsible: true,
                defaultType: 'textfield',
                defaults: {anchor: '100%'},
                layout: 'anchor',
                items: [{
                        id: 'cedula',
                        fieldLabel: 'Cedula',
                        allowBlank: false,
                        blankText: 'Este campo es obligatorio',
                        vtype:'cedulaValida'
                    }, {
                        id: 'nombre',
                        fieldLabel: 'Nombre',
                        allowBlank: false,
                        blankText: 'Este campo es obligatorio',
                    },
                    {
                        id: 'telefono',
                        fieldLabel: 'Teléfono',
                        allowBlank: false,
                        blankText: 'Este campo es obligatorio',
                        vtype:'numeroTelefono'
                    },
                    {
                        id: 'correo',
                        fieldLabel: 'Correo',
                        allowBlank: false,
                        blankText: 'Este campo es obligatorio',
                        vtype:'emailNuevo'
                    },
                    {
                        xtype: 'combobox',
                        id: 'asunto',
                        fieldLabel: 'Asunto',
                        store: motivos, //asignandole el store
                        emptyText: 'Seleccione el motivo',
                        triggerAction: 'all',
                        editable: false,
                        displayField: 'data',
                        valueField: 'data',
                        forceselection:true
                    },
                    {
                        xtype: 'textareafield',
                        id: 'observacion',
                        grow: true,
                        fieldLabel: 'Observación',
                        name: 'message',
                        anchor: '100%'
                    },
                    {
                        xtype: 'fieldset',
                        columnWidth: 0.5,
                        title: 'Información Vehículo',
                        collapsible: true,
                        defaults: {anchor: '100%'},
                        layout: 'anchor',
                        items: [
                            {
                                xtype: 'label',
                                text: 'Busqueda por'
                            },
                            {
                                xtype: 'radiogroup',
                                columns: 2,
                                items: [
                                    {boxLabel: 'Registro Municipal', name: 'rb', inputValue: '1'},
                                    {boxLabel: 'Placa', name: 'rb', inputValue: '2', checked: true}],
                                listeners: {
                                    change: function (field, newValue, oldValue) {
                                        if (parseInt(newValue['rb']) === 1) {
                                            value = 1;
                                        } else {
                                            value = 2;

                                        }
                                    }
                                }
                            },
                            {
                                id: 'bus',
                                xtype: 'textfield',
                                name: 'bus',
                                fieldLabel: 'Placa/Registro',
                            },
                            {
                                xtype: 'button',
                                text: 'Buscar',
                                style: {
                                    background: '#3A8144'
                                },
                                handler: function () {
                                    var datos;
                                    var parametro = Ext.getCmp('bus').getValue();
                                    if (parametro !== '') {
                                        if (value === 1) {
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
                                                        idVehiculo = datos[i].idEmpresa.idVehiculo;
                                                        mensaje = mensaje + '<tr><td>Propietario</td><td>' + datos[i].idConductor.apellidos + ' ' + datos[i].idConductor.nombres + '</td></tr>'
                                                                + '<tr><td>Operadora</td><td>' + datos[i].idEmpresa.empresa + '</td></tr>'
                                                                + '<tr><td>Placa</td><td>' + datos[i].placa + '</td></tr>'
                                                                + '<tr><td>Registro Municipal</td><td>' + datos[i].regMunicipal + '</td></tr>';
                                                    }
                                                    mensaje = mensaje + '</table></br><center>Denuncia a:</br>Unidad Municipal de Tránsito</br>Teléfono: 2587621</center>';
                                                    labelDatosVehicle.setHtml(mensaje);
                                                } else {
                                                    labelDatosVehicle.setHtml('<center><b>INFORMACIÓN:</b></center>');
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
                                                        idVehiculo = datos[i].idEmpresa.idVehiculo;
                                                        mensaje = mensaje + '<tr><td>Propietario</td><td>' + datos[i].idConductor.apellidos + ' ' + datos[i].idConductor.nombres + '</td></tr>'
                                                                + '<tr><td>Operadora</td><td>' + datos[i].idEmpresa.empresa + '</td></tr>'
                                                                + '<tr><td>Placa</td><td>' + datos[i].placa + '</td></tr>'
                                                                + '<tr><td>Registro Municipal</td><td>' + datos[i].regMunicipal + '</td></tr>';
                                                    }
                                                    mensaje = mensaje + '</table></br><center>Denuncia a:</br>Unidad Municipal de Tránsito</br>Teléfono: 2587621</center>';
                                                    labelDatosVehicle.setHtml(mensaje);
                                                } else {
                                                    labelDatosVehicle.setHtml('<center><b>INFORMACIÓN:</b></center>');
                                                    Ext.example.msg("Alerta", 'Datos Incorrectos');

                                                }
                                            }

                                        }
                                    } else {
                                        Ext.example.msg("Alerta", 'Debe de ingresar datos');

                                    }
                                }
                            }, labelDatosVehicle
                        ]
                    }
                ]
            }
        ],
        listeners: {
            create: function (form, data) {

            }
        },
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'bottom',
                ui: 'footer',
                items: ['->', {
                        iconCls: 'icon-add',
                        itemId: 'create',
                        text: '<span class="btn-menu">Enviar</span>',
                        scope: this,
                        tooltip: '<span class="tooltip">Crear Registro</span>',
                        handler: onSendDenuncia,
                        listeners: {
                            mouseover: function () {
                                this.setText('<span class="btn-menu-over">Crear</span>');
                            },
                            mouseout: function () {
                                this.setText('<span class="btn-menu">Crear</span>');
                            }
                        }
                    }, {
                        iconCls: 'limpiar',
                        text: '<span class="btn-menu">Limpiar</span>',
                        tooltip: '<span class="tooltip">Limpiar Campos</span>',
                        scope: this,
                        handler: onResetDenuncia
                    }, {
                        iconCls: 'icon-cancel',
                        text: '<span class="btn-menu">Cancelar</span>',
                        tooltip: '<span class="tooltip">Cancelar</span>',
                        scope: this,
                        handler: function () {
                            winDenuncias.hide();
                        }}
                ]
            }]
    });
});
function showWinAdminDenuncias() {
    if (!winDenuncias) {
        winDenuncias = Ext.create('Ext.window.Window', {
            layout: 'fit',
            title: '<div id="titulosForm">Denuncias</div>',
            resizable: false,
            width: 350,
            height: 540,
            closeAction: 'hide',
            plain: false,
            items: formDenuncias
        });
    }
    onResetDenuncia();
    winDenuncias.show();
}



function onSendDenuncia() {
    var form = formDenuncias.getForm();
    if (form.isValid()) {
        $.ajax({
            type: 'POST',
            contentType: 'application/json',
            url: 'http://190.12.61.30:5801/K-Bus/webresources/com.kradac.kbus.rest.entities.historic.denuncias',
            dataType: "json",
            data: formToJSON(),
            success: function (data, textStatus, jqXHR) {
                Ext.example.msg('Alerta', 'Se ingresaron los datos');
                onResetDenuncia();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('addWine error: ' + textStatus);
            }
        });
        function formToJSON() {
            return JSON.stringify({
                "cedula": Ext.getCmp('cedula').getValue(),
                "denunciante": Ext.getCmp('nombre').getValue(),
                "correo": Ext.getCmp('correo').getValue(),
                "idVehiculo": idVehiculo,
                "telefono": Ext.getCmp('telefono').getValue(),
                "motivo": Ext.getCmp('asunto').getValue(),
                "observacion": Ext.getCmp('observacion').getValue()
            });
        }
        ;

    } else {
        Ext.example.msg("Alerta", 'Llenar los campos marcados en rojo, correctamente ');

    }
}
function onSearch() {
    switch (parseInt(newValue['rblac'])) {
        case 1:
            break;
        case 2:

            break;
    }


}
function onResetDenuncia() {
    formDenuncias.down('#create').enable();
    formDenuncias.getForm().reset();
    formDenuncias.getForm().reset();
}


