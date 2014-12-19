var winPrecios;

function showWinPrecios() {
    if (!winPrecios) {
        var panelWinAbout = Ext.create('Ext.form.Panel', {
            id: 'panel-about',
            labelAlign: "left",
            bodyStyle: "padding:5px 5px 0",
            labelWidth: 60,
            width: 360,
            items: [{
                    html: '<div id="div-info-about"><center> ' +
                            '<h2>Tarifas del transporte Urbano Loja </h2><br>' +
                            '<img src="img/bus1.jpg" width="150px" height="80px"></center>' +
                            '<h3>Tarifa Normal $0,25.</h3><br>' +
                            '<h3>Tarifa Especial  $0,13.</h3><br>' +
                            '<p align=right>K-BUS KRADAC Cia. Ltda. Todos los derechos reservados.<br>' +
                            '<a href="http://www.kradac.com">www.kradac.com</a></p>' +
                            '</div>'
                }],
            buttonAlign: "center",
            buttons: [{
                    text: "OK",
                    handler: function() {
                        winPrecios.hide();
                        spot.hide();
                    }
                }]
        });

        winPrecios = Ext.create('Ext.window.Window', {
            layout: "fit",
            iconCls: 'icon-about',
            title: "Tarifas Transporte Loja",
            resizable: false,
            width: 300,
            height: 320,
            closeAction: "hide",
            plain: true,
            items: [panelWinAbout],
            listeners: {
                close: function(panel, eOpts) {
                    spot.hide();
                }
            }
        });

        Ext.create('Ext.fx.Anim', {
            target: panelWinAbout,
            duration: 2000,
            from: {
                opacity: 0 // Transparent
            },
            to: {
                opacity: 1 // Opaque
            }
        });
    }
    
    winPrecios.show();
}