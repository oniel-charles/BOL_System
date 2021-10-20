
function loadShortCutOption(e){
	console.log(e);
	window.location.href='maintain_bill_of_lading.html';
  }
  
//document.title='BOL';
try{
document.getElementById('glo_company_name').innerHTML ='Quality One';
}catch(e){}

$.ajaxSetup({ cache: false });
token = '';
token = getToken();
console.log(token);
try {
	$('[data-toggle="datepicker"]').datepicker({
		autoHide: true,
		zIndex: 2048,
		format: "dd/mm/yyyy"
	});
} catch (e) { }

function confirmDialog(header, message, callback) {

	if (!$('#DivID').length) {
		createConfirmBox();
	}

	var modal = $("#confirmModal");
	modal.modal("show");
	$("#confirmMessage").empty().append(message);
	$("#confirmHeader").empty().append(header);
	$('#confirmYes').off().click(function () {
		modal.modal('hide');
		callback(true);
	});
	$('#confirmNo').off().click(function () {
		modal.modal('hide');
		callback(false);
	});

}

if (!$('.loader').length) {
	var el = document.createElement('div');
	$(el).addClass('loader');	
	document.body.appendChild(el);
	$(el).hide();
}


function createConfirmBox() {
	var el = document.createElement('div');
	var domString = '<div id="confirmModal" class="modal fade" role="dialog " data-backdrop="static" data-keyboard="false">  ';
	domString += ' <div class="modal-dialog modal-sm">';
	domString += ' <div class="modal-content">';
	domString += '      <div class="modal-header" >';
	domString += '         <h4 class="modal-title" id="confirmHeader">Modal Header</h4>';
	domString += '      </div>';
	domString += '  <div class="modal-body" id="confirmMessage">';
	domString += '   </div>';
	domString += '   <div class="modal-footer">';
	domString += '      <button type="button" data-dismiss="modal" class="btn btn-primary" id="confirmYes">Yes</button>';
	domString += '      <button type="button" data-dismiss="modal" class="btn" id="confirmNo">Close</button>';
	domString += '   </div>';
	domString += ' </div>'
	domString += '</div>';
	domString += '</div>';

	el.innerHTML = domString;
	document.body.appendChild(el.firstChild);
}


function splitCamelCase(word) {
	var output, i, l, capRe = /[A-Z]/, underscore = new RegExp("_");
	if (typeof (word) !== "string") {
		throw new Error("The \"word\" parameter must be a string.");
	}
	output = [];
	for (i = 0, l = word.length; i < l; i += 1) {
		if (i === 0) {
			output.push(word[i].toUpperCase());
		}
		else {
			if (i > 0 && (capRe.test(word[i]) || underscore.test(word[i]))) {
				output.push(" ");
				if (underscore.test(word[i])) continue;
			}
			if (word[i - 1] == '_') {
				output.push(word[i].toUpperCase());
			} else {
				output.push(word[i]);
			}
		}
	}
	return output.join("");
}

function viewSource() {

	try {

		if (!document.getElementById('divsource')) {
			var htmltxt = "";
			htmltxt = '<div id="divsource" class="window" style="display:xnone; top:50; left:20; z-index:100000">';
			htmltxt = htmltxt + '<div class="titleBar">View Source</div>'
			htmltxt = htmltxt + '<img SRC="../image/closewin_icon.gif"  alt="close"  class="ximage" onClick="hideElement(divsrc); clearSourceTxt()"> </img>';
			//  htmltxt=htmltxt+'<form name="source">';
			htmltxt = htmltxt + '<textarea id=sourcetxt readonly cols=120 rows=40 wrap=virtual style="font-family:garmond"></textarea>';
			htmltxt = htmltxt + '<br>';
			htmltxt = htmltxt + '<BUTTON onclick="hideElement(divsrc); clearSourceTxt()">Close</button>';
			//  htmltxt=htmltxt+'</form>';
			htmltxt = htmltxt + '</div>';
			var divform = document.createElement('div');
			divform.innerHTML = htmltxt;
			document.getElementsByTagName('body')[0].appendChild(divform);
			//    workarea.appendChild(divform);
		}

		var txt = unescape(document.body.innerHTML);
		// txt=url.replace(/&gt/g, ">");
		// txt=url.replace(/&lt/g, "<");
		document.getElementById('sourcetxt').innerHTML = txt;
		var obj = document.getElementById('divsource');


	} catch (e) { alert(e.message) }

}

