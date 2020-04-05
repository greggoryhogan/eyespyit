/**
 * Scripts that run on front end
 */
(function($) {

	$(document).ready(function(){

        window.onresize = function() {
            document.body.height = window.innerHeight;
        }
        window.onresize(); // called to initially set the height.

        $('#phonenumber').keyup(function(e){
            var ph = this.value.replace(/\D/g,'').substring(0,10);
            // Backspace and Delete keys
            var deleteKey = (e.keyCode == 8 || e.keyCode == 46);
            var len = ph.length;
            if(len==0){
                ph=ph;
            }else if(len<3){
                ph='('+ph;
            }else if(len==3){
                ph = '('+ph + (deleteKey ? '' : ') ');
            }else if(len<6){
                ph='('+ph.substring(0,3)+') '+ph.substring(3,6);
            }else if(len==6){
                ph='('+ph.substring(0,3)+') '+ph.substring(3,6)+ (deleteKey ? '' : '-');
            }else{
                ph='('+ph.substring(0,3)+') '+ph.substring(3,6)+'-'+ph.substring(6,10);
            }
            this.value = ph;
        });

        $(document).on('click', '#verifyphone', function(e) {
            $('#phonenumber').removeClass('error');
            var phone = $('#phonenumber').val();
            var regEx = /^(\+\d)*\s*(\(\d{3}\)\s*)*\d{3}(-{0,1}|\s{0,1})\d{2}(-{0,1}|\s{0,1})\d{2}$/;
            if (!phone.match(regEx)) {
                $('#phonenumber').addClass('error');
            } else {
                $('.login-form .step1').addClass('done');
            }
            
            var data = {
				'action': 'verify_phone',
				'phone' : phone,
			};
            //ajax
            $.ajax({
                url: settings.ajaxurl,
                type: 'post',
                data: data,
                success: function (response) {
                    
                },  
                error: function (response) {
                   
                }

            });

        });

        $(document).on('click', '#verifycode', function(e) {
            var code = $('#code').val();
            if (code.length < 6) {
                $('#code').addClass('error');
            } else {
                var phone = $('#phonenumber').val();
                var data = {
                    'action': 'verify_code',
                    'phone' : phone,
                    'code' : code,
                };
                //ajax
                $.ajax({
                    url: settings.ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (response) {
                        if(response == 'error') {
                            $('#verificationerror').text('Incorrect Code');
                        } else {
                            $('#verificationerror').text('Success!');
                            var user_id = response;
                            $('.marker.inspector').attr('data-user',user_id);
                            $('.modal-login').fadeOut();
                        }
                        
                    },  
                    error: function (response) {
                        
                    }

                });
            }

        });
        
          
    });
})( jQuery );