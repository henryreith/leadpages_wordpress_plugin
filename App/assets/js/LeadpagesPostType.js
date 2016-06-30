(function ($) {

    $( document ).ready( function() {

        function getLeadPages(){

            var start = new Date().getTime();
            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'get_pages_dropdown',
                    id: ajax_object.id
                },
                beforeSend: function (data) {

                },
                success: function (response) {
                    var end = new Date().getTime();
                    console.log('milliseconds passed', end - start);
                    //var pageType = $("#leadpageType").val();
                    //if(pageType == 'nf' || pageType == 'fp'){
                    //
                    //    $("#leadpage-slug").hide();
                    //}else{
                    //    $("#leadpage-slug").show();
                    //}

                    $(".leadpagesSlug").show();
                    $(".ui-loading").hide();
                    $(".leadpageType").show();
                    $(".leadpagesSelect").show();
                    $("#leadpages_my_selected_page").append(response);
                }
            });
        }

        getLeadPages();

        var $body = $('body');

        function hideSlugFor404andHome(){
            var pageType = $("#leadpageType").val();
            if(pageType == 'nf' || pageType == 'fp'){
                $("#leadpage-slug").hide();
            }else{
                $("#leadpage-slug").show();
            }
        }

        //$body.on('change', '#leadpageType', function(){
        //    hideSlugFor404andHome();
        //});

    });
}(jQuery));