SELECT_DATA_PATH = {};
//****SELECT POPULATION */
$(".load-select").each(function () {
	$this_select = $(this);
	$this_select.empty();
	var select_path = $(this).attr("data-path");
	if (SELECT_DATA_PATH[select_path]) {
		if ($this_select.hasClass("add-all")) {
			$this_select.append('<option value=0>ALL</option>');
		}
		$.each(SELECT_DATA_PATH[select_path], function (key, value) {
			if (value.id > 0 || $this_select.hasClass("all-values")) {
				$this_select.append('<option value=' + value.id + '>' + value.description + '</option>');
			}
		});
	} else {
		$.ajax({
			url: select_path,
			type: 'GET',
			dataType: 'json',
			async: false,
			success: function (data) {
				SELECT_DATA_PATH[select_path] = data;
				if ($this_select.hasClass("add-all")) {
					$this_select.append('<option value=0>ALL</option>');
				}
				$.each(data, function (key, value) {
					if (value.id > 0 || $this_select.hasClass("all-values")) {						
						$this_select.append('<option value=' + value.id + '>' + value.description + '</option>');
					}
				});
			},
			error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
			beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
		});
	}

});

//************************************ */
$(".load-selectpicker").each(function () {
	$this_select = $(this);
	$this_select.find('option').remove().end();
	var select_path = $(this).attr("data-path");
	if (SELECT_DATA_PATH[select_path]) {
		if ($this_select.hasClass("add-all")) {
			$this_select.append('<option value=0>ALL</option>');
		}
		if ($this_select.hasClass("add-none")) {
			$this_select.append('<option value=0></option>');
		}
		$.each(SELECT_DATA_PATH[select_path], function (key, value) {
			if (value.id > 0 || $this_select.hasClass("all-values")) {
				$this_select.append('<option value=' + value.id + '>' + value.description + '</option>').selectpicker("refresh");
			}
		});
	} else {
		$.ajax({
			url: $(this).attr("data-path"),
			type: 'GET',
			dataType: 'json',
			async: false,
			success: function (data) {
				SELECT_DATA_PATH[select_path] = data;
				if ($this_select.hasClass("add-all")) {
					$this_select.append('<option value=0>ALL</option>');
				}
				if ($this_select.hasClass("add-none")) {
					$this_select.append('<option value=0></option>');
				}
				$.each(data, function (key, value) {
					if (value.id > 0 || $this_select.hasClass("all-values")) {
						$this_select.append('<option value=' + value.id + '>' + value.description + '</option>').selectpicker("refresh");
					}
				});
			},
			error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
			beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
		});
	}

});

function reloadSelect(select_field) {
	$this_select = $(select_field);
	$this_select.find('option').remove().end();
	var select_path = $this_select.attr("data-path");
	$.ajax({
		url: $this_select.attr("data-path"),
		type: 'GET',
		dataType: 'json',
		async: false,
		success: function (data) {
			SELECT_DATA_PATH[select_path] = data;
			$.each(data, function (key, value) {
				if (value.id > 0) {
					$this_select.append('<option value=' + value.id + '>' + value.description + '</option>').selectpicker("refresh");
				}
			});
		},
		error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
	});
}
//*************END SELECT POPULATION**************  
function setAjaxSelectOptions($url, desc) {
	var options = {
		ajax: {
			url: $url,
			type: 'POST',
			dataType: 'json',
			// Use "{{{q}}}" as a placeholder and Ajax Bootstrap Select will
			// automatically replace it with the value of the search query.
			data: {
				q: '{{{q}}}'
			}
		},
		locale: {
			emptyTitle: 'Select ' + desc
		},
		log: 3,
		emptyRequest: true,
		preprocessData: function (data) {
			var i, l = data.length, array = [];
			if (l) {
				for (i = 0; i < l; i++) {
					array.push($.extend(true, data[i], {
						text: data[i].description,
						value: data[i].id,
						data: {
							// subtext: data[i].description
						}
					}));
				}
			}
			// You must always return a valid array when processing data. The
			// data argument passed is a clone and cannot be modified directly.
			return array;
		}
	};
	return options;
}

