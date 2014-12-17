/*Funciones para guardar Geolocalizacion de Usuario*/
function getLocationUser() {
    if (navigator.geolocation) {
        var position = navigator.geolocation.getCurrentPosition(showPositionUser, showError);
    } else {
        //x.innerHTML="Geolocation is not supported by this browser.";
        Ext.example.msg('Error', 'Geolocalizacion no es soportada por este navegador.');
    }
}

function showPositionUser(position) {
    lonPos = position.coords.longitude;
    latPos = position.coords.latitude;

    document.getElementById("longitud").value = lonPos;
    document.getElementById("latitud").value = latPos;
}

/*Funciones para realizar Geolocalización*/
function getLocation() {
    if (connectionMap()) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            //x.innerHTML="Geolocation is not supported by this browser.";
            Ext.example.msg('Error', 'Geolocalizacion no es soportada por este navegador.');
        }
    }
}

function clearMap() {
    if (connectionMap()) {
        clearLienzoReport();
    }
}

function showPosition(position) {
    localizarDireccion(position.coords.longitude, position.coords.latitude, 17);
}

function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            //x.innerHTML="User denied the request for Geolocation."
            Ext.example.msg('Error', 'User denied the request for Geolocation.');
            break;
        case error.POSITION_UNAVAILABLE:
            //x.innerHTML="Location information is unavailable."
            Ext.example.msg('Error', 'Location information is unavailable.');
            break;
        case error.TIMEOUT:
            //x.innerHTML="The request to get user location timed out."
            Ext.example.msg('Error', 'The request to get user location timed out.');
            break;
        case error.UNKNOWN_ERROR:
            //x.innerHTML="An unknown error occurred."
            Ext.example.msg('Error', 'An unknown error occurred.');
            break;
    }
}

function checkVideo() {
    if (!!document.createElement('video').canPlayType) {
        var vidTest = document.createElement("video");
        oggTest = vidTest.canPlayType('video/ogg; codecs="theora, vorbis"');

        if (!oggTest) {
            h264Test = vidTest.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"');

            if (!h264Test) {
                document.getElementById("checkVideoResult").innerHTML = "Sorry. No video support.";
            } else {
                if (h264Test === "probably") {
                    document.getElementById("checkVideoResult").innerHTML = "Yeah! Full support!";
                } else {
                    document.getElementById("checkVideoResult").innerHTML = "Meh. Some support.";
                }
            }
        } else {
            if (oggTest === "probably") {
                document.getElementById("checkVideoResult").innerHTML = "Yeah! Full support!";
            } else {
                document.getElementById("checkVideoResult").innerHTML = "Meh. Some support.";
            }
        }
    } else {
        document.getElementById("checkVideoResult").innerHTML = "Sorry. No video support.";
    }
}

function connectionMap() {
    if (typeof OpenLayers !== 'undefined') {
        return true;
    } else {
        Ext.example.msg('Mensaje', 'El mapa se encuentra deshabilitado.');
        return false;
    }
}

//Obtener Resolucion de Panatalla del Equipo
function getResolution() {
    var height = 0;
    var width = 0;
    if (self.screen) {     // for NN4 and IE4
        width = screen.width;
        height = screen.height;
    } else if (self.java) {   // for NN3 with enabled Java
        var jkit = java.awt.Toolkit.getDefaultToolkit();
        var scrsize = jkit.getScreenSize();
        width = scrsize.width;
        height = scrsize.height;
    }
    var whRes = new Array();
    whRes.push(width);
    whRes.push(height);
    return whRes;
}

/**
 * Convierte en json lo que este almacenado en un store
 * @param {store} store description
 */
function getJsonOfStore(store) {
    var datar = new Array();
    var jsonDataEncode = "";
    //var storeData = store.getRange();
    var storeData = store.getModifiedRecords();
    //console.info(storeData);
    for (var i = 0; i < storeData.length; i++) {
        datar.push(storeData[i].data);
    }
    jsonDataEncode = Ext.JSON.encode(datar);

    return jsonDataEncode;
}

