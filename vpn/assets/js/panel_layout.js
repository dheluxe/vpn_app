function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);
    if (elt.selectedIndex == -1)
        return null;
    return elt.options[elt.selectedIndex].text;
}

function readImage_URL(input){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#profile_image_viewer').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

var everythingLoaded = setInterval(function() {
    $("#content2").addClass("hidden");
    $("#content5").addClass("hidden");
    $(".main-loading-message").removeClass("hidden");
    $("#blockDiv").removeClass("hidden");
    if (/loaded|complete/.test(document.readyState)) {
        clearInterval(everythingLoaded);
        init_page(); // this is the function that gets called when everything is loaded
    }
}, 10);
function init_page(){
    console.log("loaded");
    setTimeout(function(){
        $("#content2").removeClass("hidden");
        $("#content5").removeClass("hidden");
        $(".main-loading-message").addClass("hidden");
        $("#blockDiv").addClass("hidden");
    },ajax_loading_time_ms);
}

$(document).ready(function(){

    $("body").on("click",".self-profile-content-row",function(){
        $(".left-friend-list-content-row").removeClass("friend-selected");
        //$(this).addClass("friend-selected");
        var customer_id=$(this).attr("data-customer_id");

        location.href="main.php";
    });

    $("body").on("click",".current-friends-box .profile-info-box",function(){
            $(".left-friend-list-content-row").removeClass("friend-selected");
            $(this).closest(".left-friend-list-content-row").addClass("friend-selected");
            var customer_id = $(this).closest(".left-friend-list-content-row").attr("data-friend_id");
            location.href = "customer.php?customer_id=" + customer_id;
    });

    $("body").on("click",".left-my-self-title",function(){
        location.href="main.php";
    });

    $("body").on("change","#cloud_selector",function(){
        var cloud_id=$(this).val();
        var cloud_name = getSelectedText('cloud_selector')
        var token=$(this).attr("data-token");
        $.ajax({
            url:"request.php?request=cloud_tunnels&cloud_id="+cloud_id+"&cloud_name="+cloud_name+"&token="+token,
            success:function(resp){
                $(".filter-result-block").html(resp);
            }
        });
    });

    $("body").on("change",".account_searchable_switch",function(){
        var is_searchable=($(this).prop("checked")?"1":"0");
        var id=$(this).attr("data-customer_id");
        var database="customers_data";
        var field="is_searchable";
        $.ajax({
            url:"request.php?request=change_searchable&database="+database+"&id="+id+"&field="+field+"&value="+is_searchable,
            success:function(resp){
                console.log(resp);
                if(resp){
                    //notify_msg("success",resp);
                    notify_msg("success", "Your setting has been saved.");
                }
            }
        });
    });

    $("body").on("change",".cloud_searchable_switch",function(){
        var is_searchable=($(this).prop("checked")?"1":"0");
        var id=$(this).attr("data-cloud_id");
        var database="clouds_data";
        var field="is_searchable";
        $.ajax({
            url:"request.php?request=change_searchable&database="+database+"&id="+id+"&field="+field+"&value="+is_searchable,
            success:function(resp){
                console.log(resp);
                if(resp){
                    //notify_msg("success",resp);
                    notify_msg("success", "Your setting has been saved.");
                }
            }
        });
    });

    $("body").on("change",".tunnel_searchable_switch",function(){
        var is_searchable=($(this).prop("checked")?"1":"0");
        var id=$(this).attr("data-tunnel_id");
        var database="tunnels_data";
        var field="is_searchable";
        $.ajax({
            url:"request.php?request=change_searchable&database="+database+"&id="+id+"&field="+field+"&value="+is_searchable,
            success:function(resp){
                console.log(resp);
                if(resp){
                    //notify_msg("success",resp);
                    notify_msg("success", "Your setting has been saved.");
                }
            }
        });
    });

    $("body").on("change",".acl_searchable_switch",function(){
        var is_searchable=($(this).prop("checked")?"1":"0");
        var id=$(this).attr("data-acl_id");
        var database="tunnel_acl_relation";
        var field="is_searchable";
        $.ajax({
            url:"request.php?request=change_searchable&database="+database+"&id="+id+"&field="+field+"&value="+is_searchable,
            success:function(resp){
                console.log(resp);
                if(resp){
                    //notify_msg("success",resp);
                    notify_msg("success", "Your setting has been saved.");
                }
            }
        });
    });
    $("body").on("change","#profile_image",function(){
        readImage_URL(this);
    });

    function init_friend_list(){
        $.ajax({
            url:"request.php?request=get_friends&customer_id="+current_customer_id,
            type:"GET",
            success:function(resp){
                if(resp!="") {
                    //console.log(resp);
                    jQuery(".friends-box").html(resp);
                }
            }
        });
    }

    function update_badge_cnt(){
        $.ajax({
            url:"request.php?request=update_badge_cnt&customer_id="+current_customer_id,
            type:"GET",
            success:function(resp){
                if(resp!="") {
                    //console.log(resp);
                    var data=jQuery.parseJSON(resp);
                    jQuery(".current-friend-count").html(data.friends_cnt);
                    jQuery(".request-friend-count").html(data.request_friends_cnt);
                    jQuery(".rejected-friend-count").html(data.rejected_friends_cnt);
                }
            }
        });
    }

    $("body").on("change","#tab1", function () {
        if($(this).val()=="on"){
            $("#content2").addClass("hidden");
            $(".main-loading-message").removeClass("hidden");
            $("#blockDiv").removeClass("hidden");
            $.ajax({
                url:"home.php",
                success:function(resp){
                    $("#content2").html(resp);
                    setTimeout(function(){
                        $("#content2").removeClass("hidden");
                        $(".main-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }
            });
        }
    });

    $("body").on("change","#tab2", function () {
        if($(this).val()=="on"){
            $("#content2").addClass("hidden");
            $(".main-loading-message").removeClass("hidden");
            $("#blockDiv").removeClass("hidden");
            $.ajax({
                url:"connectivity_manager.php",
                success:function(resp){
                    $("#content2").html(resp);
                    setTimeout(function(){
                        $("#content2").removeClass("hidden");
                        $(".main-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }
            });
        }
    });

    $("body").on("change","#tab3", function () {
        if($(this).val()=="on"){
            $("#content2").addClass("hidden");
            $(".main-loading-message").removeClass("hidden");
            $("#blockDiv").removeClass("hidden");
            $.ajax({
                url:"profile_info.php",
                success:function(resp){
                    $("#content2").html(resp);
                    setTimeout(function(){
                        $("#content2").removeClass("hidden");
                        $(".main-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }
            });
        }

    });
    $("body").on("change","#tab4", function () {
        if($(this).val()=="on"){
            $("#content2").addClass("hidden");
            $(".main-loading-message").removeClass("hidden");
            $("#blockDiv").removeClass("hidden");
            $.ajax({
                url:"social.php",
                success:function(resp){
                    $("#content2").html(resp);
                    setTimeout(function(){
                        $("#content2").removeClass("hidden");
                        $(".main-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }
            });
        }
    });
    $("body").on("change","#tab5", function () {
        if($(this).val()=="on"){
            $("#content5").addClass("hidden");
            $(".main-loading-message").removeClass("hidden");
            $("#blockDiv").removeClass("hidden");
            $.ajax({
                url:"home.php",
                success:function(resp){
                    $("#content5").html(resp);
                    setTimeout(function(){
                        $("#content5").removeClass("hidden");
                        $(".main-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }
            });
        }
    });
    $("body").on("change","#tab6", function () {
        if($(this).val()=="on"){
            $("#content5").addClass("hidden");
            $(".main-loading-message").removeClass("hidden");
            $("#blockDiv").removeClass("hidden");
            $.ajax({
                url:"service.php",
                success:function(resp){
                    $("#content5").html(resp);
                    load_customer_acls(customer_id);
                    setTimeout(function(){
                        $("#content5").removeClass("hidden");
                        $(".main-loading-message").addClass("hidden");
                        $("#blockDiv").addClass("hidden");
                    },ajax_loading_time_ms);
                }
            });
        }
    });

    $("body").on("click",".tab-element",function(){
        jQuery(".tab-element").removeClass("tab-element-selected");
        jQuery(this).addClass("tab-element-selected");
        var origin_tab=jQuery("#selected_tab").val();
        var current_tab=jQuery(this).attr("data-type");
        //update_badge_cnt();
        if(origin_tab!=current_tab){
            $("#selected_tab").val(current_tab);
            $(".friend-search-text").val("");
            if(current_tab=="friends"){
                $(".friends-box").addClass("hidden");
                $(".sidebar-loading-message").removeClass("hidden");
                $(".request-friends-box").addClass("hidden");
                $(".rejected-friends-box").addClass("hidden");
                $("#blockDiv").removeClass("hidden");
                $.ajax({
                    url:"request.php?request=get_friends&customer_id="+current_customer_id,
                    type:"GET",
                    success:function(resp){
                        if(resp!="") {
                            //console.log(resp);
                            $(".friends-box").html(resp);
                            setTimeout(function(){
                                $(".friends-box").removeClass("hidden");
                                $(".sidebar-loading-message").addClass("hidden");
                                $("#blockDiv").addClass("hidden");
                            },1000);
                        }
                    }
                });
            }
            if(current_tab=="request"){
                $(".request-friends-box").addClass("hidden");
                $(".sidebar-loading-message").removeClass("hidden");
                jQuery(".friends-box").addClass("hidden");
                jQuery(".rejected-friends-box").addClass("hidden");
                jQuery(".request-friends-box").html("");
                $("#blockDiv").removeClass("hidden");
                $.ajax({
                    url:"request.php?request=get_request_friends&customer_id="+current_customer_id,
                    type:"GET",
                    success:function(resp){
                        if(resp!="") {
                            //console.log(resp);
                            jQuery(".request-friends-box").html(resp);
                            setTimeout(function(){
                                $(".request-friends-box").removeClass("hidden");
                                $(".sidebar-loading-message").addClass("hidden");
                                $("#blockDiv").addClass("hidden");
                            },1000);
                        }
                    }
                });
            }
            if(current_tab=="rejected"){
                $(".rejected-friends-box").addClass("hidden");
                $(".sidebar-loading-message").removeClass("hidden");
                jQuery(".request-friends-box").addClass("hidden");
                jQuery(".friends-box").addClass("hidden");
                jQuery(".rejected-friends-box").html("");
                $("#blockDiv").removeClass("hidden");
                $.ajax({
                    url:"request.php?request=get_rejected_friends&customer_id="+current_customer_id,
                    type:"GET",
                    success:function(resp){
                        if(resp!="") {
                            //console.log(resp);
                            jQuery(".rejected-friends-box").html(resp);
                            setTimeout(function(){
                                $(".rejected-friends-box").removeClass("hidden");
                                $(".sidebar-loading-message").addClass("hidden");
                                $("#blockDiv").addClass("hidden");
                            },1000);
                        }
                    }
                });
            }
        }
    });

    $("body").on("click",".all-customers-box .friend-action",function(){
        var friend_id=jQuery(this).attr("data-friend_id");
        var node=jQuery(this);
        $.ajax({
            url:"request.php?request=set_friend&status=request&customer_id="+current_customer_id+"&friend_id="+friend_id,
            type:"GET",
            success:function(resp){
                if(resp!="") {
                    node.closest(".left-friend-list-content-row").addClass("deleted");
                    update_badge_cnt();
                }
            }
        });
    });

    $("body").on("click",".request-friends-box .accept-action",function(){
        var friend_id=jQuery(this).attr("data-friend_id");
        var node=jQuery(this);
        $.ajax({
            url:"request.php?request=set_friend&status=accepted&customer_id="+friend_id+"&friend_id="+current_customer_id,
            type:"GET",
            success:function(resp){
                console.log(resp);
                if(resp!="") {
                    node.closest(".left-friend-list-content-row").addClass("deleted");
                    update_badge_cnt();
                }
            }
        });
    });

    $("body").on("click",".request-friends-box .reject-action",function(){
        var friend_id=jQuery(this).attr("data-friend_id");
        var node=jQuery(this);
        $.ajax({
            url:"request.php?request=set_friend&status=rejected&customer_id="+friend_id+"&friend_id="+current_customer_id,
            type:"GET",
            success:function(resp){
                console.log(resp);
                if(resp!="") {
                    node.closest(".left-friend-list-content-row").addClass("deleted");
                    update_badge_cnt();
                }
            }
        });
    });

    $("body").on("click",".delete-action",function(){
        var friend_id=jQuery(this).attr("data-friend_id");
        var node=jQuery(this).closest(".left-friend-list-content-row");
        $.ajax({
            url:"request.php?request=set_friend&status=deleted&customer_id="+friend_id+"&friend_id="+current_customer_id,
            type:"GET",
            success:function(resp){
                console.log(resp);
                if(resp!="") {
                    node.addClass("deleted");
                    update_badge_cnt();
                }
            }
        });
    });

    $("body").on("keyup",".friend-search-text",function(){

        var key_code_value=jQuery(this).val();
        var key_code=key_code_value.trim();
        var tab_status=jQuery("#selected_tab").val();
        if(tab_status=="friends"){
            var key_code=key_code.toUpperCase();
            if(key_code==""){
                jQuery(".all-customers-box").addClass("hidden");
                jQuery(".current-friends-box").removeClass("hidden");
            }else{
                jQuery(".all-customers-box").removeClass("hidden");
                jQuery(".current-friends-box").addClass("hidden");
                jQuery(".all-customers-box").html("");
                if(key_code=="#" || key_code=="%"){
                    return false;
                }

                $(".all-customers-box").addClass("hidden");
                $(".sidebar-loading-message").removeClass("hidden");
                $.ajax({
                    url:"request.php?request=get_customers&customer_id="+current_customer_id+"&key_code="+key_code,
                    type:"GET",
                    success:function(resp){
                        //console.log(resp);
                        jQuery(".all-customers-box").html(resp);
                        setTimeout(function(){
                            $(".sidebar-loading-message").addClass("hidden");
                            $(".all-customers-box").removeClass("hidden");
                        },1000);

                    }
                });
            }
        }else if(tab_status=="request"){
            var key_code=key_code.toUpperCase();

            jQuery(".request-friends-box .friend_name").each(function(key,node){
                var friend_name=jQuery(node).html();
                var friend_name=friend_name.toUpperCase();
                if(friend_name.indexOf(key_code)==-1){
                    jQuery(node).closest(".left-friend-list-content-row").addClass("hidden");
                }else{
                    jQuery(node).closest(".left-friend-list-content-row").removeClass("hidden");
                }
            });

        }else{
            var key_code=key_code.toUpperCase();
            jQuery(".rejected-friends-box .friend_name").each(function(key,node){
                var friend_name=jQuery(node).html();
                var friend_name=friend_name.toUpperCase();
                if(friend_name.indexOf(key_code)==-1){
                    jQuery(node).closest(".left-friend-list-content-row").addClass("hidden");
                }else{
                    jQuery(node).closest(".left-friend-list-content-row").removeClass("hidden");
                }
            });

        }
    });



    $("body").on("click",".dialog-tab-element",function(){
        jQuery(".dialog-tab-element").removeClass("dialog-tab-element-selected");
        jQuery(this).addClass("dialog-tab-element-selected");
    });

    $("body").on("keyup",".dialog-friend-search-text",function(){
        var key_code=jQuery(this).val();
        var key_code=key_code.toUpperCase();
        jQuery(".friend_name").each(function(key,node){
            var friend_name=jQuery(node).html();
            var friend_name=friend_name.toUpperCase();
            if(friend_name.indexOf(key_code)==-1){
                jQuery(node).closest(".dialog-left-friend-list-content-row").addClass("hidden");
            }else{
                jQuery(node).closest(".dialog-left-friend-list-content-row").removeClass("hidden");
            }
        });
    });

    $("body").on("click",".header_account_destroy",function(e){
        e.preventDefault();
        if(confirm("Are you sure to destroy this account?")){
            console.log("delete this account");
            console.log(token);
            $.ajax({
                url:"request.php?request=destroy_account&token="+token,
                success:function(resp){
                    console.log(resp);
                    $("#content2").addClass("hidden");
                    $("#content5").addClass("hidden");
                    $(".main-loading-message").removeClass("hidden");
                    $("#blockDiv").removeClass("hidden");
                    var get={"type":"destroy_account", "message_type":"request", "token":token};
                    send(JSON.stringify(get));
                }
            });

        }
    });
});

$(document).ajaxComplete(function(event,xhr,settings){
    console.log(xhr);
    var res=xhr.responseText;
    var trim_res=res.trim();
    if(trim_res == "empty_session"){
        token="";
        console.log(xhr.responseText);
        location.reload(true);
    }
});

