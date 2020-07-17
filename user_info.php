<script>
  function fnUsuarioInfoEnd() {
    localStorage['infoUsuario'] = null;
    window.location = '/mi-cuenta/';
  }
  function fnUsuarioInfoSet() {
    $(function() {
       $("a").attr('href', function(i, h) {
        if (h.toLowerCase().indexOf('user') !== -1 || h.toLowerCase().indexOf('mi-cuenta') !== -1) {
          return h + (h.indexOf('?') != -1 ? "&infoUsuario="+localStorage['infoUsuario'] : "?infoUsuario="+localStorage['infoUsuario']);          
        }else{
          return h;
        }
       });
    });
    $(function() {
       $("form").attr('action', function(i, h) {
        if (h.toLowerCase().indexOf('user') !== -1 || h.toLowerCase().indexOf('mi-cuenta') !== -1) {
          return h + (h.indexOf('?') != -1 ? "&infoUsuario="+localStorage['infoUsuario'] : "?infoUsuario="+localStorage['infoUsuario']);          
        }else{
          return h;
        }
       });
    });
  }
  $( document ).ready(function() {
    if(localStorage['infoUsuario'] == null || localStorage['infoUsuario'] == '' || localStorage['infoUsuario'] == 'null')return;
    setTimeout(function (){
      fnUsuarioInfoSet();
    }, 500);
  });
</script>