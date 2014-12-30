var winMensaje;

function showWinMensaje() {
    if (!winMensaje) {
        var panelWinMensaje = Ext.create('Ext.form.Panel', {
            labelAlign: "left",
            bodyStyle: "padding:5px 5px 0",
            labelWidth: 60,
            width: 360,
            items: [{
                    html: '<div id="div-info-about"><center> ' +
                            '<img src="img/pagina_en_construccion.jpg" width="250" height="200"><br></center>' +
                            '<center><K-BUS KRADAC Cia. Ltda. <br>Todos los derechos reservados.<br>' +
                            '<a href="http://www.kradac.com">www.kradac.com</a></center>' +
                            '</div>'
                }],
            buttonAlign: "center",
            buttons: [{
                    text: "OK",
                    handler: function() {
                        winMensaje.hide();
                        spotM.hide();
                    }
                }]
        });

        winMensaje = Ext.create('Ext.window.Window', {
            layout: "fit",
            iconCls: 'icon-about',
            title: "Transporte de Loja",
            resizable: false,
            width: 300,
            height: 320,
            closeAction: "hide",
            plain: true,
            items: [panelWinMensaje],
            listeners: {
                close: function(panel, eOpts) {
                    spotM.hide();
                }
            }
        });

        Ext.create('Ext.fx.Anim', {
            target: panelWinMensaje,
            duration: 2000,
            from: {
                opacity: 0 // Transparent
            },
            to: {
                opacity: 1 // Opaque
            }
        });
    }
    
    winMensaje.show();
}