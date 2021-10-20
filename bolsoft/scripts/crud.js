      //
      data_model={ "id": "0","code": "","description": "","amount": ""}
      var fieldValidatrs={};
      fieldValidatrs.code=createValidationField('Code','notEmpty');
      fieldValidatrs.description=createValidationField('Description','notEmpty');
      fieldValidatrs.amount=createValidationField('Amount','notEmpty');

      $("#create-rec").click(function (e) {
        
        save_method = 'POST';
        //
        e.preventDefault();
        clearForm('#edit_form');
        businessObject.beforeNewRecord();
        $('#modal_form').modal('show'); // show bootstrap modal                
        $('.modal-title').text('Add '+splitCamelCase(businessObject.data_table)); // Set Title to Bootstrap modal title
    });

function crudInit(){
      bootstrapTableIndex = 0;
      save_method = '';
      pkey = 'id';
      
      //&**************************************************

      $('#edit_form')
          .bootstrapValidator({ feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
            },
            excluded: [':disabled'],
            fields: fieldValidatrs    
            })
          .on('success.form.bv', function (e) {
              console.log('success.form.bv');
          })
          .on('success.form.bv', function (e) {
              // Prevent submit form
              e.preventDefault();
              var $form = $(e.target),  validator = $form.data('bootstrapValidator');
              businessObject.form_data=getFormData('#edit_form',data_model);
              if (save_method == 'PUT') {
                businessObject.update();
              }
              if (save_method == 'POST') {
                businessObject.save();
              }
              $form
                  .bootstrapValidator('disableSubmitButtons', false)  // Enable the submit buttons
                  .bootstrapValidator('resetForm', true);             // Reset the form
          });
        }
      //**************************************************


   
      function getData() {
          return '{"data":[{"id":"1","name":"1 West","location":"Block C"},{"id":"2","name":"1 North","location":"Block A"},{"id":"3","name":"1 South","location":"Block A"}]}';
      }


  ///////
  function createValidationField(field,type){
    if (type=='notEmpty'){
       var x="{validators: {notEmpty: {message: '"+field+" is required and cannot be empty'}}}" ;
       eval(' var fld='+x);
    }
    return fld;
 }

 function saveRecord() {

}


function BusinessObject(data_model,data_table) {
  this.data_model = data_model;
  this.data_table=data_table;
  this.save_method=''; 
  this.form_data={};

  this.beforeSave=function(){};
  this.afterSave=function(){};
  this.beforeUpdate=function(){};
  this.afterUpdate=function(){};
  this.beforeDelete=function(){};
  this.afterDelete=function(){};
  this.beforeGet=function(){};
  this.afterGet=function(){};
  this.beforeNewRecord=function(){ };

  this.save=function(){   
    console.log(this);    
    this.beforeSave();
    saveBusinessObject(this.form_data,this.data_table);
  }
  this.update = function() {
    this.beforeUpdate();
    updateBusinessObject(this.form_data,this.data_table);
  };
  this.delete = function() {
    deleteBusinessObject(this.form_data,this.data_table);
  };

}

function saveBusinessObject(form_data,data_table){         
      $.ajax({
        type: "POST",
        url: './app/'+data_table+'/',
        data: JSON.stringify(form_data),                         
        success: function (data) {
            var obj = {};
            $.extend(true, obj, form_data);
            obj.id = data;
            obj.actions = '<button onclick="editThis(' + data + ',this)" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
            obj.actions += '<button onclick="deleteThis(' + data + ',this)" style="margin-left:10px" class="btn btn-danger btn-sm"  data-target="#delete-button" ><i class="glyphicon glyphicon-trash"></i></button>';
            $('#modal_form').modal('hide');
            toastr.success('The record was successfully added', 'Success Alert', { timeOut: 5000 });
            $('#classTable').bootstrapTable("append", obj);

            businessObject.data_model.id=data;
            businessObject.afterSave();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
        },
        beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
    });
}

