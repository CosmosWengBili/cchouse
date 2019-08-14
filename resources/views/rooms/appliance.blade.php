<div>
        {{-- generate the following block to have more appliances --}}
        {{-- important: specify the id increment using js or whatever way you like in name attribute --}}
        {{-- appliances[0][subject], appliances[1][subject], appliances[2][subject] ... --}}
        <table id="appliances-table" class="table table-striped">
            <thead>
            </thead>
            <tbody>
                @foreach($appliances as $idx => $appliance)
                <tr class="appliance-row">
                    <td style="width:15%">項目 {{$idx}}</td>
                    <td style="width:35%">
                        <input type="hidden" name="{{ "appliances[{$idx}][id]" }}" value="{{ $appliance['id'] }}" />
                        <select
                            class="form-control form-control-sm"
                            name="{{ "appliances[{$idx}][subject]" }}"
                            data-toggle="selectize" 
                            data-table="appliance" 
                            data-text="subject" 
                            data-value="subject" 
                            data-selected="{{ $appliance['subject'] }}" 
                            value=""
                        />
                        </select>                                            
                    </td>
                    <td style="width:15%">型號</td>
                    <td style="width:35%">
                        <select
                            class="form-control form-control-sm"
                            name="{{ "appliances[{$idx}][spec_code]" }}"
                            data-toggle="selectize" 
                            data-table="appliance" 
                            data-text="spec_code" 
                            data-value="spec_code" 
                            data-selected="{{ $appliance['spec_code'] }}" 
                        />
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:15%">廠商</td>
                    <td style="width:35%">
                        <select
                            class="form-control form-control-sm"
                            name="{{ "appliances[{$idx}][vendor]" }}"
                            data-toggle="selectize" 
                            data-table="appliance" 
                            data-text="vendor" 
                            data-value="vendor" 
                            data-selected="{{ $appliance['vendor'] }}" 
                        />
                        </select>
                    </td>
                    <td style="width:15%">個數</td>
                    <td style="width:25%">
                        <input
                            class="form-control form-control-sm"
                            type="number"
                            name="{{ "appliances[{$idx}][count]" }}"
                            value="{{ $appliance['count'] }}" 
                        />
                    </td>
                    <td style="width:10%">
                        <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>
                    </td>
                </tr>
                    <td style="width:15%">廠商電話</td>
                    <td style="width:35%">
                        <select
                            class="form-control form-control-sm"
                            name="{{ "appliances[{$idx}][maintenance_phone]" }}"
                            data-toggle="selectize" 
                            data-table="appliance" 
                            data-text="maintenance_phone" 
                            data-value="maintenance_phone" 
                            data-selected="{{ $appliance['maintenance_phone'] }}" 
                        />
                        </select>
                    </td>
                    <td style="width:15%">備註</td>
                    <td style="width:35%">
                        <input
                            class="form-control form-control-sm"
                            type="text"
                            name="{{ "appliances[{$idx}][comment]" }}"
                            value="{{ $appliance['comment'] }}" 
                        />
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="text-center">
                        <button class="btn btn-success js-add-row" type="button">新增</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        (function () {
            var buildTemplate = function (idx) {
                return (
                    '<tr class="appliance-row">' +
                    '    <td style="width:15%">項目 ' + idx + 
                    '    </td>'+
                    '    <td style="width:35%">' +
                    '        <select class="form-control form-control-sm" name="appliances[' + idx + '][subject]"' +
                    '           data-toggle="selectize" data-table="appliance" data-text="subject" data-value="subject" >'+
                    '        </select>'+
                    '    </td>' +
                    '    <td style="width:15%">型號</td>'+
                    '    <td style="width:35%">' +
                    '        <select class="form-control form-control-sm" name="appliances[' + idx + '][spec_code]"' +
                    '           data-toggle="selectize" data-table="appliance" data-text="spec_code" data-value="spec_code" >'+
                    '        </select>'+
                    '    </td>' +
                    '</tr>' +
                    '<tr>' +
                    '    <td style="width:15%">廠商</td>'+
                    '    <td style="width:35%">' +
                    '        <select class="form-control form-control-sm" name="appliances[' + idx + '][vendor]"' +
                    '           data-toggle="selectize" data-table="appliance" data-text="vendor" data-value="vendor" >'+
                    '        </select>'+
                    '    </td>' +
                    '    <td style="width:15%">個數</td>'+
                    '    <td style="width:25%">' +
                    '        <input type="text" class="form-control form-control-sm" name="appliances[' + idx + '][count]" value="">' +
                    '    </td>' +
                    '    <td style="width:10%">' +
                    '        <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>' +
                    '    </td>' +
                    '</tr>' +
                    '<tr>' +
                    '    <td style="width:15%">廠商電話</td>' + 
                    '    <td style="width:35%">' +
                    '        <select class="form-control form-control-sm" name="appliances[' + idx + '][maintenance_phone]"' +
                    '           data-toggle="selectize" data-table="appliance" data-text="maintenance_phone" data-value="maintenance_phone" >'+
                    '        </select>'+
                    '    </td>' +
                    '    <td style="width:15%">備註</td>' + 
                    '    <td style="width:35%">' +
                    '        <input type="text" class="form-control form-control-sm" name="appliances[' + idx + '][comment]" value="">' +
                    '    </td>' +
                    '</tr>'
                );
            }
            var $table = $("#appliances-table");
            var $insertRow = $table.find('tbody > tr:last-child');
    
            $table.find('.js-add-row').on('click', function () {
                var idx = $table.find('tbody > tr.appliance-row').length;
                $(buildTemplate(idx)).insertBefore($insertRow);
                window.realtimeSelect($('[data-toggle=selectize]'))
            });
            $table.on('click', '.js-remove-row', function () {
                $middle = $(this).closest('tr')
                $prev = $middle.prev()
                $next = $middle.next()
                $middle.remove();
                $prev.remove();
                $next.remove();
            });
        })();
    </script>