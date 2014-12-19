var map;

var lienzoPointRoute;
var lienzoLineRoute;
var lienzoVehicle;
var markerInicioFin;
var marker;
var lienzoLocalizar;
var longitudKBus = -79.20733;
var latitudKBus = -3.9912;
var zoomKBus = 14;

function loadMap() {
    if (connectionMap()) {
        Ext.onReady(function () {
            toMercator = OpenLayers.Projection.transforms['EPSG:900913']['EPSG:4326'];
            lienzoLocalizar = new OpenLayers.Layer.Vector('Direcciones');
            var options = {
                controls: [
                    new OpenLayers.Control.Navigation({dragPanOptions: {enableKinetic: true}}),
                    new OpenLayers.Control.Zoom(),
                    new OpenLayers.Control.KeyboardDefaults(),
                    new OpenLayers.Control.LayerSwitcher()
                ],
                units: 'm',
                numZoomLevels: 22,
                maxResolution: 'auto'
            };
            map = new OpenLayers.Map('map', options);
            // Mapa sobre el que se trabaja
            var osm = new OpenLayers.Layer.OSM();
            var gmap = new OpenLayers.Layer.Google("Google Streets");
            var ghyb = new OpenLayers.Layer.Google(
                    "Google Hybrid",
                    {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 22}
            );
            map.addLayers([osm, gmap, ghyb]);
            map.addLayer(lienzoLocalizar);
            // Centrar el Mapa
            var lonLat = new OpenLayers.LonLat(longitudKBus, latitudKBus).transform(new OpenLayers.Projection("EPSG:4326"),
                    map.getProjectionObject());
            map.setCenter(lonLat, zoomKBus);
            map.events.register('click', map, function (e) {
                var coord = map.getLonLatFromViewPortPx(e.xy);
                var aux = new OpenLayers.Geometry.Point(coord.lon, coord.lat);
                aux.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
            });
            var styleVehicle = new OpenLayers.StyleMap({
                externalGraphic: "${iconLast}",
                graphicWidth: 16,
                graphicHeight: 16,
                fillOpacity: 0.85,
                idCompanyLast: "${idCompanyLast}",
                companyLast: "${scompanyLast}",
                idDeviceLast: "${idDeviceLast}",
                muniRegLast: "${muniRegLast}",
                dateTimeLast: "${dateTimeLast}",
                speedLast: "${speedLast}",
                addressLast: "${addressLast}",
                label: "..${muniRegLast}",
                fontColor: "${favColor}",
                fontSize: "13px",
                fontFamily: "Times New Roman",
                fontWeight: "bold",
                labelAlign: "${align}",
                labelOffset: new OpenLayers.Pixel(0, -20)
            });

            var styleRoute = new OpenLayers.StyleMap({
                fillOpacity: 0.7,
                pointRadius: 8,
                idPunto: "${idPunto}",
                geo: '${geo}',
                punto: "${punto}",
                ordPt: "${ordPt}",
                label: "${idPunto}",
                dir: "${dir}",
                fontColor: "white",
                fillColor: "${color}", //#003DF5
                strokeColor: "#FFFFFF",
                strokeOpacity: 0.7,
                fontSize: "12px",
                fontFamily: "Times New Roman",
                fontWeight: "bold"
            });
            lienzoPointRoute = new OpenLayers.Layer.Vector('Puntos de Ruta', {
                eventListeners: {
                    featureselected: function (evt) {
                        var feature = evt.feature;
                        var punto = feature.attributes.punto;
                        var geo = feature.attributes.geo;
                        var dir = feature.attributes.dir;
                        var contenidoAlternativo =
                                "<table>" +
                                "<tr><td><b>Parada:</b></td><td>" + punto.toString() + "</td></tr>" +
                                "<tr><td><b>Dirección:</b></td><td>" + dir.toString() + "</td></tr>" +
                                "</table>";
                        var popup = new OpenLayers.Popup.FramedCloud("popup",
                                OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
                                new OpenLayers.Size(200, 60),
                                contenidoAlternativo,
                                null,
                                true, function (evt) {
                                    feature.popup.destroy();
                                }
                        );
                        popup.setBackgroundColor('#dbe6f3');
                        feature.popup = popup;
                        feature.attributes.poppedup = true;
                        map.addPopup(popup);
                    },
                    featureunselected: function (evt) {
                        var feature = evt.feature;
                        map.removePopup(feature.popup);
                        //feature.popup.destroy();
                        feature.popup = null;
                    }
                },
                styleMap: styleRoute
            });
            lienzoPointRoute.id = 'pointLayer';
            lienzoVehicle = new OpenLayers.Layer.Vector("Vehiculos", {
                eventListeners: {
                    featureselected: function (evt) {
                        onVehiculoSelect(evt);
                    },
                    featureunselected: function (evt) {
                        onVehiculoUnselect(evt);
                    }
                },
                styleMap: styleVehicle
            });
            lienzoVehicle.id = 'vehicleLayer';
            var selectFeatures = new OpenLayers.Control.SelectFeature(
                    [lienzoPointRoute, lienzoVehicle], {
                hover: false,
                autoActivate: true
            });

            lienzoLineRoute = new OpenLayers.Layer.Vector("Linea de Ruta");
            markerInicioFin = new OpenLayers.Layer.Markers("Inicio-Fin");
            marker = new OpenLayers.Layer.Markers("Inicio-Fin");
            map.addLayers([
                lienzoVehicle,
                lienzoLineRoute,
                lienzoPointRoute,
                markerInicioFin,
                marker,
            ]);
            map.addControl(selectFeatures);
            selectFeatures.activate();

        });
    } else {
        Ext.getCmp('panel-map').add({
            region: 'center',
            xtype: 'image',
            src: 'img/no_network.png'

        });
    }
}
function onVehiculoSelect(evt) {
    var feature;
    if (evt.feature === undefined) {
        feature = evt;
    } else {
        feature = evt.feature;
    }

    var muniRegLast = feature.attributes.muniRegLast;
    var placaLast = feature.attributes.placaLast;
    var dateTimeLast = feature.attributes.dateTimeLast;
    var speedLast = feature.attributes.speedLast;
    var addressLast = feature.attributes.addressLast;
    if (addressLast === "") {
        addressLast = "No Definida.";
    }

    var contenidoAlternativo =
            "<table>" +
            "<tr><td><b>Registro Municipal: </b></td><td>" + muniRegLast + "</td></tr>" +
            "<tr><td><b>Placa: </b></td><td>" + placaLast + "<br>" +
            "<tr><td><b>Fecha y Hora: </b></td><td>" + dateTimeLast + "</td></tr>" +
            "<tr><td><b>Velocidad: </b></td><td>" + speedLast + " Km/h</td></tr>" +
            "</table>";

    var popup = new OpenLayers.Popup.FramedCloud("popup",
            OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
            new OpenLayers.Size(255, 125),
            contenidoAlternativo,
            null,
            true, function () {
                map.removePopup(feature.popup);
                feature.attributes.poppedup = false;
            }
    );

    popup.setBackgroundColor('#add2ed');
    feature.popup = popup;
    feature.attributes.poppedup = true;
    map.addPopup(popup);
}

