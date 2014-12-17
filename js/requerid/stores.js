var menuCoop;
var menuRoute;
var positionPoint = false;
var showCoopMap = new Array();
var showRouteMap = new Array();

Ext.define('busesModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'text'},
        {name: 'iconCls'},
        {name: 'id'},
        {name: 'leaf'}
    ],
    proxy: {
        type: 'ajax',
        url: 'php/tree/getTreeVehiculos.php',
        format: 'json'
    }
});

Ext.define('puntosModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'text'},
        {name: 'iconCls'},
        {name: 'id'},
        {name: 'leaf'}
    ],
    proxy: {
        type: 'ajax',
        url: 'php/tree/getTreePuntos.php',
        format: 'json'
    }
});

Ext.define('estacionesModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'text'},
        {name: 'iconCls'},
        {name: 'id'},
        {name: 'leaf'}
    ],
    proxy: {
        type: 'ajax',
        url: 'php/tree/getTreeEstaciones.php',
        format: 'json'
    }
});

var storeTreeBuses = Ext.create('Ext.data.TreeStore', {
    model: 'busesModel'
});

var storeTreePoints = Ext.create('Ext.data.TreeStore', {
    model: 'puntosModel'
});

var storeEstaciones = Ext.create('Ext.data.TreeStore', {
    model: 'estacionesModel'
});

var storeRoute = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboRutas.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'},
        {name: 'codRuta', type: 'string'}
    ]
});

var storeAuxRoute = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboRutasAux.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'},
        {name: 'codRuta', type: 'string'}
    ],
    listeners: {
        load: function (thisObject, records, successful, eOpts) {
            if (successful) {
                for (var i = 0; i < records.length; i++) {
                    var dataRoute = records[i].data;
                    showRouteMap[i] = [dataRoute.id, dataRoute.text, false];
                    menuRoute.add({itemId: dataRoute.id, text: dataRoute.text, checked: false});
                }
            } else {
                Ext.example.msg('Error', 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.');
            }
        }
    }
});

var storeSkyEvent = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEventos.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'},
        {name: 'color', type: 'string'}
    ]
});

var storeCompany = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEmpresas.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeAuxCompany = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEmpresasAux.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ],
    listeners: {
        load: function (thisObj, records, successful, eOpts) {
            if (successful && typeof idRolKBus !== 'undefined') {
                for (var i = 0; i < records.length; i++) {
                    var dataCoop = records[i].data;
                    if (idRolKBus === 4) {
                        showCoopMap[i] = [dataCoop.id, dataCoop.text, true];
                    } else {
                        showCoopMap[i] = [dataCoop.id, dataCoop.text, false];
                    }
                    if (idRolKBus !== 5) {
                        if (typeof usuarioKBus !== 'undefined') {
                            menuCoop.add({itemId: showCoopMap[i][0], text: showCoopMap[i][1], checked: showCoopMap[i][2]});
                        }
                    }
                }
                storeVehicle.load({
                    params: {
                        idCompany: 1
                    }
                });
            }
        }
    }
});

var storePoints = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboPuntos.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});


var storeTurn = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboTurnos.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeVehicle = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboVehiculos.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'},
        {name: 'idCompany', type: 'int'},
        {name: 'muniReg', type: 'string'}
    ]
});


var storeDevice = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEquipos.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeDeviceLibres = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEquipoLibres.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeCmdPred = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboCmdPred.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeDispatcher = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboDespachador.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storePerson = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboPersonas.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'},
        {name: 'textDocument', type: 'string'},
        {name: 'empresa', type: 'string'}
    ]
});

var storeTypeLicense = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboTipoLicencia.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storePenalty = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboSancion.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeTypeDevice = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboTipoEquipo.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeRouteService = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboServicioRuta.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeRolUser = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboRoles.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeDecision = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'No'},
        {id: 1, text: 'Si'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeHeader = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEncabezado.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'},
        {name: 'color', type: 'string'}
    ]
});

var storeStateBus = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEstadoBus.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeDisplay = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboDisplay.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeStateMechanic = Ext.create('Ext.data.Store', {
    autoLoad: true,
    autoDestroy: true,
    proxy: {
        type: 'ajax',
        url: 'php/gui/combobox/comboEstadoMecanico.php',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

/*
 * Store para Listas
 */

var storeDatoInvaList = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/listFilters/listDatoInvalido.php',
        reader: {
            type: 'array'
        }
    },
    fields: ['id', 'text']
});

var storeSancionesList = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/listFilters/listSanciones.php',
        reader: {
            type: 'array'
        }
    },
    fields: ['id', 'text']
});

var storeTypeDeviceList = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/listFilters/listTypeDevice.php',
        reader: {
            type: 'array'
        }
    },
    fields: ['id', 'text']
});

var storeTypeServiceList = Ext.create('Ext.data.Store', {
    autoDestroy: true,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: 'php/listFilters/listTypeService.php',
        reader: {
            type: 'array'
        }
    },
    fields: ['id', 'text']
});

var storeDecisionList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'No'},
        {id: 1, text: 'Si'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeActiveUser = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Bloqueado'},
        {id: 1, text: 'Activo'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeBatteyList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Bat. del Vehiculo'},
        {id: 1, text: 'Bat. del Equipo'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeGsmList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Sin Cobertura'},
        {id: 1, text: 'Con Cobertura'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeGpsList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Sin GPS'},
        {id: 1, text: 'Con GPS'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeIgnList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Apagado'},
        {id: 1, text: 'Encendido'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeRetenList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Libre'},
        {id: 1, text: 'En Reten'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeValidDispatchList = Ext.create('Ext.data.Store', {
    data: [
        {id: 0, text: 'Eliminado'},
        {id: 1, text: 'Válido'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});

var storeGender = Ext.create('Ext.data.Store', {
    data: [
        {id: 1, text: 'Masculino'},
        {id: 2, text: 'Femenino'}
    ],
    fields: [
        {name: 'id', type: 'int'},
        {name: 'text', type: 'string'}
    ]
});