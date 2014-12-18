var winDenuncias;
var formDenuncias;
Ext.onReady(function () {

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
                        fieldLabel: 'Cedula',
                        name: 'field1'
                    }, {
                        fieldLabel: 'Nombre',
                        name: 'field2'
                    },
                    {
                        fieldLabel: 'Teléfono',
                        name: 'field2'
                    },
                    {
                        fieldLabel: 'Correo',
                        name: 'field2'
                    },
                    {
                        fieldLabel: 'Asunto',
                        name: 'field2'
                    },
                    {
                        xtype: 'textareafield',
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
                                text: 'Parametro de Búsqueda',
                            },
                            {
                                xtype: 'radiogroup',
                                columns: 2,
                                items: [
                                    {boxLabel: 'Registro Municipal', name: 'rb', inputValue: '1'},
                                    {boxLabel: 'Placa', name: 'rb', inputValue: '2', checked: true}, ]
                            },
                            {
                                xtype: 'textfield',
                                name: 'name',
                                fieldLabel: 'Name',
                                allowBlank: false  // requires a non-empty value
                            },
                            {
                                xtype: 'button',
                                text: 'Buscar'
                            },
                            {
                                // Fieldset in Column 1 - collapsible via toggle button
                                xtype: 'fieldset',
                                columnWidth: 0.5,
                                title: 'Resultado',
                                collapsible: true,
                                defaults: {anchor: '100%'},
                                layout: 'anchor',
                                items: [
                                    {
                                        xtype: 'label',
                                        text: 'Parametro de Búsqueda',
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'checkboxfield',
                        boxLabel: 'Está seguro/a de realizar está denuncias?',
                        name: 'topping',
                        inputValue: '2',
                        checked: true,
                        id: 'checkbox2'
                    }
                ]
            },
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
            height: 580,
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
        formDenuncias.fireEvent('create', formDenuncias, form.getValues());
        form.reset();
        gridStorePerson.reload();
    } else {
        Ext.example.msg("Alerta", 'Llenar los campos marcados en rojo, correctamente ');

    }
}

function onResetDenuncia() {
    formDenuncias.down('#create').enable();
    formDenuncias.getForm().reset();
    formDenuncias.getForm().reset();
}