function updateBusinessObject(form_data,data_table){  
  console.log("...Checking");
    $.ajax({
      type: "PUT",
      url: './app/'+data_table+'/' + form_data['id'],
      data: JSON.stringify(form_data), // serializes the form's elements.
      success: function (data) {
          $('#modal_form').modal('hide');
          toastr.success('The record was updated successfully', 'Success Alert', { timeOut: 5000 })
          $('#classTable').bootstrapTable('updateByUniqueId', {
              id: form_data['id'],
              row: form_data
          });
          businessObject.afterUpdate();
      },
      error: function (xhr, ajaxOptions, thrownError) {
          handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
      },
      beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
  });
}

function loadBusinessObjects(data_table){  
  $('#classTable').bootstrapTable("destroy");
  $.ajax({
      url: './app/'+data_table,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
          $('#classTable').bootstrapTable({
              uniqueId: 'id',
              data: data
          });

          try { parent.autoResize('myframe'); } catch (e) { }
      },
      error: function (xhr, ajaxOptions, thrownError) {
          handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
      },
      beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
  });
}


function createActionButtons(value, row, index, field) {
  var $buttons= '<button onclick="editThis(' + row.id + ',this); return false;" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
  $buttons += '<button onclick="deleteThis(' + row.id + ',this); return false;" style="margin-left:10px" class="btn btn-danger btn-sm"  data-target="#delete-button" ><i class="glyphicon glyphicon-trash"></i></button>';
  return $buttons;
}

function deleteThis(id, el) {
  businessObject.beforeDelete();
  bootstrapTableIndex = $(el).closest("tr").index();
  var tmp = $(el).closest("tr").find("td:eq(0)").text();
  confirmDialog("Delete Request", 'Are you sure you want to delete:<br><b>' + tmp + '<b> ?', function (confirm) {
      if (!confirm) return false;
      $.ajax({
          type: "DELETE",
          url: './app/'+businessObject.data_table+'/' + id,
          success: function (data) {
              console.log('deleted ' + id);
              //$('#classTable').bootstrapTable('remove', { field: 'id', values: ['' + id] });
              $('#classTable').bootstrapTable('removeByUniqueId', id);
              toastr.success('The record was deleted successfully', 'Success Alert', { timeOut: 5000 })
              businessObject.afterDelete();
          },
          error: function (xhr, ajaxOptions, thrownError) {
              handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
          },
          beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
      });

  });


}
function editThis(id, el) {
  businessObject.beforeGet();
  bootstrapTableIndex = $(el).closest("tr").index();
  save_method = 'PUT';
  $.ajax({
      url: './app/'+businessObject.data_table+'/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
          $('#edit_form')
              .bootstrapValidator('disableSubmitButtons', false)
              .bootstrapValidator('resetForm', true);
              businessObject.data_model = data;
              businessObject.afterGet();
          loadFormData('#edit_form',businessObject.data_model);
          $('.modal-title').text('Edit '+splitCamelCase(businessObject.data_table));
          $('#modal_form').modal('show');
      },
      error: function (xhr, ajaxOptions, thrownError) {
          handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
      },
      beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
  });
}



function getBusinessObject(table,id) {
	var url='./app/'+table, obj=[];
	if(typeof(id) != "undefined"){url=url+'/'+id;	}
	$.ajax({
		url: url,
		type: 'GET',
		async:false,
		dataType: 'json',
		success: function (data) {
           obj=data;
		},
		error: function (xhr, ajaxOptions, thrownError) {handleStandardHttpErrors(xhr, ajaxOptions, thrownError);},
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
	});
    return obj;
}

function saveDataModel(form_data,data_table){         
  var id=0;
  $.ajax({ type: "POST", url: './app/'+data_table+'/', async:false,data: JSON.stringify(form_data),                         
    success: function (data) {   id=data;  },
    error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError);},
    beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
   });
   return id;
}

function updateDataModel(form_data,data_table){  
    var update_success=false; 
    $.ajax({
      type: "PUT", async:false,
      url: './app/'+data_table+'/' + form_data['id'],
      data: JSON.stringify(form_data), // serializes the form's elements.
      success: function (data) {
        update_success=true; 
      },
      error: function (xhr, ajaxOptions, thrownError) {
          handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
      },
      beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
  });
  return update_success;
}