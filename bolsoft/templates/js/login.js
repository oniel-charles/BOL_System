
    $(document).ready(function () {
        $("#loginModal").modal('show');
console.log('load token  ='+token);
        $("#submit").click(function (e) {
            token='';
            $('#app-cover').show();
            var $url= './app/login';
            if ($("#password").val()=='') { return ;}
	    if ($("#user_name").val()=='') { return ;}
    e.preventDefault();    //***************
   
                var form_data = {};
                form_data['password'] = btoa($('#pwd').val());
                form_data['user_name'] = $('#user_name').val();
                //form_data['access_type'] = 'login';
                $.ajax({
                    type: "POST",
                    url: $url,
                    dataType: 'json',
                    data: form_data, // serializes the form's elements.                        
                    success: function (data) {
                        console.log('after add -1' + data.token);
                        token=data.token;
                        $('#app-cover').hide();
                        $("#loginModal").modal('hide');
                        //window.location.href = "./#";

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(xhr.statusText);
                        console.log(xhr.responseText);
                    },
                        beforeSend: function (xhr, settings) { xhr.setRequestHeader('Authorization', 'Bearer ' + token); }

                });
            
    //*********
	
        });
    });