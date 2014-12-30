
<!DOCTYPE html>
<html lang='es'>
    <head id="carga">
        <meta charset="utf-8">
        <title>K-bus-Usuario</title>
        <link rel="shortcut icon" href="img/bus.png" type="image/x-icon">
        <link rel="stylesheet" type="text/css" href="extjs-docs-5.0.0/extjs-build/build/packages/ext-theme-neptune/build/resources/ext-theme-neptune-all.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">

        <link rel="stylesheet" type="text/css" href="extjs-docs-5.0.0/extjs-build/build/examples/shared/example.css">
        <link rel="stylesheet" type="text/css" href="extjs-docs-5.0.0/extjs-build/build/examples/ux/grid/css/GridFilters.css">
        <link rel="stylesheet" type="text/css" href="extjs-docs-5.0.0/extjs-build/build/examples/ux/grid/css/RangeMenu.css">
        <script type="text/javascript" src="extjs-docs-5.0.0/extjs-build/build/examples/shared/include-ext.js"></script>
        <script type="text/javascript" src="extjs-docs-5.0.0/extjs-build/build/examples/shared/examples.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

        <!--Dependencias-->
        <script type="text/javascript" src="js/metodos/rest.js"></script>
        <script type="text/javascript" src="js/metodos/functions.js"></script>
        <script type="text/javascript" src="js/publico.js"></script>
        <script type="text/javascript" src="js/opciones/sugerencias.js"></script>
        <script type="text/javascript" src="js/opciones/consultas.js"></script>
        <script type="text/javascript" src="js/opciones/denuncias.js"></script>
        <script type="text/javascript" src="js/opciones/precios.js"></script>
        <script type="text/javascript" src="js/opciones/mensaje.js"></script>
        <script type="text/javascript" src="js/opciones/ayuda.js"></script>
        <script type="text/javascript" src="js/ext-lang-es.js"></script>
        <!--Fin de Dependencias-->

        <!--Mapa-->
        <script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
        <script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js"></script>
        <script type="text/javascript" src="js/mapa.js"></script>
        <!--Fin Mapa-->
    </head>
    <body oncontextmenu = "return false">        
        <script type="text/javascript">
            document.write('<div id="imagen">' +
                    '<img src="img/simbología.png"  width="80" height="80"/>' +
                    '</div>');
            document.write('<div id="imagenK">' +
                    '<img  src="img/credits.png"  width="150" height="50"/><img src="img/logos.png"  width="50" height="50"/>' +
                    '</div>');
            document.write(
                    '<a href="#" title="Dar Click" class="tooltip"><div id="Botones1"><img src="img/botones-01.png" onClick="showWinConsultas()"  width="80" height="80"/></div></a>\n\
                    <a href="#" title="Dar Click" class="tooltip"><div id="Botones2"><img src="img/botones-02.png" onClick="showWinMensaje()" width="80" height="80"/></div></a>\n\
                    <a href="#" title="Dar Click" class="tooltip"><div id="Botones3"><img src="img/botones-04.png"  onClick="showWinAdminDenuncias()" width="80" height="80"/></div></a>\n\
                    <a href="#" title="Dar Click" class="tooltip"><div id="Botones4"><img src="img/botones-03.png"  onClick="showWinPrecios()" width="80" height="80"/></div></a>');

        </script>
    </body>
</html>
