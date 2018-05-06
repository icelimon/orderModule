
$(document).ready(function(){

    $(".dropdown").hover(            

        function() {

            $('.dropdown-menu', this).not('.in .dropdown-menu').stop( true, true ).slideDown("fast");

            $(this).toggleClass('open');        

        },

        function() {

            $('.dropdown-menu', this).not('.in .dropdown-menu').stop( true, true ).slideUp("fast");

            $(this).toggleClass('open');       

        }

    );

    $(function(){
     $('.datepicker').datepicker({
      format: 'd-M-yy',
      todayHighlight: true,
      autoclose: true
  });
 });

//document ready end
});