function onVehiculoUnselect(evt) {
    var feature;
    if (evt.feature === undefined) {
        feature = evt;
    } else {
        feature = evt.feature;
    }
    map.removePopup(feature.popup);
    feature.popup.destroy();
    feature.attributes.poppedup = false;
    feature.popup = null;
}

//Grafica los vehiculos luego de consultar a la BD
function addVehiculosToCanvas(cordGrap) {
    for (var i = 0; i < cordGrap.length; i++) {
        // Extraigo columnas
        var datosVeh = cordGrap[i];
        var idVehicle = datosVeh.idVehicleLast;
        //Extracción dependiendo del Layer
        var vehicleFeature = lienzoVehicle.getFeatureById('last' + idVehicle);
        //Crear un nuevo elemento para el taxi que no existe
        if (vehicleFeature === null) {
            // Coordenadas
            var x = datosVeh.longitud;
            var y = datosVeh.latitud;
            // Posicion lon : lat
            var point = new OpenLayers.Geometry.Point(x, y);
            // Transformacion de coordendas
            point.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
            var dateTimeLast = new Date(datosVeh.fechaHora);

            vehicleFeature = new OpenLayers.Feature.Vector(point, {
                iconLast: 'img/' + datosVeh.icono,
                placaLast: datosVeh.placa,
                muniRegLast: datosVeh.regMunicipal,
                dateTimeLast: Ext.Date.format(dateTimeLast, 'Y-m-d H:i:s'),
                speedLast: datosVeh.velocidad,
                favColor: 'blue',
                align: "lt"
            });

            // Se coloca el ID de veh�culo a la imagen            
//            vehicleFeature.id = 'last' + idVehicle;
            //Se añade a la capa que corresponda
            lienzoVehicle.addFeatures([vehicleFeature]);
        } else {
            // Comprobar si los datos graficados estan desactualizados
            var dateTimeLast = new Date(datosVeh.fechaHora);
            if (vehicleFeature.attributes.hora !== Ext.Date.format(datosVeh.dateTimeLast, 'H:i:s')) {
                var poppedup = false;
                poppedup = vehicleFeature.attributes.poppedup;

                // Nuevo punto
                var newPoint = new OpenLayers.LonLat(datosVeh.longitud, datosVeh.latitud);
                newPoint.transform(new OpenLayers.Projection("EPSG:4326"),
                        new OpenLayers.Projection("EPSG:900913"));
                // Asignamos icono y Movemos el vehiculo 
                vehicleFeature.attributes.iconLast = "img/" + datosVeh.icono;
                vehicleFeature.move(newPoint);

                if (poppedup) {
                    onVehiculoUnselect(vehicleFeature);
                    // Actualizamos Datos
                    vehicleFeature.attributes.dateTimeLast = Ext.Date.format(dateTimeLast, 'Y-m-d H:i:s');
                    vehicleFeature.attributes.speedLast = datosVeh.velocidad;

                    onVehiculoSelect(vehicleFeature);
                } else {
                    // Actualizamos Datos
                    vehicleFeature.attributes.dateTimeLast = Ext.Date.format(dateTimeLast, 'Y-m-d H:i:s');
                    vehicleFeature.attributes.speedLast = datosVeh.velocidad;
                }
            }
        }
    }
}