function formatSkyEvent(val) {
    for (var i = 0; i < storeSkyEvent.data.length; i++) {
        if (storeSkyEvent.getAt(i).data.id === val) {
            return '<span style="color:' + storeSkyEvent.getAt(i).data.color + '">' + storeSkyEvent.getAt(i).data.text + '</span>';
        }
    }
}

function formatTypeLicense(val) {
    for (var i = 0; i < storeTypeLicense.data.length; i++) {
        if (storeTypeLicense.getAt(i).data.id === val) {
            return storeTypeLicense.getAt(i).data.text;
        }
    }
}

function formatCompany(val) {
    for (var i = 0; i < storeCompany.data.length; i++) {
        if (storeCompany.getAt(i).data.id === val) {
            return storeCompany.getAt(i).data.text;
        }
    }
}

function formatAuxCompany(val) {
    for (var i = 0; i < storeAuxCompany.data.length; i++) {
        if (storeAuxCompany.getAt(i).data.id === val) {
            return storeAuxCompany.getAt(i).data.text;
        }
    }
}

function formatRolUser(val) {
    for (var i = 0; i < storeRolUser.data.length; i++) {
        if (storeRolUser.getAt(i).data.id === val) {
            return storeRolUser.getAt(i).data.text;
        }
    }
}

function formatPerson(val) {
    for (var i = 0; i < storePerson.data.length; i++) {
        if (storePerson.getAt(i).data.id === val) {
            return storePerson.getAt(i).data.text;
        }
    }
}

function formatRoute(val) {
    for (var i = 0; i < storeAuxRoute.data.length; i++) {
        if (storeAuxRoute.getAt(i).data.id === val) {
            return storeAuxRoute.getAt(i).data.text;
        }
    }
}

function formatHeader(val) {
    for (var i = 0; i < storeHeader.data.length; i++) {
        if (storeHeader.getAt(i).data.id === val) {
            return '<span style="color:' + storeHeader.getAt(i).data.color + '">' + storeHeader.getAt(i).data.text + '</span>';
        }
    }
}

function formatStateBus(val) {
    for (var i = 0; i < storeStateBus.data.length; i++) {
        if (storeStateBus.getAt(i).data.id === val) {
            return storeStateBus.getAt(i).data.text;
        }
    }
}

function formatDisplay(val) {
    for (var i = 0; i < storeDisplay.data.length; i++) {
        if (storeDisplay.getAt(i).data.id === val) {
            return storeDisplay.getAt(i).data.text;
        }
    }
}

function formatStateMechanic(val) {
    for (var i = 0; i < storeStateMechanic.data.length; i++) {
        if (storeStateMechanic.getAt(i).data.id === val) {
            return storeStateMechanic.getAt(i).data.text;
        }
    }
}

function formatPenalty(val) {
    for (var i = 0; i < storePenalty.data.length; i++) {
        if (storePenalty.getAt(i).data.id === val) {
            return storePenalty.getAt(i).data.text;
        }
    }
}

function formatStatePenalty(val) {
    if (val === 1) {
        return '<span style="color:red;">Sancionado</span>';
    } else {
        return '<span style="color:green;">Sin Sancionar</span>';
    }
}

function formatDecision(val) {
    if (val === 1) {
        return '<span style="color:green;">Si</span>';
    } else {
        return '<span style="color:red;">No</span>';
    }
}

function formatStateUser(val) {
    if (val === 0) {
        return '<img src="img/icon_desconect.png">';
    } else {
        return '<img src="img/icon_conect.png">';
    }
}

function formatStateConect(val) {
    if (val > 3) {
        return '<img src="img/icon_desconect.png">';
    } else {
        return '<img src="img/icon_conect.png">';
    }
}

