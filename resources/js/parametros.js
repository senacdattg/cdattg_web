$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    // Smooth collapse animation
    $('.collapse').on('show.bs.collapse hide.bs.collapse', function(e) {
        const icon = $(this).prev().find('i');
        if (e.type === 'show') {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        } else {
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    });

    // Search functionality
    $('#searchParameter').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Search button click handler
    $('#searchBtn').on('click', function() {
        let value = $('#searchParameter').val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});