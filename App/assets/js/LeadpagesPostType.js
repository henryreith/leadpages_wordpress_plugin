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
                },complete: function(data){
                    $("#leadpages_my_selected_page").trigger('change');
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

        $body.on('change', '#leadpages_my_selected_page', function(){
            var selected_page_name = ($("option:selected", this).text());
            $("#leadpages_name").val(selected_page_name);
        });

        //$body.on('change', '#leadpageType', function(){
        //    hideSlugFor404andHome();
        //});

    });
}(jQuery));