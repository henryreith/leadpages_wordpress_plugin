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
                    var pageType = $("#leadpageType").val();
                    if(pageType == 'nf' || pageType == 'fp'){

                        $("#leadpage-slug").hide();
                    }else{
                        $("#leadpage-slug").show();
                    }

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

        $body.on('change', '#leadpageType', function(){
            var pageType = $("#leadpageType").val();
            hideSlugFor404andHome();
            if(pageType == 'fp' || $leadpageType == 'nf'){
                $(".leadpage_slug_error").remove();
            }
        });

        //hide preview button for Leadpages
        $("#preview-action").hide();

        $body.on('click', '#publish', function (e) {

            $("#publishing-action .spinner").removeClass('is-active');
            $("#publish").removeClass('disabled');
            var error = false;
            $(".leadpages_error").remove();
            $('#leadpages_my_selected_page').css('border-color', '#ddd');
            $('#leadpageType').css('border-color', '#ddd');
            $leadpageType = $("#leadpageType").val();
            $selectedPage = $("#leadpages_my_selected_page").val();
            $leadpageSlug = $('#leadpages_slug_input').val();

            if($leadpageType == 'none'){
                e.preventDefault();
                $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>Please select a page type</p></div>" );
                $('#leadpageType').css('border-color', 'red');
                error = true;
            }

            if($selectedPage == 'none'){
                e.preventDefault();
                $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>Please select a Leadpage</p></div>" );
                $('#leadpages_my_selected_page').css('border-color', 'red');
                error = true;
            }
            if($leadpageType != 'fp' && $leadpageType != 'nf'){
                console.log($leadpageType);
                if($leadpageSlug.length == 0){
                    e.preventDefault();
                    $( ".wrap h1" ).after( "<div class='error notice leadpages_error leadpage_slug_error'><p>Slug appears to be empty. Please add a slug.</p></div>" );
                    $('#leadpages_slug_input').css('border-color', 'red');
                    error = true;
                }
            }

            if($leadpageType == 'fp' || $leadpageType == 'nf'){
                $(".leadpage_slug_error").remove();
            }

            if(error == true){
                return;
            }
        });

    });
}(jQuery));