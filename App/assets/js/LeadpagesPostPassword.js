(function ($) {

    $(function(){

        var $body = $('body');

        function checkPassword(e) {
            return $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action : 'homePageExists',
                    pageId : ajax_object.id
                },
                beforeSend: function( data ) {
                    console.log(data);
                },
                success: function( response ) {
                    if(response == 'error'){
                        e.preventDefault();
                        $( ".wrap h1" ).after( "<div class='error notice leadpages_error'><p>A HomePage already exists, please save this one as a draft.</p></div>" );
                    }
                }
            });
        }


        $body.on('click', '#leadpages_post_password', function () {
            checkPassword();
        });
    });
}(jQuery));