function handleStandardHttpErrors(xhr, ajaxOptions, thrownError) {

	switch (xhr.status) {
		case 428:
		toastr.error(xhr.responseText, 'Warning', { timeOut: 4000 })
			break;
		case 401:
			authenticateUser();
			break;
		case 404:
			if (xhr.responseText.indexOf('Duplicate entry') != -1) {
				toastr.error('A simmiliar record already exist ...creating duplicate entries', 'Warning', { timeOut: 4000 })
			}
			if (xhr.responseText.indexOf('delete') != -1 && xhr.responseText.indexOf('CONSTRAINT') != -1 && xhr.responseText.indexOf('FOREIGN')) {
				var x1=xhr.responseText.indexOf('(');
				var x2=xhr.responseText.indexOf('FOREIGN');
				var str=xhr.responseText.substr(x1,x2-x1);
				var constraint=str.split('`');
				toastr.error('Unable to execute delete request, there are other related records which prevents deletion of this record.<br> ==>'+splitCamelCase(constraint[3]), { timeOut: 4000 })
			}
			break;
		default:
	}
}

function authenticateUser() {
	token = ''; saveToken();
	if (!$('#loginModal').length) {
		createLoginForm();
	}
	$('#loginModal').modal('show'); $('.modal-backdrop').addClass('login-modal-backdrop-image');
	$('#login_user_name').focus();
}

function loginUser() {
	var $url = './app/login';
	if ($("#login_password").val() == '') { return; }
	if ($("#login_user_name").val() == '') { return; }
	var form_data = {};
	form_data['password'] = btoa($('#login_password').val());
	form_data['user_name'] = $('#login_user_name').val();

	$.ajax({
		type: "POST",
		url: $url,
		dataType: 'json',
		data: form_data, // serializes the form's elements.                        
		success: function (data) {
			token = data.token;
			saveToken(); 
			location.reload();
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$('#login-form').data('bootstrapValidator').updateStatus('login_password', 'INVALID', null);
			handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
		},
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
	});
}

function createLoginForm() {
	var domString = ' <div class="modal fade" id="loginModal" role="dialog" data-color="orange" data-backdrop="static" data-keyboard="false"> ';
	domString += ' <div class="modal-dialog modal-sm"> <div class="modal-content "> <div class="modal-body"> <div class="container col-xs-12 "> ';
	domString += '<form id=login-form class="form-signin " style="margin-left:-22;"> <img src="default.png" class="center-block img-responsive" style="top:-10px;width: 60%; height: auto;"/>';
	domString += '<input type="text" class="hidden" id="process_paused" > <div class="col-md-12 form-group has-feedback"> ';
	domString += '<input type="text" class="form-control has-feedback-left" name="login_user_name" id="login_user_name" placeholder="Username"> <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span> ';
	domString += '</div><div class="col-md-12 form-group has-feedback"> <input type="password" class="form-control has-feedback-left" id="login_password" placeholder="Password" name="login_password" required>';
	domString += '<span class="fa fa-lock form-control-feedback left" aria-hidden="true"></span> </div><br>';

	domString += '<button class="btn btn-lg btn-primary btn-block" type="submit" id="submit_login" style="border-radius:20px;">Login</button> ';
	domString += '</form> </div><br><i class="align-center" style="position:relative; margin-left:50px; cursor: pointer" onclick="alert(\'Contact Admin\')"> Forgot your password?</i> ';
	domString += '<br><a onclick=" deleteMysqlLog()"><i class="fa fa-refresh "></i> </a> </div></div></div></div>';

	$(domString).appendTo(document.body);
	$("#login-form").submit(function (e) {
		e.preventDefault();
		console.log('form submitted');
		loginUser(); return false;
	});
	$('#login-form').bootstrapValidator({
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		excluded: [':disabled'],
		fields: {
			login_user_name: {
				validators: {
					notEmpty: {
						message: 'Please enter your user name'
					}
				}
			},
			login_password: {
				validators: {
					notEmpty: {
						message: 'Please enter a valid password'
					}
				}
			}
		}
	});
}

