var acl_value={};
var items = ["#996600", "#003366", "#336699", "#00cc66", "#ff6666"];
var status_arr=['<i class="fa fa-fw fa-circle" style="color:#DA3838"></i>', '<i class="fa fa-fw fa-circle" style="color:#1D9E74"></i>'];
var gateway_arr=['<i class="fa fa-times" style="color:#DA3838"></i>', '<i class="fa fa-check" style="color:#1D9E74"></i>'];
var internet_arr=['<i class="fa fa-globe" style="color:#393333"></i>', '<i class="fa fa-globe" style="color:#1D9E74"></i>'];
var btn_arr=['#393333', '#1D9E74'];
var biderection_arr=[
    '<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>',
    '<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i>',
    '<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right"></i>',
    '<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i>'
];
var group_arr=['<span style="color: #ea4335;"><strong>A</strong></span>', '<span style="color: #839D1C;"><strong>B</strong></span>', '<span style="color: #00A998;"><strong>C</strong></span>', '<span style="color: #F6AE00;"><strong>D</strong></span>', '<span style="color: #4285F4;"><strong>E</strong></span>', '<span style="color: #330033;"><strong>F</strong></span>', '<span style="color: #FF404E;"><strong>G</strong></span>', '<span style="color: #FFFF00;"><strong>H</strong></span>', '<span style="color: #FF3300;"><strong>I</strong></span>', '<span style="color: #CC6600;"><strong>J</strong></span>', '<span style="color: #9999CC;"><strong>K</strong></span>', '<span style="color: #0000CC;"><strong>L</strong></span>', '<span style="color: #FF0000;"><strong>M</strong></span>', '<span style="color: #003366;"><strong>N</strong></span>', '<span style="color: #003333;"><strong>0</strong></span>', '<span style="color: #FF00CC;"><strong>P</strong></span>', '<span style="color: #FF0066;"><strong>Q</strong></span>', '<span style="color: #CC0000;"><strong>R</strong></span>', '<span style="color: #CC6600;"><strong>S</strong></span>', '<span style="color: #666666;"><strong>T</strong></span>', '<span style="color: #330066;"><strong>U</strong></span>', '<span style="color: #CC99CC;"><strong>V</strong></span>', '<span style="color: #FFCC66;"><strong>W</strong></span>', '<span style="color: #FF3399;"><strong>X</strong></span>', '<span style="color: #99CCFF;"><strong>Y</strong></span>', '<span style="color: #0099FF;"><strong>Z</strong></span>'];
var box_btn_val;
$(document).ready(function(){


    dialog = $( "#dialog-form" ).dialog({
        minWidth: 700,
        modal: false,
        autoOpen: false
    });

    dialog_sponsore = $( "#dialog-sponsore-form" ).dialog({
        minWidth: 700,
        modal: false,
        autoOpen: false
    });

    $.ajax({
        url:"request.php?request=job_queue_info",
        success:function(resp){
            var data = $.parseJSON(resp);
            console.log(data);
            $.each(data, function(key, item){
                $(".tunnel_"+item).html("<i class='fa fa-fw fa-circle-o-notch fa-spin'></i>");
                $(".tunnel_"+item).removeClass("tunnel_chk");
                $(".tunnel_"+item).attr("data-original-title", "Request submitted, please wait...");
            });
        }
    });

    $.fn.editable.defaults.mode = 'popup';

    $('body').on('click','.email',function(e){
        var ths=$(this);
        $(this).editable({
            type: 'text',
            url:'request.php?request=edit_email&token='+token,
            ajaxOptions: {
                type: 'POST',
                success:function(resp){
                    data=$.parseJSON(resp);
                    if(data.status==1){
                        //notify_msg("success", data.data);
                         ths.editable('toggleDisabled');
                        // ths.parent("div").attr("data-original-title", "This field is processing its last job, please wait...");
                        send(resp);
                    }
                }
            }
        });
    });
//
    $('body').on('mouseover','.fa-circle-o-notch.fa-spin',function(e){
        var pt = $(this).parent();
        pt.css("position", "relative");
        pt.css("overflow", "visible");
        pt.tooltip('disable');
        if(pt.data('id') > 0)
        {
            pt.append('<div id="progstat_' + pt.data('id') + '" style="display: block; position: absolute; top: 20px; left: 0; width: 100px; z-index: 1000; background-color: white; border: 1px solid grey;">Loading...</div>');

            $.ajax({
                url:"request.php?request=task_server_status&tunnelid="+pt.data('id'),
                success:function(resp){
                    var res = $.parseJSON(resp);
                    var html = '';
                    for(var srv in res) {
                        html += res[srv].remote_ip + '<br />';
                    }
                    $("#progstat_" + pt.data('id')).html(html);
                }
            });

        }
    });
    $('body').on('mouseout','.fa-circle-o-notch.fa-spin',function(e){
        var pt = $(this).parent();
        $("#progstat_" + pt.data('id')).remove();
    });
    $('body').on('click','.display',function(e){
        if($(".tunnel_"+$(this).attr("data-pk")).attr("data-val")==1){
            var ths=$(this);
            $(this).editable({
                type: 'text',
                url:'request.php?request=edit_display&token='+token,
                ajaxOptions: {
                    type: 'POST',
                    success:function(resp){
                        data=$.parseJSON(resp);
                        if(data.status==1){
                            ths.editable('toggleDisabled');
                            //ths.parent("div").attr("data-original-title", "This field is processing its last job, please wait...");
                            send(resp);
                        }
                    }
                }
            });
        }
    });

    var location_option;
    $.ajax({
        url:"request.php?request=get_server_name",
        success:function(resp){
            //alert(resp);
            location_option=$.parseJSON(resp);
            //alert(location_option);
        }
    });

    $('#login-form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            email: "Please enter a valid email address",
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            }
        },
        submitHandler: function(form) {
            $('#login-form-submit-btn').attr('disabled', 'disabled');
            $.ajax({
                url : "request.php?request=dologin",
                type : "POST",
                data : $("#login-form").serialize(),
                success : function(resp){
                    console.log(resp);

                    $('#login-form-submit-btn').removeAttr('disabled');
                    $('#login-response-message').show();
                    try {
                        var result = $.parseJSON(resp);

                        if (result.status == 1) {

                            $('#login-response-message').html(result.data);
                            $(form)[0].reset();
                            setTimeout(function () {
                                window.location.href = "main.php"
                            }, 2000);
                        }
                        else if (result.status == 0) {

                            $('#login-response-message').html(result.data);
                        }else if (result.status == 2) {

                            $('#login-response-message').html(result.data);
                            $(form)[0].reset();
                            setTimeout(function () {
                                window.location.href = "main.php"
                            }, 2000);
                        }
                        else {
                            $('#login-response-message').html("can not process your request, try again");
                        }
                    }
                    catch (e){
                        $('#login-response-message').html("unexpected error occurred, try again");
                    }
                },
                error : function(){
                }
            });
            return false;
        }
    });

    $('#customButton').on('click', function(e) {
        // Open Checkout with further options
        handler.open({
          name: 'Account',
          description: '',
          amount: $('.txt_amount').val()*100
        });
        e.preventDefault();
      });
      // Close Checkout on page navigation
    $(window).on('popstate', function() {
        //handler.close();
        //window.location.href="request.php?request=stripe_cancel";
      });

    $('.btn-plan-save').on('click', function(e) {
           var selectedVal = "";
           var selected = $("input[type='radio'][name='radio']:checked");
       if (selected.length > 0) {
           selectedVal = selected.val();
       }
       $.ajax({
           url:'request.php?request=update_plan',
           type:'POST',
           data:'selectedVal='+selectedVal,
           success:function(resp){
               //alert(resp);
               var data=$.parseJSON(resp);
               if(data.status==1){
                 send(resp);
                 $("#edtplan-response-message").html('<div class="alert alert-success">'+data.data+"</div>");
                 setTimeout(function(){
                   $("#edtplan-response-message").html('');
                 }, 3000);
               }
           }
       });
    });

    $('#btn-edit-profile').click(function(){
       $.ajax({
           url:'request.php?request=update_profile',
           type:'POST',
           data:$('#profile_form').serialize(),
           success:function(resp){
              console.log(resp);
               var data=$.parseJSON(resp);
               if(data.status==1){
               send(resp);
               $("#edtprofile-response-message").html('<div class="alert alert-success">'+data.data+"</div>");
               setTimeout(function(){
                   $("#edtprofile-response-message").html('');
                 }, 3000);
               }
           }
       });
   });

    $('#change_password_form').validate({
        rules: {
            opassword: {
                required: true,
                minlength: 6
            },
            password: {
                required: true,
                minlength: 6
            },
            cfmPassword: {
                required: true,
                equalTo: "#password"
            }
        },
            messages: {
                opassword: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                },
            password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                },
            cfmPassword: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                }
        },
        submitHandler: function(form) {
            $.ajax({
            url:'request.php?request=changePassword',
            type:'POST',
            data:$('#change_password_form').serialize(),
            success:function(resp){
                var data=$.parseJSON(resp);
                if(data.status==1){
                  $("#edtpass-response-message").html('<div class="alert alert-success">'+data.data+"</div>");
                  setTimeout(function(){
                    $("#edtpass-response-message").html('');
                  }, 2000);
                    $('#opassword').val('');
                    $('#password').val('');
                    $('#cfmPassword').val('');
                } else {
                  $("#edtpass-response-message").html('<div class="alert alert-danger">'+data.data+"</div>");
                }
            }
        });
    return false;
    }
});

    $('#reg-form').validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },

            password: {
                required: true,
                minlength: 6
            },
            cpassword: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            email: "Please enter a valid email address",
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            },
            cpassword: {
                required: "Please provide a password",
                equalTo: "CPassword not matched with "
            }
        },
        submitHandler: function(form) {
            $('#reg-form-submit-btn').attr('disabled', 'disabled');
            $.ajax({
                url : "request.php?request=user_register",
                type : "POST",
                data : $("#reg-form").serialize(),
                success : function(resp){
                    //alert(resp);
                    $('#reg-form-submit-btn').removeAttr('disabled');
                    $('#reg-response-message').show();
                    try {
                        var result = $.parseJSON(resp);
                        if (result.status == 1) {
                            $('#reg-response-message').html(result.data);
                            $(form)[0].reset();
                            setTimeout(function () {
                                window.location.href = "login.php"
                            }, 2000);
                        }
                        else if (result.status == 0) {
                            $('#reg-response-message').html(result.data);
                        }else if (result.status == 2) {
                            $('#reg-response-message').html(result.data);
                            $(form)[0].reset();
                            setTimeout(function () {
                                window.location.href = "registration.php"
                            }, 2000);
                        }
                        else {
                            $('#reg-response-message').html("can not process your request, try again");
                        }
                    }
                    catch (e){
                        $('#reg-response-message').html("unexpected error occurred, try again");
                    }
                },
                error : function(){
                }
            });
            return false;
        }
    });

    $('#add_voucher_form').validate({
        rules: {
            voucher: {
                required: true,
            }
        },
        messages: {
            voucher: {
                required: "Please provide a Voucher Code"
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url:'request.php?request=add_voucher',
                type:'POST',
                data : $(form).serialize(),
                success : function(resp){
                    //alert(resp);
                        var result = $.parseJSON(resp);
                        //alert(result.status);
                        if(result.status == 1){
                            $('#manual_voucher_success_message').show();
                            $('#manual_voucher_error_message').hide();
                            $('#manual_voucher_success_message').html(result.data);
                            setTimeout(function(){location.reload()}, 2000)
                        }
                        else{
                            $('#manual_voucher_success_message').hide();
                            $('#manual_voucher_error_message').html(result.data);
                            $('#manual_voucher_error_message').show();
                        }
                }
            });
            return false;
        }
    });

    $('#add_contact_manually_form').validate({
        rules: {
            contact_name: {
                required: true
            },
            contact_mail: {
                required: true,
                email: true
            }
        },
        messages: {
            contact_name: {
                required: "Please provide a Name"
            },
            contact_mail: {
                required: "Please enter a valid email address"
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url:'request.php?request=add_contact',
                type:'POST',
                data : $(form).serialize(),
                success : function(resp){
                    //alert(resp);
                    var result = $.parseJSON(resp);
                    //alert(result.status);
                    if(result.status == 1){
                        $('#manual_add_message').html('<div class="alert alert-success">'+result.data+"</div>");
                        setTimeout(function(){location.reload()}, 2000)
                    } else{
                        $('#manual_add_message').html('<div class="alert alert-danger">'+result.data+"</div>");
                    }
                }
            });
            return false;
        }
    });

    $("body").on("click", "#add_cloud_btn", function(){
        var cloud_name=$("#cloud_name").val();
        var cloud_email="abcd@email.com";
        var get={
            "type":"add_cloud",
            "message_type":"request",
            "data":{
                "token":token,
                "cloud_name":cloud_name,
                "cloud_email":cloud_email
            }
        };
        send(JSON.stringify(get));
    });

    $("body").on("click", "#tunnel_form_close_btn", function(){
        $("#tunnel_form").html("");
        $("#tunnels_form_field").css("display", "none");
    });

    $('body').on('click', '.tunnel_add_form_btn',function(){
        var cloud=$(this).attr("data-cloud");
        console.log('add tunnel');
        var get={
            "type":"add_tunnel",
            "message_type":"request",
            "data":{
                "token":token,
                "cloud_id":$(this).attr("data-val"),
                "mail_id":$(this).attr("data-mail")
            }
        };
        send(JSON.stringify(get));
    });

    $("body").on("click", ".delete_tunnel", function(){
        var id=$(this).attr('data-id');
        if($(".tunnel_"+id).attr("data-val")==1){
            var type=$(this).attr('data-type');
            var ths=$(this);
            var deletable=false;
            $.ajax({
                url:"request.php?request=check_tunnel_sponsored&id="+id,
                success:function(res){
                    console.log(res);
                    resp=jQuery.parseJSON(res);
                    if(resp.status=='1'){
                        var str="This tunnel is shared with "+resp.shared_with;
                        alert(str);
                        deletable=false;
                    }else{
                        deletable=true;
                    }
                    if(deletable){
                        if(confirm("Are you sure?")){
                            var get={
                                "type":"delete_tunnel",
                                "message_type":"request",
                                "data":{
                                    "id":id,
                                    "type":type,
                                    "token":token
                                }
                            };
                            send(JSON.stringify(get));
                            $(".tunnel_"+id).html('<i class="fa fa-fw fa-circle-o-notch fa-spin"></i>');
                        }
                    }
                }
            });
        }else{
            notify_msg("error", "Please 1st select this tunnel");
        }
    });

    //status change
    $('body').on('click','.status',function(e){
        console.log('status_change');
        var id = $(this).attr('data-id');
        var type=$(this).attr("type");
        if(type=="data"){
            if($(".tunnel_"+id).attr('data-val')==1){
                var key = parseInt($(".tunnel_stat_"+id).attr('data-val'));
                if(key==0){
                    key=1;
                    $(".tunnel_stat_"+id).attr('data-val',key);
                    $(".tunnel_stat_"+id).html(status_arr[key]);
                }else{
                    key=0;
                    $(".tunnel_stat_"+id).attr('data-val',key);
                    $(".tunnel_stat_"+id).html(status_arr[key]);
                }
                var get={
                    "type":"status_change",
                    "message_type":"request",
                    "data":{
                        "id":id,
                        "val":key,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }
        }
    });

    //gateway change
    $('body').on('click','.gateway',function(e){
        var id = $(this).attr('data-id');
        var type=$(this).attr("type");
        if(type=="data"){
            if($(".tunnel_"+id).attr('data-val')==1) {
                var key = parseInt($(".tunnel_gate_" + id).attr('data-val'));
                if (key == 0) {
                    key = 1;
                    $('.gateway_' + id).val(key);
                    $(".tunnel_gate_" + id).attr('data-val', key);
                    $(".tunnel_gate_" + id).html(gateway_arr[key]);
                } else {
                    key = 0;
                    $('.gateway_' + id).val(key);
                    $(".tunnel_gate_" + id).attr('data-val', key);
                    $(".tunnel_gate_" + id).html(gateway_arr[key]);
                }
                var get = {
                    "type": "gateway_change",
                    "message_type": "request",
                    "data": {
                        "id": id,
                        "val": key,
                        "token": token
                    }
                };
                send(JSON.stringify(get));
            }
        }
    });

    $('body').on('click','.biderection',function(e){
        var id = $(this).attr('data-id');
        var type=$(this).attr("type");
        if(type=="data"){
            if($(this).hasClass("inactive")==false){
                if($(".tunnel_"+id).attr('data-val')==1){
                    var key = parseInt($(".tunnel_bi_"+id).attr('data-val'));
                    key=key+1;
                    if(key in biderection_arr){
                        $(".tunnel_bi_"+id).attr('data-val',key);
                        $(".tunnel_bi_"+id).html(biderection_arr[key]);
                    }else{
                        key=0;
                        if(key in biderection_arr){
                            $(".tunnel_bi_"+id).attr('data-val',key);
                            $(".tunnel_bi_"+id).html(biderection_arr[key]);
                        }
                    }
                    var get = {
                        "type": "bidirection_change",
                        "message_type": "request",
                        "data": {
                            "id": id,
                            "val": key,
                            "token": token
                        }
                    };
                    send(JSON.stringify(get));
                }
            }
        }



    });

    $('body').on('click','.group',function(e){
        var type=$(this).attr("type");
        var ths=$(this);
        if(type=="data"){
            var data_val=$(this).attr('data-id');
            var val=$(this).attr('data-val');
            if($(".tunnel_"+data_val).attr('data-val')==1)
            {
                $(".tunnel_chk").each(function(){
                    if($(this).attr('data-val')==1){
                        var id=$(this).attr('data-id');
                        var j=$(ths).attr('data-pos');
                        if(j>0){
                            var key=parseInt($(".tunnel_grp_"+id).attr("data-val"));
                            key=key+1;
                        }else{
                            key=parseInt($(ths).attr("data-val"));
                        }
                        //alert(key);
                        if(key in group_arr){
                            $(".tunnel_grp_"+id).attr('data-val',key);
                            $(".tunnel_grp_"+id).html(group_arr[key]);
                            $(ths).html(group_arr[key]);
                            $(".tunnel_grp_"+id).parent('.tunnel_chk').addClass('tunnel_grp_chk_'+key);
                        }else{
                            key=0;
                            if(key in group_arr){
                                $(".tunnel_grp_"+id).attr('data-val',key);
                                $(".tunnel_grp_"+id).html(group_arr[key]);
                                $(ths).html(group_arr[key]);
                                $(".tunnel_grp_"+id).parent('.tunnel_chk').addClass('tunnel_grp_chk_'+key);
                            }
                        }
                        $.ajax({
                            url:"request.php?request=group_change&id="+id+"&val="+key+"&token="+token,
                            success:function(resp){
                                var data=$.parseJSON(resp);
                                if(data.status==1){

                                }
                            }
                        });
                    }
                });
                $(".group").attr('data-pos', 1);
            }else if($(".tunnel_"+data_val).attr('data-val')==0)
            {
                var i=0;
                $(".group").attr('data-pos', 0);
                $(".tunnel_chk").each(function(){
                    if($(this).attr('data-val')==1){
                        var id=$(this).attr('data-id');
                        var key=parseInt($(ths).attr("data-val"));
                        if(key in group_arr){
                            $(".tunnel_grp_"+id).attr('data-val',key);
                            $(".tunnel_grp_"+id).html(group_arr[key]);
                            $(ths).html(group_arr[key]);
                            $(".tunnel_grp_"+id).parent('.tunnel_chk').addClass('tunnel_grp_chk_'+key);
                        }else{
                            key=0;
                            if(key in group_arr){
                                $(".tunnel_grp_"+id).attr('data-val',key);
                                $(".tunnel_grp_"+id).html(group_arr[key]);
                                $(ths).html(group_arr[key]);
                                $(".tunnel_grp_"+id).parent('.tunnel_chk').addClass('tunnel_grp_chk_'+key);
                            }
                        }
                        $.ajax({
                            url:"request.php?request=group_change&id="+id+"&val="+key+"&token="+token,
                            success:function(resp){
                                var data=$.parseJSON(resp);
                                if(data.status==1){

                                }
                            }
                        });
                        i=i+1;
                    }
                });
                if(i==0){
                    var val=$(this).attr("data-val");
                    if($(".tunnel_grp_chk_"+val).hasClass("tunnel_chk")){
                        $(".tunnel_grp_chk_"+val).html("<i class='fa fa-fw fa-check-square-o'></i>");
                        $(".tunnel_grp_chk_"+val).attr("data-val", 1);
                        $(".tunnel_grp_chk_"+val).attr("data-val", 1);
                        $(".tunnel_grp_chk_"+val).parent('.list_body').addClass('row_chk');
                    }
                }
            }
        }
    });

    //tunnel type change
    $("body").on("click", ".change_tunnel", function(){
        var id=$(this).attr("data-id");
        if($(".tunnel_"+id).attr("data-val")==1){
            var type=$(this).attr("data-type");
            if(type == "server")
            {
                type = "client";
                $(".change_tunnel_"+id).attr("data-type", type);
                $(".change_tunnel_"+id).css("opacity", "0.25");
                $(".change_tunnel_"+id).css("color", "black");
                $(".change_tunnel_"+id).css("background-color", "transparent");
            } else if(type == "client")
            {
                type = "server";
                $(".change_tunnel_"+id).attr("data-type", type);
                $(".change_tunnel_"+id).css("background-color", "#b9c3c8");
                $(".change_tunnel_"+id).css("opacity", "1");
                $(".change_tunnel_"+id).css("color", "white");
            }
            var get={
                "type":"change_tunnel",
                "message_type":"request",
                "data":{
                    "id":id,
                    "val":type,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }
    });
    //sponsore change
    $("body").on("click",".sponsore",function(){
        var ths=$(this);
        var tunnel_id=ths.attr('data-tid');
        var sponsored=false;
        if($(this).html()=='Sponsoring'){
            //var str="This tunnel is shared with "+resp.shared_with;
            var str="This tunnel will stop sponsoring.\n Are you sure?";
            if(confirm(str)){
                var get={
                    "type":"remove_sharing",
                    "message_type":"request",
                    "data":{
                        "tunnel_id":tunnel_id,
                        "user_id":current_customer_id,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }
            sponsored=true;
        }else{
            sponsored=false;
        }
        if(!sponsored){
            var cloud_id=ths.attr('data-cloud');
            var u_id=ths.attr('data-u');
            var tunnel_id=ths.attr('data-tid');
            var get={
                "type":"get_friend_list",
                "message_type":"request",
                "data":{
                    "cloud_id":cloud_id,
                    "tunnel_id":tunnel_id,
                    "u_id":u_id
                }
            };
            send(JSON.stringify(get));
        }
    });
    //internet change section
    $('body').on('click','.internet_change',function(e){
        var type=$(this).attr("type");
        if(type=="data"){
            var id=$(this).attr('data-id');
            if($(".tunnel_"+id).attr('data-val')==1){
                var key=($(".tunnel_internet_"+id).attr('data-val')==0 ? 1: 0);
                if(key==0){
                    $(".tunnel_internet_"+id).attr('data-val',key);
                    $(".tunnel_internet_"+id).css("background-color", "transparent");
                    $(".tunnel_internet_"+id).css("opacity", "0.35");
                    $(".tunnel_internet_"+id).css("color", "black");
                }else{
                    $(".tunnel_internet_"+id).attr('data-val',key);
                    $(".tunnel_internet_"+id).css("background-color", "#b9c3c8");
                    $(".tunnel_internet_"+id).css("opacity", "1");
                    $(".tunnel_internet_"+id).css("color", "white");
                }
                var get={
                    "type":"internet_change",
                    "message_type":"request",
                    "data":{
                        "id":id,
                        "val":key,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }
        }
    });

    //route change section
    $('body').on('click','.route_change',function(e){
        var type=$(this).attr("type");
        if(type=="data"){
            var id=$(this).attr('data-id');
            if($(".tunnel_"+id).attr('data-val')==1){
                var key=($(".tunnel_route_"+id).attr('data-val')==0 ? 1: 0);
                if(key==0){
                    $(".tunnel_route_"+id).attr('data-val',key);
                    $(".tunnel_route_"+id).css("background-color", "transparent");
                    $(".tunnel_route_"+id).css("opacity", "0.35");
                    $(".tunnel_route_"+id).css("color", "black");
                }else{
                    $(".tunnel_route_"+id).attr('data-val',key);
                    $(".tunnel_route_"+id).css("background-color", "#b9c3c8");
                    $(".tunnel_route_"+id).css("opacity", "1");
                    $(".tunnel_route_"+id).css("color", "white");
                }
                var get={
                    "type":"route_change",
                    "message_type":"request",
                    "data":{
                        "id":id,
                        "val":key,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }
        }
    });

    //set premium or not
    $("body").on("click", ".acc_type", function(){
        var id=$(this).attr("data-id");
        var ths=$(this);
        var val=ths.attr("data-val");
        if($(".tunnel_"+id).attr('data-val')==1){
            if (val == 1) {
                val = 2;
                $(".acc_type_" + id).attr("data-val", 2);
                $(".acc_type_" + id).html("Premium");
                $(".acc_type_" + id).css("opacity", "0.25");
                $(".acc_type_" + id).css("color", "black");
                $(".acc_type_" + id).css("background-color", "transparent");
            } else if (val == 2) {
                val = 1;
                $(".acc_type_" + id).attr("data-val", 1);
                $(".acc_type_" + id).html("Premium");
                $(".acc_type_" + id).css("color", "white");
                $(".acc_type_" + id).css("opacity", "1");
                $(".acc_type_" + id).css("background-color", "#b9c3c8");
            }
            var get={
                "type":"plan_change",
                "message_type":"request",
                "data":{
                    "id":id,
                    "val":val,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }
    });

    //dev-status change section
    $('body').on('click','.dev_status',function(e){
        var tid = $(this).data('tid');
        if($(".tunnel_"+tid).attr('data-val')==1){
            if($(".dev-status-label_"+tid).html()!='dev-connecting') {
                var dev_icon= '';
                var dev_message = '';
                $(this).html('<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>');

                $(".dev-status-label_"+tid).html('Initiating');
                var ths = $(this);
                var get={
                    "type":"change_dev_status",
                    "message_type":"request",
                    "data":{
                        "id":tid
                    }
                };
                send(JSON.stringify(get));
                /*$.ajax({
                    url: "request.php?request=dev_status_toggle&id=" + tid + "&token=" + token,
                    type: "POST",
                    success: function (resp) {
                        var res = $.parseJSON(resp);
                        console.log("dev_status success");
                        if (res.status == 1) {

                            if(res.message.st == 1){
                                dev_icon = '<i class="fa fa-times" aria-hidden="true"></i>';
                                dev_message = res.message.DeV;
                            }
                            else if(res.message.st == 0){
                                dev_icon = '<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>';
                                dev_message = 'Initiating';
                            }
                            else if(res.message.st == -1){
                                dev_icon = '<i class="fa fa-share-square-o" aria-hidden="true"></i>';
                                dev_message = 'Disconnected';
                            }
                            setTimeout(function(){
                                ths.html(dev_icon);
                                $(".dev-status-label_"+tid).html(dev_message);
                            }, 2000);
                        }
                    },
                    error: function(){
                        console.log("dev_status error");
                        setTimeout(function(){
                            ths.html('<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>');
                            $(".dev-status-label_"+tid).html('Initiating');
                        }, 2000);
                    }
                });*/
            }
        }else{
            notify_msg("error", "Please 1st select this tunnel");
        }
    });

    $("body").on("click", ".add_clone", function(){
        var id=$(this).attr("data-id");
        if($(".tunnel_"+id).attr("data-val")==1){
            var type=$(this).attr("data-type");
            if(type=="server"){
                var get={
                    "type":"add_server_clone",
                    "message_type":"request",
                    "data":{
                        "id":id,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }else if(type=="client"){
                var get={
                    "type":"add_client_clone",
                    "message_type":"request",
                    "data":{
                        "id":id,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }
        } else{
            notify_msg("error", "Please 1st seect this tunnel");
        }
    });

    $("body").on("click",".chk_all_tunnel",function(){
        var val=$(this).attr("data-val");

        if(val==0){
            $(this).html("<i class='fa fa-fw fa-check-square-o'></i>");
            $(this).attr("data-val", 1);
            $(this).closest(".cloud-tunnels").find(".tunnel_chk").each(function(key,node){
                $(node).html("<i class='fa fa-fw fa-check-square-o'></i>");
                $(node).attr("data-val", 1);
                $(node).parent('.list_body').addClass('row_chk');
            });
        }else if(val==1){
            $(this).html("<i class='fa fa-fw fa-square-o'></i>");
            $(this).attr("data-val", 0);
            $(this).closest(".cloud-tunnels").find(".tunnel_chk").each(function(key,node){
                $(node).html("<i class='fa fa-fw fa-square-o'></i>");
                $(node).attr("data-val", 0);
                $(node).parent('.list_body').removeClass('row_chk');
            });
        }
        /*$(".display").trigger("click");
        $(".change_location").trigger("click");*/
    });

    $("body").on("click", ".tunnel_chk", function(){
        console.log('tunnel_check_click');
        var html="";
        var val=$(this).attr("data-val");
        var id=$(this).attr("data-id");
        if(val==0){
            $(this).html("<i class='fa fa-fw fa-check-square-o'></i>");
            $(this).attr("data-val", 1);
            $(this).parent('.list_body').addClass('row_chk');
        }else if(val==1){
            $(this).html("<i class='fa fa-fw fa-square-o'></i>");
            $(this).attr("data-val", 0);
            $(this).parent('.list_body').removeClass('row_chk');
            //$("#p_div_"+id).html("");
            // $("#chk_all_tunnel").attr("data-chk", 0);
        }
    });

    $('#contact_ms').select2({
        placeholder: "Select users...",
        allowClear: true
    });

    $("body").on("click", ".tunnel_vew_by_tnl", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_gateway_"+cloud+", .tunnel_view_by_grp_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_gateway_"+cloud+", .tunnel_view_by_grp_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=tunnel",
            success:function(resp){
                //alert(resp);
                 var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                 $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="server"){
                    ths.attr("data-dif", "client");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }else{
                    ths.attr("data-dif", "server");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }
            }
        });
    });

    $("body").on("click", ".tunnel_vew_by_grp", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_gateway_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_gateway_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=group",
            success:function(resp){
                var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });

    /*$("body").on("click", "#tunnel_vew_by_email", function(){
        $("#tunnel_vew_by_grp, #tunnel_vew_by_name, #tunnel_vew_by_bidirection, #tunnel_vew_by_gateway").html('<i class="fa fa-sort"></i>');
        $("#tunnel_vew_by_grp, #tunnel_vew_by_name, #tunnel_vew_by_bidirection, #tunnel_vew_by_gateway").attr("data-dif", "asc");
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=email",
            success:function(resp){
                //alert(resp);
                 var data=$.parseJSON(resp);
                 $("#tunnel_body").html(tunnels(data));
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });*/

    $("body").on("click", ".tunnel_vew_by_name", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_gateway_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_gateway_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=name",
            success:function(resp){
                var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });

    $("body").on("click", ".tunnel_vew_by_bidirection", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_gateway_"+cloud+", .tunnel_vew_by_name_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_gateway_"+cloud+", .tunnel_vew_by_name_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=bidirection",
            success:function(resp){
                var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });

    $("body").on("click", ".tunnel_vew_by_gateway", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_name_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_name_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=gateway",
            success:function(resp){
                var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });

    $("body").on("click", "#tunnel_vew_by_internet", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_route_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_route_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=internet",
            success:function(resp){
                var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });

    $("body").on("click", "#tunnel_vew_by_route", function(){
        var cloud=$(this).attr("data-cloud");
        var dif=$(this).attr("data-dif");
        var ths=$(this);
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_internet_"+cloud).html('<i class="fa fa-sort"></i>');
        $(".tunnel_vew_by_grp_"+cloud+", .tunnel_vew_by_email_"+cloud+", .tunnel_vew_by_bidirection_"+cloud+", .tunnel_vew_by_name_"+cloud+", .tunnel_vew_by_internet_"+cloud).attr("data-dif", "asc");

        $.ajax({
            url:"request.php?request=getTunnel&cloud="+cloud+"&dif="+dif+"&type=route",
            success:function(resp){
                var data=$.parseJSON(resp);
                var res_html="";
                $.each(data,function(key,value){
                    var tunnel_data={};
                    tunnel_data[0]=value;
                    res_html+=tunnels(tunnel_data);
                });

                $(".tunnel_body_"+cloud).html(res_html);
                 $(".tunnel_editable").trigger("click");
                 if(dif=="asc"){
                    ths.attr("data-dif", "desc");
                    ths.html("<i class='fa fa-caret-down'></i>");
                 }else{
                    ths.attr("data-dif", "asc");
                    ths.html("<i class='fa fa-caret-up'></i>");
                 }
            }
        });
    });

    $("body").on("click", ".change_location", function(){
        if($(".tunnel_"+$(this).attr("data-pk")).attr("data-val")==1){
            var ths=$(this);
            $(this).editable({
                url:"request.php?request=change_location&token="+token,
                ajaxOptions: {
                    type:"POST",
                    success:function(resp){
                        data=$.parseJSON(resp);
                        console.log("change_location");
                        console.log(data);
                        if(data.status==1){
                            notify_msg("success", data.data);
                            ths.editable('toggleDisabled');
                            ths.parent("div").attr("data-original-title", "This field is processing its last job, please wait...");
                            send(resp);
                        }
                    }
                }
            });
        }
    });

    $(".tunnel_editable").trigger("click");

    $("body").on("click", ".save_this_client", function(){
        var id = $(this).attr("data-id");
        var get={
            "type":"save_a_tunnel",
            "message_type":"request",
            "data":{
                "id":id
            }
        };
        send(JSON.stringify(get));
    });

    $("body").on("click", ".all_tunnel_save_btn", function(){
        $.ajax({
            url:"request.php?request=save_all_tunnel&token="+token,
            success:function(resp){
                data=$.parseJSON(resp);
                if(data.status==1){
                    notify_msg("success", data.data);
                    $.each(data.ids, function(key, val){
                        $(".tunnel_"+val).html("<i class='fa fa-fw fa-circle-o-notch fa-spin'></i>");
                        $(".tunnel_"+val).removeClass("tunnel_chk");
                        $(".tunnel_"+val).attr("data-original-title", "Request submitted, please wait...");
                        $(".tunnel_body_"+val).removeClass("row_chk");
                    });
                    send(resp);
                }else if(data.status==0){
                    notify_msg("error", data.data);
                }
            }
        });
    });

    $("body").on("click", ".delete_cloud", function(){
        if(confirm("Are you sure want to delete this cloud?")){
            var cloud=$(this).attr("data-val");
            var get={
                "type":"delete_cloud",
                "message_type":"request",
                "data":{
                    "cloud_id":cloud,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }
    });



    //for acl modal
    $(".simple").show();
    $(".advanced").hide();
    $("body").on('click', '.sm', function(){
      $('.advanced').show();
      $('.simple').hide();

   });

    $("body").on('click', '.ad', function(){
      $('.simple').show();
      $('.advanced').hide();

   });


    $("body").on('click', '.showACL', function(){
        //$("#aclModal").modal("show");
        var opened = $(this).hasClass('open');
        var tunnel_id = $(this).attr("data-id");
        var cloud = $(this).attr("data-cloud");
        var ths = $(this);
        if(!opened){
            $(this).addClass('open');
            var get={
                "type":"get_acl_info",
                "message_type":"request",
                "data":{
                    "id":tunnel_id
                }
            };
            send(JSON.stringify(get));
            /*$.ajax({
                url:"request.php?request=get_acl_info&id="+tunnel_id,
                success:function(resp){
                    var data = $.parseJSON(resp);
                    box_btn_val = data;
                    if(data.status != undefined && data.status == 0){
                        notify_msg("error", data.message);
                    }
                    else{
                        $(".source_acl_content_"+tunnel_id).html('');
                        $(".destination_acl_content_"+tunnel_id).html('');
                        console.log('show_acl_data');
                        console.log(data);
                        acl(data, null, tunnel_id);
                        $(".box").trigger("mouseover");
                        $(".tunnel_acl_div_"+ths.attr("data-id")).toggle();
                    }
                }
            });*/
        }
        else{
            $(this).removeClass('open');
            $(".source_acl_content_"+ths.attr("data-id")).next('div');
            $(".destination_acl_content_"+ths.attr("data-id")).next('div');
            $('.tunnel_acl_div_'+ths.attr("data-id")).toggle();
        }
    });

    $("body").on("click", ".font-awesome", function(){
        var btn_type = $(this).attr("btn-type");
        var acl_id = $(this).attr("data-id");
        var tid = $(this).attr("data-tid");
        //alert(btn_type);
        if(btn_type=="clone"){
            var get={
                "type":"create_acl_clone",
                "message_type":"request",
                "data":{
                    "id":acl_id,
                    "tid":tid,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }
        if(btn_type=="delete"){
            if(confirm("Are you sure want to delete?")){
                var get={
                    "type":"delete_acl",
                    "message_type":"request",
                    "data":{
                        "id":acl_id,
                        "tid":tid,
                        "token":token
                    }
                };
                send(JSON.stringify(get));
            }
        }
        if(btn_type=="clear"){
            if(confirm("Are you sure want to clear all data?")){
                $.ajax({
                    url:"request.php?request=clear_acl_values&id="+acl_id+"&tid="+tid+"&token="+token,
                    success:function(resp){
                        data=$.parseJSON(resp);
                        if(data.status==1){
                            notify_msg("success", data.data);
                        }
                    }
                });
            }
        }
        if(btn_type=="change"){
            //alert($(this).attr("data-val"));
            if(confirm("Are you sure want to change ACL base?")){
                $.ajax({
                    url:"request.php?request=change_acl&id="+acl_id+"&tid="+tid+"&token="+token+"&val="+$(this).attr("data-val"),
                    success:function(resp){
                        data=$.parseJSON(resp);
                        if(data.status==1){
                            notify_msg("success", data.data);

                        }
                    }
                });
            }
        }
        if(btn_type=="save"){
            console.log('save_acl');
            console.log(acl_value);
            if(!$.isEmptyObject(acl_value)){
                $.ajax({
                    url:"request.php?request=save_acl_values",
                    data:{id:acl_id, tid:tid, token:token, data:acl_value},
                    type:"POST",
                    success:function(resp){
                        data=$.parseJSON(resp);
                        console.log('save_acl_data');
                        console.log(data);
                        if(data.status==1){
                            notify_msg("success", data.data);
                            delete acl_value[acl_id];
                            send(resp);
                        }else{
                            notify_msg("error", data.data);

                        }
                    }
                });
            }
        }
        if(btn_type=="set_default_acl"){
            var ths=$(this);
            tid=ths.closest(".tunnel_acl_div").attr("data-id");
            var val=$(this).attr("data-val");
            var get={
                "type":"set_default_acl",
                "message_type":"request",
                "data":{
                    "id":acl_id,
                    "tid":tid,
                    "val":val,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }
    });
    $("body").on('click', '#simple_Destination', function(){
        $("#simple_tool_body").css("display", "block");
        $("#simple_tool_source").css("display", "none");
        $("#simple_tool_destination").css("display", "block");
    });

    $("body").on('click', '#simple_Source', function(){
        $("#simple_tool_body").css("display", "block");
        $("#simple_tool_destination").css("display", "none");
        $("#simple_tool_source").css("display", "block");
    });

    $("body").on('click', '#advance_destination', function(){
        //$(".series").css("display", "block");
        $(".series").css("display", "none");
        $("#advance_destination_own_div").css("display", "block");
        $("#advance_destination_full_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "none");
    });

    $("body").on('click', '#advance_source', function(){
        //$(".series").css("display", "block");
        $(".series").css("display", "none");
        $("#advance_source_own_div").css("display", "block");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "block");
    });

    $("body").on('click', '#advance_destination_forwarding', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "block");
        $("#advance_destination_forwarding_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "none");
    });

    $("body").on('click', '#advance_source_firewall', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_source_firewall_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "block");
    });

    $("body").on('click', '#advance_source_tos', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_source_tos_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "block");
    });

    $("body").on('click', '#advance_source_qos', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_source_qos_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "block");
    });

    $("body").on('click', '#advance_source_forwarding', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_source_forwarding_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "none");
        $("#advance_source_full_div").css("display", "block");
    });

    $("body").on('click', '#advance_cloud_forwarding', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_cloud_forwarding_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "block");
        $("#advance_source_full_div").css("display", "none");
    });

    $("body").on('click', '#advance_cloud_qos', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_cloud_qos_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "block");
        $("#advance_source_full_div").css("display", "none");
    });

    $("body").on('click', '#advance_cloud_routing', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_cloud_routing_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "block");
        $("#advance_source_full_div").css("display", "none");
    });

    $("body").on('click', '#advance_cloud_firewall', function(){
        $(".series").css("display", "none");
        $("#advance_destination_full_div").css("display", "none");
        $("#advance_cloud_firewall_div").css("display", "block");
        $("#advance_cloud_full_div").css("display", "block");
        $("#advance_source_full_div").css("display", "none");
    });

    $("body").on('click', '#acl_modal_close', function(){
        $(".series").css("display", "block");
        $(".simple").css("display", "block");
        $(".advanced").css("display", "none");
        $("#simple_tool_body").css("display", "none");
        $("#advance_source_full_div").css("display", "none");
        $("#advance_cloud_full_div").css("display", "none");
    });

    var box_ths;
    /*$( "body" ).on( "click", ".box", function() {
        var id = $(this).attr("data-id");
        //alert($(".destination_"+id).find("b").length);
        var type = $(this).attr('data-type');
        var cloud = $(this).attr('data-cloud');
        var tunnel_id = $(this).attr('data-tid');
        //$(".btn_add_acl_"+tunnel_id).attr("data-id", id);
        box_ths = $(this);
        var html="";
        $.each(box_btn_val[id][type], function(key, val){
            if(key == "specific_tunnel" || key == "specific_group"){
                html+='<button class="btn btn-primary acl_spl_btn" data-tid="'+tunnel_id+'" data-cloud="'+cloud+'" data-type="'+type+'" data-id="'+id+'" data-value="'+key+'" type="button">'+val.label.full+'</button>';
            }else if(key == "ip_port" || key == "ip_port_protoco"){
                html+='<button class="btn btn-primary acl_ip_spl_btn" data-tid="'+tunnel_id+'" data-cloud="'+cloud+'" data-type="'+type+'" data-id="'+id+'" data-value="'+key+'" type="button">'+val.label.full+'</button>';
            } else{
                html+='<button class="btn btn-primary acl_btn" data-type="'+type+'" data-id="'+id+'" data-value="'+key+'" type="button">'+val.label.full+'</button>';
            }
        });
        html+='<div class="row">';
            html+='<div class="col-md-12 t20" style="display:none;">';
                html+='<input type="text" class="acl_val_text" data-type="'+type+'" data-id="'+id+'" style="margin-right:10px;">';
                html+='<button class="btn btn-sm btn-info update_acl" data-type="'+type+'" data-id="'+id+'" type="button">Update</button>';
                html+='<button class="btn btn-warning reset_acl" data-type="'+type+'" data-id="'+id+'" type="button">Reset</button>';
            html+='</div>';
        html+='</div>';aaa

        html+='<div class="row">';
            html+='<div class="col-md-12 one-day" style="display:none;">';
                html+='<select class="acl_option" class="" data-type="'+type+'" data-id="'+id+'">';
                html+='</select>';
                //html+='<input type="checkbox" class="btn btn-sm btn-default" data-type="'+type+'" data-id="'+id+'"/> This tunnel';
            html+='</div>';
        html+='</div>';

        html+='<div class="row">';
            html+='<div class="col-md-12 ranji" style="display:none;">';
            html+='</div>';
        html+='</div>';
        if(type=="destination" || type=="source"){
            $("#acl_div_cont").html(html);
            dialog.dialog( "open" );
        } else{
            if($(".destination_"+id).find("div").length>0 && $(".source_"+id).find("div").length>0){
                $("#acl_div_cont").html(html);
                dialog.dialog( "open" );
            }
        }

    });*/

    var popOverSettings = {
    //toggle:"popover",
    placement:"right",
    html: true,
    trigger: "hover",
    selector: '.box-con',
    content: function () {
        var html = '';
        $(this).children('div').each(function(){
            var value = $(this).attr("title");
            var color = $(this).attr("style").split(":");
            if(color[0]=="display"){
                var style = $(this).attr("style").split("; ");
                b_clr = style[1].split(":");
                //alert(style[2]);
                html += ' <b style="background-color:'+b_clr[1]+'">'+value+'</b>';
            }
            if(color[0] == "background-color"){
                html += ' <b style="background-color:'+color[1]+'">'+value+'</b>';
            }

        });
        return html;
        }
    };

    var counter;

    /*$('body').popover(
        popOverSettings
    );*/

    /*$("body").on("mouseover", ".box-con", function(){
        //alert("dshjf");
        $(this).webuiPopover({
                        trigger:'hover',
                        width:'auto',
                        delay:{
                            show:0,
                            hide:100
                        },
                        content: function () {
                                var html = '';

                                $(this).children('div').each(function(){
                                    var value = $(this).attr("title");
                                    var tunnel_id = $(this).attr("data-tid");
                                    var color = $(this).attr("style").split(":");
                                    var clas = $(this).prop("class").split(" ");
                                    if(clas[2] == undefined){
                                        clas[2]="";
                                    }

                                    if(color[0]=="display"){
                                        var style = $(this).attr("style").split("; ");
                                        b_clr = style[1].split(":");

                                        html+='<b class="popover-box '+clas[1]+' '+clas[2]+'" data-tid="'+tunnel_id+'" style="background-color:'+b_clr[1]+'; color:#fff; '+style[2]+'">'+value+'</b>';
                                    }
                                    if(color[0] == "background-color"){
                                        var style1 = $(this).attr("style").split("; ");
                                        //alert(style1[1]);
                                        //opacity = style1[1].split(":");
                                        html+='<b class="popover-box '+clas[1]+' '+clas[2]+'" data-tid="'+tunnel_id+'" style="background-color:'+color[1]+'; color:#fff; '+style1[1]+'">'+value+'</b>';
                                    }

                                });
                                return html;
                                }
                        });
    });*/

    /*$("body").on("mouseleave", ".box-con", function(){
        $(this).webuiPopover('destroy');
    });*/

    $("body").on("click", ".acl_btn", function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var data = $(this).attr('data-value');
        $(".acl_val_text").attr("data-type", type);
        $(".acl_val_text").attr("data-id", id);
        $(".acl_val_text").attr("data-value", data);
        $(".acl_val_text").attr("data-text", $(this).text());
        if(data !="specific_tunnel" && data !="specific_group"){
            $.ajax({
                url:"request.php?request=get_acl_val",
                data:{"id":id, "type":type, "name":data},
                type:"POST",
                success:function(resp){
                    if(resp!=0){
                        $(".acl_val_text").val(resp);
                    } else{
                        $(".acl_val_text").val("");
                    }
                    $(".one-day, .ranji").css("display", "none");
                    $(".t20").css("display", "block");
                }
            });
        }
    });

    $("body").on("click", ".acl_ip_spl_btn", function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var data = $(this).attr('data-value');
        var html="";
        //if(data=="ip_port"){
            html+='<div class="row">';
                html+='<div class="col-md-4">';
                    html+='<input type="text" class="acl_ip_text" data-type="'+type+'" data-id="'+id+'" style="margin-right:10px;" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                html+='</div>';
                html+='<div class="col-md-4">';
                    html+='<input type="text" class="acl_ip_port" data-type="'+type+'" data-id="'+id+'" style="margin-right:10px;" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                html+='</div>';

        //}else if(data=="ip_port_protoco"){

        //}
            html+='<div class="col-md-4">';
                html+='<input type="button" class="btn acl_ip_btn" data-type="'+type+'" data-id="'+id+'" value="Save">';
            html+='</div>';
        html+='</div>';

        $(".ranji").html(html);

        $(".acl_ip_text").attr("data-type", type);
        $(".acl_ip_text").attr("data-id", id);
        $(".acl_ip_text").attr("data-value", data);
        $(".acl_ip_text").attr("data-text", $(this).text());
        if(data !="specific_tunnel" && data !="specific_group"){
            $.ajax({
                url:"request.php?request=get_acl_val",
                data:{"id":id, "type":type, "name":data},
                type:"POST",
                success:function(resp){
                    res = resp.split(",");
                    if(resp!=0){
                        $(".acl_ip_text").val(res[0]);
                        $(".acl_ip_port").val(res[1]);
                    } else{
                        $(".acl_ip_text").val("");
                        $(".acl_ip_port").val("");
                    }
                    $(".ranji").css("display", "block");
                    $(".one-day, .t20").css("display", "none");
                }
            });
        }
    });

    $("body").on("click", ".acl_ip_btn", function(){
        var type = $("#acl_ip_text").attr('data-type');
        var id = $("#acl_ip_text").attr('data-id');
        var text = $("#acl_ip_text").attr("data-text");
        var protocols = [];
        var ips = [];
        var ports = [];

        var i=0;
        var val="";

        $("input[name='ip[]']").each(function() {
            i++;
            ips.push($(this).val());
        });
        $("input[name='port[]']").each(function() {
            ports.push($(this).val());
        });
        $("select[name='protocol[]']").each(function() {
            //i++;
            protocols.push($(this).val());
        });
        if(i==1){
            if(ips[0]=="" && ports[0]=="" && protocols[0]==""){
                val = "";
                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id][type]==undefined)
                    acl_value[id][type]={};
                acl_value[id][type][text]="";
                $("."+text+"-"+type+"-"+id).css("opacity", 0.3);
                $("."+text+"-"+type+"-"+id).css("color", "black");
                notify_msg("warning", "You have to save this settings...");
            }else{
                val += ips[0]+","+ports[0]+","+protocols[0];
                if(ipv4addr($(".acl_ip_text").val()) == true){
                    if(isNumber($(".acl_ip_port").val(), 4) == true){
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][text]=val.replace(/:+$/, '');
                        $("."+text+"-"+type+"-"+id).css("opacity", 1);
                        notify_msg("warning", "You have to save this settings...");

                    }else {
                        $(".acl_ip_port").next("label").html("Please enter valid 4 digit port number");
                        $(".acl_ip_port").addClass("error");
                    }
                }else{
                    $(".acl_ip_text").next("label").html("Please enter valid IP address");
                    $(".acl_ip_text").addClass("error");
                }
            }
        }else{
            for(j=0; j<i; j++){
                val += ips[j]+","+ports[j]+","+protocols[j]+":";
            }
            if(ipv4addr($(".acl_ip_text").val()) == true){
                if(isNumber($(".acl_ip_port").val(), 4) == true){
                    if(acl_value[id]==undefined)
                        acl_value[id]={};
                    if(acl_value[id][type]==undefined)
                        acl_value[id][type]={};
                    acl_value[id][type][text]=val.replace(/:+$/, '');
                    $("."+text+"-"+type+"-"+id).css("opacity", 1);
                    notify_msg("warning", "You have to save this settings...");

                }else {
                    $(".acl_ip_port").next("label").html("Please enter valid 4 digit port number");
                    $(".acl_ip_port").addClass("error");
                }
            }else{
                $(".acl_ip_text").next("label").html("Please enter valid IP address");
                $(".acl_ip_text").addClass("error");
            }
        }
    });

    $("body").on("click", ".source_ip_save", function(){
        var type = $("#acl_ip_text").attr('data-type');
        var id = $("#acl_ip_text").attr('data-id');
        var text = $("#acl_ip_text").attr("data-text");
        var ips = [];

        var i=0;
        var val="";

        $("input[name='ip[]']").each(function() {
            i++;
            ips.push($(this).val());
        });

        if(i==1){
            if(ips[0]==""){
                val = "";
                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id][type]==undefined)
                    acl_value[id][type]={};
                acl_value[id][type][text]="";
                $("."+text+"-"+type+"-"+id).css("opacity", 0.3);
                $("."+text+"-"+type+"-"+id).css("color", "black");
                notify_msg("warning", "You have to save this settings...");
            }else{
                val += ips[0];
                if(ipv4addr($(".acl_ip_text").val()) == true){
                    if(acl_value[id]==undefined)
                        acl_value[id]={};
                    if(acl_value[id][type]==undefined)
                        acl_value[id][type]={};
                    acl_value[id][type][text]=val.replace(/:+$/, '');
                    $("."+text+"-"+type+"-"+id).css("opacity", 1);
                    notify_msg("warning", "You have to save this settings...");
                }else{
                    $(".acl_ip_text").next("label").html("Please enter valid IP address");
                    $(".acl_ip_text").addClass("error");
                }
            }
        }else{
            for(j=0; j<i; j++){
                val += ips[j]+":";
            }
            if(ipv4addr($(".acl_ip_text").val()) == true){

                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id][type]==undefined)
                    acl_value[id][type]={};
                acl_value[id][type][text]=val.replace(/:+$/, '');
                $("."+text+"-"+type+"-"+id).css("opacity", 1);
                notify_msg("warning", "You have to save this settings...");

            }else{
                $(".acl_ip_text").next("label").html("Please enter valid IP address");
                $(".acl_ip_text").addClass("error");
            }
        }
    });

    $("body").on("click", ".s_aliasing_btn", function(){
        var type = $("#acl_ip_text").attr('data-type');
        var id = $("#acl_ip_text").attr('data-id');
        var text = $("#acl_ip_text").attr("data-text");
        var ips = [];
        var ports = [];

        var i=0;
        var val="";

        $("input[name='ip[]']").each(function() {
            i++;
            ips.push($(this).val());
        });
        $("input[name='port[]']").each(function() {
            ports.push($(this).val());
        });

        if(i==2){ //if single value
            if((ips[0]=="" && ports[0]=="") && (ips[1]=="" && ports[1]=="")){
                val = "";
                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id][type]==undefined)
                    acl_value[id][type]={};
                acl_value[id][type][text]="";
                $("."+text+"-"+type+"-"+id).css("opacity", 0.3);
                $("."+text+"-"+type+"-"+id).css("color", "black");

                var value_box = text+'-'+type+'-'+id;
                var c_firewall_box=$('.' + value_box).closest(".soumya").find("div[data-type='c_firewall']");

                $(c_firewall_box).find(".disabled_color_box").each(function(){
                    var main_class_data=$(this).attr("class").split(" ");
                    var main_class=(main_class_data[0]=="disabled_color_box" ? main_class_data[1] : main_class_data[0]);
                    $("."+main_class).addClass("xxxxxxxxxx");
                    $(".xxxxxxxxxx").attr("class","color-box "+main_class);
                    $("."+main_class).removeClass("xxxxxxxxxx");
                });
                $(".internal-d_final-"+id).css("color","#000000");
                $(".internal-d_final-"+id).css("opacity","0.3");
                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id]['d_final']==undefined)
                    acl_value[id]['d_final']={};
                acl_value[id]['d_final']['internal']=0;

                notify_msg("warning", "You have to save this settings...");
            }else{
                val += ips[0]+","+ports[0]+","+ips[1]+","+ports[1];
                if(ipv4addr($(".acl_ip_text").val()) == true){
                    if(isNumber($(".acl_ip_port").val(), 4) == true){
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][text]=val.replace(/:+$/, '');
                        $("."+text+"-"+type+"-"+id).css("opacity", 1);
                        $("."+text+"-"+type+"-"+id).css("color", "white");
                        var value_box = text+'-'+type+'-'+id;
                        var c_firewall_box=$('.' + value_box).closest(".soumya").find("div[data-type='c_firewall']");
                        $(c_firewall_box).find(".color-box").each(function(){
                            var main_class_data=$(this).attr("class").split(" ");
                            var main_class=main_class_data[1];
                            $("."+main_class).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class","disabled_color_box "+main_class);
                            $("."+main_class).removeClass("xxxxxxxxxx");
                        });
                        $(".internal-d_final-"+id).css("color","#ffffff");
                        $(".internal-d_final-"+id).css("opacity","1");
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id]['d_final']==undefined)
                            acl_value[id]['d_final']={};
                        acl_value[id]['d_final']['internal']=1;
                        console.log('acl_value');
                        console.log(acl_value);
                        notify_msg("warning", "You have to save this settings...");
                    }else {
                        $(".acl_ip_port").next("label").html("Please enter valid 4 digit port number");
                        $(".acl_ip_port").addClass("error");
                    }
                }else{
                    $(".acl_ip_text").next("label").html("Please enter valid IP address");
                    $(".acl_ip_text").addClass("error");
                }
            }
        }else{ //if multipule value
            for(j=0; j < i/2; j++){
                val += ips[j]+","+ports[j]+","+ips[j+1]+","+ports[j+1]+":";
            }
            if(ipv4addr($(".acl_ip_text").val()) == true){
                if(isNumber($(".acl_ip_port").val(), 4) == true){
                    if(acl_value[id]==undefined)
                        acl_value[id]={};
                    if(acl_value[id][type]==undefined)
                        acl_value[id][type]={};
                    acl_value[id][type][text]=val.replace(/:+$/, '');
                    $("."+text+"-"+type+"-"+id).css("opacity", 1);
                    $("."+text+"-"+type+"-"+id).css("color", "white");

                    var value_box = text+'-'+type+'-'+id;
                    var c_firewall_box=$('.' + value_box).closest(".soumya").find("div[data-type='c_firewall']");
                    $(c_firewall_box).find(".color-box").each(function(){
                        var main_class_data=$(this).attr("class").split(" ");
                        var main_class=main_class_data[1];
                        $("."+main_class).addClass("xxxxxxxxxx");
                        $(".xxxxxxxxxx").attr("class","disabled_color_box "+main_class);
                        $("."+main_class).removeClass("xxxxxxxxxx");
                    });
                    $(".internal-d_final-"+id).css("color","#ffffff");
                    $(".internal-d_final-"+id).css("opacity","1");
                    if(acl_value[id]==undefined)
                        acl_value[id]={};
                    if(acl_value[id]['d_final']==undefined)
                        acl_value[id]['d_final']={};
                    acl_value[id]['d_final']['internal']=1;
                    notify_msg("warning", "You have to save this settings...");

                }else {
                    $(".acl_ip_port").next("label").html("Please enter valid 4 digit port number");
                    $(".acl_ip_port").addClass("error");
                }
            }else{
                $(".acl_ip_text").next("label").html("Please enter valid IP address");
                $(".acl_ip_text").addClass("error");
            }
        }
    });

    $("body").on("click", ".c_forwarding_btn", function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var text = $(this).attr("data-text");
        var ips = [];
        var ports = [];

        var i=0;
        var val="";

        $("input[name='ip[]']").each(function() {
            i++;
            ips.push($(this).val());
        });
        $("input[name='port[]']").each(function() {
            ports.push($(this).val());
        });

        for(j=0; j<i; j++){
            val += ips[j]+","+ports[j]+":";
        }
        //alert(val.replace(/:+$/, ''));
        if(ipv4addr($(".acl_ip_text").val()) == true){
            if(isNumber($(".acl_ip_port").val(), 4) == true){
                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id][type]==undefined)
                    acl_value[id][type]={};
                acl_value[id][type][text]=val.replace(/:+$/, '');
                $("."+text+"-"+type+"-"+id).css("opacity", 1);
                $("."+text+"-"+type+"-"+id).css("color", "white");

                acl_value[id][type]["specific_tunnel"]="";
                $(".specific_tunnel"+"-"+type+"-"+id).attr("data-avl_attr",0);
                $(".specific_tunnel"+"-"+type+"-"+id).css("color","black");
                $(".specific_tunnel"+"-"+type+"-"+id).css("opacity","0.35");

                $(".specific_tunnel"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"specific_tunnel"+"-"+type+"-"+id);
                $(".specific_tunnel"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");

                notify_msg("warning", "You have to save this settings...");
            }else{
                $(".acl_ip_port").next("label").html("Please enter valid 4 digit port number");
                $(".acl_ip_port").addClass("error");
            }
        }else{
            $(".acl_ip_text").next("label").html("Please enter valid IP address");
            $(".acl_ip_text").addClass("error");
        }
    });

    $("body").on("click", ".acl_d_final_app_btn", function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var text = $(this).attr("data-text");

        var i=0;
        var val="";

        var d_final_app = $("select.d_final_app").val();
        var d_final_app_port = $(".acl_ip_port").val();
        val=d_final_app+","+d_final_app_port;

        if(isNumber($(".acl_ip_port").val(), 4) == true){
            if(acl_value[id]==undefined)
                acl_value[id]={};
            if(acl_value[id][type]==undefined)
                acl_value[id][type]={};
            acl_value[id][type][text]=val;
            $("."+text+"-"+type+"-"+id).css("opacity", 1);
            notify_msg("warning", "You have to save this settings...");
        }else {
            $(".acl_ip_port").next("label").html("Please enter valid 4 digit port number");
            $(".acl_ip_port").addClass("error");
        }
    });

    $("body").on("click", ".acl_websites_btn", function(){
        var type = $(".acl_websites_text").attr('data-type');
        var id = $(".acl_websites_text").attr('data-id');
        var data = $(".acl_websites_text").attr("data-text");
        var doamain_pattern = /^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/;
        var val="";
        var allowed = true;
        $("input[name='websites[]']").each(function() {
            if($.trim($(this).val())!=""){
                val += $(this).val()+":";
            }
            if(data == "websites")
            {
                if (!doamain_pattern.test($(this).val())) {
                    $(this).css("border-color", "red");
                    notify_msg("warning", "Please enter valid domain!");
                    allowed =  false;
                }
                else
                {
                    $(this).css("border-color", "#e5e9ec");
                }
            }
        });
        if(!allowed) {
            return false;
        }

        if(acl_value[id]==undefined)
            acl_value[id]={};
        if(acl_value[id][type]==undefined)
            acl_value[id][type]={};
        var value_box = data+'-'+type+'-'+id;

        if(val!=""){
            acl_value[id][type][data]=val.replace(/:+$/, '');
            $('.'+value_box).css("opacity", 1);
            $('.' + value_box).css("color", "white");
        }else{
            acl_value[id][type][data]="";
            $('.'+value_box).css("opacity", 0.25);
            $('.' + value_box).css("color", "black");

            notify_msg("warning", "Now you have no value set for this option.");
        }

        notify_msg("warning", "You have to save this settings...");
    });

    $("body").on("click",".acl_real_ip_save_btn",function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var data = $(this).attr("data-text");
        var val="";
        if($("#create_real_ip_radio").prop("checked")==1){
            val=$(".acl_real_ip").val();
            console.log('create_real_ip');
        }else{
            val=$(".acl_real_ip_select").val();
        }

        if(acl_value[id]==undefined)
            acl_value[id]={};
        if(acl_value[id][type]==undefined)
            acl_value[id][type]={};
        var value_box = data+'-'+type+'-'+id;

        if(val!=""){
            acl_value[id][type][data]=val.replace(/:+$/, '');
            $('.'+value_box).css("opacity", 1);
            $('.' + value_box).css("color", "white");
            notify_msg("warning", "You have to save this settings...");
        }else{
            acl_value[id][type][data]="";
            $('.'+value_box).css("opacity", 0.25);
            $('.' + value_box).css("color", "black");
            notify_msg("warning", "Now you have no value set for this option.");
        }

    });


    function Generator() {};

    Generator.prototype.rand =  Math.floor(Math.random() * 26) + Date.now();

    Generator.prototype.getId = function() {
        return this.rand++;
    };
    var idGen =new Generator();
    $("body").on("mouseenter", ".color-box", function(){
        return false;
        console.log("in");
        $(this).uniqueId();
        $(this).parent().css('position','');
        console.log( $(this).attr('id'));
        if($("#" + $(this).attr('id') + "_hoverdata").length)
        {
            console.log('hoverdata');
        }
        else
        {
            var destination = $(this).offset();
            $('body').append('<div data-loaded="0" id="' + $(this).attr('id') + "_hoverdata" + '" class="hoverdata" style="top:' + destination.top + 'px; left:' + destination.left + 'px;  display: block; z-index: 100000; position: absolute; background-color: white; border: 1px solid black;"></div>');
            $("#" + $(this).attr('id') + "_hoverdata").css({top: destination.top + $(this).outerHeight(), left: destination.left});
        }
        var destination = $(this).offset();
        $("#" + $(this).attr('id') + "_hoverdata").css({top: destination.top + $(this).outerHeight(), left: destination.left});
        $("#" + $(this).attr('id') + "_hoverdata").show();
        if($("#" + $(this).attr('id') + "_hoverdata").data("loaded") == 0)
        {
            var qz = $("#" + $(this).attr('id') + "_hoverdata");
            var info = $(this).prop("class");
            var all = info.split(" ")[1].split("-");
            var field = all[0];
            var database = all[1];
            var id = all[2];
            var html = "";
                $.ajax({
                    url: "request.php?request=get_acl_val",
                    data: {"id": id, "type": database, "name": field},
                    type: "POST",
                    success: function (resp) {
                        if (resp.indexOf(":") == -1) {
                            if (resp != 0) {
                                var ips = resp.split(",");
                            }
                            html += '<div class="row">';
                            html += '<div class="col-md-12 acl_ip_text_div acl_ip_text_div_1">';
                            html += '<div class="col-md-4">';
                            html += (resp != 0 ? ips[0] : "");
                            html += '</div>';
                            if(resp != 0)
                            if(ips[1])
                            {
                                html += '<div class="col-md-4">';
                                html += ips[1];
                                html += '</div>';
                            }
                            html += '</div>';
                            html += '</div>';
                        } else {
                            var i = 0;
                            ipNports = resp.split(":");
                            html += '<div class="row">';

                            for (x in ipNports) {
                                i++;
                                var ipNport = ipNports[x].split(",");
                                html += '<div class="row">';
                                html += '<div class="col-md-12 acl_ip_text_div acl_ip_text_div_' + i + '">';
                                html += '<div class="col-md-4">';
                                html += ipNport[0];
                                html += '</div>';
                                if(ipNport[1])
                                {
                                    html += '<div class="col-md-4">';
                                    html += ipNport[0];
                                    html += '</div>';
                                }
                                html += '</div>';
                                html += '</div>';
                            }
                            html += '</div>';
                        }
                        $(qz).html(html);
                    }
                });
        }
    });
    $("body").on("mouseleave", ".color-box", function(){
        console.log("out");
        $("#" + $(this).attr('id') + "_hoverdata").hide();
    });

    $("body").on("click", ".disabled_color_box", function(){

        var tt_id = $(this).attr("data-tid");
        if($(".destination_acl_content_18").hasClass("disabled"))
            return false;

        var info = $(this).prop("class");
        var all = info.split(" ")[1].split("-");
        var field = all[0];
        var database = all[1];
        var id = all[2];

        if(field == "new_dst" && database == "s_aliasing"){
            notify_msg("error",'FWD "S" should be activated first');
        }

    });

    $("body").on("click", ".color-box", function(){
        if($(this).hasClass("no")){}else{
            var tt_id = $(this).attr("data-tid");
            if($(".destination_acl_content_18").hasClass("disabled"))
                return false;

            var info = $(this).prop("class");
            var all = info.split(" ")[1].split("-");
            var field = all[0];
            var database = all[1];
            var id = all[2];
            var html="";

            console.log(info);
            if(field=="every_cloud"){ //todo removed
                return false;
            }
            if(field == "deny_allow_all")
            {
                var type = database;
                var data = field;
                var val_attr="";
                var this_node=$(this);
                val_attr=$(this).attr("data-avl_attr");
                if(val_attr==undefined || val_attr==""){
                    $.ajax({
                        url: "request.php?request=get_acl_val",
                        data: {"id": id, "type": database, "name": field},
                        type: "POST",
                        success: function (resp) {
                            console.log(resp);
                            console.log(resp);
                            var val=0;
                            val = (parseInt(resp)+1)%3;
                            if(val==0){
                                this_node.attr("data-avl_attr",val);
                                this_node.attr("style","color:#000000;opacity:0.35;background-color:#808080 !important;");
                            }else if(val==1){ //allow_all
                                this_node.attr("data-avl_attr",val);
                                this_node.attr("style","color:#00ff00;opacity:1;background-color:#ffffff !important;");
                            }else{ //deny_all
                                this_node.attr("data-avl_attr",val);
                                this_node.attr("style","color:#ff0000;opacity:1;background-color:#ffffff !important;");
                            }

                            if(acl_value[id]==undefined)
                                acl_value[id]={};
                            if(acl_value[id][type]==undefined)
                                acl_value[id][type]={};
                            acl_value[id][type][data]=val;
                            notify_msg("warning", "You have to save this settings...");
                            return true;
                        }
                    });
                }else{
                    var val = (parseInt(val_attr)+1)%3;
                    if(val==0){
                        this_node.attr("data-avl_attr",val);
                        this_node.attr("style","color:#000000;opacity:0.35;background-color:#808080 !important;");
                    }else if(val==1){ //allow_all
                        this_node.attr("data-avl_attr",val);
                        this_node.attr("style","color:#00ff00;opacity:1;background-color:#ffffff !important;");
                    }else{ //deny_all
                        this_node.attr("data-avl_attr",val);
                        this_node.attr("style","color:#ff0000;opacity:1;background-color:#ffffff !important;");
                    }

                    if(acl_value[id]==undefined)
                        acl_value[id]={};
                    if(acl_value[id][type]==undefined)
                        acl_value[id][type]={};
                    acl_value[id][type][data]=val;

                    notify_msg("warning", "You have to save this settings...");
                    return true;
                }
                return true;
            }
            else if(field == "allow_all" || field == "deny_all"|| field == "country" || field == "bind_all" || field=="internal")
            {//this is activate or diactivate action
                var type = database;
                var data = field;
                var val_attr="";
                var this_node=$(this);
                val_attr=$(this).attr("data-avl_attr");

                if(val_attr==undefined || val_attr==""){
                    $.ajax({
                        url: "request.php?request=get_acl_val",
                        data: {"id": id, "type": database, "name": field},
                        type: "POST",
                        success: function (resp) {
                            var val=1;
                            if (resp != 0) {
                                val = 0;
                            }

                            if(val==0){
                                this_node.attr("data-avl_attr",val);
                                this_node.css("color","black");
                                this_node.css("opacity","0.35");
                            }else{
                                this_node.attr("data-avl_attr",val);
                                this_node.css("color","white");
                                this_node.css("opacity","1");
                            }

                            if(acl_value[id]==undefined)
                                acl_value[id]={};
                            if(acl_value[id][type]==undefined)
                                acl_value[id][type]={};
                            acl_value[id][type][data]=val;
                            if(field=="allow_all"){
                                if(val==1){
                                    $(".deny_all"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".deny_all"+"-"+type+"-"+id).css("color","black");
                                    $(".deny_all"+"-"+type+"-"+id).css("opacity","0.35");

                                    $(".deny_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"deny_all"+"-"+type+"-"+id);
                                    $(".deny_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");

                                    acl_value[id][type]["deny_all"]="0";
                                }else{
                                    $(".deny_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"deny_all"+"-"+type+"-"+id);
                                    $(".deny_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                }

                            }
                            if(field=="deny_all"){
                                if(val==1){
                                    $(".allow_all"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".allow_all"+"-"+type+"-"+id).css("color","black");
                                    $(".allow_all"+"-"+type+"-"+id).css("opacity","0.35");

                                    $(".allow_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"allow_all"+"-"+type+"-"+id);
                                    $(".allow_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");

                                    acl_value[id][type]["allow_all"]="0";
                                }else{
                                    $(".allow_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"allow_all"+"-"+type+"-"+id);
                                    $(".allow_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                }

                            }
                            notify_msg("warning", "You have to save this settings...");
                            return true;
                        }
                    });
                }else{
                    if(val_attr==1){
                        val_attr=0;
                        $(this).attr("data-avl_attr",val_attr);
                        $(this).css("color","black");
                        $(this).css("opacity","0.35");
                    }else{
                        val_attr=1;
                        $(this).attr("data-avl_attr",val_attr);
                        $(this).css("color","white");
                        $(this).css("opacity","1");
                    }
                    if(acl_value[id]==undefined)
                        acl_value[id]={};
                    if(acl_value[id][type]==undefined)
                        acl_value[id][type]={};
                    acl_value[id][type][data]=val_attr;

                    if(field=="allow_all"){
                        if(val_attr==1){
                            $(".deny_all"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".deny_all"+"-"+type+"-"+id).css("color","black");
                            $(".deny_all"+"-"+type+"-"+id).css("opacity","0.35");

                            $(".deny_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"deny_all"+"-"+type+"-"+id);
                            $(".deny_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");

                            acl_value[id][type]["deny_all"]="0";
                        }else{
                            $(".deny_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"deny_all"+"-"+type+"-"+id);
                            $(".deny_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                        }

                    }
                    if(field=="deny_all"){
                        if(val_attr==1){
                            $(".allow_all"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".allow_all"+"-"+type+"-"+id).css("color","black");
                            $(".allow_all"+"-"+type+"-"+id).css("opacity","0.35");

                            $(".allow_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"allow_all"+"-"+type+"-"+id);
                            $(".allow_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");

                            acl_value[id][type]["allow_all"]="0";
                        }else{
                            $(".allow_all"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"allow_all"+"-"+type+"-"+id);
                            $(".allow_all"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                        }

                    }
                    notify_msg("warning", "You have to save this settings...");
                    return true;
                }
                return true;
            }
            else if(field == "priority")
            {
                var _tid = $(this).data("tid");
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        var vall=resp;
                        if(resp.indexOf(":")==-1)
                        {
                            vall=resp;
                        }
                        else {
                            var arr = resp.split(":");
                            vall = arr[0];
                        }
                        html+='<div class="row">';
                        html+='<div class="acl-multi-val">';
                        for(var i = 0; i < 8; i++)
                        {
                            var checked = "";
                            if(i == vall)
                            {
                                checked = 'checked';
                            }
                            html+='<div class="col-md-12">';
                            html+='<input class="acl_val_data" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" type="radio" name="acl_val_grp" value="' + i + '" ' + checked + '>' + i;
                            html+='</div>';
                        }
                        html+='</div>';
                        html+='<div class="row">';
                        html+='<div class="col-md-12">';
                        html+='<div class="col-md-1">';
                        html+='<button class="btn btn-sm btn-info update_acl_radio" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" type="button">Update</button>';
                        html+='</div>';
                        html+='</div>';
                        html+='</div>';
                        html+='</div>';
                        $("#acl_div_cont").html(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });

            }
            else if(field == "app" || field == "process" || field == "apps" || field == "processes")
            {
                // var get={"type":"get_"+field, "message_type":"request"};
                var get={"type":"get_app", "message_type":"request", "class":field+"-"+database+"-"+id};
                send(JSON.stringify(get));
                $("#acl_div_cont").html("<i class='fa fa-spinner fa-spin'></i>Please Wait... Your request under process.");
                dialog.dialog( "open" );
                if(field == "app" || field == "apps"){
                    $("span.ui-dialog-title").text('Select Apps');
                }else{
                    $("span.ui-dialog-title").text('Select Processes');
                }
            }
            else if(field == "new_dst" && database == "s_aliasing")
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        if(resp.indexOf(":")==-1){
                            if(resp!=0){
                                var ips = resp.split(",");
                            } else {
                                var ips=["","",""];
                            }
                            html+='<div class="row">';
                            html+='<form class="ip-protoco-form">';

                            html+='<div class="aliasing_block aliasing_block_1">';
                            html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_1">';
                            html+='<div class="col-md-2 aliasing-label">';
                            html+='From: ';
                            html+='</div>';
                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[0]:"")+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';
                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[1]:"")+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';
                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-danger acl-ip-data-delete" data-val="1"><i class="fa fa-trash"></i></a>';
                            html+='</div>';
                            html+='</div>';
                            html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_1">';
                            html+='<div class="col-md-2 aliasing-label">';
                            html+='To: ';
                            html+='</div>';
                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[2]:"")+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';
                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[3]:"")+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';
                            html+='<div class="col-md-2">';
                            html+='</div>';
                            html+='</div>';
                            html+='<div class="clearfix"></div>';
                            html+='<div class="line-sepearator"></div>';
                            html+='</div>';
                            html+='</form>';


                            html+='<div class="col-md-12 pull-right">';
                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-primary s_aliasing_add_btn" data-text="'+field+'" data-type="'+database+'" data-val="1" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';
                            html+='<div class="col-md-2">';
                            html+='<input type="button" class="btn s_aliasing_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        } else {
                            var i=0;
                            ipNports=resp.split(":");
                            html+='<div class="row">';
                            html+='<form class="ip-protoco-form">';

                            for (x in ipNports){
                                i++;
                                var ipNport = ipNports[x].split(",");

                                html+='<div class="aliasing_block aliasing_block_'+i+'">';

                                html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
                                html+='<div class="col-md-2 aliasing-label">';
                                html+='From: ';
                                html+='</div>';
                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[0]+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';
                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[1]+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';
                                html+='<div class="col-md-2">';
                                html+='<a class="btn btn-danger acl-ip-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
                                html+='</div>';
                                html+='</div>';

                                html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
                                html+='<div class="col-md-2 aliasing-label">';
                                html+='To: ';
                                html+='</div>';
                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[2]+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';
                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[3]+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';
                                html+='<div class="col-md-2">';
                                html+='</div>';
                                html+='</div>';
                                html+='<div class="clearfix"></div>';
                                html+='<div class="line-sepearator"></div>';

                                html+='</div>';
                            }

                            html+='</form>';
                            html+='<div class="row">';
                            html+='<div class="col-md-12">';
                            html+='<div class="col-md-1">';
                            html+='<a class="btn btn-primary s_aliasing_add_btn" data-text="'+field+'" data-type="'+database+'" data-val="'+i+'" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';

                            html+='<div class="col-md-1">';
                            html+='<input type="button" class="btn s_aliasing_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        }
                        $("#acl_div_cont").html(html);
                        console.log(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });
            }
            else if(field == "new_dst" && database == "c_forwarding")
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        if(resp.indexOf(":")==-1){
                            if(resp!=0){
                                var ips = resp.split(",");
                            }
                            html+='<div class="row">';
                            html+='<form class="ip-protoco-form">';
                            html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_1">';
                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[0]:"")+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';
                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[1]:"")+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';

                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-danger c_forwarding_dst-delete" data-val="1"><i class="fa fa-trash"></i></a>';
                            html+='</div>';
                            html+='</div>';
                            html+='</form>';

                            html+='<div class="col-md-12 pull-right">';
                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-primary c_forwarding_add_btn" data-text="'+field+'" data-type="'+database+'" data-val="1" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';
                            html+='<div class="col-md-2">';
                            html+='<input type="button" class="btn c_forwarding_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        } else{
                            var i=0;
                            ipNports=resp.split(":");
                            html+='<div class="row">';
                            html+='<form class="ip-protoco-form">';

                            for (x in ipNports){
                                i++;
                                var ipNport = ipNports[x].split(",");
                                html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[0]+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';
                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[1]+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';

                                html+='<div class="col-md-2">';
                                html+='<a class="btn btn-danger c_forwarding_dst-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
                                html+='</div>';
                                html+='</div>';
                            }


                            html+='</form>';
                            html+='<div class="row">';
                            html+='<div class="col-md-12">';
                            html+='<div class="col-md-1">';
                            html+='<a class="btn btn-primary s_aliasing_add_btn" data-text="'+field+'" data-type="'+database+'" data-val="'+i+'" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';

                            html+='<div class="col-md-1">';
                            html+='<input type="button" class="btn s_aliasing_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        }
                        $("#acl_div_cont").html(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });

            }
            else if(field == "ip_port" || field == "ip_port_protoco")
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        if(resp.indexOf(":")==-1){
                            if(resp!=0){
                                var ips = resp.split(",");
                            }else{
                                var ips=["","",""];
                            }
                            html+='<div class="row">';
                                html+='<form class="ip-protoco-form">';
                                    html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_1">';
                                        html+='<div class="col-md-3">';
                                            html+='<select class="acl_protocol_select" name="protocol[]" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="width:147px;">';
                                                html+='<option value="">Select Protocol...</option>';
                                                html+='<option value="tcp" '+(ips[2]=="tcp"?"selected":"")+'>TCP</option>';
                                                html+='<option value="udp" '+(ips[2]=="udp"?"selected":"")+'>UDP</option>';
                                                html+='<option value="icmp" '+(ips[2]=="icmp"?"selected":"")+'>ICMP</option>';

                                            html+='</select>';
                                        html+='</div>';
                                        html+='<div class="col-md-4">';
                                            html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[0]:"")+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                                        html+='</div>';
                                        html+='<div class="col-md-4">';
                                            html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[1]:"")+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                                        html+='</div>';
                                        html+='<div class="col-md-1">';
                                            html+='<a class="btn btn-danger acl-ip-data-delete" data-val="1"><i class="fa fa-trash"></i></a>';
                                        html+='</div>';
                                    html+='</div>';
                                html+='</form>';

                                html+='<div class="col-md-12 pull-right">';
                                    html+='<div class="col-md-2">';
                                        html+='<a class="btn btn-primary ip-protoco-btn" data-text="'+field+'" data-type="'+database+'" data-val="1" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                                    html+='</div>';
                                    html+='<div class="col-md-2">';
                                        html+='<input type="button" class="btn acl_ip_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                                    html+='</div>';
                                html+='</div>';
                            html+='</div>';
                        } else{
                            var i=0;
                            ipNports=resp.split(":");
                            html+='<div class="row">';
                                html+='<form class="ip-protoco-form">';

                                    for (x in ipNports){
                                        i++;
                                        var ipNport = ipNports[x].split(",");
                                        html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
                                            html+='<div class="col-md-4">';
                                                html+='<select class="acl_protocol_select" name="protocol[]" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'">';
                                                    html+='<option value="">Select Protocol...</option>';
                                                    html+='<option value="tcp" '+(ipNport[2]=="tcp"?"selected":"")+'>TCP</option>';
                                                    html+='<option value="udp" '+(ipNport[2]=="udp"?"selected":"")+'>UDP</option>';
                                                    html+='<option value="icmp" '+(ipNport[2]=="icmp"?"selected":"")+'>ICMP</option>';

                                                html+='</select>';
                                            html+='</div>';
                                            html+='<div class="col-md-3">';
                                                html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[0]+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                                            html+='</div>';
                                            html+='<div class="col-md-3">';
                                                html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[1]+'" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
                                            html+='</div>';
                                            html+='<div class="col-md-2">';
                                                html+='<a class="btn btn-danger acl-ip-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
                                            html+='</div>';
                                        html+='</div>';
                                    }


                                html+='</form>';
                                html+='<div class="row">';
                                    html+='<div class="col-md-12">';
                                        html+='<div class="col-md-1">';
                                            html+='<a class="btn btn-primary ip-protoco-btn" data-text="'+field+'" data-type="'+database+'" data-val="'+i+'" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                                        html+='</div>';

                                        html+='<div class="col-md-1">';
                                            html+='<input type="button" class="btn acl_ip_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                                        html+='</div>';
                                    html+='</div>';
                                html+='</div>';
                        html+='</div>';
                        }
                        $("#acl_div_cont").html(html);
                        console.log(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });

            }
            else if(field == "websites" && database == "c_firewall")
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        if(resp.indexOf(":")==-1){
                            if(resp!=0){
                                var websites = resp.split(",");
                            }else{
                                var websites=["","",""];
                            }
                            html+='<div class="row">';
                            html+='<form class="websites-form">';
                            html+='<div class="col-md-12 acl_websites_text_div acl_websites_text_div_1">';

                            html+='<div class="col-md-6">';
                            html+='<input type="text" id="acl_websites_text" class="acl_websites_text" size="35" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?websites[0]:"")+'" name="websites[]" placeholder="Enter website domain..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';

                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-danger acl-websites-data-delete" data-val="1"><i class="fa fa-trash"></i></a>';
                            html+='</div>';

                            html+='</div>';
                            html+='</form>';

                            html+='<div class="col-md-12 pull-right">';
                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-primary websites-btn" data-text="'+field+'" data-type="'+database+'" data-val="1" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';
                            html+='<div class="col-md-2">';
                            html+='<input type="button" class="btn acl_websites_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        } else{
                            var i=0;
                            websites=resp.split(":");
                            html+='<div class="row">';
                            html+='<form class="websites-form">';

                            for (x in websites){
                                i++;
                                var website = websites[x].split(",");
                                html+='<div class="col-md-12 acl_websites_text_div acl_websites_text_div_'+i+'">';

                                html+='<div class="col-md-6">';
                                html+='<input type="text" id="acl_websites_text" class="acl_websites_text" size="35" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+website[0]+'" name="websites[]" placeholder="Enter website domain..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';
                                html+='<div class="col-md-2">';
                                html+='<a class="btn btn-danger acl-websites-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
                                html+='</div>';
                                html+='</div>';
                            }

                            html+='</form>';
                            html+='<div class="row">';
                            html+='<div class="col-md-12">';
                            html+='<div class="col-md-1">';
                            html+='<a class="btn btn-primary websites-btn" data-text="'+field+'" data-type="'+database+'" data-val="'+i+'" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';

                            html+='<div class="col-md-1">';
                            html+='<input type="button" class="btn acl_websites_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        }
                        $("#acl_div_cont").html(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });

            }
            else if((field=="real_ip")&&(database=="destination"))
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        console.log("real_ip_data");
                        var data=$.parseJSON(resp);
                        console.log(data);
                        var real_ips=data.real_ips;
                        var installed_real_ips=data.installed_real_ips;
                        var cur_real_ip=data.cur_real_ip;

                        html+='<div style="margin-top: 15px;">';
                        html+='<div class="col-md-6 create_real_ip_div">';
                            html+='<fieldset class="custom_radio_box">';
                                html+='<legend class="custom_frame_title">';
                                    html+='<label class="custom_radio_label" for="create_real_ip_radio">';
                                        html+='<input type="radio" class="custom_radio acl_real_ip_type" value="1" name="real_ip_radio" id="create_real_ip_radio" checked>';
                                        html+='<span class="radio_icon">Generate real ip</span>';
                                    html+='</label>';
                                html+='</legend>';
                                html+='<div class="real_ip_row">';
                                    html+='<span class="real_ip_span"><input type="text" class="acl_real_ip" name="acl_real_ip" size="16" readonly value="'+cur_real_ip+'"></span>';
                                    html+='<span class="real_ip_span"><button class="btn real_ip_generate_btn">Generate</button></span>';
                                html+='</div>';
                            html+='</fieldset>';
                        html+='</div>';

                        html+='<div class="col-md-6 select_real_ip_div">';
                            html+='<fieldset class="custom_radio_box">';
                                html+='<legend class="custom_frame_title">';
                                    html+='<label class="custom_radio_label" for="select_real_ip_radio">';
                                        html+='<input type="radio" class="custom_radio acl_real_ip_type" value="0" name="real_ip_radio" id="select_real_ip_radio">';
                                        html+='<span class="radio_icon">Select real ip</span>';
                                    html+='</label>';
                                html+='</legend>';
                                html+='<div class="real_ip_row">';
                                    html+='<select class="acl_real_ip_select" name="acl_real_ip_select" disabled>';
                                    $.each(real_ips,function(key,value){
                                        if(key==0)
                                            html+='<option value="'+value+'" selected>'+value+'</option>';
                                        else
                                            html+='<option value="'+value+'">'+value+'</option>';
                                    });
                                    $.each(installed_real_ips,function(key,value){
                                        html+='<option value="'+value+'" disabled>'+value+'</option>';
                                    });
                                    html+='</select>';
                                html+='</div>';
                            html+='</fieldset>';
                        html+='</div>';

                        html+='<div class="col-md-12 save_real_ip_div">';
                            html+='<div class="real_ip_row" style="text-align: right;">';
                                html+='<button class="btn btn-primary acl_real_ip_save_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'">Save</button>';
                            html+='</div>';
                        html+='</div>';

                        html+='</div>';
                        $("#acl_div_cont").html(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Real ip Settings');
                    }
                });

            }
            else if((field == "every_cloud" || field == "my_clouds" || field == "my_cloud" || field == "internet" || field=="this_tunnel")&&(database=="destination"))
            { //this is activate or diactivate action
                var type = database;
                var data = field;
                var val_attr="";
                var this_node=$(this);
                val_attr=$(this).attr("data-avl_attr");

                if(field=="every_cloud"){
                    if(val_attr==undefined || val_attr==""){
                        $.ajax({
                            url: "request.php?request=get_acl_val",
                            data: {"id": id, "type": database, "name": field},
                            type: "POST",
                            success: function (resp) {
                                var val=1;
                                if (resp != 0) {
                                    val = 0;
                                }
                                if(val==0){
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","black");
                                    this_node.css("opacity","0.35");
                                }else{
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","white");
                                    this_node.css("opacity","1");
                                }

                                if(acl_value[id]==undefined)
                                    acl_value[id]={};
                                if(acl_value[id][type]==undefined)
                                    acl_value[id][type]={};
                                acl_value[id][type][data]=val;

                                if(val==1){//deselect all other stripes(M,M,S,S)
                                    acl_value[id][type]["specific_tunnel"]=0;
                                    acl_value[id][type]["specific_group"]=0;
                                    acl_value[id][type]["my_cloud"]=0;
                                    acl_value[id][type]["my_clouds"]=0;

                                   /* $(".specific_tunnel"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".specific_tunnel"+"-"+type+"-"+id).css("color","black");
                                    $(".specific_tunnel"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".specific_tunnel"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"specific_tunnel"+"-"+type+"-"+id);*/
                                    $(".specific_group"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".specific_group"+"-"+type+"-"+id).css("color","black");
                                    $(".specific_group"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".specific_group"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"specific_group"+"-"+type+"-"+id);
                                    $(".my_cloud"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".my_cloud"+"-"+type+"-"+id).css("color","black");
                                    $(".my_cloud"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".my_cloud"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"my_cloud"+"-"+type+"-"+id);
                                    $(".my_clouds"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".my_clouds"+"-"+type+"-"+id).css("color","black");
                                    $(".my_clouds"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".my_clouds"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"my_clouds"+"-"+type+"-"+id);
                                }else{
                                    /*$(".specific_tunnel"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"specific_tunnel"+"-"+type+"-"+id);
                                    $(".specific_tunnel"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");*/
                                    $(".specific_group"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"specific_group"+"-"+type+"-"+id);
                                    $(".specific_group"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                    $(".my_cloud"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"my_cloud"+"-"+type+"-"+id);
                                    $(".my_cloud"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                    $(".my_clouds"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"my_clouds"+"-"+type+"-"+id);
                                    $(".my_clouds"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                }

                                notify_msg("warning", "You have to save this settings...");
                                return true;
                            }
                        });
                    }else{
                        if(val_attr==1){
                            val_attr=0;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","black");
                            $(this).css("opacity","0.35");
                        }else{
                            val_attr=1;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","white");
                            $(this).css("opacity","1");
                        }
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][data]=val_attr;
                        if(val_attr==1){//deselect all other stripes(M,M,S,S)
                            acl_value[id][type]["specific_tunnel"]=0;
                            acl_value[id][type]["specific_group"]=0;
                            acl_value[id][type]["my_cloud"]=0;
                            acl_value[id][type]["my_clouds"]=0;

                            /*$(".specific_tunnel"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".specific_tunnel"+"-"+type+"-"+id).css("color","black");
                            $(".specific_tunnel"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".specific_tunnel"+"-"+type+"-"+id).removeClass("color-box");
                            $(".specific_tunnel"+"-"+type+"-"+id).addClass("disabled_color_box");*/
                            $(".specific_group"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".specific_group"+"-"+type+"-"+id).css("color","black");
                            $(".specific_group"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".specific_group"+"-"+type+"-"+id).removeClass("color-box");
                            $(".specific_group"+"-"+type+"-"+id).addClass("disabled_color_box");
                            $(".my_cloud"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".my_cloud"+"-"+type+"-"+id).css("color","black");
                            $(".my_cloud"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".my_cloud"+"-"+type+"-"+id).removeClass("color-box");
                            $(".my_cloud"+"-"+type+"-"+id).addClass("disabled_color_box");
                            $(".my_clouds"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".my_clouds"+"-"+type+"-"+id).css("color","black");
                            $(".my_clouds"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".my_clouds"+"-"+type+"-"+id).removeClass("color-box");
                            $(".my_clouds"+"-"+type+"-"+id).addClass("disabled_color_box");
                        }else{
                            /*$(".specific_tunnel"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"specific_tunnel"+"-"+type+"-"+id);
                            $(".specific_tunnel"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");*/
                            $(".specific_group"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"specific_group"+"-"+type+"-"+id);
                            $(".specific_group"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                            $(".my_cloud"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"my_cloud"+"-"+type+"-"+id);
                            $(".my_cloud"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                            $(".my_clouds"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"my_clouds"+"-"+type+"-"+id);
                            $(".my_clouds"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                        }
                        notify_msg("warning", "You have to save this settings...");
                        return true;
                    }
                }else if(field=="my_clouds"){
                    if(val_attr==undefined || val_attr==""){
                        $.ajax({
                            url: "request.php?request=get_acl_val",
                            data: {"id": id, "type": database, "name": field},
                            type: "POST",
                            success: function (resp) {
                                var val=1;
                                if (resp != 0) {
                                    val = 0;
                                }
                                if(val==0){
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","black");
                                    this_node.css("opacity","0.35");
                                }else{
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","white");
                                    this_node.css("opacity","1");
                                }

                                if(acl_value[id]==undefined)
                                    acl_value[id]={};
                                if(acl_value[id][type]==undefined)
                                    acl_value[id][type]={};
                                acl_value[id][type][data]=val;

                                if(val==1){//deselect all other stripes(M,M,S,S)
                                    acl_value[id][type]["specific_group"]=0;
                                    acl_value[id][type]["my_cloud"]=0;

                                    $(".specific_group"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".specific_group"+"-"+type+"-"+id).css("color","black");
                                    $(".specific_group"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".specific_group"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"specific_group"+"-"+type+"-"+id);
                                    $(".my_cloud"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".my_cloud"+"-"+type+"-"+id).css("color","black");
                                    $(".my_cloud"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".my_cloud"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"my_cloud"+"-"+type+"-"+id);

                                }else{
                                    $(".my_cloud"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"my_cloud"+"-"+type+"-"+id);
                                    $(".my_cloud"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                    $(".specific_group"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"specific_group"+"-"+type+"-"+id);
                                    $(".specific_group"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                }

                                notify_msg("warning", "You have to save this settings...");
                                return true;
                            }
                        });
                    }else{
                        if(val_attr==1){
                            val_attr=0;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","black");
                            $(this).css("opacity","0.35");
                        }else{
                            val_attr=1;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","white");
                            $(this).css("opacity","1");
                        }
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][data]=val_attr;
                        if(val_attr==1){//deselect all other stripes(M,M,S,S)
                            acl_value[id][type]["specific_tunnel"]=0;
                            acl_value[id][type]["specific_group"]=0;
                            acl_value[id][type]["my_cloud"]=0;
                            acl_value[id][type]["my_clouds"]=0;

                            $(".specific_group"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".specific_group"+"-"+type+"-"+id).css("color","black");
                            $(".specific_group"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".specific_group"+"-"+type+"-"+id).removeClass("color-box");
                            $(".specific_group"+"-"+type+"-"+id).addClass("disabled_color_box");
                            $(".my_cloud"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".my_cloud"+"-"+type+"-"+id).css("color","black");
                            $(".my_cloud"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".my_cloud"+"-"+type+"-"+id).removeClass("color-box");
                            $(".my_cloud"+"-"+type+"-"+id).addClass("disabled_color_box");
                        }else{
                            $(".my_cloud"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"my_cloud"+"-"+type+"-"+id);
                            $(".my_cloud"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                            $(".specific_group"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"specific_group"+"-"+type+"-"+id);
                            $(".specific_group"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                        }
                        notify_msg("warning", "You have to save this settings...");
                        return true;
                    }
                }else if(field=="my_cloud")
                {
                    if(val_attr==undefined || val_attr==""){
                        $.ajax({
                            url: "request.php?request=get_acl_val",
                            data: {"id": id, "type": database, "name": field},
                            type: "POST",
                            success: function (resp) {
                                var val=1;
                                if (resp != 0) {
                                    val = 0;
                                }
                                if(val==0){
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","black");
                                    this_node.css("opacity","0.35");
                                }else{
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","white");
                                    this_node.css("opacity","1");
                                }

                                if(acl_value[id]==undefined)
                                    acl_value[id]={};
                                if(acl_value[id][type]==undefined)
                                    acl_value[id][type]={};
                                acl_value[id][type][data]=val;

                                if(val==1){//deselect all other stripes(M,M,S,S)
                                    acl_value[id][type]["specific_group"]=0;
                                    acl_value[id][type]["my_cloud"]=0;

                                    $(".specific_group"+"-"+type+"-"+id).attr("data-avl_attr",0);
                                    $(".specific_group"+"-"+type+"-"+id).css("color","black");
                                    $(".specific_group"+"-"+type+"-"+id).css("opacity","0.35");
                                    $(".specific_group"+"-"+type+"-"+id).attr("class", "disabled_color_box "+"specific_group"+"-"+type+"-"+id);
                                }else{
                                    $(".specific_group"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                                    $(".xxxxxxxxxx").attr("class", "color-box "+"specific_group"+"-"+type+"-"+id);
                                    $(".specific_group"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                                }

                                notify_msg("warning", "You have to save this settings...");
                                return true;
                            }
                        });
                    }else{
                        if(val_attr==1){
                            val_attr=0;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","black");
                            $(this).css("opacity","0.35");
                        }else{
                            val_attr=1;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","white");
                            $(this).css("opacity","1");
                        }
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][data]=val_attr;
                        if(val_attr==1){//deselect all other stripes(M,M,S,S)
                            acl_value[id][type]["specific_tunnel"]=0;
                            acl_value[id][type]["specific_group"]=0;
                            acl_value[id][type]["my_cloud"]=0;
                            acl_value[id][type]["my_clouds"]=0;

                            $(".specific_group"+"-"+type+"-"+id).attr("data-avl_attr",0);
                            $(".specific_group"+"-"+type+"-"+id).css("color","black");
                            $(".specific_group"+"-"+type+"-"+id).css("opacity","0.35");
                            $(".specific_group"+"-"+type+"-"+id).removeClass("color-box");
                            $(".specific_group"+"-"+type+"-"+id).addClass("disabled_color_box");
                        }else{
                            $(".specific_group"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
                            $(".xxxxxxxxxx").attr("class", "color-box "+"specific_group"+"-"+type+"-"+id);
                            $(".specific_group"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
                        }
                        notify_msg("warning", "You have to save this settings...");
                        return true;
                    }
                }
                else if(field=="internet")
                {
                    if($(".change_tunnel_"+tt_id).attr("data-type")=="client"){
                        notify_msg("error","please activate internet tag first");
                        return false;
                    }
                    if(val_attr==undefined || val_attr==""){
                        $.ajax({
                            url: "request.php?request=get_acl_val",
                            data: {"id": id, "type": database, "name": field},
                            type: "POST",
                            success: function (resp) {
                                var val=1;
                                if (resp != 0) {
                                    val = 0;
                                }
                                if(val==0){
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","black");
                                    this_node.css("opacity","0.35");
                                }else{
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","white");
                                    this_node.css("opacity","1");
                                }

                                if(acl_value[id]==undefined)
                                    acl_value[id]={};
                                if(acl_value[id][type]==undefined)
                                    acl_value[id][type]={};
                                acl_value[id][type][data]=val;
                                notify_msg("warning", "You have to save this settings...");
                                return true;
                            }
                        });
                    }else{
                        if(val_attr==1){
                            val_attr=0;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","black");
                            $(this).css("opacity","0.35");
                        }else{
                            val_attr=1;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","white");
                            $(this).css("opacity","1");
                        }
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][data]=val_attr;
                        notify_msg("warning", "You have to save this settings...");
                        return true;
                    }
                }
                else
                {

                    if(val_attr==undefined || val_attr==""){
                        $.ajax({
                            url: "request.php?request=get_acl_val",
                            data: {"id": id, "type": database, "name": field},
                            type: "POST",
                            success: function (resp) {
                                var val=1;
                                if (resp != 0) {
                                    val = 0;
                                }
                                if(val==0){
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","black");
                                    this_node.css("opacity","0.35");
                                }else{
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","white");
                                    this_node.css("opacity","1");
                                }

                                if(acl_value[id]==undefined)
                                    acl_value[id]={};
                                if(acl_value[id][type]==undefined)
                                    acl_value[id][type]={};
                                acl_value[id][type][data]=val;
                                notify_msg("warning", "You have to save this settings...");
                                return true;
                            }
                        });
                    }else{
                        if(val_attr==1){
                            val_attr=0;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","black");
                            $(this).css("opacity","0.35");
                        }else{
                            val_attr=1;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","white");
                            $(this).css("opacity","1");
                        }
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][data]=val_attr;
                        notify_msg("warning", "You have to save this settings...");
                        return true;
                    }
                }
                return true;
            }
            else if(field == "specific_tunnel" && database == "c_forwarding")
            {
                var cloud_id  = $(this).closest(".cloud-row").attr("data-cid");
                var tunnel = $(this).attr("data-tid");
                var aclid  = $(this).parent().attr("data-id");
                var res="";
                var ths = $(this);
                $.ajax({
                    url:"request.php?request=chk_res&type="+database+"&id="+id+"&val="+field+"&tunnel="+tunnel,
                    success:function(resp){
                        res = $.parseJSON(resp);
                        console.log('check_res');
                        console.log(res);
                        var cloud_group_ids=res.cloud_group_ids;
                        $.ajax({
                            url:"request.php?request=getAllTunnel&cloud_id="+cloud_id,
                            success:function(resp){
                                data = $.parseJSON(resp);
                                console.log('getAllTunnel');
                                console.log(data);
                                html+='<div class="row">';
                                html+='<div class="col-md-10">';
                                html+='<select class="acl_tunnels_option" data-value="'+field+'" data-type="'+database+'" data-id="'+id+'">';
                                html+='<option>Select one...</option>';
                                var this_cloud=data.this_cloud;
                                html+='<optgroup label="This Cloud">';
                                $.each(this_cloud.tunnels,function(key,value){
                                    //if(value.tunnel_id!=tunnel){
                                        html+='<option value="'+value.tunnel_id+'" '+(parseInt(res.option_val)==value.tunnel_id?"selected":"")+'>'+(value.display_name!=""?value.display_name:"Tunnel "+value.tunnel_id)+'</option>';
                                    //}
                                });
                                html+='</optgroup>';

                                var other_clouds=data.other_clouds;
                                $.each(other_clouds,function(num,other_cloud){
                                    html+='<optgroup label="'+other_cloud.cloud_name+'">';
                                    $.each(other_cloud.tunnels,function(key,value){
                                        if(value.tunnel_id!=tunnel){
                                            html+='<option value="'+value.tunnel_id+'" '+(parseInt(res.option_val)==value.tunnel_id?"selected":"")+'>'+(value.display_name!=""?value.display_name:"Tunnel "+value.tunnel_id)+'</option>';
                                        }
                                    });
                                    html+='</optgroup>';
                                });

                                html+='</select>';
                                html+='</div>';

                                /*html+='<div class="col-md-2">';
                                html+='<input type="button" class="btn acl_specific_tunnel_btn" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                                html+='</div>';*/
                                html+='</div>';

                                $("#acl_div_cont").html(html);
                                dialog.dialog( "open" );
                                $("span.ui-dialog-title").text('Select Tunnel');
                            }
                        });
                    }
                });
            }
            else if(field == "specific_tunnel" || field == "specific_group")
            {
                var user_cloud=$(this).closest(".cloud-row").attr("data-cid");
                var tunnel = $(this).attr("data-tid");
                var aclid  = $(this).parent().attr("data-id");
                var res="";

                var ths = $(this);
                if(database == "destination" || database == "source"){
                    $.ajax({
                        url:"request.php?request=chk_res&type="+database+"&id="+id+"&val="+field+"&tunnel="+tunnel,
                        success:function(resp){
                            res = $.parseJSON(resp);
                            console.log('check_res');
                            console.log(res);
                            var cloud_group_ids=res.cloud_group_ids;
                            console.log(cloud_group_ids);
                            if(field != "specific_group"){
                                $.ajax({
                                    url:"request.php?request=getTunnel&cloud="+user_cloud+"&dif=asc&type=tunnel&rect=" + aclid,
                                    success:function(resp){
                                        data = $.parseJSON(resp);
                                        console.log('getTunnel');
                                        console.log(data);
                                        html+='<select class="acl_option" style="width:50%;" multiple="multiple" data-value="'+field+'" data-type="'+database+'" data-id="'+id+'">';
                                        html+='<option>Select one...</option>';
                                        $.each(data,function(key,value){
                                            if(parseInt(res.exist_tunnel) != value.tunnel_id){
                                                var display_name=value.display_name;
                                                if(display_name==""){
                                                    display_name="Tunnel "+value.tunnel_id;
                                                }
                                                if(res.option_val.indexOf(":")==-1){
                                                    html+='<option value="'+value.tunnel_id+'" '+(parseInt(res.option_val)==value.tunnel_id?"selected":"")+'>'+display_name+' (Group '+group_arr[value.group_id]+')</option>';
                                                } else {
                                                    var tnls = res.option_val.split(":");
                                                    html+='<option value="'+value.tunnel_id+'" '+(($.inArray(value.tunnel_id, tnls)!==-1)?"selected":"")+'>'+display_name+' (Group '+group_arr[value.group_id]+')</option>';
                                                }
                                            }
                                        });
                                        html+='</select>';
                                        $("#acl_div_cont").html(html);
                                        dialog.dialog( "open" );
                                        $("span.ui-dialog-title").text('Select Tunnel');
                                    }
                                });
                            }else{
                                html+='<select class="acl_option" class="" multiple="multiple" data-value="'+field+'" data-type="'+database+'" data-id="'+id+'">';
                                    html+='<option>Select group...</option>';
                                console.log('group_arr');
                                console.log(group_arr);
                                    $.each(group_arr, function(key, val){
                                        var key_str=key+"";
                                        if($.inArray(key_str,cloud_group_ids)!=-1){
                                            if(res.option_val.indexOf(":")==-1){
                                                html+='<option value="'+key+'" '+(parseInt(res.option_val)==key?"selected":"")+'>'+val+'</option>';
                                            }else{
                                                var grps = res.option_val.split(":");
                                                var selected="";
                                                for(y in grps){
                                                    if(parseInt(grps[y])==key){
                                                        selected="selected";
                                                    }
                                                }
                                                html+='<option value="'+key+'" '+selected+'>'+val+'</option>';
                                            }
                                        }
                                    });
                                html+='</select>';
                                $("#acl_div_cont").html(html);
                                dialog.dialog( "open" );
                                $("span.ui-dialog-title").text('Select Group');
                            }
                        }
                    });
                }
            }
            else if(field == "internet" && database == "source")
            {
                //var is_this_tunnel=$(".this_tunnel-destination-"+id).attr("data-avl_attr");
                if($(".this_tunnel-destination-"+id).css("opacity")!=1){
                    var type = database;
                    var data = field;
                    var val_attr="";
                    var this_node=$(this);
                    val_attr=$(this).attr("data-avl_attr");

                    if(val_attr==undefined || val_attr==""){
                        $.ajax({
                            url: "request.php?request=get_acl_val",
                            data: {"id": id, "type": database, "name": field},
                            type: "POST",
                            success: function (resp) {
                                var val=1;
                                if (resp != 0) {
                                    val = 0;
                                }

                                if(val==0){
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","black");
                                    this_node.css("opacity","0.35");
                                }else{
                                    this_node.attr("data-avl_attr",val);
                                    this_node.css("color","white");
                                    this_node.css("opacity","1");
                                }

                                if(acl_value[id]==undefined)
                                    acl_value[id]={};
                                if(acl_value[id][type]==undefined)
                                    acl_value[id][type]={};
                                acl_value[id][type][data]=val;
                                notify_msg("warning", "You have to save this settings...");
                                return true;
                            }
                        });
                    }else{
                        if(val_attr==1){
                            val_attr=0;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","black");
                            $(this).css("opacity","0.35");
                        }else{
                            val_attr=1;
                            $(this).attr("data-avl_attr",val_attr);
                            $(this).css("color","white");
                            $(this).css("opacity","1");
                        }
                        if(acl_value[id]==undefined)
                            acl_value[id]={};
                        if(acl_value[id][type]==undefined)
                            acl_value[id][type]={};
                        acl_value[id][type][data]=val_attr;
                        notify_msg("warning", "You have to save this settings...");
                        return true;
                    }
                    return true;
                }
            }
            else if(field == "source_ip" && database == "s_tos")
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        if(resp.indexOf(":")==-1){
                            var ips=resp.split(",");
                            html+='<div class="row">';
                            html+='<form class="source_ip-form">';
                            html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_1">';

                            html+='<div class="col-md-4">';
                            html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+(resp!=0?ips[0]:"")+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                            html+='</div>';

                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-danger acl-ip-data-delete" data-val="1"><i class="fa fa-trash"></i></a>';
                            html+='</div>';
                            html+='</div>';
                            html+='</form>';

                            html+='<div class="col-md-12 pull-right">';
                            html+='<div class="col-md-2">';
                            html+='<a class="btn btn-primary source_ip_add_btn" data-text="'+field+'" data-type="'+database+'" data-val="1" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';
                            html+='<div class="col-md-2">';
                            html+='<input type="button" class="btn source_ip_save" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        } else{
                            var i=0;
                            ipNports=resp.split(":");
                            html+='<div class="row">';
                            html+='<form class="source_ip-form">';

                            for (x in ipNports){
                                i++;
                                var ipNport = ipNports[x].split(",");
                                html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';

                                html+='<div class="col-md-4">';
                                html+='<input type="text" id="acl_ip_text" class="acl_ip_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" style="margin-right:10px;" value="'+ipNport[0]+'" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
                                html+='</div>';

                                html+='<div class="col-md-2">';
                                html+='<a class="btn btn-danger acl-ip-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
                                html+='</div>';
                                html+='</div>';
                            }

                            html+='</form>';
                            html+='<div class="row">';
                            html+='<div class="col-md-12">';
                            html+='<div class="col-md-1">';
                            html+='<a class="btn btn-primary source_ip_add_btn" data-text="'+field+'" data-type="'+database+'" data-val="'+i+'" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                            html+='</div>';

                            html+='<div class="col-md-1">';
                            html+='<input type="button" class="btn source_ip_save" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" value="Save">';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                            html+='</div>';
                        }
                        $("#acl_div_cont").html(html);
                        console.log(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });
            }
            else
            {
                $.ajax({
                    url:"request.php?request=get_acl_val",
                    data:{"id":id, "type":database, "name":field},
                    type:"POST",
                    success:function(resp){
                        var i=0;
                        html+='<div class="row">';
                            html+='<div class="acl-multi-val">';
                                if(resp.indexOf(":")==-1){
                                    i=1;
                                    html+='<div class="col-md-12 t20 t20_'+i+'">';
                                        html+='<input type="text" class="acl_val_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" name="acl_val[]" value="'+resp+'" style="margin-right:10px;margin-bottom:2px;">';
                                    if((database=="s_qos")||(database=="c_qos")){
                                        html+='<input type="text" disabled class="acl_val_metric_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" name="acl_val_matric[]" value="kbps" style="margin-right:10px;margin-bottom:2px;width:60px;">';
                                    }
                                        html+='<a class="btn btn-sm btn-danger acl-val-input-delete" data-val="'+i+'" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'"><i class="fa fa-trash"></i></a>';

                                    html+='</div>';
                                } else{
                                    var arr = resp.split(":");
                                    for(x in arr){
                                        i++;
                                        html+='<div class="col-md-12 t20 t20_'+i+'">';
                                            html+='<input type="text" class="acl_val_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" name="acl_val[]" value="'+arr[x]+'" style="margin-right:10px;margin-bottom:2px;">';
                                        if((database=="s_qos")||(database=="c_qos")){
                                            html+='<input type="text" disabled class="acl_val_metric_text" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" name="acl_val_matric[]" value="kbps" style="margin-right:10px;margin-bottom:2px;width:60px;">';
                                        }
                                            html+='<a class="btn btn-sm btn-danger acl-val-input-delete" data-val="'+i+'" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'"><i class="fa fa-trash"></i></a>';
                                        html+='</div>';
                                    }
                                }
                            html+='</div>';
                            html+='<div class="row">';
                                html+='<div class="col-md-12">';
                                    html+='<div class="col-md-1">';
                                        html+='<a class="btn btn-sm btn-primary multi-acl-val-btn" data-text="'+field+'" data-type="'+database+'" data-val="'+i+'" data-id="'+id+'"><i class="fa fa-plus"></i></a>';
                                    html+='</div>';
                                    html+='<div class="col-md-1">';
                                        html+='<button class="btn btn-sm btn-info update_acl" data-text="'+field+'" data-type="'+database+'" data-id="'+id+'" type="button">Update</button>';
                                    html+='</div>';
                                html+='</div>';
                            html+='</div>';
                        html+='</div>';
                        $("#acl_div_cont").html(html);
                        dialog.dialog( "open" );
                        $("span.ui-dialog-title").text('ACL Settings');
                    }
                });
            }
        }
    });

    $("body").on("change", ".acl_real_ip_type",function(){
        var type=$(this).val();
        if(type==1){
            $(".acl_real_ip_select").prop("disabled",true);
            $(".real_ip_generate_btn").prop("disabled",false);
            $(".acl_real_ip").prop("disabled",false);
        }else{
            $(".acl_real_ip_select").prop("disabled",false);
            $(".real_ip_generate_btn").prop("disabled",true);
            $(".acl_real_ip").prop("disabled",true);
        }
    });

    $("body").on("click",".real_ip_generate_btn",function(){
        $.ajax({
            url:"request.php?request=get_available_real_ip",
            type:"GET",
            success:function(res){
                $(".acl_real_ip").val(res);
            }
        })
    });

    $("body").on("click", ".websites-btn", function(){

        var i = $(this).attr("data-val");
        i++;
        var html="";
        html+='<div class="col-md-12 acl_websites_text_div acl_websites_text_div_'+i+'">';

        html+='<div class="col-md-6">';
        html+='<input type="text" id="acl_websites_text" class="acl_websites_text" size="35" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="websites[]" placeholder="Enter website domain..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-2">';
        html+='<a class="btn btn-danger acl-websites-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
        html+='</div>';

        html+='</div>';

        $(this).attr("data-val", i);
        $(".websites-form").append(html);
    });

    $("body").on("click", ".ip-protoco-btn", function(){
        var i = $(this).attr("data-val");
        i++;
        var html="";
        html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
            html+='<div class="col-md-3">';
                html+='<select class="acl_protocol_select" name="protocol[]" data-text="" data-type="" data-id="" style="width: 147px;">';
                    html+='<option value="">Select Protocol...</option>';
                    html+='<option value="tcp">TCP</option>';
                    html+='<option value="udp">UDP</option>';
                    html+='<option value="icmp">ICMP</option>';
                html+='</select>';
            html+='</div>';
            html+='<div class="col-md-4">';
                html+='<input type="text" class="acl_ip_text" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
            html+='</div>';
            html+='<div class="col-md-4">';
                html+='<input type="text" class="acl_ip_port" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
            html+='</div>';
            html+='<div class="col-md-1">';
                html+='<a class="btn btn-danger acl-ip-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
            html+='</div>';
        html+='</div>';

        $(this).attr("data-val", i);
        $(".ip-protoco-form").append(html);
    });

    $("body").on("click", ".source_ip_add_btn", function(){
        var i = $(this).attr("data-val");
        i++;
        var html="";
        html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';

        html+='<div class="col-md-4">';
        html+='<input type="text" class="acl_ip_text" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-1">';
        html+='<a class="btn btn-danger acl-ip-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
        html+='</div>';
        html+='</div>';

        $(this).attr("data-val", i);
        $(".source_ip-form").append(html);
    });

    $("body").on("click", ".s_aliasing_add_btn", function(){
        var i = $(this).attr("data-val");
        i++;
        var html="";
        html+='<div class="aliasing_block aliasing_block_'+i+'">';

        html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
        html+='<div class="col-md-2 aliasing-label">From: </div>';
        html+='<div class="col-md-4">';
        html+='<input type="text" class="acl_ip_text" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-4">';
        html+='<input type="text" class="acl_ip_port" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-2">';
        html+='<a class="btn btn-danger acl-ip-data-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
        html+='</div>';
        html+='</div>';

        html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
        html+='<div class="col-md-2 aliasing-label">To: </div>';
        html+='<div class="col-md-4">';
        html+='<input type="text" class="acl_ip_text" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-4">';
        html+='<input type="text" class="acl_ip_port" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-2">';
        html+='</div>';
        html+='</div>';

        html+='<div class="clearfix"></div>';
        html+='<div class="line-sepearator"></div>';
        html+='</div>';

        $(this).attr("data-val", i);
        $(".ip-protoco-form").append(html);
    });

    $("body").on("click", ".c_forwarding_add_btn", function(){
        var i = $(this).attr("data-val");
        i++;
        var html="";
        html+='<div class="col-md-12 acl_ip_text_div acl_ip_text_div_'+i+'">';
        html+='<div class="col-md-4">';
        html+='<input type="text" class="acl_ip_text" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="ip[]" placeholder="Enter IP address..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';
        html+='<div class="col-md-4">';
        html+='<input type="text" id="acl_ip_port" class="acl_ip_port" data-text="" data-type="" data-id="" style="margin-right:10px;" value="" name="port[]" placeholder="Enter IP port..."><label class="error" style="background-color:#FFFFFF"></label>';
        html+='</div>';

        html+='<div class="col-md-2">';
        html+='<a class="btn btn-danger c_forwarding_dst-delete" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
        html+='</div>';
        html+='</div>';

        $(this).attr("data-val", i);
        $(".ip-protoco-form").append(html);
    });

    $("body").on("click", ".multi-acl-val-btn", function(){
        var i = $(this).attr("data-val");
        var database=$(this).attr('data-type');
        i++;
        var html="";
        html+='<div class="col-md-12 t20_'+i+'">';
            html+='<input type="text" class="acl_val_text" data-text="" data-type="" data-id="" value="" name="acl_val[]" style="margin-right:10px;margin-bottom:2px;">';
        if((database=="s_qos")||(database=="c_qos")){
            html+='<input type="text" disabled class="acl_val_metric_text" name="acl_val_matric[]" value="kbps" style="margin-right:10px;margin-bottom:2px;width:60px;">';
        }
            html+='<a class="btn btn-sm btn-danger acl-val-input-delete" data-text="" data-type="" data-id="" data-val="'+i+'"><i class="fa fa-trash"></i></a>';
        html+='</div>';
        $(this).attr("data-val", i);
        $(".acl-multi-val").append(html);
    });

    $("body").on("click", ".acl-val-input-delete", function(){
        if($(".t20").length==1){
            $(".acl_val_text").val("");
        }else{
            $(".t20_"+$(this).attr("data-val")).remove();
        }
    });

    $("body").on("blur", ".acl_websites_text", function(){
        var doamain_pattern = /^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/;
        if(!doamain_pattern.test($(this).val())){
            $(this).next("label").html("Please enter valid domain");
            $(this).addClass("error");
        } else{
            $(this).next("label").html("");
            $(this).removeClass("error");
        }
    });

    $("body").on("blur", ".acl_ip_text", function(){
        var val = ipv4addr($(this).val());
        if(val==false){
            $(this).next("label").html("Please enter valid IP address");
            $(this).addClass("error");
        } else{
            $(this).next("label").html("");
            $(this).removeClass("error");
        }
    });

    $("body").on("blur", ".acl_ip_port", function(){
        var val = isNumber($(this).val(), 4);
        if(val == false){
            $(this).next("label").html("Please enter valid 4 digit port number");
            $(this).addClass("error");
        } else{
            $(this).next("label").html("");
            $(this).removeClass("error");
        }
    });

    $("body").on("click", ".acl_spl_btn", function(){
        var cloud = $(this).attr("data-cloud");
        var tunnel = $(this).attr("data-tid");
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var data = $(this).attr('data-value');
        var res="";
        var ress;
        $(".acl_option").attr("data-type", type);
        $(".acl_option").attr("data-id", id);
        $(".acl_option").attr("data-value", data);
        $(".acl_option").attr("data-text", $(this).text());
        var html="";
        var ths = $(this);
        if(type == "destination" || type == "source"){
            $.ajax({
                url:"request.php?request=chk_res&type="+type+"&id="+id+"&val="+data,
                success:function(resp){
                    res = $.parseJSON(resp);
                    if(ths.attr("data-value") == "specific_tunnel"){
                        var typ = "tunnel";
                    }
                    $(".t20").css("display", "none");
                    if(ths.attr("data-value") != "specific_group"){

                        $.ajax({
                            url:"request.php?request=getTunnel&cloud="+cloud+"&dif=asc&type="+typ,
                            success:function(resp){
                                data = $.parseJSON(resp);
                                html+='<option>Select one...</option>';

                                for(x in data){
                                    if(parseInt(res.exist_tunnel) != data[x].tunnel_id){
                                        if(data[x].tunnel_id == tunnel){
                                            html+='<option value="'+data[x].tunnel_id+'" '+(parseInt(res.option_val)==data[x].tunnel_id?"selected":"")+'>'+data[x].display_name+'(Group '+group_arr[data[x].group_id]+')(This tunnel)</option>';
                                        }else {
                                            html+='<option value="'+data[x].tunnel_id+'" '+(parseInt(res.option_val)==data[x].tunnel_id?"selected":"")+'>'+data[x].display_name+'(Group '+group_arr[data[x].group_id]+')</option>';
                                        }
                                    }
                                }
                                $(".acl_option").html(html);
                                $(".one-day").css("display", "block");
                            }
                        });
                    }else{
                        //console.log(ress);
                        html+='<option>Select one...</option>';
                        $.each(group_arr, function(key, val){
                            html+='<option value="'+key+'" '+(parseInt(res.option_val)==key?"selected":"")+'>Group '+val+'</option>';
                        });
                        $(".acl_option").html(html);
                        $(".one-day").css("display", "block");
                    }
                }
            });
        }

    });

    $("body").on("click", ".acl-ip-data-delete", function(){
        if($(".aliasing_block").length==1){
            $(".acl_ip_text").val("");
            $(".acl_ip_port").val("");
        }else{
            $(".aliasing_block_"+$(this).attr("data-val")).remove();
        }
    });

    $("body").on("click", ".c_forwarding_dst-delete", function(){
        if($(".acl_ip_text_div").length==1){
            $(".acl_ip_text").val("");
            $(".acl_ip_port").val("");
        }else{
            $(".acl_ip_text_div_"+$(this).attr("data-val")).remove();
        }
    });

    $("body").on("click", ".acl-websites-data-delete", function(){
        if($(".acl_websites_text_div").length==1){
            $(".acl_websites_text").val("");
        }else{
            $(".acl_websites_text_div_"+$(this).attr("data-val")).remove();
        }
    });

    $("body").on("click", ".update_acl_radio", function(){
        var type = $(".acl_val_data").attr('data-type');
        var id = $(".acl_val_data").attr('data-id');
        var data = $(".acl_val_data").attr("data-text");
        var value_box = data+'-'+type+'-'+id;
        var val=$('input[name=acl_val_grp]:checked').val();
        if(acl_value[id]==undefined)
            acl_value[id]={};
        if(acl_value[id][type]==undefined)
            acl_value[id][type]={};
        acl_value[id][type][data]=val;
        if(val > 0) {
            $('.' + value_box).css("opacity", 1);
            $('.' + value_box).css("color", "white");
        }
        else {
            $('.' + value_box).css("opacity", 0.25);
            $('.' + value_box).css("color", "black");
        }
        notify_msg("warning", "You have to save this settings...");
    });

    $("body").on("click", ".update_acl", function(){
        var type = $(".acl_val_text").attr('data-type');
        var id = $(".acl_val_text").attr('data-id');
        var data = $(".acl_val_text").attr("data-text");
        var ippattern = /\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/;
        var val="";
        var allowed = true;
        $("input[name='acl_val[]']").each(function() {
            if($.trim($(this).val())!=""){
                val += $(this).val()+":";
            }
            if(data == "new_dst")
            {

                if (!ippattern.test($(this).val())) {
                    $(this).css("border-color", "red");
                    notify_msg("warning", "Please enter valid ip!");
                    allowed =  false;
                }
                else
                {
                    $(this).css("border-color", "#e5e9ec");
                }
            }
            else if(data == "websites")
            {
                var uripattern = /(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/;
                if (!uripattern.test($(this).val())) {
                    $(this).css("border-color", "red");
                    notify_msg("warning", "Please enter valid url!");
                    allowed =  false;
                }
                else
                {
                    $(this).css("border-color", "#e5e9ec");
                }
            }
        });
        if(!allowed) {
            return false;
        }
        //alert(val.replace(/:+$/, ''));

        //alert(type+"=="+data+"=="+id);
        if(acl_value[id]==undefined)
            acl_value[id]={};
        if(acl_value[id][type]==undefined)
            acl_value[id][type]={};
        var value_box = data+'-'+type+'-'+id;

        if(val!=""){
            acl_value[id][type][data]=val.replace(/:+$/, '');
            $('.'+value_box).css("opacity", 1);
            $('.' + value_box).css("color", "white");
        }else{
            acl_value[id][type][data]="";
            $('.'+value_box).css("opacity", 0.25);
            $('.' + value_box).css("color", "black");

            notify_msg("warning", "Now you have no value set for this option.");
        }

        notify_msg("warning", "You have to save this settings...");
    });

    $("body").on("change", ".acl_option", function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var data = $(this).attr('data-value');
        var val = "";
        $(".acl_option option:selected").each(function (){
            var $this = $(this);
            if ($this.length) {
                val += $this.val()+":";
            }
        });
        //alert(val);
        //var text = $(this).attr("data-text");
        //alert(type+"=="+data+"=="+id);
        if(acl_value[id]==undefined)
            acl_value[id]={};
        if(acl_value[id][type]==undefined)
            acl_value[id][type]={};
        acl_value[id][type][data]=val.replace(/:+$/, '');

        console.log(acl_value);
        $("."+data+"-"+type+"-"+id).css("opacity", 1);
        notify_msg("warning", "You have to save this settings...");
    });

    $("body").on("change", ".acl_tunnels_option", function(){
        var type = $(this).attr('data-type');
        var id = $(this).attr('data-id');
        var data = $(this).attr('data-value');
        var val = $(this).val();
        console.log(val);
        if(val=="Select one..."){
            val="";
        }
        if(acl_value[id]==undefined)
            acl_value[id]={};
        if(acl_value[id][type]==undefined)
            acl_value[id][type]={};
        acl_value[id][type][data]=val;

        acl_value[id][type]["new_dst"]="";
        $(".new_dst"+"-"+type+"-"+id).attr("data-avl_attr",0);
        $(".new_dst"+"-"+type+"-"+id).css("color","black");
        $(".new_dst"+"-"+type+"-"+id).css("opacity","0.35");

        $(".new_dst"+"-"+type+"-"+id).addClass("xxxxxxxxxx");
        $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"new_dst"+"-"+type+"-"+id);
        $(".new_dst"+"-"+type+"-"+id).removeClass("xxxxxxxxxx");
        console.log(acl_value);
        $("."+data+"-"+type+"-"+id).css("opacity", 1);
        notify_msg("warning", "You have to save this settings...");

    });

    $("body").on("click", ".reset_acl", function(){
        var type = $(".acl_val_text").attr('data-type');
        var id = $(".acl_val_text").attr('data-id');
        var data = $(".acl_val_text").attr('data-value');
        var text = $(".acl_val_text").attr("data-text");
        $.ajax({
            url:"request.php?request=acl_update",
            data:{"id":id, "type":type, "name":data, "val":0},
            type:"POST",
            success:function(resp){
                if(resp==1){
                    var value_box = data+'-'+type+'-'+id;
                    if($('.'+value_box).length > 0){
                        if(box_ths.children('b').length>4){
                            var i=0;
                            box_ths.children('b').each(function(){
                                if(i == 4){
                                    $(this).removeAttr("style");
                                }
                                i++;
                            });

                        }
                        $('.'+value_box).remove();
                    }

                    if(box_ths.children('b').length == 0){
                         if(box_ths.hasClass('"box-con')){
                             box_ths.removeClass("box-con");
                        }
                        box_ths.addClass('blank');
                    }
                    notify_msg("success", "ACL updated successfully");
                }else{
                    notify_msg("error", "Somthing went wrong");
                }
            }
        });
    });

    $("body").on("click", ".acl_radio", function(){
        var id = $(this).attr("data-id");
        var val = $(this).val();
        var get={
            "type":"create_new_acl",
            "message_type":"request",
            "data":{
                "id":id,
                "val":val,
                "token":token
            }
        };
        send(JSON.stringify(get));

    });

    var btn_add_acl=$(".btn_add_acl");

    $("body").on("mouseover",".btn_add_acl", function(){
        btn_add_acl=$(this);
        btn_add_acl.webuiPopover({
                        trigger:'click',
                        width:'auto',
                        delay:{
                            show:0,
                            hide:100
                        },
                        content: function () {
                            var html = '';
                            var tunnel_id = $(this).attr("data-id");
                            html+='<input type="radio" class="acl_radio" data-id="'+tunnel_id+'" name="type" value="destination"/> Destination &nbsp;';
                            html+='<input type="radio" class="acl_radio" data-id="'+tunnel_id+'" name="type" value="source"/> Source';
                            return html;
                        }
        });
    });

    /*$('#Parks').dragScroll({});*/

    /*setTimeout(function(){
        var data={"type":"get_DeV","message_type":"request","data":{"id":43, "value":0}};
        send(JSON.stringify(data));
    },3000);*/

    $(".img_upload").click(function(){
        $(".edit_modal").modal("show");
    });

    $("#send_point_btn").click(function(){
        console.log('send point');
        console.log($("input[name=point]").val());
        if($("input[name=point]").val()==""){
            $("input[name=point]").addClass("error");
            $("input[name=point]").removeClass("ok");
        } else {
            $("input[name=point]").removeClass("error");
            $("input[name=point]").addClass("ok");
        }
        if($("input[name=email]").val()==""){
            $("input[name=email]").addClass("error");
            $("input[name=email]").removeClass("ok");
        } else {
            var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
            if (testEmail.test($("input[name=email]").val())){
                $("input[name=email]").removeClass("error");
                $("input[name=email]").addClass("ok");
            } else {
                $("input[name=email]").addClass("error");
                $("input[name=email]").removeClass("ok");
            }
        }
        if($("input[name=email]").hasClass("ok")){
            if($("input[name=point]").hasClass("ok")){
                $.ajax({
                    url:"request.php?request=point",
                    data: $("#send_point_form").serialize(),
                    type: "POST",
                    success:function(resp){
                        if(resp==1){
                            $("#alert-msg").css("display", "block");
                            $("#alert-msg").addClass("alert-success");
                            $("#alert-msg").removeClass("alert-danger");
                            $("#alert-msg").html($("input[name=point]").val()+" points debited from your account.");
                        }else{
                            $("#alert-msg").css("display", "block");
                            $("#alert-msg").addClass("alert-danger");
                            $("#alert-msg").removeClass("alert-success");
                            $("#alert-msg").html("Something went wrong, email id not exist or you don't have sufficient points");
                        }
                    }
                });
                setTimeout(function(){
                    $(".alert").css("display", "none");
                    $(".alert").html("");
                }, 3000);
            }
        }
    });

    $("#popup_send_point_btn").click(function(){
        if($("input[name=point]").val()==""){
            $("input[name=point]").addClass("error");
            $("input[name=point]").removeClass("ok");
        } else {
            $("input[name=point]").removeClass("error");
            $("input[name=point]").addClass("ok");
        }
        if($("input[name=friend_id]").val()==""){
            $(".alert-danger").css("display", "block");
            $(".alert-success").css("display", "none");
            $(".alert-danger").html("Please select any friend.");
            return false;
        }else{
            $(".alert-danger").css("display", "none");
            $(".alert-success").css("display", "none");
        }

        if($("input[name=point]").hasClass("ok")){
            $.ajax({
                url:"request.php?request=send_point_to_friend",
                data: $("#send_point_form").serialize(),
                type: "POST",
                success:function(resp){
                    if(resp==1){
                        $(".alert-success").css("display", "block");
                        $(".alert-danger").css("display", "none");
                        $(".alert-success").html($("input[name=point]").val()+" points debited from your account.");
                    }else{
                        $(".alert-danger").css("display", "block");
                        $(".alert-success").css("display", "none");
                        $(".alert-danger").html("Something went wrong, customer not exist or you don't have sufficient points");
                    }
                }
            });
            setTimeout(function(){
                $(".alert").css("display", "none");
                $(".alert").html("");
            }, 3000);
        }

    });

    $(".img_upload").initial();

    $("body").on("click",".sponsored",function(){
        console.log("sponsored");
        var ths=$(this);
        var tunnel_id=ths.attr('data-tid');
        var sponsored=false;
        var str="This tunnel will stop being sponsored.\n Are you sure?";
        if(confirm(str)){
            $.ajax({
                url:"request.php?request=remove_sharing&tunnel_id="+tunnel_id+"&shared_with="+current_customer_id,
                type:"GET",
                success:function(resp){
                    var obj=jQuery.parseJSON(resp);
                    if(obj.status==1) {
                        ths.closest(".p_div").addClass("hidden");
                    }
                }
            });
        }
    });

    $(".customer_search_btn").click(function(){
        var shared_val = $(".customer_search_input").val();
        var val="";
        var user_id=$(".customer_search_btn").attr('data-u');
        var t_id=$(".customer_search_btn").attr('data-tid');
        var c_id=$(".customer_search_btn").attr('data-cloud');
        $.ajax({
            url:"request.php?request=shared_tunnel_search",
            data:{"user_id":user_id, "t_id":t_id, "c_id":c_id, "shared_with":shared_val},
            type:"POST",
            success:function(resp){
                console.log(resp);
                if(resp){
                    $("#sponsorModal #msg").addClass("alert-success");
                    $("#sponsorModal #msg").html(resp);
                    $(".sponsored_"+t_id).css("background-color","#b9c3c8");
                    $(".sponsored_"+t_id).css("color","#ffffff");
                    $(".sponsored_"+t_id).css("opacity","1");
                    $(".sponsored_"+t_id).html("Sponsoring");
                }else{
                    $(".customer_search_input").addClass("error");
                    $(".customer_search_input").next("level").html("Invalid input data.");
                    $(".customer_search_input").next("level").addClass("error");
                }
            }
        });
    });

    $("body").on("click","#sponsorModal .left-friend-list-content-row",function(){
        var shared_val = $(this).attr("data-friend_id");
        var user_id=$(".customer_search_btn").attr('data-u');
        var t_id=$(".customer_search_btn").attr('data-tid');
        var c_id=$(".customer_search_btn").attr('data-cloud');
        $.ajax({
            url:"request.php?request=shared_tunnel",
            data:{"user_id":user_id, "t_id":t_id, "c_id":c_id, "shared_with":shared_val},
            type:"POST",
            success:function(resp){
                console.log(resp);
                if(resp){
                    $("#sponsorModal #msg").addClass("alert-success");
                    $("#sponsorModal #msg").html(resp);
                    $(".sponsored_"+t_id).css("background-color","#b9c3c8");
                    $(".sponsored_"+t_id).css("color","#ffffff");
                    $(".sponsored_"+t_id).css("opacity","1");
                    $(".sponsored_"+t_id).html("Sponsoring");
                }else{
                    $(".customer_search_input").addClass("error");
                    $(".customer_search_input").next("level").html("Invalid input data.");
                    $(".customer_search_input").next("level").addClass("error");
                }
            }
        });
    });

    $("#cust_ms").select2({
        placeholder: "Select customer...",
        allowClear: true
    });

    $(".acl_search_btn").click(function(){
        var tunnel_id = $(this).attr("data-tunnel");
        var email = $(".acl_search_input").val();
        var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        if (testEmail.test(email)){
            $(".acl_search_input").removeClass("error");
            $(".acl_search_input").next("level").removeClass("error");
            $(".acl_search_input").next("level").html("");

            $.ajax({
                url:"request.php?request=get_acl_destination_base&email="+email+"&tunnel_id="+tunnel_id,
                type:"POST",
                success:function(resp){
                    //alert(resp);
                    if(resp!=0){
                        var res = $.parseJSON(resp);
                        console.log("search_acl");
                        console.log(res);
                        share_acl(res.acl_destinations, tunnel_id,res.cur_tunnel_acls);
                    }else if(resp==0){
                        $(".acl_search_input").addClass("error");
                        $(".acl_search_input").next("level").html("No acl found, please try another one");
                        $(".acl_search_input").next("level").addClass("error");
                    }
                }
            });
        } else {
            $(".acl_search_input").addClass("error");
            $(".acl_search_input").next("level").html("Enter valid email id");
            $(".acl_search_input").next("level").addClass("error");
        }
        setTimeout(function(){
            $(".acl_search_input").next("level").removeClass("error");
            $(".acl_search_input").next("level").html("");
        }, 5000);
    });

    $(".dialog-left-friend-list-content-row").click(function(){
        $(".dialog-left-friend-list-content-row").removeClass("friend-selected");
        $(this).addClass("friend-selected");
        var tunnel_id = $(".acl_search_btn").attr("data-tunnel");
        var customer_id = $(this).attr("data-customer_id");

            $(".acl_search_input").removeClass("error");
            $(".acl_search_input").next("level").removeClass("error");
            $(".acl_search_input").next("level").html("");

            $.ajax({
                url:"request.php?request=get_acl_destination_base_from_customer_id&customer_id="+customer_id+"&tunnel_id="+tunnel_id,
                type:"POST",
                success:function(resp){
                    //alert(resp);
                    if(resp!=0){
                        var res = $.parseJSON(resp);
                        console.log("search_acl");
                        console.log(res);
                        share_acl(res.acl_destinations, tunnel_id,res.cur_tunnel_acls);
                    }else if(resp==0){
                        $(".acl_search_input").addClass("error");
                        $(".acl_search_input").next("level").html("No acl found, please try another one");
                        $(".acl_search_input").next("level").addClass("error");
                    }
                }
            });
        setTimeout(function(){
            $(".acl_search_input").next("level").removeClass("error");
            $(".acl_search_input").next("level").html("");
        }, 5000);
    });

    $('.acl_search_result').dragScroll({});

    $("body").on("click", ".acl_destination_search_btn", function(){
        $("#ACLsearchModal").modal("show");
        $(".acl_search_btn").attr("data-tunnel", $(this).attr("data-tid"));
    });

    $("body").on("click", ".install_acl", function(){
        var tunnel_id=$(this).attr("data-tunnel");
        var acl_id=$(this).attr("data-acl");
        $.ajax({
            url:"request.php?request=install_acl",
            data:{"tunnel_id":tunnel_id, "acl_id":acl_id},
            type:"POST",
            success:function(resp){
                if(resp==1){
                    $("#msg").removeClass("alert-danger");
                    $("#msg").addClass("alert-success");
                    $("#msg").html("ACL Install successfully");
                }else{
                    $("#msg").removeClass("alert-success");
                    $("#msg").addClass("alert-danger");
                    $("#msg").html("Somthing went wrong");
                }
            }
        });
        setTimeout(function(){
            $("#msg").removeClass("alert-success");
            $("#msg").removeClass("alert-danger");
            $("#msg").html("");
        },3000);
        $(this).addClass("installed_acl");
        $(this).val("Installed");
        $(this).prop('disabled',true);
        $(this).removeClass("install_acl");
    });

    $('#profile_pic_change_btn').change(function(evt){
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            var files = evt.target.files;
            var file=files[0];
            if (!file.type.match('image.*')) {
                return false;
            }else{
                var formData = new FormData($('#profile_pic_change')[0]);
                $.ajax({
                    url: 'request.php?request=change_profile_picture',
                    type: 'POST',
                    data: formData,
                    async: true,
                    success: function (data){
                        var message=$.parseJSON(data);
                        if (message.status=='success') {
                            $('.img_upload').attr('src','assets/user_img/'+message.msg);
                            //$('#pro_pic').attr('src','assets/user_img/'+message.msg);
                        }else if(message.status=='unsuccess') {
                            notify_msg('error', 'Error: Please Try again!');
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        } else {
            alert('The File APIs are not fully supported in this browser.');
        }
    });

    $("#layout-condensed-toggle").trigger("click");
    $('#main-menu').mouseover(function(){
        if($(this).hasClass('mini')){
          $("#layout-condensed-toggle").trigger("click");
        }
    });
    $('#main-menu').mouseout(function(){
        if(!$(this).hasClass('mini')){
          $("#layout-condensed-toggle").trigger("click");
        }
    });

    $("body").on("blur",".acl_name",function(){
        var acl_id=$(this).attr("data-id");
        var key=$(this).attr('class').split(" ")[0];
        var value=$(this).val();
        console.log($(this).val());
        var ths=$(this);
        if(ths.attr('data-value')!=value){
            save_acl_name_description(ths, acl_id, key, value);
        }
    });
    $("body").on("blur",".acl_description",function(){
        var acl_id=$(this).attr("data-id");
        var key=$(this).attr('class').split(" ")[0];
        var value=$(this).val();
        console.log($(this).val());
        var ths=$(this);
        if(ths.attr('data-value')!=value){
            save_acl_name_description(ths, acl_id, key, value);
        }
    });
    $("body").on("keypress",".acl_name",function(event){
        var acl_id=$(this).attr("data-id");
        var key=$(this).attr('class').split(" ")[0];
        var value=$(this).val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            console.log($(this).val());
            var ths=$(this);
            if(ths.attr('data-value')!=value){
                save_acl_name_description(ths, acl_id, key, value);
            }
        }
    });
    $("body").on("keypress",".acl_description",function(event){
        var acl_id=$(this).attr("data-id");
        var key=$(this).attr('class').split(" ")[0];
        var value=$(this).val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            console.log($(this).val());
            var ths=$(this);
            if(ths.attr('data-value')!=value){
                save_acl_name_description(ths, acl_id, key, value);
            }
        }
    });

    $("body").on("click", ".assign_action_btn",function(){
        console.log('request real ip');
        var id=$(this).attr("data-id");
        if($(".tunnel_"+id).attr("data-val")==1){
            var val=$(this).attr("data-val");
            $(".real_ip_select_box_"+id+" .active_option .assign_action_btn i").attr("class","fa fa-fw fa-circle-o-notch fa-spin");
            $(".real_ip_select_box_"+id+" .active_option").addClass("processing_option");
            $(".real_ip_select_box_"+id+" .processing_option").removeClass("active_option");
            var get={
                "type":"request_real_ip",
                "message_type":"request",
                "data":{
                    "id":id,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }else{
            notify_msg("error", "Please 1st select this tunnel");
        }
    });
    $("body").on("clear",".active_option", function(){
        console.log('clear real ip');
        var id=$(this).closest(".custom_select_box").attr("data-id");
        console.log(id);
        var real_ip=$(".real_ip_select_box_"+id+" .active_option .display_value").attr("data-value");
        if($(".tunnel_"+id).attr("data-val")==1){
            var val=$(this).attr("data-val");
            $(".real_ip_select_box_"+id+" .active_option .action_btn i").attr("class","fa fa-fw fa-circle-o-notch fa-spin");
            $(".real_ip_select_box_"+id+" .active_option").addClass("processing_option");
            $(".real_ip_select_box_"+id+" .processing_option").removeClass("active_option");
            var get={
                "type":"clear_tunnel_real_ip",
                "message_type":"request",
                "data":{
                    "id":id,
                    "real_ip":real_ip,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }else{
            notify_msg("error", "Please 1st select this tunnel");
        }
    });

    $("body").on("change",".active_option", function(e,f){
        console.log('change real ip');
        var id=$(this).closest(".custom_select_box").attr("data-id");
        var cur_real_ip=$(".real_ip_select_box_"+id+" .active_option .display_value").attr("data-value");
        var real_ip=f;
        if($(".tunnel_"+id).attr("data-val")==1){
            $(".real_ip_select_box_"+id+" .active_option .assign_action_btn").addClass("action_btn");
            $(".real_ip_select_box_"+id+" .active_option .action_btn").removeClass("assign_action_btn");
            $(".real_ip_select_box_"+id+" .active_option .display_value").html(real_ip);
            $(".real_ip_select_box_"+id+" .active_option .display_value").attr("data-value",real_ip);
            var val=$(this).attr("data-val");
            $(".real_ip_select_box_"+id+" .active_option .action_btn i").attr("class","fa fa-fw fa-circle-o-notch fa-spin");
            $(".real_ip_select_box_"+id+" .active_option").addClass("processing_option");
            $(".real_ip_select_box_"+id+" .processing_option").removeClass("active_option");
            var get={
                "type":"change_tunnel_real_ip",
                "message_type":"request",
                "data":{
                    "id":id,
                    "real_ip":real_ip,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }else{
            notify_msg("error", "Please 1st select this tunnel");
        }
    });
    $("body").on("clear",".inactive_option", function(){
        console.log('clear acl real ip');
        var id=$(this).closest(".custom_select_box").attr("data-id");
        var aid=$(this).attr("data-aid");
        var real_ip=$(this).children(".display_value").attr("data-value");

        if($(".tunnel_"+id).attr("data-val")==1){
            $(".real_ip_select_box_"+id+" .inactive_option_"+aid+" .action_btn i").attr("class","fa fa-fw fa-circle-o-notch fa-spin");
            $(".real_ip_select_box_"+id+" .inactive_option_"+aid).addClass("processing_inactive_option");
            $(".real_ip_select_box_"+id+" .inactive_option_"+aid).removeClass("inactive_option");
            var get={
                "type":"clear_acl_real_ip",
                "message_type":"request",
                "data":{
                    "id":id,
                    "aid":aid,
                    "real_ip":real_ip,
                    "token":token
                }
            };
            send(JSON.stringify(get));
        }else{
            notify_msg("error", "Please 1st select this tunnel");
        }
    });
    $("body").on("click",function(e){
        if(e.target.className!="display_value"){
            $(".custom_select_box .inactive_option").addClass("hidden");
            $(".custom_select_box .processing_inactive_option").addClass("hidden");
        }
    });

    $("body").on("click",".get_remote_server_info", function(){
        var tunnel_id=1;
        $.ajax({
            url:"request.php?request=get_remote_server_info&tunnel_id="+tunnel_id+"&token="+token,
            success:function(resp){
                var data=$.parseJSON(resp);
                if(data.status==1){
                    notify_msg("success", data.data);
                }else{
                    notify_msg("error", data.data);
                }
            }
        });
    });
    $("body").on("click",".set_remote_server_info", function(){
        var tunnel_id=1;
        $.ajax({
            url:"request.php?request=set_remote_server_info&tunnel_id="+tunnel_id+"&root_c="+tunnel_id+"&cr_c="+tunnel_id+"&k_c="+tunnel_id+"&ip="+tunnel_id+"&port="+tunnel_id+"&protocol="+tunnel_id+"&extra="+tunnel_id+"&token="+token,
            success:function(resp){
                var data=$.parseJSON(resp);
                if(data.status==1){
                    notify_msg("success", data.data);
                }else{
                    notify_msg("error", data.data);
                }
            }
        });
    });

});

function save_acl_name_description(ths, acl_id, key, value){

    var get={
        "type":"save_acl_name_description",
        "message_type":"request",
        "data":{
            "acl_id":acl_id,
            "field":key,
            "value":value
        }
    };
    console.log(get);
    send(JSON.stringify(get));
    /*ths.attr('data-value',value);
     $.ajax({
     url:"request.php?request=save_acl_name_description&acl_id="+acl_id+"&field="+key+"&value="+value,
     type:"GET",
     success:function(resp){
     if(resp){
     console.log(resp);
     if(key=="acl_name"){
     notify_msg("success", "ACL name saved successfully");
     }else{
     notify_msg("success", "ACL description saved successfully");
     }
     }
     }
     });*/
}