function formatStateConectFastrack(val) {
    if (val > 15) {
        return '<img src="img/icon_desconect.png">';
    } else {
        return '<img src="img/icon_conect.png">';
    }
}

function formatGsm(val) {
    if (val === 0) {
        return '<span style="color:red;">Sin Cobertura</span>';
    } else {
        return '<span style="color:green;">Con Cobertura</span>';
    }
}
function formatIgn(val) {
    if (val === 0) {
        return '<span style="color:red;">Apagado</span>';
    } else {
        return '<span style="color:green;">Encendido</span>';
    }
}

function formatGps(val) {
    if (val === 0) {
        return '<span style="color:red;">Sin GPS</span>';
    } else {
        return '<span style="color:green;">Con GPS</span>';
    }
}

function formatBateria(val) {
    if (val === 1) {
        return '<span style="color:green;">Bat. del Vehiculo</span>';
    } else {
        return '<span style="color:red;">Bat. del Equipo</span>';
    }
}

function formatBateriaPapeleta(val) {
    if (val === '1') {
        return '<span style="color:green;">Bat. del Vehiculo</span>';
    } else if (val === '0') {
        return '<span style="color:red;">Bat. del Equipo</span>';
    } else {
        return '';
    }
}

function formatBatIgnGsmGps3(val) {
    if (val === 1) {
        return '<span style="color:green;">SI</span>';
    } else {
        return '<span style="color:red;">NO</span>';
    }
}
function formatEstadoLabores(val) {
    if (val === 1) {
        return '<span style="color:green;">Inicio de Jornada</span>';
    } else {
        return '<span style="color:red;">Fin de Jornada</span>';
    }
}

function formatSpeed(val) {
    if (val > 60) {
        return '<span style="color:orange;">' + val + '</span>';
    } else if (val > 90) {
        return '<span style="color:red;">' + val + '</span>';
    } else {
        return '<span style="color:blue;">' + val + '</span>';
    }
}

function formatInterval(val) {
    if (val < 1) {
        return '<span style="color:yellow;">' + val + ' minutos';
    } else if (val < 30) {
        return '<span style="color:orange;">' + val + ' minutos</span>';
    } else if (val < 60) {
        return '<span style="color:blue;">' + val + ' minutos</span>';
    } else {
        return '<span style="color:red;">' + val + ' minutos';
    }
}

function formatPanic(val) {
    if (val === 0) {
        return '<span style="color:blue;">Normal</span>';
    } else {
        return '<span style="color:red;">Panico</span>';
    }
}

function formatStateMec(val) {
    if (val === 'OK') {
        return '<span style="color:green;">' + val + '</span>';
    } else {
        return val;
    }
}

function formatReten(val) {
    if (val === 1) {
        return '<span style="color:orange;">En Reten</span>';
    } else {
        return '<span style="color:blue;">Libre</span>';
    }
}

function formatActiveUser(val) {
    for (var i = 0; i < storeActiveUser.data.length; i++) {
        if (storeActiveUser.getAt(i).data.id === val) {
            if (val === 1) {
                return '<span style="color:green;">' + storeActiveUser.getAt(i).data.text + '</span>';
            } else {
                return '<span style="color:red;">' + storeActiveUser.getAt(i).data.text + '</span>';
            }
        }
    }
}

function formatoAtraso(val) {
    if (val === "00:00:00") {
        return '<span style="color:blue">' + val + '</span>';
    } else {
        if (val > "00:00:00") {
            return '<span style="color:red">' + val + '</span>';
        } else {
            return '<span style="color:green">' + val + '</span>';
        }
    }
}

function formatoAtrasoTotal(val) {
    if (val >= "00:00:00") {
        if (val === "00:00:00") {
            return '<span style="color:green">' + val + '</span>';
        } else if (val > "00:02:00") {
            return '<span style="color:red">' + val + '</span>';
        } else {
            return '<span style="color:blue">' + val + '</span>';
        }
    } else {
        if (val > "-00:02:00") {
            return '<span style="color:red">' + val + '</span>';
        } else {
            return '<span style="color:blue">' + val + '</span>';
        }
    }
}

