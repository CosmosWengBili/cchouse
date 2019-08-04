/* Constant Variable */
var LANGUAGE = {
    "processing":   "處理中...",
    "loadingRecords": "載入中...",
    "lengthMenu":   "顯示 _MENU_ 項結果",
    "zeroRecords":  "沒有符合的結果",
    "info":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
    "infoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
    "infoFiltered": "(從 _MAX_ 項結果中過濾)",
    "infoPostFix":  "",
    "search":       "搜尋:",
    "paginate": {
        "first":    "第一頁",
        "previous": "上一頁",
        "next":     "下一頁",
        "last":     "最後一頁"
    },
    "aria": {
        "sortAscending":  ": 升冪排列",
        "sortDescending": ": 降冪排列"
    }
}
var PAGELENGTH = 30

function renderDataTable(selectors, options){
    options = (typeof options !== 'undefined') ?  options : {};
    options['pageLength'] = options['pageLength'] || PAGELENGTH;
    options['language'] = options['language'] || LANGUAGE;

    /* DataTable Initialize */
    var tables = {}
    for( var i = 0; i < selectors.length; i++ ){
        var table = $(`${selectors[i]}`);
        tables[selectors[i]] = table.DataTable(options);
        var headers = $(`${selectors[i]} thead th`)
            .map(function(element, value){ if(value.innerText != "") return value.innerText})

        /* Set query column elements */
        var columnElement = ""
        for( var j = 0; j < headers.length; j++ ){
            columnElement += `<option value="${j}">${headers[j]}</option>`
        }

        /* Set query elements */
        var queryElement= `
            <div class="query-row">
                <select name="query[column]">
                    ${columnElement}
                </select>
                <select name="query[rule]">
                    <option value="like">包含</option>
                    <option value="between">在範圍內</option>
                    <option value="in">有特定值</option>
                    <option value=">">大於</option>
                    <option value="<">小於</option>
                    <option value="=">等於</option>
                </select>
                <input type="text" name='query[value]'>
                <i class="fa fa-minus-circle" data-toggle="datatable-query-delete"></i>
            </div>`
        $(`[data-target="${selectors[i]}"]`).find('.query-box').append(queryElement)
        $(`[data-target="${selectors[i]}"]`).find('[data-toggle="datatable-query-delete"]').remove()
    }

    /* Add query row to query-box */
    $('[data-toggle="datatable-query-add"]').on('click', function(){
        $(this).parent().find('.query-box').append(queryElement)
    })

    /* Delete query row in query-box */
    $('body').on('click','[data-toggle="datatable-query-delete"]', function(){
        $(this).parent().remove()
    })

    /* Search like sql query */
    $('[data-toggle="datatable-query"]').submit(function(e) {
        e.preventDefault();
        var parentTable = $(this).data('target')
        var columns = $(this).find("[name='query[column]']")
            .map(function(){return $(this).val();}).get();
        var rules = $(this).find("[name='query[rule]']")
            .map(function(){return $(this).val();}).get();
        var values = $(this).find("[name='query[value]']")
            .map(function(){return $(this).val().replace(' ','');}).get();
        var searchFunction =  function( settings, data, dataIndex ) {
            for(var i=0; i<columns.length;i++){
                columnIndex = columns[i]
                column_value = data[columnIndex]
                if( !query(column_value, rules[i], values[i]) ) return false
            }
            return true;
        }

        $.fn.dataTable.ext.search = []
        $.fn.dataTable.ext.search.push(searchFunction);

        tables[parentTable].draw();
    } );
}


function query(column_value, rule, value){
    switch(rule){
        case 'like':
            if( column_value.includes(value) ) return true
            break
        case 'between':
            min = value.split(',')[0]
            max = value.split(',')[1]
            if( column_value > min && column_value < max ) return true
            break
        case 'in':
            values = value.split(',')
            if( values.includes(column_value) ) return true
            break
        case '>':
            if( column_value > value ) return true
            break
        case '<':
            if( column_value < value ) return true
            break
        case '=':
            if( column_value == value ) return true
            break
        default:
            return false
    }

    return false
}
