function load_customer_acls(customer_id){
    console.log("get_customer_acl_destination");
    $.ajax({
        url:"request.php?request=get_customer_acl_destination&customer_id="+customer_id,
        type:"POST",
        success:function(resp){
            if(resp!=0){
                var res = $.parseJSON(resp);
                console.log("search_acl");
                console.log(res);
                show_customer_acl(res.acl_destinations);
            }else if(resp==0){
                notify_msg("No acl exists.");
            }
        }
    });
}

function old_show_customer_acl(data){
    var cur_tunnel = null;
    var tunnel="";
    //var html="";
    var xyz=0;
    var source="";
    var destination="";
    var source_count=0;
    var destination_count=0;
    var html="";
    var new_class="";
    $(".customer-search-acl-result-block").html("");
    $.each(data, function(key, value){
        var id = key;
        xyz++;
        tunnel = value.tunnel_id;
        cur_tunnel=tunnel;

        if(cur_tunnel == parseInt(value.destination.specific_tunnel.value)) {

            html += '<div class="acl_upper_div">';
            html += '<div class="acl_div_' + id + ' acl_div" style="display:block;">';

            html += '<div class="soumya_btn_search">';
            html+='<div class="acl_name_div">';
            html+='<input type="text" name="acl_name" class="acl_name" data-tid="'+value.tunnel_id+'" data-id="'+id+'" size="20" placeholder="Name of ACL" data-value="'+value.acl_name+'" value="'+value.acl_name+'" disabled>';
            html+='</div>';
            html+='<div class="acl_description_div">';
            html+='<input type="text" name="acl_description" class="acl_description" data-tid="'+value.tunnel_id+'" data-id="'+id+'" size="50" placeholder="Description of ACL" data-value="'+value.acl_description+'" value="'+value.acl_description+'" disabled>';
            html+='</div>';
            html+='<div class="not_subscribed"></div>';
            html+='<div class="clearfix"></div>';
            html += '</div>';

            html += '<div class="soumya">';
            html += '<div class="box-holder">';
            html += '<span>then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-id="' + id + '" data-type="d_final" data-toggle="confirmation">' + show_updated_value(value.d_final, "d_final", id, tunnel) + '</div>';
            html += '<label>Final Dst</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            if (cur_tunnel != parseInt(value.destination.specific_tunnel.value)) {
                html += '<div class="box box-con box_' + id + ' destination_' + id + '  ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="destination" data-id="' + id + '" style="border:3px solid #000">' + show_updated_value(value.destination, "destination", id, tunnel, cur_tunnel) + '</div>';
            } else {
                destination_count++;
                html += '<div class="box box-con box_' + id + ' destination_' + id + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="destination" data-id="' + id + '" style="border:3px solid #000;background-color:#00A998">&nbsp;</div>';
            }
            html += '<label>Destination</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left green"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 34px;">';
            html += '<span>then</span>';
            html += '<div  style="min-width: 34px;" class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_forwarding" data-id="' + id + '">' + show_updated_value(value.c_forwarding, "c_forwarding", id, tunnel) + '</div>';
            html += '<label style="margin-left: -17px;">Forwarding</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 34px;">';
            html += '<span>then</span>';
            html += '<div  style="min-width: 34px;" class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_qos" data-id="' + id + '">' + show_updated_value(value.c_qos, "c_qos", id, tunnel) + '</div>';

            html += '<span>QOS</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_routing" data-id="' + id + '">' + show_updated_value(value.c_routing, "c_routing", id, tunnel) + '</div>';

            html += '<span>Routing</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_firewall" data-id="' + id + '">' + show_updated_value(value.c_firewall, "c_firewall", id, tunnel) + '</div>';

            html += '<span>Firewall</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left green"></i>';
            html += '</div>';

            html += '<div class="box-holder"  style="width: 34px;">';
            html += '<span>Aliasing</span>';
            html += '<div  style="min-width: 34px;" class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_aliasing"  data-id="' + id + '">' + show_updated_value(value.s_aliasing, "s_aliasing", id, tunnel) + '</div>';
            html += '<span>&nbsp;</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            if (cur_tunnel != parseInt(value.source.specific_tunnel.value)) {
                html += '<div class="box box-con box_' + id + ' source_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="source" data-id="' + id + '" style="border:3px solid #000">' + show_updated_value(value.source, "source", id, tunnel, cur_tunnel) + '</div>';
            } else {
                source_count++;
                html += '<div class="box box-con box_' + id + ' source_' + id + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="source" data-id="' + id + '" style="border:3px solid #000; background-color:#00A998">&nbsp;</div>';
            }
            html += '<span>Source</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<div style="min-width: 34px;" class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_qos" data-id="' + id + '">' + show_updated_value(value.s_qos, "s_qos", id, tunnel);

            //html+='<i class="fa  fa-close lg_close"></i>';
            html += '</div>';
            html += '<span>QOS</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_firewall" data-id="' + id + '">' + show_updated_value(value.s_firewall, "s_firewall", id, tunnel) + '</div>';

            html += '<span>Firewall</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_tos" data-id="' + id + '">' + show_updated_value(value.s_tos, "s_tos", id, tunnel) + '</div>';

            html += '<span>binding/TOS</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';
            html += '<div class="box-install">';

            html += '</div>';
            html += '<div class="clearfix">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }
    });
    $(".customer-search-acl-result-block").prepend(html);
}