function formatTypePenalty(val) {
    switch (val) {
        case 1:
            return "Ninguna";
            break;
        case 2:
            return '<span style="color:#5b2cd3;">Informe</span>';
            break;
        case 3:
            return '<span style="color:#5b2cd3;">Vuelta</span>';
            break;
        case 4:
            return '<span style="color:#5b2cd3;">Casa</span>';
            break;
    }
}

function formatValidDispatch(val) {
    if (val === 1) {
        return '<span style="color:blue;">Valido</span>';
    } else {
        return '<span style="color:red;">Eliminado</span>';
    }
    return val;
}
/*
 function check_cedula(b) {
 var h = b.split("");
 var c = h.length;
 if (c === 10) {
 var f = 0;
 var a = (h[9] * 1);
 for (i = 0; i < (c - 1); i++) {
 var g = 0;
 if ((i % 2) !== 0) {
 f = f + (h[i] * 1);
 } else {
 g = h[i] * 2;
 if (g > 9) {
 f = f + (g - 9);
 } else {
 f = f + g;
 }
 }
 }
 var e = f / 10;
 e = Math.floor(e);
 e = (e + 1) * 10;
 var d = (e - f);
 if ((d === 10 && a === 0) || (d === a)) {
 return true;
 } else {
 return false;
 }
 } else {
 return false;
 }
 }
 */
