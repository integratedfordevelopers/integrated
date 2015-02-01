$(function() {
    $('.form_datetime').each(function(index, elm){
        $(elm).datetimepicker({
            language:  $(elm).data('locale'),
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1
        });
    });
});