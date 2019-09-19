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
                if (msg !== "invalid") {
                    var tableElements = ""
                    for (var i = 0; i < msg.length; i++) {
                        tableElements += `<option value="${msg[i][value]}">${msg[i][text]}</option>`
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

// getQueryString('key').setInputValue('input_key', 'input_value').show('true|false');

/**
 *
 * @type {{getQueryStrings, setInputValue}}
 */
window.myQueryString = function () {
    
    let queryStrings = [];
    const url = new URL(location.href);
    const { searchParams } = url;
    
    for(let [key, value] of searchParams.entries()) {
        queryStrings[key] = value;
    }
    
    /**
     * get url query string
     * @returns {Array}
     */
    function getQueryStrings() {
        return queryStrings;
    }
    
    /**
     * set value, from query string, to an input by input id
     * @param queryStringKey
     * @param inputId
     * @param setName is set input name equal to input id
     * @return boolean
     */
    function setInputValue(queryStringKey, inputId, setName=true) {
    
        const $input = $('#' + inputId);
        const queryStringValue = queryStrings[queryStringKey] || null;
        if ($input.length === 0 || queryStringValue === null) return false;
        
        $input.val(queryStringValue);
        setName && $input.attr('name', inputId)
        
        return true;
    }
    
    return {
        getQueryStrings,
        setInputValue
    }
};

window._ = require('lodash');