function createChangePasswordForm() {
	var domString = ' <div class="modal fade" id="change-loginModal" role="dialog" data-color="orange" data-backdrop="static" data-keyboard="false"> ';
	domString += ' <div class="modal-dialog modal-sm"> <div class="modal-content "> <div class="modal-body"> <div class="container col-xs-12 "> ';
	domString += '<form id=change-login-form class="form-signin " style="margin-left:-22;"> ';
	domString += '<div class="col-md-12 form-group has-feedback"> <input type="password" class="form-control has-feedback-left" id="current_password" placeholder="Password" name="current_password" required>';
	domString += '<span class="fa fa-lock form-control-feedback left" aria-hidden="true"></span> </div><br>';
	domString += '<div class="col-md-12 form-group has-feedback"> <input type="password" class="form-control has-feedback-left" id="new_password" placeholder="New Password" name="new_password" required>';
	domString += '<span class="fa fa-random form-control-feedback left" aria-hidden="true"></span> </div><br>';
	domString += '<div class="col-md-12 form-group has-feedback"> <input type="password" class="form-control has-feedback-left" id="confirm_password" placeholder="Confirm Password" name="confirm_password" required>';
	domString += '<span class="fa fa-random form-control-feedback left" aria-hidden="true"></span> </div><br>';
	domString += '<button class="btn btn-sm btn-primary btn-block" type="submit" id="change-submit_login" style="border-radius:20px;">Login</button> ';
	domString += '<button  class="btn btn-danger btn-sm btn-block" data-dismiss="modal" style="border-radius:20px;">Cancel</button> ';
	domString += '</form> </div><i class="align-center" style="position:relative; margin-left:50px;" ></i> </div></div></div></div>';

	$(domString).appendTo(document.body);

	$('#change-login-form').bootstrapValidator({
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		excluded: [':disabled'],
		fields: {
			current_password: {
				validators: {
					notEmpty: {
						message: 'Please enter a valid password'
					}
				}
			},
			new_password: {
                validators: {
                    identical: {
                        field: 'confirm_password',
                        message: 'The new password  and its confirm are not the same'
					},
					notEmpty: {
                        message: 'This field is required'
                    }
                }
            },
            confirm_password: {
                validators: {
                    identical: {
                        field: 'new_password',
                        message: 'The new password and its confirm are not the same'
                    },
					notEmpty: {
                        message: 'This field is required'
                    }
                }
            }
        }		
	}) .on('success.form.bv', function (e) {
		e.preventDefault();
		changePassword(); return false;
	});
}
function passwordChangeRequest() {
	if (!$('#change-loginModal').length) {
		createChangePasswordForm();
	}
	$('#change-loginModal').modal('show'); //$('.modal-backdrop').addClass('login-modal-backdrop-image');
	$('#current_password').focus();
}
function changePassword(){
	var $url = './app/change_password';
	var form_data = {};
	form_data['current_password'] = btoa($('#current_password').val());
	form_data['new_password'] = btoa($('#new_password').val());
	form_data['confirm_password'] = btoa($('#confirm_password').val());

	$.ajax({
		type: "POST",
		url: $url,
		dataType: 'json',
		data: form_data, // serializes the form's elements.                        
		success: function (data) {
			token = data.token;
			saveToken();
			toastr.success('Password was successfully changed', 'Success Alert', { timeOut: 5000 });
			$('#change-loginModal').modal('hide');
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$('#change-login-form').data('bootstrapValidator').updateStatus('current_password', 'INVALID', null);
			handleStandardHttpErrors(xhr, ajaxOptions, thrownError);
		},
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
	});
}

function getFormData($form, $model) {
	var obj = {};
	for (var x in $model) {
		try {
			var field = $($form).find("#" + x);
			if (field.length == 0) {
				field=$($form+" input[name='"+x+"']:checked");
				obj[x] = field.val();
			} else {
				obj[x] = field.val();
				if (field.attr('type') == 'checkbox') {
					if (field.prop('checked')) {
						obj[x] = 1;
					} else {
						obj[x] = 0;
					}
				}
				if (field.attr('type') == 'radio') {
					obj[x]=$("input[name='"+x+"']:checked").val();					
				}
			}
		} catch (e) {}
	}
	return obj;
}
function loadFormData($form, $model) {
	console.log($model);
	$($form).bootstrapValidator('disableSubmitButtons', false)
		.bootstrapValidator('resetForm', true);
	for (var x in $model) {
		var field = $($form).find("#" + x);				
		field.val($model[x]);
		if (field.length == 0) {
			field=$($form+" input[name='"+x+"']");
			if (field.length != 0 && field.attr('type') == 'radio') {
				$('input:radio[name="'+x+'"][value="'+$model[x]+'"]').prop('checked', true);
			}
		} 
		if (field.attr('type') == 'checkbox') {
			field.iCheck('uncheck');
			field.prop('checked', false);
			if ($model[x] == 1) {
				field.iCheck('check');
				field.prop('checked', true);
			}
		}
		if (field.hasClass("selectpicker")) {
			field.selectpicker('val', $model[x]);
		}
		if (field.hasClass("load-selectpicker")) {
			field.selectpicker('val', $model[x]);
		}
		if (field.hasClass("selectpicker with-ajax")) {
			field.selectpicker('refresh');
			field
				.html('<option value="1" >Quyn</option>')
				.selectpicker('refresh');
			field.selectpicker('render');
			var xoptions = setAjaxSelectOptions('./app/package/search', 'Package Type');
			$('#package_type_id').selectpicker().filter('.with-ajax').ajaxSelectPicker(xoptions);

			//field.selectpicker().filter('.with-ajax').ajaxSelectPicker(xoptions);
			//field.selectpicker('val','1');
		}
	}
}


