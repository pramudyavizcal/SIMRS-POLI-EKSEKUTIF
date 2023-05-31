$(document).ready(function(){
  var menu = $("#menuAktifUser").val();
  var subMenu = $("#subMenuAktifUser").val();
  
  if (menu != "") {
    $(".nav-link").addClass("collapsed");
    $(".nav-content").addClass("collapsed");
    $(".nav-content").removeClass("show");
    $(".sub-menu").removeClass("active");
    if (menu == 1) {
      $("#menu-"+menu).removeClass("collapsed");
    } else {
      $("#menu-"+menu).removeClass("collapsed");
      $("#menu-content-"+menu).removeClass("collapsed");
      $("#menu-content-"+menu).addClass("show");
      $("#sub-menu-"+menu+"-"+subMenu).addClass("active");
    }
  }
});