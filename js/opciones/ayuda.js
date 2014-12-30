var winAyuda;

function showWinAyuda() {
    if (!winAyuda) {
        var panelWinAyuda = Ext.create('Ext.form.Panel', {
            labelAlign: "left",
            bodyStyle: "padding:5px 5px 0",
            width: 800,
            items: [{
                    html: '<img src="img/datos.png" width="900" height="400"><br></center>'
                            
                }],
            buttonAlign: "center",
            buttons: [{
                    text: "OK",
                    handler: function() {
                        winAyuda.hide();
                        spotA.hide();
                    }
                }]
        });

        winAyuda = Ext.create('Ext.window.Window', {
            layout: "fit",
            iconCls: 'icon-about',
            title: "Información",
            resizable: false,
            width: 900,
            height: 500,
            closeAction: "hide",
            plain: true,
            items: [panelWinAyuda],
            listeners: {
                close: function(panel, eOpts) {
                    spotA.hide();
                }
            }
        });

        Ext.create('Ext.fx.Anim', {
            target: panelWinAyuda,
            duration: 2000,
            from: {
                opacity: 0 // Transparent
            },
            to: {
                opacity: 1 // Opaque
            }
        });
    }
    
    winAyuda.show();
}