function clearForm($form) {
	var $the_form = $($form);
	$the_form.find(':checkbox').iCheck('uncheck');
	$the_form.find('img').attr('src', '#');
	//$the_form.find("select").val('default').selectpicker("refresh");
	$the_form
		.bootstrapValidator('disableSubmitButtons', false)  // Enable the submit buttons
		.bootstrapValidator('resetForm', true);             // Reset the form
	//save_method = 'POST';
	$the_form[0].reset(); // reset form on modals        
	$($form + ' #id').val('');
}

function validDate($d) {
	var dateObject = {};
	dateObject.date = 0;
	dateObject.valid = false;
	var bits = $d.split('/');
	var d = new Date(bits[2], bits[1] - 1, bits[0]);
	dateObject.date = numeric('' + d.getFullYear() + pad((d.getMonth() + 1), 2) + pad(d.getDate(), 2));
	dateObject.valid = (d && (d.getMonth() + 1) == bits[1] && d.getDate() == bits[0]);
	return dateObject;
}

function numeric(str) {
	if (str == null) return 0;
	if (isNaN(parseFloat(str))) return 0;
	var dec = 2;
	if (arguments.length > 1) { dec = arguments[1]; }
	str = '' + str;
	if (str.trim() == '') return 0;
	//if (isNaN(Number(Number.parseFloat(str).toFixed(dec)))){alert(str+'   \n not number')}
	return Number(parseFloat(str.replace(",","")).toFixed(dec));
}
function numericStr(str) {
	//num.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})

	if (str == null) return 0;
	if (isNaN(str)) return 0;
	var dec = 0;
	if (arguments.length > 1) { dec = arguments[1]; }
	str = '' + str;
	if (str.trim() == '') return 0;
	return parseFloat(str.replace(",","")).toFixed(dec).replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',');
}

//Leading Zeros
function pad(num, size) {
	try {
		if (num.toString().length >= size) return num;
	} catch (e) { return num; }
	return (Math.pow(10, size) + Math.floor(num)).toString().substring(1);
}

function formatDate($date) {
	var d = '' + $date;
	if (d.length < 8) return '';
	return d.substr(6, 2) + '/' + d.substr(4, 2) + '/' + d.substr(0, 4);
}


function setSelectSearchOptions($url, desc) {
	var options = {
		ajax: {
			url: $url,
			type: 'POST',
			dataType: 'json',
			// Use "{{{q}}}" as a placeholder and Ajax Bootstrap Select will
			// automatically replace it with the value of the search query.
			data: {
				q: '{{{q}}}'
			}
		},
		locale: {
			emptyTitle: 'Select ' + desc
		},
		log: 3,
		emptyRequest: true,
		preprocessData: function (data) {
			var i, l = data.length, array = [];
			if (l) {
				for (i = 0; i < l; i++) {
					array.push($.extend(true, data[i], {
						text: data[i].description,
						value: data[i].id,
						data: {
							//subtext: data[i].description
						}
					}));
				}
			}
			// You must always return a valid array when processing data. The
			// data argument passed is a clone and cannot be modified directly.
			return array;
		}
	};
	return options;
}

