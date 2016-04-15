(function ($) {

    $(function () {


        var $body = $('body');

       $body.on('change', '#leadboxesTime', function(){

           populateTimedStats(this);

       });


        function populateTimedStats($this){
            var timeTillAppear = $($this).find(':selected').data('timeappear');
            var pageView       = $($this).find(':selected').data('pageview');
            var daysTillAppear = $($this).find(':selected').data('daysappear');

            var stats =  stat_row("Time before it appears:", timeTillAppear + ' seconds') +
                stat_row("Page views before it appears:", pageView + ' views') +
                stat_row("Don't reshow for the next ", daysTillAppear + ' days') ;
            console.log(stats);
            $("#selectedLeadboxSettings").html(stats);
        }

        function stat_row(label, value) {
            return '<div class="row-fluid">' +
                '<div class="span3">' + label + '</div>' +
                '<div class="span4">' + value + '</div>' +
                '</div>';
        }

    });

}(jQuery));