jQuery(document).ready(function() {
  "use strict";
  var changeMade = false;

  $(window).on('beforeunload', function(){
	  if( changeMade && "nav-menus.php" !== args.changeCheck)
			  return 'Are you sure you want to leave?';

  });

  $('#caweb-options-form').submit(function(){ changeMade = false; this.submit(); });

  // Reset Fav Icon
  $('#resetFavIcon').click(function() {
    var ico = args.defaultFavIcon;
    var icoName = ico.substring( ico.lastIndexOf('/') + 1 );

    $('input[type="text"][name="ca_fav_ico"]').val(icoName);
    $('input[type="hidden"][name="ca_fav_ico"]').val(ico);
    $('#ca_fav_ico_img').attr('src', ico);

    changeMade = true;
  });

  // If Google Translate is set to Custom, show extra options
  $('#ca_google_trans_enabled_custom, label[for="ca_google_trans_enabled_custom"]').click(function(){
    $('#ca_google_trans_enabled_custom_extras').collapse('toggle');
  });

});