function downloadTXTFile(data, exportedFilenmae, type) {
	//type  = plain/text  or text/csv"
	var blob = new Blob([data], { type: type + ';charset=utf-8;' });
	if (navigator.msSaveBlob) { // IE 10+
		navigator.msSaveBlob(blob, exportedFilenmae);
	} else {
		var link = document.createElement("a");
		if (link.download !== undefined) { // feature detection
			// Browsers that support HTML5 download attribute
			var url = URL.createObjectURL(blob);
			link.setAttribute("href", url);
			link.setAttribute("download", exportedFilenmae);
			link.style.visibility = 'hidden';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	}
}

function chunckString(str, length) {
	console.log(str);
	if (str == null) str = '';
	//return str.match(new RegExp('.{1,' + length + '}', 'g'));
	//return str.match(/(.|[\r\n]){1,5}/g);
	var arr = str.match(new RegExp('(.|[\r\n]){1,' + length + '}', 'g'));
	if (arr == null) arr = [''];
	return arr;
}

var getJsonObjectValue = function (array, key, value) {
	return array.filter(function (object) {
		return object[key] === value;
	});
};

function saveToken() {
	setCookie('bol_user', token, 1);
}
function getToken() {
	return getCookie('bol_user');
}
/****** Cookie functions */
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toGMTString();
	//document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	document.cookie = cname + "=" + cvalue + ";path=/";
}

function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

function checkCookie() {
	var user = getCookie("username");
	if (user != "") {
		alert("Welcome again " + user);
	} else {
		user = prompt("Please enter your name:", "");
		if (user != "" && user != null) {
			setCookie("username", user, 30);
		}
	}
}

function parseToken($token) {
	try {
		var base64Url = $token.split('.')[1];
		var base64 = base64Url.replace('-', '+').replace('_', '/');
		return JSON.parse(window.atob(base64));
	} catch (e) { return {} }
};
function createLargeReportModal() {
	if ($('#large_report_modal').length) {
		return true;
	}
	var el = document.createElement('div');
	var domString = '<div class="modal fade" id="large_report_modal" role="dialog">';
	domString += ' <div class="modal-dialog modal-lg" style="width:95%; height: 95%;">';
	domString += ' 	<div class="modal-content" style=" height: 95%;">';
	domString += ' <div style="height:40px" class="modal-header">';
	domString += ' <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
	domString += ' <span aria-hidden="true">&times;</span>';
	domString += ' </button>';
	domString += ' <h4 class="modal-title">Import Entry Form</h4>';
	domString += ' </div>';
	domString += ' <div class="modal-body" style=" height: 95%;">';
	domString += ' <iframe id="large-report1-iframe" type="application/pdf" style="height:100%;width:100%"></iframe>';
	domString += ' </div>';
	domString += ' </div></div></div>';
	el.innerHTML = domString;
	document.body.appendChild(el.firstChild);
}
function createReportModal() {
	if ($('#med_report_modal').length) {
		return true;
	}
	var el = document.createElement('div');
	var domString = '<div id="confirmModal" class="modal fade" role="dialog " data-backdrop="static" data-keyboard="false">  ';
	domString += ' <div class="modal-dialog modal-sm">';
	domString += ' <div class="modal-content">';
	domString += '      <div class="modal-header" >';

	var domString = '<div class="modal fade" id="med_report_modal" role="dialog">';
	domString += ' <div class="modal-dialog modal-lg">';
	domString += ' 	<div class="modal-content">';
	domString += ' <div style="height:40px" class="modal-header">';
	domString += ' <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
	domString += ' <span aria-hidden="true">&times;</span>';
	domString += ' </button>';
	domString += ' <h4 class="modal-title">Import Entry Form</h4>';
	domString += ' </div>';
	domString += ' <ul class="nav nav-tabs">';
	domString += ' <li class="active">';
	domString += ' <a id=report1-tab data-toggle="tab" href="#report1-div">Home</a>';
	domString += ' </li>';
	domString += ' <li>';
	domString += ' <a id=report2-tab data-toggle="tab" href="#report2-div">Receipt 2</a>';
	domString += ' </li>';
	domString += ' </ul>';
	domString += ' <div class="tab-content">';
	domString += ' <div id="report1-div" class="tab-pane fade in active">';
	domString += ' <iframe id="my-report1-iframe" type="application/pdf" style="min-height:400px;width:100%"></iframe>';
	domString += ' </div>';
	domString += ' <div id="report2-div" class="tab-pane fade">';
	domString += ' <iframe id="my-report2-iframe" type="application/pdf" style="min-height:400px;width:100%"></iframe>';
	domString += ' </div></div></div></div></div>';
	el.innerHTML = domString;
	document.body.appendChild(el.firstChild);
}
function showMediumReport($url, $fileName, $header, p_iframe, rpt_data) {
	createReportModal();
	if (typeof (p_iframe) == 'undefined') { $iframe = $('#my-report1-iframe'); }
	else { $iframe = $(p_iframe); }
	// TBC cheick iframe medium-report-frame exist	
	$('#report1-tab').tab('show');

	$('#med_report_modal').modal('show');
	$('#med_report_modal .modal-title').html($header);
	var report_data = rpt_data;
	if (typeof (rpt_data) == 'undefined') { report_data = {} }
	$.ajax({
		cache: false,
		type: 'POST',
		url: $url,
		data: JSON.stringify(report_data),
		contentType: false,
		cache: false,
		processData: false,
		xhrFields: {
			responseType: 'blob'
		},
		success: function (response, status, xhr) {
			var blob1 = new Blob([response], { type: 'application/pdf' }),
				urlx = URL.createObjectURL(blob1);

			if (window.navigator && window.navigator.msSaveOrOpenBlob) { // IE workaround
				$('#med_report_modal').modal('hide');
				window.navigator.msSaveOrOpenBlob(blob1, $fileName);
				toastr.success('Click open to view the ' + $header, $fileName, { timeOut: 3000 })
			} else {
				var iframe = $iframe;
				iframe.attr('src', urlx + '#page=1&zoom=75');
			}

		},
		error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }

	});
}
function showMediumReportEmbed($url, $fileName, $header, p_iframe, rpt_data) {	
	createReportModal();
	if (typeof (p_iframe) == 'undefined') { $iframe = $('#my-report1-iframe'); }
	else { $iframe = $(p_iframe); }
	$iframe .attr('src', '');
	// TBC cheick iframe medium-report-frame exist	
	$('#report1-tab').tab('show');

	$('#med_report_modal').modal('show');
	$('#med_report_modal .modal-title').html($header);
	var report_data = rpt_data;
	if (typeof (rpt_data) == 'undefined') { report_data = {} }
	$.ajax({
		cache: false,
		type: 'POST',
		url: $url,
		data: JSON.stringify(report_data),
		contentType: false,
		async: false,
		processData: false,
		success: function (response, status, xhr) {
				var iframe = $iframe;
				iframe.attr('src', './app'+response + '#page=1&zoom=75');			

		},
		error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }

	});
}


