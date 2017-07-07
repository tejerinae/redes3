<form id="myForm"> 
  <input type="hidden" name="lat" value="" /> 
  <input type="hidden" name="long" value="" /> 
  <div id="contenido">
    <div class="titulo2">Dirección</div>
    <div class="search">
      <input id="address" name="direccion" class="titulo" placeholder="Enter your Address" type ="text"  value=""/>
    </div>
  </div>
  <div id="contenido">
    <div class="search">
      <div class="titulo2">Ciudad</div>
      <input id="address2" name="localidad" class="titulo1" placeholder="Enter your Address" type ="text"  value="Rosario"/>
      <input id="search" type="button" class="carga2" value ="Buscar" />
      <div class="clear"></div>
    </div>
  </div>
  <div id="contenido">
    <div class="titulo2">Ubicacion</div>
    <div class="titulo">
      <div id="map_canvas"></div>
    </div>
  </div>
  <div id="contenido">
    <div class="titulo2">Ubicaciones cercanas</div>
    <div class="search">
      <ul id="cercanas"></ul>
    </div>
  </div>
</form>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCcfiQWvsxye1gLl1IIFiEnGeHrmZ_QOM&libraries=places"></script>
<script type="text/javascript">
  var map;
  function busca(address) {
    var geoCoder = new google.maps.Geocoder(address)
    var request = {address:address};
    geoCoder.geocode(request, function(result, status){
      var latlng = new google.maps.LatLng(result[0].geometry.location.lat(), result[0].geometry.location.lng());
      console.log(result[0].geometry.location.lat());
      console.log(result[0].geometry.location.lng());
      var myOptions = {
        zoom: 15,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
      infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
        service.nearbySearch({
          location: latlng,
          radius: 500
        }, callback);
      var marker = new google.maps.Marker({position:latlng,map:map,title:'title',draggable: true});
      google.maps.event.addListener(marker, 'dragend', function(){
        updatePosition(marker.getPosition());
      });
      updatePosition(latlng);

    });
  }
  
  function initialize(lat, lng){
    var latlng = new google.maps.LatLng(lat, lng);
      var myOptions = {
        zoom: 15,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
  }
  function updatePosition(latlng){
      $('#myForm').find('input[name="lat"]').val(latlng.lat());
      $('#myForm').find('input[name="long"]').val(latlng.lng());

  }
  function callback(results, status) {
      //console.log(status);
      document.getElementById("cercanas").innerHTML="";
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            var div = document.getElementById('cercanas');
            div.innerHTML = div.innerHTML + '<li><img src="'+results[i]['icon']+'" width:10px ><b>'+results[i]['name']+'</b> - '+results[i]['vicinity']+'</li>'
            //console.log(results[i]);
            createMarker(results[i]);
          }
        }
      }

      function createMarker(place) {
        var placeLoc = place.geometry.location;
        var marker = new google.maps.Marker({
          map: map,
          icon: {
            scaledSize: new google.maps.Size(18, 18)
          } ,
          scale: 5,
          position: place.geometry.location
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(place.name);
          infowindow.open(map, this);
        });
      }
</script>

<script>
    $(document).ready(function() {
      var iniciolat = -32.943988;
      var iniciolng = -60.65194839999998;
      initialize(iniciolat, iniciolng);
        $('#search').bind('click',function(){
            busca($('#address').val()+" "+$('#address2').val());    
        });
    });

  /* attach a submit handler to the form */
  $("#myForm").submit(function(event) {
    event.preventDefault(); 
  });
</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>