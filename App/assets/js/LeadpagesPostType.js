(function ($) {

    $( document ).ready( function() {

        function getLeadPages(){

            //var loading ='<div class="ui-loading"> <div class="ui-loading__dots ui-loading__dots--1"></div> <div class="ui-loading__dots ui-loading__dots--2"></div> <div class="ui-loading__dots ui-loading__dots--3"></div> </div>';
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
                    var pageType = $("#leadpageType").val();
                    var leadpagesSlug = $("#leadpage-slug");
                    if(pageType == 'nf' || pageType == 'fp'){
                        leadpagesSlug.hide();
                    }else{
                        leadpagesSlug.show();
                    }
                    $(".ui-loading").hide();
                    leadpagesSlug.show();
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

        $body.on('change', '#leadpageType', function(){
            hideSlugFor404andHome();
        });



    });
}(jQuery));