function showLargeReport($url, $fileName, $header, rpt_data) {
	createLargeReportModal();
	if (typeof (p_iframe) == 'undefined') { $iframe = $('#large-report1-iframe'); }
	else { $iframe = $(p_iframe); }
	$('#large_report_modal').modal('show');
	$('#large_report_modal .modal-title').html($header);
	var report_data = rpt_data;
	if (typeof (rpt_data) == 'undefined') { report_data = {} }
	$.ajax({
		cache: false,
		type: 'POST',
		url: $url,
		data: JSON.stringify(report_data),
		contentType: false,
		processData: false,
		xhrFields: {
			responseType: 'blob'
		},
		success: function (response, status, xhr) {
			var blob1 = new Blob([response], { type: 'application/pdf' }),
				urlx = URL.createObjectURL(blob1);

			if (window.navigator && window.navigator.msSaveOrOpenBlob) { // IE workaround
				$('#large_report_modal').modal('hide');
				window.navigator.msSaveOrOpenBlob(blob1, $fileName);
				toastr.success('Click open to view the ' + $header, $fileName, { timeOut: 3000 })
			} else {
				var iframe = $iframe;
				iframe.attr('src', urlx + '#page=1&zoom=page');
			}

		},
		error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }

	});
}


function jsDateToDisplay($date) {
	var d = $date.toISOString().substr(0, 10).split('-');
	return d[2] + '/' + d[1] + '/' + d[0];
}


