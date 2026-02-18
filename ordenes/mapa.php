<!DOCTYPE html>
<html>
  <head>
    <title>Mapa simple</title>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCHPzAithvpryPL-RIgv_S7nAXTzotVd5s&callback=initMap" async defer></script>
  </head>
  <body>
    
        <div id="map" style="height:500px;width:100%;"></div>

        <script>
      function initMap() {
        // Coordenadas para Torreón
        const ubicacion = { lat: 25.5428, lng: -103.4068 }; 
        
        // Inicializa el mapa buscando el elemento con ID 'map'. 
        // Esto ahora funcionará porque el script se ejecuta DESPUÉS de que se crea el div.
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 12,
          center: ubicacion,
        });
        
        // Añade un marcador
        new google.maps.Marker({ position: ubicacion, map: map });
      }
    </script>
  </body>
</html>