function centrarMapa(ln, lt, zoom) {
    var lonlatCenter = new OpenLayers.LonLat(ln, lt);
    map.setCenter(lonlatCenter, zoom);
}

function drawLineRoute(json, idRuta, color) {
    markerStartFinish(json);
    var puntosRuta = new Array();
    for (var i = 0; i < json.length; i++) {
        var pt = new OpenLayers.Geometry.Point(json[i].longitud, json[i].latitud);
        pt.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
        puntosRuta.push(pt);
    }
    var ruta = new OpenLayers.Geometry.LineString(puntosRuta);
    //Estilo de Linea de Recorrido
    var style = {
        strokeColor: color,
        strokeOpacity: 1,
        strokeWidth: 4
    };
    var lineFeature = new OpenLayers.Feature.Vector(ruta, null, style);
    lienzoLineRoute.addFeatures([lineFeature]);
    for (var i = 0; i < showRouteMap.length; i++) {
        if (showRouteMap[i][0] === idRuta) {
            showRouteMap[i][1] = lineFeature;
        }
    }
}
function drawPointsRoute(coordPuntos, idRuta) {
    var features = new Array();
    for (var i = 0; i < coordPuntos.length; i++) {
        var dataRuta = coordPuntos[i];
        var pt = new OpenLayers.Geometry.Point(dataRuta.longitud, dataRuta.latitud);
        pt.transform(new OpenLayers.Projection("EPSG:4326"),
                new OpenLayers.Projection("EPSG:900913"));
        var puntoMap = new OpenLayers.Feature.Vector(pt, {
            idPunto: dataRuta.idEstacion,
            geo: dataRuta.codigo,
            punto: dataRuta.estacion,
            dir: dataRuta.direccion,
            color: 'red',
            icono: 'img/parada_bus.png',
            poppedup: false
        });
        features.push(puntoMap);
    }
    lienzoPointRoute.addFeatures(features);
    for (var i = 0; i < showRouteMap.length; i++) {
        if (showRouteMap[i][0] === idRuta) {
            showRouteMap[i][2] = features;
        }
    }
}
function markerStartFinish(json) {
    var size = new OpenLayers.Size(32, 32);
    var iconIni = new OpenLayers.Icon(
            'img/inicio_ruta.png',
            size, null, 0);
    var iconFin = new OpenLayers.Icon(
            'img/fin_ruta.png',
            size, null, 0);
    markerInicioFin.clearMarkers();
    var pInicio = new OpenLayers.LonLat(json[0].longitud, json[0].latitud);
    pInicio.transform(new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection("EPSG:900913"));
    markerInicioFin.addMarker(new OpenLayers.Marker(pInicio, iconIni));
    var filFin = json[json.length - 1];
    var pFin = new OpenLayers.LonLat(filFin.longitud, filFin.latitud);
    pFin.transform(new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection("EPSG:900913"));
    markerInicioFin.addMarker(new OpenLayers.Marker(pFin, iconFin));
}

