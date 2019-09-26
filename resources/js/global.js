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
        const $select = $(this)
        const table = $select.data('table')
        const text = $select.data('text')
        const selected = $select.data('selected')
        const value = $select.data('value') || 'id'

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
            .then(function (options) {
                if (options !== "invalid") {
                    // Render HTML code
                    $select.selectize({
                        valueField: value,
                        labelField: text,
                        create: true,
                        options: options,
                        sortField: 'text'
                    });
                    
                    return options;
                } else {
                    alert('SQL Query issue');
                }
            })
            .then(function (options) {
                if (!! options) {
                    let select_selectize = $select[0].selectize;
                    let tag_name_is_select = $select.prop('tagName') === 'SELECT';
                    let has_data_toggle_selectize = $select.data('toggle') === 'selectize';

                    if (tag_name_is_select && has_data_toggle_selectize) {
                        if (selected === 0) {
                            // set default selected value as the first one
                            select_selectize.setValue(options[0][value])
                        } else {
                            select_selectize.setValue(selected)
                        }
                    }
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
    
    for (let [key, value] of searchParams.entries()) {
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
        if ($input.length === 0 || queryStringValue === null) {
            return false;
        }
        
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

// jquery validation plugin localization zh-TW
$.extend($.validator.messages, {
    required: "必須填寫",
    remote: "請修正此欄位",
    email: "請輸入有效的電子郵件",
    url: "請輸入有效的網址",
    date: "請輸入有效的日期",
    dateISO: "請輸入有效的日期 (YYYY-MM-DD)",
    number: "請輸入正確的數值",
    digits: "只可輸入數字",
    creditcard: "請輸入有效的信用卡號碼",
    equalTo: "請重複輸入一次",
    extension: "請輸入有效的後綴",
    maxlength: $.validator.format("最多 {0} 個字"),
    minlength: $.validator.format("最少 {0} 個字"),
    rangelength: $.validator.format("請輸入長度為 {0} 至 {1} 之間的字串"),
    range: $.validator.format("請輸入 {0} 至 {1} 之間的數值"),
    step: $.validator.format("請輸入 {0} 的整數倍值"),
    max: $.validator.format("請輸入不大於 {0} 的數值"),
    min: $.validator.format("請輸入不小於 {0} 的數值")
});
