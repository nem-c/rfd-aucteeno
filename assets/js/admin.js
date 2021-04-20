let SimplePickers = [];
jQuery(function ($) {
    let lastField;
    const dateTimePicker = new SimplePicker({
        zIndex: 999,
    });
    $('input.simplepicker').on('focus', function (event) {
        event.preventDefault();
        lastField = this;
        let current = $(lastField).val();
        if (0 < current.length) {
            dateTimePicker.reset(moment(current, 'YYYY-MM-DD\THH:mm:ss').toDate());
        } else {
            dateTimePicker.reset();
        }
        dateTimePicker.open();
    });

    dateTimePicker.on(
        'submit',
        function (date, readableDate) {
            $(lastField).val(moment(date).format('YYYY-MM-DD\THH:mm:ss'));
        }
    );
});