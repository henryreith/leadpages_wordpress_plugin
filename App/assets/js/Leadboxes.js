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

        if($("#leadboxesTime").val() != 'none'){
            populateTimedStats($("#leadboxesTime"));
        }
        if($("#leadboxesExit").val() != 'none'){
            populateExitStats($("#leadboxesExit"));
        }

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

            var stats ="<h4 style='background:#E4E4EB'>Timed LeadBox&trade; Pop-Up Settings (from publish settings) <a style='margin-left:50px;' href=\"https://my.leadpages.net\" target=\"_blank\"> Go to LeadPages to change </a></h4>"+
                stat_row("Time before it appears: ", timeTillAppear + ' seconds') +
                stat_row("Page views before it appears: ", pageView + ' views') +
                stat_row("Don't reshow for the next: ", daysTillAppear + ' days');
            $("#selectedLeadboxSettings").html(stats);
        }

        function populateExitStats($this) {
            var daysTillAppear = $($this).find(':selected').data('daysappear');
            var stats ="<h4 style='background:#E4E4EB'>Exit LeadBox&trade; Pop-Up Settings (from publish settings)<a style='margin-left:50px;' href=\"https://my.leadpages.net\" target=\"_blank\"> Go to LeadPages to change </a></h4>"+
                stat_row("Don't reshow for the next ", daysTillAppear + ' days');
            $("#selectedExitLeadboxSettings").html(stats);
        }

        function stat_row(label, value) {
            return '<ul>' +
                '<li><strong>' + label +'</strong> '+ value+'</li>' +
                '</ul>';
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