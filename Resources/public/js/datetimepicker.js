$(document).ready(function() {
    $('.form_datetime').datetimepicker({
        language:  '{{ app.request.locale }}',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });
});