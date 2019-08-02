$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val()
        }
    });
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

function realtimeSelect(selectizeElements) {

    selectizeElements.each(function () {
        // Set data
        const select = $(this)
        const table = select.data('table')
        const text = select.data('text')
        const selected = select.data('selected')

        // Call API
        $.ajax({
                method: "POST",
                url: "/api/selectize",
                data: {
                    table: table,
                    text: text
                }
            })
            .done(function (msg) {
                if (msg !== "invalid") {
                    const tableElements = ""
                    for (var i = 0; i < msg.length; i++) {
                        if (selected === msg[i]['id']) {
                            tableElements += `<option value="${msg[i]['id']}" selected>${msg[i][text]}</option>`
                        } else {
                            tableElements += `<option value="${msg[i]['id']}">${msg[i][text]}</option>`
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