function show_customer_acl(data){
    var cur_tunnel = null;
    var tunnel="";
    //var html="";
    var xyz=0;
    var source="";
    var destination="";
    var source_count=0;
    var destination_count=0;
    var html="";
    var new_class="";
    var box_status = "disabled";

    $(".customer-search-acl-result-block").html("");
    $.each(data, function(key, value) {
        var id = key;
        xyz++;
        tunnel = value.tunnel_id;
        cur_tunnel = tunnel;

        if (cur_tunnel == parseInt(value.destination.specific_tunnel.value)){
            html += '<div class="acl_upper_div">';
            html += '<div class="acl_div_' + id + ' acl_div" style="display:block;">';
            html += '<div class="soumya_btn_search">';
            html += '<div class="acl_name_div1">';
            html += '<input type="text" name="acl_name" class="acl_name" data-tid="' + value.tunnel_id + '" data-id="' + id + '" size="10" placeholder="Name of ACL" data-value="' + value.acl_name + '" value="' + value.acl_name + '" disabled>';
            html += '</div>';
            html += '<div class="acl_description_div1">';
            html += '<input type="text" name="acl_description" class="acl_description" data-tid="' + value.tunnel_id + '" data-id="' + id + '" size="33" placeholder="Description of ACL" data-value="' + value.acl_description + '" value="' + value.acl_description + '" disabled>';
            html += '</div>';

            html += '<div>';



            html += '<div class="soumya">';
            html += '<div class="box-holder">';
            html += '<span>then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-id="' + id + '" data-type="d_final" data-toggle="confirmation">' + show_updated_value(value.d_final, "d_final", id, tunnel, cur_tunnel, box_status) + '</div>';
            html += '<label>Final Dst</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 41px;">';
            html += '<div class="box box-con box_' + id + ' destination_' + id + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="destination" data-id="' + id + '" style="border:3px solid #000;background-color:#00A998">' + show_updated_value(value.destination, "destination", id, tunnel, cur_tunnel, box_status) + '</div>';
            html += '<label style="margin-left: -12px;">Destination</label>';

            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left green"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 34px;">';
            html += '<span style="margin-left: 3px;">then</span>';
            html += '<div  style="min-width: 36px;" class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_forwarding" data-id="' + id + '">' + show_updated_value(value.c_forwarding, "c_forwarding", id, tunnel, cur_tunnel,box_status) + '</div>';
            html += '<label style="margin-left: 2px;">FWD</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 25px;">';
            html += '<span>then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_qos" data-id="' + id + '">' + show_updated_value(value.c_qos, "c_qos", id, tunnel, cur_tunnel, box_status) + '</div>';
            html += '<label>QOS</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width:25px;">';
            html += '<span style="margin-left:-3px;">then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_routing" data-id="' + id + '">' + show_updated_value(value.c_routing, "c_routing", id, tunnel, cur_tunnel,box_status) + '</div>';
            html += '<label style="margin-left: -13px;">Routing</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';


            html += '<div class="box-holder">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_firewall" data-id="' + id + '">' + show_updated_value(value.c_firewall, "c_firewall", id, tunnel, cur_tunnel, box_status) + '</div>';

            html += '<span>Firewall</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left green"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 25px;">';
            html += '<span>&nbsp;</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_aliasing" data-id="' + id + '">' + show_updated_value(value.s_aliasing, "s_aliasing", id, tunnel, cur_tunnel, box_status) + '</div>';
            html += '<label style="margin-left: -12px;">Aliasing</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            if (cur_tunnel == parseInt(value.destination.specific_tunnel.value)) { //if(cur_tunnel != parseInt(value.source.specific_tunnel.value)){
                html += '<div class="box box-con box_' + id + ' source_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="source" data-id="' + id + '" style="border:3px solid #000">' + show_updated_value(value.source, "source", id, tunnel, cur_tunnel, box_status) + '</div>';
            } else {
                source_count++;
                html += '<div class="box box-con box_' + id + ' source_' + id + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="source" data-id="' + id + '" style="border:3px solid #000; background-color:#00A998">&nbsp;</div>';
            }
            html += '<span>Source</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 25px;">';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_qos" data-id="' + id + '">' + show_updated_value(value.s_qos, "s_qos", id, tunnel, cur_tunnel, box_status);

            //html+='<i class="fa  fa-close lg_close"></i>';
            html += '</div>';
            html += '<span>QOS</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_firewall" data-id="' + id + '">' + show_updated_value(value.s_firewall, "s_firewall", id, tunnel, cur_tunnel, box_status) + '</div>';

            html += '<span>Firewall</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 53px;">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_tos" data-id="' + id + '">' + show_updated_value(value.s_tos, "s_tos", id, tunnel, cur_tunnel, box_status) + '</div>';

            html += '<span style="margin-left: -12px;">Binding/TOS</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            if (cur_tunnel == parseInt(value.source.specific_tunnel.value)) {
                $(".source_acl_content_" + value.tunnel_id).prepend(html);
            } else if (cur_tunnel == parseInt(value.destination.specific_tunnel.value)) {
                $(".destination_acl_content_" + value.tunnel_id).prepend(html);
            } else {
                $(".source_acl_content_" + cur_tunnel).prepend(html);
            }

            //alert("source=="+source_count+"   destination count"+destination_count);
            if (source_count == 0) {
                $(".source_no_data_p_" + cur_tunnel).html("  (Source ACL not found)");
            } else {
                $(".source_no_data_p_" + cur_tunnel).html("");
            }
            if (destination_count == 0) {
                $(".destination_no_data_p_" + cur_tunnel).html("  (Destination ACL not found)");
            } else {
                $(".destination_no_data_p_" + cur_tunnel).html("");
            }

            if (destination_count == 0 && source_count == 0) {
                $(".source_" + id).attr("style", "border:3px solid #000; background-color:#00A998");
                $(".source_" + id).html("&nbsp;");
            }

        }
    });
    $(".customer-search-acl-result-block").prepend(html);
}