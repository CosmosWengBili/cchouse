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
