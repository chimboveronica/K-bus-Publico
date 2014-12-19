/*Funciones para guardar Geolocalizacion de Usuario*/
function getLocationUser() {
    if (navigator.geolocation) {
        var position = navigator.geolocation.getCurrentPosition(showPositionUser, showError);
    } else {
        //x.innerHTML="Geolocation is not supported by this browser.";
        Ext.example.msg('Error', 'Geolocalizacion no es soportada por este navegador.');
    }
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

function connectionMap() {
    if (typeof OpenLayers !== 'undefined') {
        return true;
    } else {
        Ext.example.msg('Mensaje', 'El mapa se encuentra deshabilitado.');
        return false;
    }
}
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

