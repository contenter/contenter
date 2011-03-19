$(document).ready(function(){
//===========On country change
    $("#country_id").change( function(){
        var country_id = $(this).val();
        //alert("ssss");
        $.getJSON("/user/ccountry/"+country_id, {}, answer);
        function answer(res) {
   	  	$("#gateway_id").html(res.newlist);
             };
        return false;
    });


    $("#gateway_id").change( function(){
        var gateway_id = $(this).val();
        $.get("/user/cgateway/"+gateway_id,
                function answer(response) {
               $("#gateway-element").html(response);
        });
        return false;
    });

//             $.get("/comment/child/"+cid,
//function(data) {
//    $(mmm).find('.subreply').html(data);
//        })


});