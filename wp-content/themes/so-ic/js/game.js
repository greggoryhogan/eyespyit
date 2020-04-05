/**
 * Scripts that run on front end
 */
(function($) {

	$(document).ready(function(){
        
        window.onresize = function() {
            document.body.height = window.innerHeight;
        }
        window.onresize(); // called to initially set the height.
        
        if($('.ictext').length) {
            setTimeout(function() {
                $('.ictext').addClass('hidden');
            },2000);
        }

        $('.toggletip').click(function() {
            $('.ictext').toggleClass('hidden');
        });

        $('#ic-image').change(function() {
            $('#upload-ic').submit();
        });
        $('#upload-ic').submit(function(e) {
            e.preventDefault();
            if ($('#ic-image').prop('files')[0]) {
                var form_data = new FormData();
                var file = $('input[type=file]')[0].files[0];
                var post_id = $('#post_id').val();
                form_data.append('action', 'save_ic_image');
                form_data.append('file', file);
                form_data.append('post_id', post_id);

                $('.image-upload-wrap .drag-text h3').hide();
                $('.image-upload-wrap .drag-text .loading-icon').addClass('active');
                
                
                $.ajax({
                    url: settings.ajaxurl,
                    type: 'post',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (response) {
                        $('.image-upload-wrap').fadeOut();
                        $('.content-wrapper .content').fadeOut();
                        $('#commander-image').html('<img src="'+response+'" />').fadeIn();
                        $('#commander-content').addClass('active');
                        $('#commander-overlay').addClass('active');
                        $('.step.step1').addClass('on');
                    },  
                    error: function (response) {
                       
                    }

                });      
            
            } else {
                removeUpload();
            }
        });
          
        function removeUpload() {
            $('.file-upload-input').replaceWith($('.file-upload-input').clone());
            $('.file-upload-content').hide();
            $('.image-upload-wrap').show();
        }

        var left = 0;
        var top = 0;

        $(document).on('click', '#commander-overlay .tempit', function(e) {
            if($('.step.step1').hasClass('on')) {
                $('.step.step1').removeClass('on');
                $('.step.step2').addClass('on');
            }
            if(!$('.tempit').hasClass('tempting')) {
                $('.tempit').addClass('tempting');
            }
            $('#commander-overlay .placeit').remove();

            var width = $(window).width();
            var height = $(window).height();
            left = (e.pageX / width) * 100;
            top = (e.pageY / height) * 100;

            $('#commander-overlay').append('<div class="placeit" style="left: '+e.pageX+'px; top:'+e.pageY+'px;"><div class="checkmark"><div class="check"></div></div></div>');
        });

        $(document).on('click', '#commander-overlay .checkmark', function(e) {
            $('.step.step2').removeClass('on');
            $('.step.step3').addClass('on');
            $('.placeit').addClass('placed');
            $('#inspectorhint').focus();
        });

        $(document).on('click', '.step3 .checkarea', function(e) {
            $('.step.step3.text').removeClass('on');
            $('.step.step4').addClass('on');
            $('.step.step3.inputarea').addClass('done');

            var text = $('#inspectorhint').val();
            var post_id = $('#inspectorhint').attr('data-id');
            var user_id = $('#inspectorhint').attr('data-user');
            var form_data = new FormData();
            form_data.append('action', 'start_ic_round');
            form_data.append('text', text);
            form_data.append('left', left);
            form_data.append('top', top);
            form_data.append('post_id', post_id);
            form_data.append('user_id', user_id);
            //ajax
            $.ajax({
                url: settings.ajaxurl,
                type: 'post',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
                    $('#commander-overlay').addClass('activated');
                    $('.step').removeClass('on');
                    $('.step5').addClass('on');
                },  
                error: function (response) {
                    
                }

            });

        });

        

        $(document).on('click', '#inspector-image', function(e) {
            var user_id = $('.marker.inspector').attr('data-user');
            if(user_id < 1) {
                $('.modal-login').show().removeClass('unneeded');
            } else {
                if($('.wrongmarker.right').length) {

                } else {
                    $('#inspector-image').append('<div class="wrongmarker" style="left: '+e.pageX+'px; top:'+e.pageY+'px;">x</div>');
                } 
            }
        });
        $(document).on('click', '.marker.inspector', function(e) {
            $('#inspector-image').append('<div class="wrongmarker right" style="left: '+e.pageX+'px; top:'+e.pageY+'px;"><div class="check"></div></div>');
            var post_id = $(this).attr('data-id');
            var user_id = $(this).attr('data-user');
            if(user_id < 1) {
                $('.modal-login').show().removeClass('unneeded');
            } else {
                var data = {
                    'action': 'end_ic_round',
                    'user_id' : user_id,
                    'post_id' : post_id
                };
                $.ajax({
                    url: settings.ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (response) {
                        
                    },  
                    error: function (response) {
                       
                    }
    
                });
            }
        });
          
    });
})( jQuery );