loadOptions();
function loadOptions() {
	opt_list = [];
	show_dashboard = false;
	$.ajax({
		url: './app/menu_item/select',
		type: 'GET',
		dataType: 'json',
		success: function (data) {
			var option = data;
			var txt = '', current_menu = '', ul_tag_open = false;
			for (var i = 0; i < option.length; i++) {
				if (option[i].title == 'Dashboard') { show_dashboard = true; }
				if (current_menu != option[i].g_title) {
					current_menu = option[i].g_title;
					if (ul_tag_open) txt += '</ul>';
					ul_tag_open = false;
					if (i != 0) txt += '</li>';
					var menu_url = '';
					var g_url = (option[i].g_url == null ? '' : option[i].g_url);
					if (g_url.length > 0) { menu_url = 'href="' + option[i].g_url + '"' }
					txt += '<li><a ' + menu_url + '><i class="' + option[i].g_icon + '"></i>' + option[i].g_title + '<span class="fa fa-chevron-down"></span></a>';
					if (g_url.length == 0) { txt += '<ul class="nav child_menu" >'; ul_tag_open = true; }
					if (menu_url != '') { opt_list.push(option[i].g_url) }
				}
				var url = (option[i].url == null ? '' : option[i].url);
				if (url.length > 0) {
					txt += ' <li><a href="' + option[i].url + '">' + option[i].title + '</a></li>';
					opt_list.push(option[i].url);
				}

			}
			if (ul_tag_open) txt += '</ul>';
			txt += '</li>';
			$('.side-menu').html(txt);

		},
		async: false,
		error: function (xhr, ajaxOptions, thrownError) { handleStandardHttpErrors(xhr, ajaxOptions, thrownError) },
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
	});
}
if (token) { 
	var opt = window.location.pathname.split('/').slice(-1)[0];
	var runcode = false;
	for (var i = 0; i < opt_list.length; i++) {
		if (opt_list[i] == opt) { runcode = true; }
	}
	
	if (!runcode && (opt!='index.html' && opt!='') && opt!='CRUDcreator.html'  && opt!='tempCrud.html'  && opt!='tempCrudV.html'  && opt!='tempCrudVision.html') {
		//console.log(token);
		//console.log(opt);
		console.log(opt_list); 
		window.location = './index.html';
		token = ''; saveToken();
	}
	/* TBC
	$.ajax({
		url: './app/load/' + window.location.pathname.split('/').slice(-1)[0],
		type: 'GET',
		dataType: 'text',
		async: false,
		success: function (data) {
		},
		error: function (xhr, ajaxOptions, thrownError) {
			token = ''; saveToken(); window.location = './index.html';
		},
		beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }
	});
	*/
}

function parseJwt() {	
	if (token){
	var base64Url = token.split('.')[1];
	var base64 = base64Url.replace('-', '+').replace('_', '/');
	return JSON.parse(window.atob(base64));
	}
}
//Dislay user
try{$('.user_fullname_disaplay').html(parseJwt().full_name); }catch(e){}

function displayHelp(){
	window.open("./help/bolsoft.html"); 
}
document.onhelp = function() { displayHelp(); } 

function CSVtoArray(text) {
    var re_valid = /^\s*(?:'[^'\\]*(?:\\[\S\s][^'\\]*)*'|"[^"\\]*(?:\\[\S\s][^"\\]*)*"|[^,'"\s\\]*(?:\s+[^,'"\s\\]+)*)\s*(?:,\s*(?:'[^'\\]*(?:\\[\S\s][^'\\]*)*'|"[^"\\]*(?:\\[\S\s][^"\\]*)*"|[^,'"\s\\]*(?:\s+[^,'"\s\\]+)*)\s*)*$/;
    var re_value = /(?!\s*$)\s*(?:'([^'\\]*(?:\\[\S\s][^'\\]*)*)'|"([^"\\]*(?:\\[\S\s][^"\\]*)*)"|([^,'"\s\\]*(?:\s+[^,'"\s\\]+)*))\s*(?:,|$)/g;

    // Return NULL if input string is not well formed CSV string.
    if (!re_valid.test(text)) return null;

    var a = []; // Initialize array to receive values.
    text.replace(re_value, // "Walk" the string using replace with callback.
        function(m0, m1, m2, m3) {

            // Remove backslash from \' in single quoted values.
            if (m1 !== undefined) a.push(m1.replace(/\\'/g, "'"));

            // Remove backslash from \" in double quoted values.
            else if (m2 !== undefined) a.push(m2.replace(/\\"/g, '"'));
            else if (m3 !== undefined) a.push(m3);
            return ''; // Return empty string.
        });

    // Handle special case of empty last value.
    if (/,\s*$/.test(text)) a.push('');
    return a;
}

$( ".modal" ).each(function() {
	$( this ).draggable({
	   handle: ".modal-header"
	 });
  });

