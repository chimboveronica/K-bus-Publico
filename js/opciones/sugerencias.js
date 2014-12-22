var winSugerencias;
var formSugerencias;
var pregunta1 = 0;
var pregunta2 = 0;
var pregunta3 = 0;

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
                    {boxLabel: 'Si', name: 'rb1', inputValue: '1'},
                    {boxLabel: 'No', name: 'rb1', inputValue: '2', checked: true},
                ],
                listeners: {
                    change: function (field, newValue, oldValue) {
                        if (parseInt(newValue['rb1']) === 1) {
                            pregunta1 = 1;
                        }
                    }
                }
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
                    {boxLabel: 'Si', name: 'rb2', inputValue: '1'},
                    {boxLabel: 'No', name: 'rb2', inputValue: '2', checked: true}, ],
                listeners: {
                    change: function (field, newValue, oldValue) {
                        if (parseInt(newValue['rb2']) === 1) {
                            pregunta2 = 1;
                        }
                    }
                }
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
                    {boxLabel: 'Si', name: 'rb3', inputValue: '1'},
                    {boxLabel: 'No', name: 'rb3', inputValue: '2', checked: true}, ],
                listeners: {
                    change: function (field, newValue, oldValue) {
                        if (parseInt(newValue['rb3']) === 1) {
                            pregunta3 = 1;
                        }
                    }
                }
            },
            {
                xtype: 'label',
                text: 'Dejanos tu comentario por favor',
            },
            {
                xtype: 'textareafield',
                grow: true,
                id: 'sugerencia',
                name: 'sugerencia', anchor: '100%'}


        ],
        listeners: {
            create: function (form, data) {

            }
        },
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'bottom', ui: 'footer',
                items: ['->', {iconCls: 'icon-add', itemId: 'create',
                        text: 'Enviar',
                        style: {
                            background: '#3A8144',
                        },
                        scope: this,
                        tooltip: '<span class="tooltip">Crear Registro</span>',
                        handler: onSendSugerencia,
                    }, {
                        iconCls: 'limpiar',
                        text: 'Limpiar',
                        style: {
                            background: '#3A8144',
                        },
                        tooltip: '<span class="tooltip">Limpiar Campos</span>',
                        scope: this,
                        handler: onResetSugerencia
                    }, {
                        iconCls: 'icon-cancel',
                        text: 'Cancelar',
                        style: {
                            background: '#3A8144',
                        },
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
    var form = formDenuncias.getForm();
    $.ajax({
        type: 'POST',
        contentType: 'application/json',
        url: 'http://190.12.61.30:5801K-Gestion/webresources/com.kradac.kgestion.rest.entities.sugerencias',
        dataType: "json",
        data: formToJSON(),
        success: function (data, textStatus, jqXHR) {
            Ext.example.msg('Alerta', 'Se ingresaron los datos');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('addWine error: ' + textStatus);
        }
    });
    function formToJSON() {
        return JSON.stringify({
            "pregunta1": pregunta1,
            "pregunta2": pregunta2,
            "pregunta3": pregunta3,
            "sugerencia": Ext.getCmp('sugerencia').getValue(),
        });
    }
    ;

}

function onResetSugerencia() {
    formSugerencias.down('#create').enable();
    formSugerencias.getForm().reset();
    formSugerencias.getForm().reset();
}


