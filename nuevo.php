<?php
if (isset($opc) && $opc>0) {
    conectar();
    $query="SELECT * FROM mapa_home WHERE id=".$opc."";
    $result = MYSQL_QUERY($query) or die(mysql_error());
    $id_f = mysql_result($result, 0,"id");
    $nombre_f = mysql_result($result, 0,"nombre");
    $direccion_f = mysql_result($result, 0,"direccion");
    $tipo_f = mysql_result($result, 0,"tipo");
    $lat_f = mysql_result($result, 0,"lat");
    $lng_f = mysql_result($result, 0,"lng");
    $transporte_f = mysql_result($result, 0,"transporte");
} else {    
    $id_f = 0;
    $id_f = $nombre_f = $direccion_f = $tipo_f = $lat_f = $lng_f = $transporte_f = '';
}
if ($opc<0) {
  $tipo_f="tm";
} else {
  $tipo_f="ua";
}
?>
<form id="myForm" action="includes/save.php" method="post" name="<?php  echo $id_f ?>"> 
  <input type="hidden" name="lat" value="<?php echo $lat_f ?>" /> 
  <input type="hidden" name="long" value="<?php echo $lng_f ?>" /> 
  <input type="hidden" name="tipo" value="<?php echo $tipo_f ?>" /> 
  <div id="contenido">
    <div class="titulo2">Dirección</div>
    <div class="search">
      <input id="address" name="direccion" class="titulo" placeholder="Enter your Address" type ="text"  value="<?php echo $direccion_f ?>"/>
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
          radius: 500,
          type: ['store']
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
       infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
        service.nearbySearch({
          location: latlng,
          radius: 500,
          type: ['store']
        }, callback);
      var marker = new google.maps.Marker({position:latlng,map:map,title:'title',draggable: true});
      google.maps.event.addListener(marker, 'dragend', function(){
        updatePosition(marker.getPosition());
      });
  }
  function updatePosition(latlng){
      $('#myForm').find('input[name="lat"]').val(latlng.lat());
      $('#myForm').find('input[name="long"]').val(latlng.lng());

  }
  function callback(results, status) {
      console.log(status);
      document.getElementById("cercanas").innerHTML="";
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            var div = document.getElementById('cercanas');
            div.innerHTML = div.innerHTML + '<li><img src="'+results[i]['icon']+'" width:10px ><b>'+results[i]['name']+'</b> - '+results[i]['vicinity']+'</li>'
            console.log(results[i]);
            createMarker(results[i]);
          }
        }
      }

      function createMarker(place) {
        var placeLoc = place.geometry.location;
        var marker = new google.maps.Marker({
          map: map,
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
      var iniciolat = ($('#myForm').find('input[name="lat"]').val())? $('#myForm').find('input[name="lat"]').val() : 'Rosario Argentina';
      var iniciolng = ($('#myForm').find('input[name="long"]').val())? $('#myForm').find('input[name="long"]').val() : 'Rosario Argentina';
        ($('#myForm').find('input[name="lat"]').val())? initialize(iniciolat, iniciolng) : busca(iniciolat);
        $('#search').bind('click',function(){
            busca($('#address').val()+" "+$('#address2').val());    
        });
        $(".elimina").each(function(){
            $(this).live('click', function(){
                var button = $(this), interval;
                if (EliminarDato(button.attr('id'), '<?php echo $opc ?>')) { 
                    $(this).parent().find('input[name='+button.attr('id')+']').val('');

                    $(this).hide();
                };
            });

        });
    });

/*
    $("form").submit(function() {
      if ($("input:first").val() == "correct") {
        $("span").text("Validated...").show();
        return true;
      }
      $("span").text("Not valid!").show().fadeOut(1000);
      return false;
    });*/
  /* attach a submit handler to the form */
  $("#myForm").submit(function(event) {
    //$("textarea[name=detalle]").val(CKEDITOR.instances.detalle.getData());
    /* stop form from submitting normally */
    event.preventDefault(); 
    
        
    /* get some values from elements on the page: */
    var $form = $( this ),
        nombre = $form.find( 'input[name="nombre"]' ).val(),
        direccion = $form.find( 'input[name="direccion"]' ).val(),
        tipo = $form.find( 'input[name="tipo"]' ).val(),
        lat = $form.find( 'input[name="lat"]' ).val(),
        lng = $form.find( 'input[name="long"]' ).val(),
        transporte = $form.find( 'input[name="transporte"]' ).val(),
        accion = $form.attr( 'name' ),
        url = $form.attr( 'action' );
    /* Send the data using post and put the results in a div */
    $.post( url, { nombre: nombre, tipo: tipo, direccion: direccion, lat: lat, lng: lng, transporte: transporte, accion: accion,},
      function( data ) {
          window.location='index.php?acc=0&tab='+data;
      }
    );
  });
</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>