function localizarDireccion(ln, lt, zoom) {
    var punto = new OpenLayers.Geometry.Point(ln, lt);
    punto.transform(new OpenLayers.Projection("EPSG:4326"),
            map.getProjectionObject());
    map.setCenter(punto, zoom);

    var pulsate = function (feature) {
        var point = feature.geometry.getCentroid(),
                bounds = feature.geometry.getBounds(),
                radius = Math.abs((bounds.right - bounds.left) / 2),
                count = 0,
                grow = 'up';

        var resize = function () {
            if (count > 16) {
                clearInterval(window.resizeInterval);
            }
            var interval = radius * 0.03;
            var ratio = interval / radius;
            switch (count) {
                case 4:
                case 12:
                    grow = 'down';
                    break;
                case 8:
                    grow = 'up';
                    break;
            }
            if (grow !== 'up') {
                ratio = -Math.abs(ratio);
            }
            feature.geometry.resize(1 + ratio, point);
            lienzoLocalizar.drawFeature(feature);
            count++;
        };
        window.resizeInterval = window.setInterval(resize, 50, point, radius);
    };

    lienzoLocalizar.removeAllFeatures();
    var circle = new OpenLayers.Feature.Vector(
            OpenLayers.Geometry.Polygon.createRegularPolygon(
                    new OpenLayers.Geometry.Point(punto.x, punto.y),
                    50,
                    40,
                    0
                    ),
            {}, {
        fillColor: '#000',
        fillOpacity: 0.1,
        strokeWidth: 0
    });

    lienzoLocalizar.addFeatures([
        new OpenLayers.Feature.Vector(
                punto,
                {},
                {
                    graphicName: 'cross',
                    strokeColor: '#f00',
                    strokeWidth: 2,
                    fillOpacity: 0,
                    pointRadius: 10
                }
        ),
        circle
    ]);
    map.zoomToExtent(lienzoLocalizar.getDataExtent());
    pulsate(circle);
}
function clearMapa() {
    for (var i = 0; i < showRouteMap.length; i++) {

        clearMarks();
        lienzoLineRoute.destroyFeatures(showRouteMap[i][1]);
        lienzoPointRoute.destroyFeatures(showRouteMap[i][2]);

    }
}
function clearLienzoRoute() {
    clearLienzoLineRoute();
    clearLienzoPointsRoute();
}
function clearLienzoPointsRoute() {
    lienzoPointRoute.destroyFeatures();
    clearPopups();
}
function clearLienzoLineRoute() {
    lienzoLineRoute.destroyFeatures();
}
function clearVehiclesByRoute() {
    lienzoVehicle.destroyFeatures();
    clearPopups();
}
function clearVehicles(records) {
    for (var i = 0; i < records.length; i++) {
        var vehicleFeature = lienzoVehicle.getFeatureById('last' + records[i].idVehicleLast);
        if (vehicleFeature !== null) {
            lienzoVehicle.removeFeatures(vehicleFeature);
        }
    }
}
function clearPopups() {
    if (map.popups.length === 1) {
        map.removePopup(map.popups[0]);
    }
}
function clearMarks() {
    markerInicioFin.clearMarkers();
}