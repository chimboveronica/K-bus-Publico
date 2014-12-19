var winDenuncias;
var formDenuncias;
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
                    },
                    {
                        id: 'correo',
                        fieldLabel: 'Correo',
                        allowBlank: false,
                        blankText: 'Este campo es obligatorio',
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
                        valueField: 'data'
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
                                    {boxLabel: 'Placa', name: 'rb', inputValue: '2', checked: true}]
                            },
                            {
                                id: 'bus',
                                xtype: 'textfield',
                                name: 'name',
                                fieldLabel: 'Name',
                                allowBlank: false  // requires a non-empty value
                            },
                            {
                                xtype: 'button',
                                text: 'Buscar',
                            }
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
            height: 500,
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


