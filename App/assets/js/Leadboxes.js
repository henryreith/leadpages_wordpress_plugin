(function ($) {

    $(function () {


        var $body = $('body');

        function init() {
            timedLeadBoxes();
            exitLeadBoxes();
            setPostTypes();
            $('#leadboxesLoading').hide();
            $("#leadboxesForm").show();
        }

        init();


        $body.on('change', '#leadboxesTime', function () {
            if($(this).val() == 'none'){
                $('#selectedLeadboxSettings').hide();
            }
            populateTimedStats(this);

        });

        $body.on('change', '#leadboxesExit', function () {
            if($(this).val() == 'none'){
                $('#selectedExitLeadboxSettings').hide();
            }
            populateExitStats(this);

        });


        function populateTimedStats($this) {
            var timeTillAppear = $($this).find(':selected').data('timeappear');
            var pageView = $($this).find(':selected').data('pageview');
            var daysTillAppear = $($this).find(':selected').data('daysappear');

            var stats = stat_row("Time before it appears:", timeTillAppear + ' seconds') +
                stat_row("Page views before it appears:", pageView + ' views') +
                stat_row("Don't reshow for the next ", daysTillAppear + ' days');
            $("#selectedLeadboxSettings").html(stats);
        }

        function populateExitStats($this) {
            var daysTillAppear = $($this).find(':selected').data('daysappear');
            var stats = stat_row("Don't reshow for the next ", daysTillAppear + ' days');
            $("#selectedExitLeadboxSettings").html(stats);
        }

        function stat_row(label, value) {
            return '<div class="row-fluid">' +
                '<div class="span3">' + label + '</div>' +
                '<div class="span4">' + value + '</div>' +
                '</div>';
        }

        function timedLeadBoxes() {
            $('.timeLeadBoxes').html(leadboxes_object.timedLeadboxes);
        }

        function exitLeadBoxes() {
            $('.exitLeadBoxes').html(leadboxes_object.exitLeadboxes);
        }

        function setPostTypes() {
            $('.postTypesForTimedLeadbox').html(leadboxes_object.postTypesForTimedLeadboxes);
            $('.postTypesForExitLeadbox').html(leadboxes_object.postTypesForExitLeadboxes);
            $('.postTypesForExitLeadbox').html(leadboxes_object.postTypesForExitLeadboxes);
        }

    });

}(jQuery));