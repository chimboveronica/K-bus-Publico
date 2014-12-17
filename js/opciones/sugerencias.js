var winSugerencias;
var formSugerencias;
Ext.onReady(function () {

    formSugerencias = Ext.create('Ext.form.Panel', {
        region: 'center',
        width: '100%',
        bodyPadding: '15 15 15 15',
        items: [
            {
                xtype: 'label',
                text: 'Te parecio fácil utilizarla?',
            },
            {
                xtype: 'radiogroup',
                columns: 2,
                vertical: true,
                items: [
                    {boxLabel: 'Si', name: 'rb', inputValue: '1'},
                    {boxLabel: 'No', name: 'rb', inputValue: '2', checked: true}, ]
            },
            {
                xtype: 'label',
                text: 'Te parecio útil?',
            },
            {
                xtype: 'radiogroup',
                columns: 2,
                vertical: true,
                items: [
                    {boxLabel: 'Si', name: 'rb', inputValue: '1'},
                    {boxLabel: 'No', name: 'rb', inputValue: '2', checked: true}, ]
            },
            {
                xtype: 'label',
                text: 'Crees que se debe mejorar?',
            },
            {
                xtype: 'radiogroup',
                columns: 2,
                vertical: true,
                items: [
                    {boxLabel: 'Si', name: 'rb', inputValue: '1'},
                    {boxLabel: 'No', name: 'rb', inputValue: '2', checked: true}, ]
            },
            {
                xtype: 'label',
                text: 'Dejanos tu comentario por favor',
            },
            {
                xtype: 'textareafield',
                grow: true,
                name: 'message',
                anchor: '100%'
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
                        handler: onSendSugerencia,
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
                        handler: onResetSugerencia
                    }, {
                        iconCls: 'icon-cancel',
                        text: '<span class="btn-menu">Cancelar</span>',
                        tooltip: '<span class="tooltip">Cancelar</span>',
                        scope: this,
                        handler: function () {
                            winSugerencias.hide();
                        }}
                ]
            }]
    });
});
function showWinAdminSugerencias() {
    if (!winSugerencias) {
        winSugerencias = Ext.create('Ext.window.Window', {
            layout: 'fit',
            title: '<div id="titulosForm">Ayudanos a mejorar</div>',
            resizable: false,
            width: 300,
            height: 350,
            closeAction: 'hide',
            plain: false,
            items: formSugerencias
        });
    }
    onResetSugerencia();
    winSugerencias.show();
}





function onSendSugerencia() {
    var form = formSugerencias.getForm();
    if (form.isValid()) {
        formSugerencias.fireEvent('create', formSugerencias, form.getValues());
        form.reset();
        gridStorePerson.reload();
    } else {
        Ext.example.msg("Alerta", 'Llenar los campos marcados en rojo, correctamente ');

    }
}

function onResetSugerencia() {
    formSugerencias.down('#create').enable();
    formSugerencias.getForm().reset();
    formSugerencias.getForm().reset();
}


