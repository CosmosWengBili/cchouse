function realtimeSelect(){

    var tasks = []

    var selectizeElements = $('[data-toggle=selectize]')
    selectizeElements.each(function(){
        var select = $(this)
        var table = select.data('table')
        var text = select.data('text')
        var selected = select.data('selected')

        var $d = $.Deferred();

        var task = $.ajax({
            method: "POST",
            url: "/api/selectize",
            data: { table: table, text: text }
        })
        .done(function( msg ) {
            var tableElements = ""
            for( var i = 0 ; i < msg.length; i++ ){
                if( selected == msg[i]['id'] ){
                    tableElements += `<option value="${msg[i]['id']}" selected>${msg[i][text]}</option>` 
                }
                else{
                    tableElements += `<option value="${msg[i]['id']}">${msg[i][text]}</option>`
                }
            }
            console.log(tableElements)
            select.append(tableElements)
            select.selectize({
                create: true,
                sortField: 'text'
            });
            $d.resolve('done')

            return $d.promise()
        });

        tasks.push(task)
    })
}


