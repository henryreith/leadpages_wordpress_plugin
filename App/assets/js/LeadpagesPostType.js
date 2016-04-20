(function ($) {

    $(function () {


        var $body = $('body');

        $("#preview-action").hide();

        function checkHomePageExists(e) {
            return $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action : 'homePageExists',
                    pageId : ajax_object.id
                },
                beforeSend: function( data ) {
                },
                success: function( response ) {
                    if(response == 'error'){
                        e.preventDefault();
                        $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>A HomePage already exists, please save this one as a draft.</p></div>" );
                    }
                }
            });
        }

        function checkWelcomeGatePageExists(e) {
            return $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action : 'welcomeGatePageExists',
                    pageId : ajax_object.id
                },
                beforeSend: function( data) {
                },
                success: function( response ) {
                    e.preventDefault();

                    if(response == 'error'){
                        $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>A Welcome Gate&trade; already exists, please save this one as a draft.</p></div>" );
                    }
                }
            });
        }

        $body.on('click', '#confirmSubmit', function(){
           $('#post').submit();
        });

        $body.on('click', '#publish', function (e) {

            $("#publishing-action .spinner").removeClass('is-active');
            $("#publish").removeClass('disabled');
            var error = false;
            $(".leadpages_error").remove();
            $('#leadpages_my_selected_page').css('border-color', '#ddd');
            $('#leadpageType').css('border-color', '#ddd');
            $leadpageType = $("#leadpageType").val();
            $selectedPage = $("#leadpages_my_selected_page").val();

            if($leadpageType == 'none'){
                e.preventDefault();
                $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>Please select a page type</p></div>" );
                $('#leadpageType').css('border-color', 'red');
                error = true;
            }

            if($selectedPage == 'none'){
                e.preventDefault();
                //$( "<div class='error notice is-dismissable'>Test</div>" ).append( ".wrap h1" );
                $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>Please select a Leadpage</p></div>" );
                $('#leadpages_my_selected_page').css('border-color', 'red');
                error = true;
            }

            //return here to fix these errors before checking page type
            if(error == true){
                return;
            }


            if($leadpageType == 'fp'){
               /* checkHomePageExists(e)
                    .done(function(response){
                        console.log(response);
                       if(response == 'error'){
                           e.preventDefault();
                       }
                    });*/
            }
            if($leadpageType == 'wg'){
               /* checkWelcomeGatePageExists(e)
                    .done(function(response){
                        console.log(response);
                        if(response == 'error'){
                            e.preventDefault();
                        }
                    });*/
            }
        });


    });

}(jQuery));