function check_cedula(cedula) {
    var h = cedula.split("");
    var tamanoCedula = h.length;
    if (tamanoCedula === 10) {
        var digitoProvincia = cedula.substring(0, 2);
        var digitoTres = cedula.substring(2, 3);
        if ((digitoProvincia > 0 && digitoProvincia < 25) && digitoTres < 6) {
            var f = 0;
            var a = (h[9] * 1);
            for (i = 0; i < (tamanoCedula - 1); i++) {
                var g = 0;
                if ((i % 2) !== 0) {
                    f = f + (h[i] * 1);
                } else {
                    g = h[i] * 2;
                    if (g > 9) {
                        f = f + (g - 9);
                    } else {
                        f = f + g;
                    }
                }
            }
            var e = f / 10;
            e = Math.floor(e);
            e = (e + 1) * 10;
            var d = (e - f);
            if ((d === 10 && a === 0) || (d === a)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function formatDecimal(val) {

    return parseFloat(val, 2);

}

function getNavigator() {
    if (navigator.appName === "Microsoft Internet Explorer") {
        return 'img/explorer.png';
        //return '<img src="img/explorer.png" width="16" height="16">';
    } else {
        if (navigator.userAgent.indexOf('Chrome') !== -1) {
            return 'img/chrome.png';
            //return '<img src="img/chrome.png" width="16" height="16">';
        } else if (navigator.userAgent.indexOf('Firefox') !== -1) {
            return 'img/firefox.png';
            //return '<img src="img/firefox.png" width="16" height="16">';
        } else if (navigator.userAgent.indexOf('Apple') !== -1) {
            return 'img/safari.png';
            //return '<img src="img/safari.png" width="16" height="16">';
        } else {
            return 'Desconocido';
        }
    }
}

function checkRolSesion(idRolKBus) {
    Ext.Ajax.request({
        url: 'php/login/checkLogin.php',
        params: {
            idRolKBus: idRolKBus
        },
        success: function(response) {
            if (parseInt(response.responseText) === 1) {
                window.location = 'index.php';
            }
        }
    });

    setTimeout(function() {
        checkRolSesion(idRolKBus);
    }
    , 3 * 1000);
}

function formatTmpDes(val) {
    if (val < 3) {
        return '<span style="color:green;">' + val + '</span>';
    } else if (val === 3) {
        return '<span style="color:orange;">' + val + '</span>';
    } else {
        return '<span style="color:red;">' + val + '</span>';
    }


}
function formatTmpDesFast(val) {
    if (val < 15) {
        return '<span style="color:green;">' + val + '</span>';
    } else if (val == 15) {
        return '<span style="color:orange;">' + val + '</span>';
    } else {
        return '<span style="color:red;">' + val + '</span>';
    }


}

function reloadStore(store, cant) {
    setTimeout(function() {
        reloadStore(store, cant);
        store.reload();
    }
    , cant * 1000);
}

function exportExcelReportManual(recordsArray, muniReg, codRuta, fechaIni, horaIni) {
    var a = document.createElement('a');
    var dataType = 'data:application/vnd.ms-excel';
    var tiLetra = 'FontB11';
    var nomPunto, numPunto, timeDifView;
    var stringTableDiv = "<body onload='javascript:window.print()' on>" +
            "<font face='" + tiLetra + "'><table><tr><th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONTROL DE RUTA</th></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INSTITUCI&Oacute;N:</b> UMTTTSV</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UNIDAD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> " + muniReg + "</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RUTA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> " + codRuta + "</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HORA INICIO:</b> " + horaIni + "</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FECHA:</b> " + fechaIni + "</td></tr>" +
            "<tr></tr>";

    for (var i = 0; i < recordsArray.length; i++) {
        stringTableDiv += "<tr>";
        nomPunto = recordsArray[i].data.pointView.substr(0, 7);
        nomPunto = nomPunto.replace(" ", "&nbsp;");
        timeDifView = recordsArray[i].data.timeDifView.substr(0, 1);
        if (timeDifView === "0") {
            timeDifView = "&nbsp;" + recordsArray[i].data.timeDifView;
        } else if (timeDifView === "N") {
            timeDifView = recordsArray[i].data.timeDifView.substr(0, 9);
        } else {
            timeDifView = recordsArray[i].data.timeDifView;
        }

        numPunto = recordsArray[i].data.orderPointView;
        if (numPunto < 10) {
            numPunto = "&nbsp;" + numPunto;
        }

        stringTableDiv += "<td>" + numPunto + " "
                + nomPunto + ' ' + recordsArray[i].data.timeDebView + ""
                + ' ' + recordsArray[i].data.timeLlView + ' ' + timeDifView + "</td>";
        stringTableDiv += "</tr>";
    }

    stringTableDiv += "</table></font></body>";

    var table_html = stringTableDiv.replace(/ /g, '%20');

    var horaArchivo = new Date(fechaIni + " " + horaIni);
    var hour = horaArchivo.getHours();
    var minute = horaArchivo.getMinutes();
    var second = horaArchivo.getSeconds();

    a.href = dataType + ', ' + table_html;
    //setting the file name
    a.download = 'Reporte Manual ' + muniReg + ' ' + hour + "h" + minute + "m" + second + 's.xls';

    //triggering the function
    a.click();
}

function exportExcelReportManualSpeed(recordsArray, muniReg, codRuta, fechaIni, horaIni) {
    var a = document.createElement('a');
    var dataType = 'data:application/vnd.ms-excel';
    var tiLetra = 'FontB11';
    var nomPunto, numPunto, timeDifView;
    var stringTableDiv = "<body onload='javascript:window.print()' on>" +
            "<font face='" + tiLetra + "'><table><tr><th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONTROL DE RUTA</th></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INSTITUCI&Oacute;N:</b> UMTTTSV</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UNIDAD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> " + muniReg + "</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RUTA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> " + codRuta + "</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HORA INICIO:</b> " + horaIni + "</td></tr>" +
            "<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FECHA:</b> " + fechaIni + "</td></tr>" +
            "<tr></tr>";

    for (var i = 0; i < recordsArray.length; i++) {
        stringTableDiv += "<tr>";
        nomPunto = recordsArray[i].data.pointView.substr(0, 7);
        nomPunto = nomPunto.replace(" ", "&nbsp;");
        timeDifView = recordsArray[i].data.timeDifView.substr(0, 1);
        if (timeDifView === "0") {
            timeDifView = "&nbsp;" + recordsArray[i].data.timeDifView;
        } else if (timeDifView === "N") {
            timeDifView = recordsArray[i].data.timeDifView.substr(0, 9);
        } else {
            timeDifView = recordsArray[i].data.timeDifView;
        }

        numPunto = recordsArray[i].data.orderPointView;
        if (numPunto < 10) {
            numPunto = "&nbsp;" + numPunto;
        }

        stringTableDiv += "<td>" + numPunto + " "
                + nomPunto + ' ' + recordsArray[i].data.timeDebView + ""
                + ' ' + recordsArray[i].data.timeLlView + ' ' + recordsArray[i].data.velocidad + "</td>";
        stringTableDiv += "</tr>";
    }

    stringTableDiv += "</table></font></body>";

    var table_html = stringTableDiv.replace(/ /g, '%20');

    var horaArchivo = new Date(fechaIni + " " + horaIni);
    var hour = horaArchivo.getHours();
    var minute = horaArchivo.getMinutes();
    var second = horaArchivo.getSeconds();

    a.href = dataType + ', ' + table_html;
    //setting the file name
    a.download = 'Reporte Manual ' + muniReg + ' ' + hour + "h" + minute + "m" + second + 's.xls';

    //triggering the function
    a.click();
}

function getDataOfFeature(listPoint) {
    var auxPointsRoute = "";
    for (var i = 0; i < listPoint.length; i++) {
        var pt = new OpenLayers.Geometry.Point(listPoint[i].x, listPoint[i].y);
        var pointConvert = pt.transform(new OpenLayers.Projection('EPSG:900913'), new OpenLayers.Projection('EPSG:4326'));
        auxPointsRoute += (Math.round(pointConvert.y * 100000000)) / 100000000 + "," + (Math.round(pointConvert.x * 100000000)) / 100000000 + ";";
    }
    return auxPointsRoute;
}

function exportExcel(grid, nameFile, nameSheet, titleSheet) {
    var columnsArray = grid.getView().grid.columns;
    var storeData = grid.getStore();

    if (storeData.getCount() > 0) {
        var a = document.createElement('a');
        var data_type = 'data:application/vnd.ms-excel';
        var numFil = storeData.data.length;
        var numCol = columnsArray.length;
        var tiLetra = 'Calibri';
        var table_div = "<?xml version='1.0'?><?mso-application progid='Excel.Sheet'?><Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'><DocumentProperties xmlns='urn:schemas-microsoft-com:office:office'><Author>KRADAC SOLUCIONES TECNOLÃ“GICAS</Author><LastAuthor>KRADAC SOLUCIONES TECNOLÃ“GICAS</LastAuthor><Created>2014-08-20T15:33:48Z</Created><Company>KRADAC</Company><Version>15.00</Version>";
        table_div += "</DocumentProperties> " +
                "<Styles> " +
                "<Style ss:ID='Default' ss:Name='Normal'>   <Alignment ss:Vertical='Bottom'/>   <Borders/>   <Font ss:FontName='" + tiLetra + "' x:Family='Swiss' ss:Size='11' ss:Color='#000000'/>   <Interior/>   <NumberFormat/>   <Protection/>  </Style>  " +
                "<Style ss:ID='encabezados'><Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>   <Font ss:FontName='Calibri' x:Family='Swiss' ss:Size='11' ss:Color='Blue' ss:Bold='1'/>  </Style>  " +
                "<Style ss:ID='fString'><NumberFormat ss:Format='@'/></Style>" +
                "<Style ss:ID='fInteger'><Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/><NumberFormat ss:Format='0'/></Style>" +
                "<Style ss:ID='fDouble'><NumberFormat ss:Format='Fixed'/></Style>" +
                "<Style ss:ID='fDateTime'><NumberFormat ss:Format='yyyy\-mm\-dd hh:mm:ss;@'/></Style>" +
                "<Style ss:ID='fDate'><NumberFormat ss:Format='yyyy\-mm\-dd;@'/></Style>" +
                "<Style ss:ID='fTime'><NumberFormat ss:Format='hh:mm:ss;@'/></Style>" +
                "</Styles>";
        //Definir el numero de columnas y cantidad de filas de la hoja de calculo (numFil + 2))
        table_div += "<Worksheet ss:Name='" + nameSheet + "'>"; //Nombre de la hoja
        table_div += "<Table ss:ExpandedColumnCount='" + numCol + "' ss:ExpandedRowCount='" + (numFil + 2) + "' x:FullColumns='1' x:FullRows='1' ss:DefaultColumnWidth='100' ss:DefaultRowHeight='15'>";
        for (var i = 0; i < columnsArray.length; i++) {
            table_div += "<Column ss:AutoFitWidth='0' ss:Width='" + (columnsArray[i].text.length * 10) + "'/>";
        }
        table_div += "<Row ss:AutoFitHeight='0'><Cell ss:MergeAcross='" + (numCol - 1) + "' ss:StyleID='encabezados'><Data ss:Type='String'>" + titleSheet + "</Data></Cell></Row>";
        table_div += "<Row ss:AutoFitHeight='0'>";
        for (var i = 0; i < columnsArray.length; i++) {
            table_div += "<Cell ss:StyleID='encabezados'><Data ss:Type='String'>" + columnsArray[i].text + "</Data></Cell>";
        }
        table_div += "</Row>";
        for (var i = 0; i < storeData.getCount(); i++) {
            table_div += "<Row ss:AutoFitHeight='0'>";
            for (var j = 0; j < columnsArray.length; j++) {
                if (!columnsArray[j].renderer) {
                    table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + storeData.data.items[i].get(columnsArray[j].dataIndex) + "</Data></Cell>";
                } else {
                    var cadena = String(columnsArray[j].renderer).split(' ');

                    if (cadena[1] === '(k,b,g,c,e,a,j){var') {
                        table_div += "<Cell ss:StyleID='fInteger'><Data ss:Type='Number'>" + (i + 1) + "</Data></Cell>";
                    }
                    if (cadena[1] === '(c){return') {
                        table_div += "<Cell ss:StyleID='fDate'><Data ss:Type='DateTime'>" + Ext.Date.format(storeData.data.items[i].get(columnsArray[j].dataIndex), 'Y-m-d') + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatoAtrasoTotal(val)') {
                        table_div += "<Cell ss:StyleID='fTime'><Data ss:Type='DateTime'>" + formatoAtrasoTotal(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatStatePenalty(val)') {
                        table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + formatStatePenalty(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatValidDispatch(val)') {
                        table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + formatValidDispatch(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatPerson(val)') {
                        table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + formatPerson(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatCompany(val)') {
                        table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + formatCompany(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatRolUser(val)') {
                        table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + formatRolUser(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                    if (cadena[1] === 'formatActiveUser(val)') {
                        table_div += "<Cell ss:StyleID='fString'><Data ss:Type='String'>" + formatActiveUser(storeData.data.items[i].get(columnsArray[j].dataIndex)) + "</Data></Cell>";
                    }
                }
            }
            table_div += "</Row>";
        }

        table_div += "</Table> </Worksheet></Workbook>";
        var table_xml = table_div.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_xml;
        a.download = nameFile + '.xml';
        a.click();
    }
}

function distanceBetweenPoints(lat1, lon1, lat2, lon2) {
    rad = function(x) {
        return x * Math.PI / 180;
    };
    var R = 6378.137;                          //Radio de la tierra en km
    var dLat = rad(lat2 - lat1);
    var dLong = rad(lon2 - lon1);

    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLong / 2) * Math.sin(dLong / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c;
    return d.toFixed(2);                      //Retorna dos decimales
}