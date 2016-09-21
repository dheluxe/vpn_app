$(document).ready(function(){
    load_customer_acls(customer_id);
});

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
            /*if(jQuery.inArray(parseInt(id), cur_tunnel_acls)==-1){
             html += '<input type="button" class="btn btn-primary install_acl" data-acl="' + id + '" data-tunnel="' + tunnel_id + '" value="Install"/>';
             }else{
             html += '<input type="button" class="btn btn-primary installed_acl" disabled data-acl="' + id + '" data-tunnel="' + tunnel_id + '" value="Installed"/>';
             }*/
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
