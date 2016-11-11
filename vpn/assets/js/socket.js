var Server;

function send( text ) {
    Server.send( 'message', text );
}
$(document).ready(function(){
    //fancy webocket
    console.log('Connecting...');
    Server = new FancyWebSocket('ws://'+MAIN_SERVER_IP+':'+WEB_SOCKET_PORT);
    console.log(Server);
    //Let the user know we're connected
    Server.bind('open', function() {
        console.log( "Connected." );
        var data={"type":"authorize","message_type":"request","value":{"token":token}};
        console.log(data);
        send(JSON.stringify(data));
    });
    //OH NOES! Disconnection occurred.
    Server.bind('close', function( data ) {
        console.log(token);
        console.log( "Disconnected." );
        if(token!=""){
            //location.reload(true);
        }
    });
    //Log any messages sent from server
    Server.bind('message', function( payload ) {
        //console.log(payload);
        var data=$.parseJSON(payload);
        console.log(data);
        /*if(data.free_field=="yes"){
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            $(".tunnel_"+data.data.id).attr("data-original-title", "Select tunnel");
        }
        else */
        if(data.type=="destroy_account"){
            console.log(token);
            token="";
            location.reload(true);
        }

        if(data.type=="get_tunnels"){
            var content_html=connectivity_page_content(data.data);
            $("#content2").html(content_html);
            setTimeout(function(){
                $("#content2").removeClass("hidden");
                $(".main-loading-message").addClass("hidden");
                $("#blockDiv").addClass("hidden");
            },ajax_loading_time_ms);
        }
        else if(data.type=="get_profile_info"){
            var content_html=profile_info_page_content(data.data);
            $("#content2").html(content_html);
            setTimeout(function(){
                $("#content2").removeClass("hidden");
                $(".main-loading-message").addClass("hidden");
                $("#blockDiv").addClass("hidden");
            },ajax_loading_time_ms);
        }
        else if(data.type=="get_home_info"){
            var content_html=get_home_info_page_content(data.data);
            $("#content2").html(content_html);
            setTimeout(function(){
                $("#content2").removeClass("hidden");
                $(".main-loading-message").addClass("hidden");
                $("#blockDiv").addClass("hidden");
            },ajax_loading_time_ms);
        }
        else if(data.type=="get_social_info"){
            var content_html=get_social_info_page_content(data.data);
            $("#content2").html(content_html);
            setTimeout(function(){
                $("#content2").removeClass("hidden");
                $(".main-loading-message").addClass("hidden");
                $("#blockDiv").addClass("hidden");
            },ajax_loading_time_ms);
        }
        else if(data.type=="change_searchable"){
            if(data.data.status=="1"){
                var selector="";
                if(data.data.data.database=="customers_data"){
                    selector=".account_searchable_switch_block #cmn-toggle-"+data.data.data.id;
                }else if(data.data.data.database=="clouds_data"){
                    selector="#cmn-toggle-cloud-"+data.data.data.id;
                }else if(data.data.data.database=="tunnels_data"){
                    selector="#cmn-toggle-tunnel-"+data.data.data.id;
                }else if(data.data.data.database=="tunnel_acl_relation"){
                    selector="#cmn-toggle-acl-"+data.data.data.id;
                }
                if(selector!=""){
                    if(data.data.data.value==1){
                        $(selector).prop("checked",true);
                    }else{
                        $(selector).prop("checked",false);
                    }
                    notify_msg("success", "Searchable changed successfully.");
                }
            }else{
                notify_msg("error", "You can not change this setting.");
            }
        }
        else if(data.type=="change_dev_status"){
            if (data.data.status == 1) {
                var selector=".dev_status_"+data.data.data.id;
                var dev_icon="";
                var dev_message="";
                if(data.data.data.st == 1){
                    dev_icon = '<i class="fa fa-times" aria-hidden="true"></i>';
                    dev_message = data.data.data.DeV;
                }
                else if(data.data.data.st == 0){
                    dev_icon = '<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>';
                    dev_message = 'Initiating';
                }
                else if(data.data.data.st == -1){
                    dev_icon = '<i class="fa fa-share-square-o" aria-hidden="true"></i>';
                    dev_message = 'Disconnected';
                }
                setTimeout(function(){
                    $(selector).html(dev_icon);
                    $(".dev-status-label_"+data.data.data.id).html(dev_message);
                    notify_msg("success", "Dev status changed successfully.");
                }, 1000);
            }else{
                notify_msg("error", "You can not change this setting.");
            }
        }
        else if(data.type=="route_change"){

        }
        else if(data.type=="plan_change"){

        }
        else if(data.type=="internet_change"){

        }
        else if(data.type=="remove_sharing"){
            if(data.data.status==1){
                var tunnel_id=data.data.data.tunnel_id;
                $(".sponsored_"+tunnel_id).css("background-color","transparent");
                $(".sponsored_"+tunnel_id).css("color","black");
                $(".sponsored_"+tunnel_id).css("opacity","0.25");
                $(".sponsored_"+tunnel_id).html("Sponsore");
            }
        }
        else if(data.type=="get_friend_list"){
            if(data.status==1){
                var friend_list=data.data;
                if(data.data.cloud_id==undefined || data.data.cloud_id==""){
                    var html=get_friend_list_page_for_sidebar(friend_list);
                    $(".friends-box").html(html);
                    var get={
                        "type":"get_badge_cnt",
                        "message_type":"request",
                        "data":{
                            "token":token
                        }
                    };
                    send(JSON.stringify(get));

                    setTimeout(function(){
                        $(".friends-box").removeClass("hidden");
                        $(".sidebar-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }else{
                    var html=get_friend_list_page_for_dialog(friend_list);
                    $("#sponsor-friend-list-box").html(html);
                    $(".customer_search_btn").attr("data-cloud", data.data.cloud_id);
                    $(".customer_search_btn").attr("data-u", data.data.u_id);
                    $(".customer_search_btn").attr("data-tid", data.data.tunnel_id);
                    $("#sponsorModal").modal("show");
                }
            }
        }
        else if(data.type=="save_a_tunnel"){
            var id=data.data.id;
            if(data.status==1){
                notify_msg("success", data.message);
                $(".tunnel_"+id).html("<i class='fa fa-fw fa-circle-o-notch fa-spin'></i>");
                $(".tunnel_"+id).removeClass("tunnel_chk");
                $(".tunnel_"+id).attr("data-original-title", "Request submitted, please wait...");
                $(".tunnel_body_"+id).removeClass("row_chk");
            }else if(data.status==0){
                notify_msg("error", data.message);
            }
        }
        else if(data.type=="delete_tunnel"){
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="add_tunnel"){
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="request_real_ip"){
            if(data.status==1){
                notify_msg("success",data.data);
            }else{
                notify_msg("error",data.data);
            }
        }
        else if(data.type=="clear_tunnel_real_ip"){
            console.log('clear_tunnel_real_ip');
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="change_tunnel_real_ip"){
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="clear_acl_real_ip"){
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="get_acl_info"){
            console.log("get acl info");
            console.log(data);
            var tunnel_acl_info = data.data;
            var tunnel_id=data.value;
            box_btn_val = tunnel_acl_info;
            if(tunnel_acl_info.status != undefined && tunnel_acl_info.status == 0){
                notify_msg("error", tunnel_acl_info.data);
            }
            else{
                $(".source_acl_content_"+tunnel_id).html('');
                $(".destination_acl_content_"+tunnel_id).html('');
                console.log('show_acl_data');
                console.log(tunnel_acl_info);
                acl(tunnel_acl_info, null, tunnel_id);
                $(".box").trigger("mouseover");
                $(".tunnel_acl_div_"+tunnel_id).toggle();
            }
        }
        else if(data.type=="delete_acl"){
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="create_new_acl"){
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="set_default_acl"){
            console.log(data);
            if(data.status==1){
                notify_msg("success", data.data);
                var tid=data.tid;
                var id=data.id;
                update_tunnel_acl(tid);
            }
        }
        else if(data.type=="create_acl_clone"){
            console.log(data);
            if(data.status==1){
                notify_msg("success",data.data);
            }
        }
        else if(data.type=="save_acl_name_description"){
            console.log('save_acl_name_description');
            console.log(data);
            if(data.status==1){
                var name_description_data=data.data;
                $("."+name_description_data.field+"_"+name_description_data.acl_id).attr('data-value',name_description_data.value);
                $("."+name_description_data.field+"_"+name_description_data.acl_id).val(name_description_data.value);

                if(name_description_data.field=="acl_name"){
                    notify_msg("success", "ACL name saved successfully");
                }else{
                    notify_msg("success", "ACL description saved successfully");
                }
            }
        }
        else if(data.type=="add_server_clone"){
            console.log('add_server_clone');
            console.log(data);
            if(data.status==1){
                notify_msg("success", data.data);
            }
        }
        else if(data.type=="add_client_clone"){
            console.log('add_client_clone');
            console.log(data);
            if(data.status==1){
                notify_msg("success", data.data);
            }
        }
        else if(data.type=="delete_cloud"){
            console.log('delete_cloud');
            console.log(data);
            if(data.status==1){
                notify_msg("success", data.data);
                var cloud_id=data.cloud_id;
                $(".cloud-row-"+cloud_id).addClass("deleted");
                $("#cloud_selector option[value='"+cloud_id+"']").addClass("deleted");
            }
        }
        else if(data.type=="add_cloud"){
            console.log('add_cloud');
            console.log(data);
            if(data.status==1){
                location.reload(true);
            }
        }
        else if(data.type=="get_request_friend_list"){
            if(data.status==1){
                var friend_list=data.data;
                var html=get_request_friend_list_page_for_sidebar(friend_list);
                jQuery(".request-friends-box").html(html);
                var get={
                    "type":"get_badge_cnt",
                    "message_type":"request",
                    "data":{
                        "token":token
                    }
                };
                send(JSON.stringify(get));

                setTimeout(function(){
                    $(".request-friends-box").removeClass("hidden");
                    $(".sidebar-loading-message").addClass("hidden");
                    $("#blockDiv").addClass("hidden");
                },ajax_loading_time_ms);
            }
        }
        else if(data.type=="get_rejected_friend_list"){
            if(data.status==1){
                var friend_list=data.data;
                var html=get_rejected_friend_list_page_for_sidebar(friend_list);
                jQuery(".rejected-friends-box").html(html);
                var get={
                    "type":"get_badge_cnt",
                    "message_type":"request",
                    "data":{
                        "token":token
                    }
                };
                send(JSON.stringify(get));

                setTimeout(function(){
                    $(".rejected-friends-box").removeClass("hidden");
                    $(".sidebar-loading-message").addClass("hidden");
                    $("#blockDiv").addClass("hidden");
                },ajax_loading_time_ms);
            }
        }
        else if(data.type=="set_friend"){
            console.log('recieve request');
            if(data.status==1){
                var friend_id=data.data.customer_id;
                var my_id=data.data.friend_id;
                /*if(data.data.status=="deleted"){*/
                    $(".left-friend-list-content-row-"+friend_id).addClass("deleted");
                    $(".left-friend-list-content-row-"+my_id).addClass("deleted");
                    notify_msg("success",data.message);
                    var get={
                        "type":"get_badge_cnt",
                        "message_type":"request",
                        "data":{
                            "token":token
                        }
                    };
                    send(JSON.stringify(get));
                /*}*/
            }
        }
        else if(data.type=="get_badge_cnt"){
            if(data.status==1){
                $(".current-friend-count").html(data.data.friends_cnt);
                $(".request-friend-count").html(data.data.request_friends_cnt);
                $(".rejected-friend-count").html(data.data.rejected_friends_cnt);
            }
        }














        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        else if(data.type=="edit_display"){
            $(".display_"+data.value.id).editable('toggleDisabled');
            $(".display_"+data.value.id).parent("div").attr("data-original-title", "This field is processing its last job, please wait...");
        }else if(data.type=="change_location"){
            $(".location_"+data.value.id).editable('toggleDisabled');
            $(".location_"+data.value.id).parent("div").attr("data-original-title", "This field is processing its last job, please wait...");
        }else if(data.type=="change_tunnel_to_server"){

        }else if(data.type=="change_tunnel_to_client"){

        }else if(data.type=="real_ip_status"){
            //$(".overlay_"+data.value.id).css("display", "block");
            $(".tunnel_"+data.value.id).html("<i class='fa fa-fw fa-circle-o-notch fa-spin'></i>");
            $(".tunnel_"+data.value.id).removeClass("tunnel_chk");
            $(".tunnel_"+data.value.id).attr("data-original-title", "Request submitted, please wait...");
        }else if(data.type=="gateway_change_result"){
            if(data.data.value==1){
                $(".tunnel_gate_"+data.data.id).html('<i class="fa fa-check" style="color:#1D9E74"></i>');
                $(".tunnel_gate_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_gate_"+data.data.id).attr("data-original-title", "Yes");
            }else if(data.data.value==0){
                $(".tunnel_gate_"+data.data.id).html('<i class="fa fa-times" style="color:#DA3838"></i>');
                $(".tunnel_gate_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_gate_"+data.data.id).attr("data-original-title", "No");
            }
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Gateway changed successfully");
        }else if(data.type=="status_change_result"){
            if(data.data.value==1){
                $(".tunnel_stat_"+data.data.id).html('<i class="fa fa-circle" style="color:#1D9E74"></i>');
                $(".tunnel_stat_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_stat_"+data.data.id).attr("data-original-title", "Active");
                $(".tunnel_body_"+data.data.id).css("background-color", "");
            }else if(data.data.value==0){
                $(".tunnel_stat_"+data.data.id).html('<i class="fa fa-circle" style="color:#DA3838"></i>');
                $(".tunnel_stat_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_stat_"+data.data.id).attr("data-original-title", "Inactive");
                $(".tunnel_body_"+data.data.id).css("background-color", "#cecece");
            }

            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Status changed successfully");
        }else if(data.type=="bidirection_change_result"){
            console.log(data);
            if(data.data.value==0){
                $(".tunnel_bi_"+data.data.id).html('<i class="fa fa-chevron-left"><i class="fa fa-chevron-right"></i>');
                $(".tunnel_bi_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_bi_"+data.data.id).attr("data-original-title", "Mode 1");
            }else if(data.data.value==1){
                $(".tunnel_bi_"+data.data.id).html('<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i>');
                $(".tunnel_bi_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_bi_"+data.data.id).attr("data-original-title", "Mode 2");
            }else if(data.data.value==2){
                $(".tunnel_bi_"+data.data.id).html('<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right"></i>');
                $(".tunnel_bi_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_bi_"+data.data.id).attr("data-original-title", "Mode 3");
            }else if(data.data.value==3){
                $(".tunnel_bi_"+data.data.id).html('<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i>');
                $(".tunnel_bi_"+data.data.id).attr("data-val", data.data.value);
                $(".tunnel_bi_"+data.data.id).attr("data-original-title", "Mode 4");
            }

            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Bidirection mode changed successfully");
        }else if(data.type=="group_change_result"){
            if(data.data.value in group_arr){
                $(".tunnel_grp_"+data.data.id).attr('data-val',data.data.value);
                $(".tunnel_grp_"+data.data.id).html(group_arr[data.data.value]);
                $(".tunnel_grp_"+data.data.id).parent("div").closest('.tunnel_chk').addClass('tunnel_grp_chk_'+data.data.value);
                $(".tunnel_grp_"+data.data.id).attr('data-original-title','Group '+group_arr[data.data.value]);
            }else{
                data.data.value=0;
                if(data.data.value in group_arr){
                    $(".tunnel_grp_"+data.data.id).attr('data-val',data.data.value);
                    $(".tunnel_grp_"+data.data.id).html(group_arr[data.data.value]);
                    $(".tunnel_grp_"+data.data.id).parent("div").closest('.tunnel_chk').addClass('tunnel_grp_chk_'+data.data.value);
                    $(".tunnel_grp_"+data.data.id).attr('data-original-title','Group '+group_arr[data.data.value]);
                }
            }
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Group changed successfully");
        }else if(data.type=="internet_change_result"){
            if(data.data.value==1){
                $(".tunnel_internet_"+data.data.id).css("background-color", '#b9c3c8');
                $(".tunnel_internet_"+data.data.id).css("color", '#ffffff');
                $(".tunnel_internet_"+data.data.id).css("opacity", 1);
                $(".tunnel_internet_"+data.data.id).attr("data-val", data.data.value);
            }else if(data.data.value==0){
                $(".tunnel_internet_"+data.data.id).css("background-color", 'transparent');
                $(".tunnel_internet_"+data.data.id).css("color", 'black');
                $(".tunnel_internet_"+data.data.id).css("opacity", 0.25);
                $(".tunnel_internet_"+data.data.id).attr("data-val", data.data.value);
            }

            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Internet changed successfully");

        }else if(data.type=="route_change_result"){
            if(data.data.value==1){
                $(".tunnel_route_"+data.data.id).css("background-color", '#b9c3c8');
                $(".tunnel_route_"+data.data.id).css("color", '#ffffff');
                $(".tunnel_route_"+data.data.id).css("opacity", 1);
                $(".tunnel_route_"+data.data.id).attr("data-val", data.data.value);
            }else if(data.data.value==0){
                $(".tunnel_route_"+data.data.id).css("background-color", 'transparent');
                $(".tunnel_route_"+data.data.id).css("color", 'black');
                $(".tunnel_route_"+data.data.id).css("opacity", 0.25);
                $(".tunnel_route_"+data.data.id).attr("data-val", data.data.value);
            }

            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Route changed successfully");
        }else if(data.type=="plan_change_result"){
            if(data.data.value==2){
                $(".acc_type_"+data.data.id).attr("data-val", 2);
                $(".acc_type_"+data.data.id).html("Premium");
                $(".acc_type_"+data.data.id).css("color", "black");
                $(".plan_cost_"+data.data.id).html(data.data.p_cost);
            }else if(data.data.value==1){
                $(".acc_type_"+data.data.id).attr("data-val", 1);
                $(".acc_type_"+data.data.id).html("Premium");
                $(".acc_type_"+data.data.id).css("color", "white");
                $(".plan_cost_"+data.data.id).html(data.data.p_cost);
            }
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
            notify_msg("success", "Account type has been changed successfully");
        }else if(data.type=="edit_email_result"){
            $(".tunnel_email_"+data.data.id).html('<a href="javascript:void(0);" class="email sck_editable" data-type="text" data-pk="'+data.data.id+'" data-title="Enter Email ID">'+data.data.value+'</a>');
            $(".tunnel_email_"+data.data.id).attr("data-original-title", data.data.value);
            $(".sck_editable").trigger("click");
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="edit_display_result"){
            console.log(data);
            $(".tunnel_display_"+data.data.id).html('<a href="javascript:void(0);" class="display sck_editable" data-type="text" data-pk="'+data.data.id+'" data-title="Enter display_name">'+data.data.value+'</a>');
            $(".tunnel_display_"+data.data.id).attr("data-original-title", data.data.value);
            $(".sck_editable").trigger("click");
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="change_location_result"){
            $(".tunnel_location_"+data.data.id).html('<a href"javascript:void(0);" class="change_location sck_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="'+data.data.id+'">'+location_option[data.data.value]+'</a>');
            $(".tunnel_display_"+data.data.id).attr("data-original-title", data.data.value);
            $(".sck_editable").trigger("click");
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="change_tunnel_to_server_result"){
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").addClass("bg_yellow");
            $(".not_client_"+data.data.id).html("");
            $(".change_tunnel_"+data.data.id).html('Client');

            $(".change_tunnel_"+data.data.id).attr("data-type", "client");

            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="change_tunnel_to_client_result"){
            var html="";
            html+='<div class="meta width-100" data-toggle="tooltip" title="Not assigned"><a href="javascript:void(0);" class="real_ip real_ip_'+data.data.id+'" data-val="-1" data-id="'+data.data.id+'">Not assigned</a></div>';
            html+='<div class="meta cursor"><div class="gateway tunnel_gate_'+data.data.id+'" type="data" data-toggle="tooltip" title="No" data-cast="" data-val="0" data-id="'+data.data.id+'"><i class="fa fa-times" style="color:#DA3838"></i></div></div>';
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").removeClass("bg_yellow");
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").addClass("bg_green");
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").css("width","1383px");

            $(".not_client_"+data.data.id).html(html);
            $(".change_tunnel_"+data.data.id).html('Server');
            $(".change_tunnel_"+data.data.id).attr("data-type", "server");

            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="add_tunnels_result"){
            //var res_data=tunnels(data.data);
            console.log('new_tunnel');
            console.log(data);
            var cloud_id=data.data[0].cloud_id;
            update_cloud(cloud_id);
            notify_msg("success", "A new tunnel created successfully");
        }else if(data.type=="add_server_clone_result"){
            //var res_data=tunnels(data.data);
            var cloud_id=data.data['tunnel'].cloud_id;
            $.ajax({
                url : "request.php?request=update_cloud&cloud_id="+cloud_id,
                type : "GET",
                success : function(resp){
                    $(".cloud-row-"+cloud_id).html(resp);
                    notify_msg("success", "Server clone created");
                },
                error : function(){;
                }
            });
        }else if(data.type=="add_client_clone_result"){
            //var res_data=tunnels(data.data);
            var cloud_id=data.data['tunnel'].cloud_id;
            $.ajax({
                url : "request.php?request=update_cloud&cloud_id="+cloud_id,
                type : "GET",
                success : function(resp){
                    $(".cloud-row-"+cloud_id).html(resp);
                    notify_msg("success", "Client clone created");
                },
                error : function(){
                }
            });
        }else if(data.type=="change_tunnel_server_result"){
            var html="";
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").removeClass("bg_yellow");
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").addClass("bg_green");
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").css("width","1383px");

            $(".change_tunnel_"+data.data.id).attr("data-type", "server");
            console.log(".destination_acl_content_"+data.data.id);
            $(".destination_acl_content_"+data.data.id).removeClass("disabled");

            $(".not_client_"+data.data.id+" .width-140").html('<div class="real_ip_select_box_'+data.data.id+' custom_select_box" data-val="-1" data-id="'+data.data.id+'"><div class="custom_option active_option"><div class="display_value" data-value="">Not assigned</div><div class="assign_action_btn" data-id="'+data.data.id+'"><i class="fa fa-fw fa-plus"></i></div></div><div class="custom_option inactive_option hidden"><div class="display_value" data-value="">None</div></div></div>');
            $(".not_client_"+data.data.id+" .width-140").removeClass("meta");
            $(".not_client_"+data.data.id+" .width-140").addClass("real_ip_meta");
            $(".not_client_"+data.data.id+" .width-140").attr("data-toggle","tooltip");
            $(".not_client_"+data.data.id+" .width-140").attr("data-placement","");
            $(".not_client_"+data.data.id+" .width-140").attr("title","");

            $(".not_client_"+data.data.id+" .width-60").html('<input type="hidden" class="edit_gateway_s" name="gateway" value="0"><div class="gateway tunnel_gate_259" data-pos="0" type="data" data-toggle="tooltip" title="No" data-cast="5" data-val="0" data-id="259"><i class="fa fa-times" style="color:#DA3838"></i></div>');
            $(".not_client_"+data.data.id+" .width-60").addClass("meta");
            $(".not_client_"+data.data.id+" .width-60").attr("data-toggle","");
            $(".not_client_"+data.data.id+" .width-60").attr("data-placement","");
            $(".not_client_"+data.data.id+" .width-60").attr("title","");

            notify_msg("success", "Client converted into server successfully done");
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="change_tunnel_client_result"){
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").addClass("bg_yellow");
            $(".change_tunnel_"+data.data.id).attr("data-type", "client");

            $(".not_client_"+data.data.id+" .width-140").html('<a href="javascript:void(0)" class="change_tunnel change_tunnel_'+data.data.id+'" data-type="client" data-id="'+data.data.id+'"><i class="fa fa-long-arrow-up"></i></a>');
            $(".not_client_"+data.data.id+" .width-140").addClass("meta");
            $(".not_client_"+data.data.id+" .width-140").attr("data-toggle","tooltip");
            $(".not_client_"+data.data.id+" .width-140").attr("data-placement","right");
            $(".not_client_"+data.data.id+" .width-140").attr("title","To activate this field upgrade to server");

            $(".not_client_"+data.data.id+" .width-60").html('<a href="javascript:void(0)" class="change_tunnel change_tunnel_'+data.data.id+'" data-type="client" data-id="'+data.data.id+'"><i class="fa fa-long-arrow-up"></i></a>');
            $(".not_client_"+data.data.id+" .width-60").addClass("meta");
            $(".not_client_"+data.data.id+" .width-60").attr("data-toggle","tooltip");
            $(".not_client_"+data.data.id+" .width-60").attr("data-placement","right");
            $(".not_client_"+data.data.id+" .width-60").attr("title","To activate this field upgrade to server");

            notify_msg("success", "Server converted into client successfully done");
            console.log(".destination_acl_content_"+data.data.id);
            $(".destination_acl_content_"+data.data.id).addClass("disabled");
            $(".change_tunnel_"+data.data.id).parent(".meta").parent(".list_body").removeClass("row_chk");
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="request_real_ip_result"){
            console.log("request_real_ip_result");
            $(".real_ip_select_box_"+data.data.id+" .processing_option").addClass("active_option");
            $(".real_ip_select_box_"+data.data.id+" .active_option").removeClass("processing_option");
            $(".real_ip_select_box_"+data.data.id+" .active_option .display_value").attr("data-value",data.data.real_ip);
            $(".real_ip_select_box_"+data.data.id+" .active_option .display_value").html(data.data.real_ip);
            $(".real_ip_select_box_"+data.data.id+" .active_option .assign_action_btn").addClass("action_btn");
            $(".real_ip_select_box_"+data.data.id+" .active_option .action_btn").removeClass("assign_action_btn");
            $(".real_ip_select_box_"+data.data.id+" .active_option .action_btn i").attr("class","fa fa-fw fa-times");
            $.ajax({
                url:"request.php?request=get_cost_data_from_tunnel_id&tunnel_id="+data.data.id,
                success:function(resp){
                    var res= $.parseJSON(resp);
                    console.log(res);
                    $(".plan_cost_"+res.tunnel_id).html(res.tunnel_cost);
                    $(".cloud-cost-"+res.cloud_id).html("&nbsp;(Total cost = "+res.cloud_cost+")");
                    notify_msg("success", "Real ip created successfully");
                }
            });
            $(".tunnel_"+data.data.id).html("<i class='fa fa-fw fa-square-o'></i>");
            $(".tunnel_"+data.data.id).attr("title", "Select tunnel");
            $(".tunnel_"+data.data.id).attr("data-val", "0");
            $(".tunnel_"+data.data.id).addClass("tunnel_chk");
        }else if(data.type=="clear_tunnel_real_ip_result"){
            console.log("clear_tunnel_real_ip_result");
            var empty_txt="Not assigned";
            $(".real_ip_select_box_"+data.data.id+" .processing_option").addClass("active_option");
            $(".real_ip_select_box_"+data.data.id+" .active_option").removeClass("processing_option");
            $(".real_ip_select_box_"+data.data.id+" .active_option .display_value").html(empty_txt);
            $(".real_ip_select_box_"+data.data.id+" .active_option .display_value").attr("data-value","");
            $(".real_ip_select_box_"+data.data.id+" .active_option .action_btn").addClass("assign_action_btn");
            $(".real_ip_select_box_"+data.data.id+" .active_option .assign_action_btn").removeClass("action_btn");
            $(".real_ip_select_box_"+data.data.id+" .active_option .assign_action_btn i").attr("class","fa fa-fw fa-plus");
            $.ajax({
                url:"request.php?request=get_cost_data_from_tunnel_id&tunnel_id="+data.data.id,
                success:function(resp){
                    var res= $.parseJSON(resp);
                    console.log(res);
                    $(".plan_cost_"+res.tunnel_id).html(res.tunnel_cost);
                    $(".cloud-cost-"+res.cloud_id).html("&nbsp;(Total cost = "+res.cloud_cost+")");
                    notify_msg("success", "Real ip removed successfully");
                }
            });
        }else if(data.type=="change_tunnel_real_ip_result"){
            console.log("change_tunnel_real_ip_result");
            $(".real_ip_select_box_"+data.data.id+" .processing_option").addClass("active_option");
            $(".real_ip_select_box_"+data.data.id+" .active_option").removeClass("processing_option");
            $(".real_ip_select_box_"+data.data.id+" .active_option .display_value").html(data.data.real_ip);
            $(".real_ip_select_box_"+data.data.id+" .active_option .display_value").attr("data-value",data.data.real_ip);
            $(".real_ip_select_box_"+data.data.id+" .active_option .assign_action_btn").addClass("action_btn");
            $(".real_ip_select_box_"+data.data.id+" .active_option .action_btn").removeClass("assign_action_btn");
            $(".real_ip_select_box_"+data.data.id+" .active_option .action_btn i").attr("class","fa fa-fw fa-times");
            $.ajax({
                url:"request.php?request=get_cost_data_from_tunnel_id&tunnel_id="+data.data.id,
                success:function(resp){
                    var res= $.parseJSON(resp);
                    console.log(res);
                    $(".plan_cost_"+res.tunnel_id).html(res.tunnel_cost);
                    $(".cloud-cost-"+res.cloud_id).html("&nbsp;(Total cost = "+res.cloud_cost+")");
                    notify_msg("success", "Real ip changed successfully");
                }
            });
        }else if(data.type=="clear_acl_real_ip_result"){
            console.log("clear_acl_real_ip_result");
            var aid=data.data.aid;
            var id=data.data.id;
            $(".real_ip_select_box_"+id+" .inactive_option_"+aid).addClass("deleted");
            $(".real_ip-destination-"+aid).css("opacity",0.3);
            $(".real_ip-destination-"+aid).css("color","black");
            $.ajax({
                url:"request.php?request=get_cost_data_from_tunnel_id&tunnel_id="+data.data.id,
                success:function(resp){
                    var res= $.parseJSON(resp);
                    console.log(res);
                    $(".plan_cost_"+res.tunnel_id).html(res.tunnel_cost);
                    $(".cloud-cost-"+res.cloud_id).html("&nbsp;(Total cost = "+res.cloud_cost+")");
                    notify_msg("success", "Real ip removed successfully");
                }
            });
        }
        else if(data.type=="real_ip_status_result"){
            if(data.data.value==1){
                $(".real_ip_"+data.data.id).css("color", "#1D9E74");
                $(".real_ip_"+data.data.id).attr("data-val", 1);
            }else if(data.data.value==0){
                $(".real_ip_"+data.data.id).css("color", "#1B1E24");
                $(".real_ip_"+data.data.id).attr("data-val", 0);
            }
            $(".plan_cost_"+data.data.id).html(data.data.cost);
            notify_msg("success", "Real ip status has been changed");
        }else if(data.type=="delete_tunnel_result"){
            console.log("delete_tunnel_result");
            console.log(data);
            var tunnel_id=data.data.data.id;
            var cloud_id=$(".tunnel_body_"+tunnel_id).closest(".cloud-row").attr("data-cid");
            update_cloud(cloud_id);
            notify_msg("success", "Tunnel deleted successfully.");
        }else if(data.type=="save_all_tunnel"){
            $.each(data.ids, function(key, val){
                //$(".overlay_"+val).css("display", "block");
                $(".tunnel_"+val).html("<i class='fa fa-fw fa-circle-o-notch fa-spin'></i>");
                $(".tunnel_"+val).removeClass("tunnel_chk");
                $(".tunnel_"+val).attr("data-original-title", "Request submitted, please wait...");
            });
        }else if(data.type=="get_subnet"){
            $(".subnet_"+data.data.id).html(data.data.ip);
        }else if(data.type=="get_DeV"){
            if(data.data.state==-1){
                $("#DeV_"+data.data.id).removeClass();
                $("#DeV_"+data.data.id).addClass("dev-disconnected");
                $("#DeV_"+data.data.id).addClass("width-50");
                 $("#DeV_"+data.data.id).html(data.data.device);
            }else if(data.data.state==0){
                $("#DeV_"+data.data.id).removeClass();
                $("#DeV_"+data.data.id).addClass("dev-connecting");
                $("#DeV_"+data.data.id).addClass("width-50");
                 $("#DeV_"+data.data.id).html(data.data.device);
            }else if(data.data.state==1){
                $("#DeV_"+data.data.id).removeClass();
                $("#DeV_"+data.data.id).addClass("dev-connected");
                $("#DeV_"+data.data.id).addClass("width-50");
                $("#DeV_"+data.data.id).html(data.data.device);
            }
        }else if(data.type=="deduct_cash_result"){
            var sett_val = $(".show_div_b").attr("data-p");
            var res = data.data.toString();
            var res = res*sett_val;
            var res1 = Math.round(res);

            $(".show_div").html('<b class="show_div_b" data-p="'+sett_val+'">Available Points: '+res1+'</b>');
        }
        //31.03.2016
        else if(data.type=="delete_acl_result"){
            console.log(data);
            var res = data.data.id;
            $(".acl_div_"+res).remove();
            notify_msg("success","ACL deleted successfully");
        }else if(data.type=="clear_acl_values_result"){
            var res = data.data.id
            $(".box_"+res).children("div").css("opacity", "0.35");
            notify_msg("success","ACL cleared successfully");
        }else if(data.type=="create_acl_clone_result"){
            console.log("create_acl_clone_result");
            console.log(data);
            var tunnel_id=0;
            $.each(data.data, function(key,value){
                tunnel_id = value.tunnel_id;
            });
            update_tunnel_acl(tunnel_id);
            notify_msg("success","ACL clone created successfully");
        }else if(data.type=="create_new_acl_result"){
            $.ajax({
                url:"request.php?request=get_acl_info&id="+data.data.id,
                success:function(resp){
                    var res = $.parseJSON(resp);
                    acl(res.data, null, res.value);
                }
            });
            notify_msg("success","New ACL created successfully");
        }else if(data.type=="save_acl_values_result"){
            console.log("save_acl_values_result");
            console.log(data);
            var html="";
            var acl_id="";
            var value="";
            var database="";
            var val="";
            var k="";
            var v="";
            $.each(data.data.data, function(key, value){
                acl_id = key;
                $.each(value, function(ky, val){
                    database = ky;
                    $.each(val, function(k, v){
                        console.log(ky);
                        console.log(k);
                        console.log(v);
                        if(((ky=="c_firewall" || ky=="c_routing") && (k=="allow_all" || k=="deny_all" || k=="country"))||((k == "every_cloud" || k == "my_clouds" || k == "my_cloud" || k == "specific_group" || k == "specific_tunnel" || k == "internet")&&(ky=="destination"))){
                            if(v!=""){
                                $('.'+k+'-'+ky+'-'+key).attr("title", v);
                            }
                        }
                        else if(ky=="c_forwarding" && k=="specific_tunnel"){
                            if(v!=""){
                                $(".new_dst-s_aliasing-"+acl_id).addClass("xxxxxxxxxx");
                                $(".xxxxxxxxxx").attr("class", "color-box "+"new_dst-s_aliasing-"+acl_id);
                                $(".new_dst-s_aliasing-"+acl_id).removeClass("xxxxxxxxxx");
                                $('.'+k+'-'+ky+'-'+key).css("opacity", "1");
                                $('.'+k+'-'+ky+'-'+key).css("color", "#ffffff");
                                $('.'+k+'-'+ky+'-'+key).attr("title", v);
                            }else{
                                $(".new_dst-s_aliasing-"+acl_id).addClass("xxxxxxxxxx");
                                $(".xxxxxxxxxx").attr("class", "disabled_color_box "+"new_dst-s_aliasing-"+acl_id);
                                $(".new_dst-s_aliasing-"+acl_id).removeClass("xxxxxxxxxx");
                                $('.'+k+'-'+ky+'-'+key).css("opacity", "0.3");
                                $('.'+k+'-'+ky+'-'+key).css("color", "#000000");
                                $('.'+k+'-'+ky+'-'+key).attr("title", v);
                            }
                        }
                        else{
                            if(v!=""){
                                $('.'+k+'-'+ky+'-'+key).css("opacity", "1");
                                $('.'+k+'-'+ky+'-'+key).attr("title", v);
                            }
                        }
                    });
                });
            });
            notify_msg("success", "ACL value saved successfully.");
        }else if(data.type=="change_acl_result"){

            console.log(data.data);
        } else if (data.type=="get_app_result"){
            console.log("get_app_result");
            console.log(data);

            var res = data.data;
            var html="";
            var html2 = "";
            var itemz = data.class.split("-");
            var d_final_app_data="";
            $.ajax({
                url:"request.php?request=get_acl_detail&id="+itemz[2],
                success:function(resp){
                    var acl_data = $.parseJSON(resp);
                    console.log("acl_data");
                    console.log(acl_data);
                    if(itemz[0]=="app"){
                        d_final_app_data=acl_data.d_final.app.value.split(",");
                    }else if(itemz[0]=="process"){
                        d_final_app_data=acl_data.d_final.process.value.split(",");
                    }
                    var d_final_app=d_final_app_data[0];
                    var d_final_app_port=d_final_app_data[1];
                    if(d_final_app_port==undefined){
                        d_final_app_port="";
                    }
                    console.log(d_final_app_data);
                    html+="<div class='row'>";
                    html+="<div class='col-md-4'><select class='d_final_app' data-id='"+itemz[2]+"' data-type='"+itemz[1]+"' data-value='"+itemz[0]+"'>";
                    html+="<option value=''>Select one...</option>";
                    for(x in res){
                        if(d_final_app==res[x].value){
                            html+="<option value='"+res[x].value+"' selected>"+res[x].label+"</option>";
                        }else{
                            html+="<option value='"+res[x].value+"'>"+res[x].label+"</option>";
                        }
                    }
                    html+="</select></div>";
                    html2 += itemz[0];
                    html+="<div class='col-md-4'><input type='text' class='acl_ip_port' placeholder='Enter IP port...' value='"+d_final_app_port+"'/></div>";
                    html+="</div>";

                    html+="<div class='row' style='margin-top: 5px;'>";
                    html+="<div class='col-md-6'>"
                    html+="<input type='button' class='btn acl_d_final_app_btn' data-text='"+itemz[0]+"' data-type='"+itemz[1]+"' data-id='"+itemz[2]+"' value='Save'>";
                    html+="</div>";
                    html+="</div>";
                    $("#acl_div_cont").html(html);
                }
            });
        }
        else if (data.type=="destroy_account_result"){
            console.log("destroy_account_result");
            console.log(data);
            var cus_token = data.token;
            location.href="request.php?request=acc_logout";
        }
        else if(data.type=="doupdate_profile"){
            $("#name").val(data.value.name);
            $("#phone").val(data.value.phone);
            $("#remail").val(data.value.remail);
        }else if(data.type=="update_plan"){
            $("#radio1").prop( "checked", false );
                $( "#radio2" ).prop( "checked", false );
                if(data.value.plan_id==1){
                $( "#radio1" ).prop( "checked", true );
           }else{
                $( "#radio2" ).prop( "checked", true );
           }
        }else if(data.type=="get_remote_server_info_result"){
            console.log(data);
            alert(JSON.stringify(data));
        }else if(data.type=="set_remote_server_info_result"){
            console.log(data);
        }
    });
    Server.connect();
});



