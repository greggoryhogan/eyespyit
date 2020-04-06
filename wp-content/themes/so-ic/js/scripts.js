/**
 * Scripts that run on front end
 */
(function($) {

	$(document).ready(function(){

        //hanlde ios 100vh bug
        window.onresize = function() {
            document.body.height = window.innerHeight;
            $('main').height(window.innerHeight);
            $('.wcContainer .wcMessages').height(window.innerHeight * .6);
        }
        window.onresize(); // called to initially set the height.

        //format phone number
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

        //verify phone number
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
            $.ajax({
                url: settings.ajaxurl,
                type: 'post',
                data: data,
                success: function () {
                    
                },  
            });

        });

        //handle code verification
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
                $.ajax({
                    url: settings.ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (response) {
                        if(response == 'error') {
                            $('#verificationerror').text('Incorrect Code');
                        } else if(response == 'banned') {
                            $('#verificationerror').text('This account has been suspended.');
                        } else {
                            $('#verificationerror').text('Success!');
                            $('body').attr('data-user',response)
                            setTimeout(function() {
                                $('.login-form .step1').addClass('done moredone');
                            },600);
                            
                        }
                    }
                });
            }
        });

        //handle setting name initially
        $(document).on('click', '#addname', function(e) {
            var name = $('#name').val();
            var user_id = $('body').attr('data-user');
            if (name.length < 1) {
                $('#name').addClass('error');
            } else {
                var data = {
                    'action': 'save_user_name',
                    'user_id' : user_id,
                    'name' : name,
                };
                $.ajax({
                    url: settings.ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (response) {
                        if(response == 'error') {
                            $('#nameerror').text('Enter a name');
                        } else {
                            $('#nameerror').text('Thanks for joining '+name+'!');
                            setTimeout(function() {
                                location.reload(true);
                            },600);     
                        }
                    }
                });
            }
        });

        //left navigation trigger
        $('.navtrigger').click(function() {
            if($(this).hasClass('is-active')) {
                $('nav.navigation').removeClass('push');
                $('main').removeClass('push');
                $('.navtrigger').removeClass('is-active');
            } else {
                $('.chattrigger').removeClass('is-active');
                $('.navtrigger').addClass('is-active');
                $('nav.navigation').addClass('push');
                $('main').removeClass('push-right');
                $('main').addClass('push');
                $('nav.chat').removeClass('push-right');
            }
        });
        
        //right navigation trigger
        $('.chattrigger').click(function() {
            if($(this).hasClass('is-active')) {
                $('nav.chat').removeClass('push-right');
                $('main').removeClass('push-right');
                $('.chattrigger').removeClass('is-active');
            } else {
                $('.navtrigger').removeClass('is-active');
                $('.chattrigger').addClass('is-active');
                $('nav.chat').addClass('push-right');
                $('main').removeClass('push');
                $('main').addClass('push-right');
                $('nav.navigation').removeClass('push');
            }
        });

        //update user display name
        $('.displayname .checkcontainer').click(function(e) {
            var name = $('#displayname').val();
            var user_id = $('body').attr('data-user');
            if (name.length < 1) {
                $('#displayname').addClass('error');
            } else {
                var data = {
                    'action': 'save_user_name',
                    'user_id' : user_id,
                    'name' : name,
                };
                $.ajax({
                    url: settings.ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (response) {
                        $('.displayname .checkcontainer').addClass('zoomed');
                        setTimeout(function() {
                            $('.displayname .checkcontainer').removeClass('zoomed');
                        },500);
                    }
                });
            }
        });

        //report image from sidebar
        $('#reportimage').click(function(e) {
            var post_id = $('body').attr('data-id');
            var user_id = $('body').attr('data-user');
            var data = {
                'action': 'report_image',
                'post_id' : post_id,
                'user_id' : user_id
            };
            $.ajax({
                url: settings.ajaxurl,
                type: 'post',
                data: data,
                success: function (response) {
                    $('#reportimage').text(response);
                    setTimeout(function() {
                        $('#reportimage').text('Report Image');
                    },2500);   
                }
            });
        });
    });
})( jQuery );