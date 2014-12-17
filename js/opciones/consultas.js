var winConsultas;
var formConsultas;
Ext.onReady(function () {

    formConsultas = Ext.create('Ext.form.Panel', {
        region: 'center',
        width: '100%',
        bodyPadding: '15 15 15 15',
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
                    }]}
        ],
    });
});
function showWinConsultas() {
    if (!winConsultas) {
        winConsultas = Ext.create('Ext.window.Window', {
            layout: 'fit',
            title: '<div id="titulosForm">Consultas</div>',
            resizable: false,
            width: 400,
            height: 350,
            closeAction: 'hide',
            plain: false,
            items: formConsultas
        });
    }

    winConsultas.show();
}

