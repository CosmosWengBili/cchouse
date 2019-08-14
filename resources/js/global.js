$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
        }
    });

    // Realtime search module
    window.realtimeSelect($('[data-toggle=selectize]'))

    // Auto assigned value on each data resource select element
    $('select').each(function (index, element) {
        var selected_value = element.getAttribute('value')
        var options = element.options
        for (var i = 0; i < options.length; i++) {
            if (selected_value == options[i].value) {
                options[i].selected = true
            }
        }
    })
});
$(document).on('click', 'a.jquery-postback', function (e) {
    e.preventDefault();
    if (confirm('是否刪除?')) {
        $.post({
            type: $(this).data('method'),
            url: $(this).attr('href')
        }).done(function () {
            location.reload();
        });
    } else {
        alert("取消刪除")
    }

});


window.realtimeSelect = function (selectizeElements) {

    selectizeElements.each(function () {
        // Set data
        const select = $(this)
        const table = select.data('table')
        const text = select.data('text')
        const selected = select.data('selected')
        const value = select.data('value') || 'id'

        // Call API
        $.ajax({
                method: "POST",
                url: "/api/selectize",
                data: {
                    table: table,
                    text: text,
                    value: value
                }
            })
            .done(function (msg) {
                console.log(msg)
                if (msg !== "invalid") {
                    var tableElements = ""
                    for (var i = 0; i < msg.length; i++) {
                        if (selected === msg[i][value]) {
                            tableElements += `<option value="${msg[i][value]}" selected>${msg[i][text]}</option>`
                        } else {
                            tableElements += `<option value="${msg[i][value]}">${msg[i][text]}</option>`
                        }
                    }

                    // Render HTML code
                    select.append(tableElements)
                    select.selectize({
                        create: true,
                        sortField: 'text'
                    });
                } else {
                    alert('SQL Query issue');
                }
            })
    })
}
