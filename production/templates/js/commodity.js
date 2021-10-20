
        
                $(document).ready(function () {
                    //
 data_model={ "id": "0","commodity_code": "","description": ""}
                    
                    bootstrapTableIndex = 0;
                    save_method = '';
                    pkey='id';
                    
                    //&**************************************************
        
                    $('#form')
                        .bootstrapValidator({ feedbackIcons: {
  valid: 'glyphicon glyphicon-ok',
  invalid: 'glyphicon glyphicon-remove',
  validating: 'glyphicon glyphicon-refresh'
  },
  excluded: [':disabled'],
 fields: {
 commodity_code:{
   validators: {
     notEmpty: {
        message: 'Commodity Code is  required and cannot be empty'
      }
    }
 },
 description:{
   validators: {
     notEmpty: {
        message: 'Description is  required and cannot be empty'
      }
    }
 }
}})
                        .on('success.form.bv', function (e) {
                            // Prevent submit form
                            e.preventDefault();
                            var $form = $(e.target),
                                validator = $form.data('bootstrapValidator');
                            if ($('input[name=name]').val() == '') { return; }
                            for (var x in data_model) {
                                try { data_model[x] = $('input[name=' + x + ']').val(); } catch (e) { }
                            }
                            if (save_method == 'PUT') {
                                $.ajax({
                                    type: "PUT",
                                    url: './api.php/commodity/' + data_model['id'],
                                    data: JSON.stringify(data_model), // serializes the form's elements.
                                    success: function (data) {
                                        $('#modal_form').modal('hide');
                                        toastr.success('The record was updated successfully', 'Success Alert', { timeOut: 5000 })
                                        $('#classTable').bootstrapTable('updateByUniqueId', {
                                            id: data_model['id'],
                                            row: data_model
                                        });
        
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        console.log(xhr.status);
                                        console.log(xhr.statusText);
                                        console.log(xhr.responseText);
                                    },
                                    beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
                                });
                            }
                            if (save_method == 'POST') {
                                $.ajax({
                                    type: "POST",
                                    url: './api.php/commodity/',
                                    data: JSON.stringify(data_model), // serializes the form's elements.                        
                                    success: function (data) {
                                        console.log('after add -1' + data);
                                        //var object = $.extend({}, object1, object2);
                                        var obj = {};
                                        $.extend(true, obj, data_model);
                                        obj.id = data;
                                        obj.actions = '<button onclick="editThis(' + data + ',this)" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
                                        obj.actions += '<button onclick="deleteThis(' + data + ',this)" style="margin-left:10px" class="btn btn-danger btn-sm"  data-target="#delete-button" ><i class="glyphicon glyphicon-trash"></i></button>';
        
                                        console.log(obj);
                                        $('#modal_form').modal('hide');
                                        toastr.success('The record was successfully added', 'Success Alert', { timeOut: 5000 });
                                        $('#classTable').bootstrapTable("append", obj);
        
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        console.log(xhr.status);
                                        console.log(xhr.statusText);
                                        console.log(xhr.responseText);
                                    },
                                    beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
                                });
                            }
                            $form
                                .bootstrapValidator('disableSubmitButtons', false)  // Enable the submit buttons
                                .bootstrapValidator('resetForm', true);             // Reset the form
                        })
                        .on('click', 'tbody > tr > td', function (e){ 
                        var table = $table.data('bootstrap.table'),
                $element = $(this),
                $tr = $element.parent(),
                row = table.data[$tr.data('index')];
                alert( row); 
                });
        
                    //**************************************************
        
                    $("#create-rec").click(function (e) {
                        e.preventDefault();
                        $('#form')
                            .bootstrapValidator('disableSubmitButtons', false)  // Enable the submit buttons
                            .bootstrapValidator('resetForm', true);             // Reset the form
                        save_method = 'POST';
                        $('#form')[0].reset(); // reset form on modals
                        $('input[name=id]').val('');
                        $('#modal_form').modal('show'); // show bootstrap modal                
                        $('.modal-title').text('Add Commodity'); // Set Title to Bootstrap modal title
                    });
        
        
                    $.ajax({
                        url: './api.php/commodity',
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#classTable').bootstrapTable({
                                uniqueId: 'id',
                                data: data
                            });
        
                            try { parent.autoResize('myframe'); } catch (e) { }
                        },
                        beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
                    });
        
                    function getData() {
                        return '{"data":[{"id":"1","name":"1 West","location":"Block C"},{"id":"2","name":"1 North","location":"Block A"},{"id":"3","name":"1 South","location":"Block A"}]}';
                    }
        
        
                });
                function saveRecord() {
        
                }
                function deleteThis(id, el) {
                    bootstrapTableIndex = $(el).closest("tr").index();
                    var tmp = $(el).closest("tr").find("td:eq(0)").text();
                    confirmDialog("Delete Request", 'Are you sure you want to delete:<br><b>' + tmp + '<b> ?', function (confirm) {
                        if (!confirm) return false;
                        $.ajax({
                            type: "DELETE",
                            url: './api.php/commodity/' + id,
                            success: function (data) {
                                console.log('deleted ' + id);
                                //$('#classTable').bootstrapTable('remove', { field: 'id', values: ['' + id] });
                                $('#classTable').bootstrapTable('removeByUniqueId', id);
                                toastr.success('The record was deleted successfully', 'Success Alert', { timeOut: 5000 })
        
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                console.log(xhr.status);
                                console.log(xhr.statusText);
                                console.log(xhr.responseText);
                            },
                            beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
                        });
        
                    });
        
        
                }
                function editThis(id, el) {
                    bootstrapTableIndex = $(el).closest("tr").index();
                    save_method = 'PUT';
                    $.ajax({
                        url: './api.php/commodity/' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#form')
                                .bootstrapValidator('disableSubmitButtons', false)
                                .bootstrapValidator('resetForm', true);
                            data_model = data;
                            for (var x in data_model) {
                                $('input[name=' + x + ']').val(data_model[x]);
                            }
                            $('.modal-title').text('Edit Commodity');
                            $('#modal_form').modal('show');
                        },
                        beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
                    });
        
        
        
        
                }