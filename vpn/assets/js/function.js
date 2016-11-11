
//gateway change php section
function gateway(stat, tunnel_id, user_id, ttype){
    var html="";
    if(ttype != "client"){
        /*  if($stat >= 0 && $stat < 2) {
         $html .= '<input type="hidden" class="edit_gateway_s" name="gateway" value=' . $stat . '>';
         $html .= '<div class="gateway_stat_' . $stat . ' gateway tunnel_gate_' . $tunnel_id . '"  data-pos="0" type="data" data-toggle="tooltip" title="No" data-cast="' . $_SESSION['user_id'] . '" data-val="' . $stat .  '" data-id="' . $tunnel_id . '"><i class="fa fa-times"></i></div>';
         }*/
        if(stat==0){
            html +='<input type="hidden" class="edit_gateway_s" name="gateway" value=0>';
            html +='<div class="gateway tunnel_gate_' + tunnel_id + '"  data-pos="0" type="data" data-toggle="tooltip" title="No" data-cast="' + user_id + '" data-val="0" data-id="' + tunnel_id +'"><i class="fa fa-times" style="color:#DA3838"></i></div>';
            /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
             $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
        }else if(stat==1){
            html +='<input type="hidden" class="edit_gateway_s" name="gateway" value=1>';
            html +='<div class="gateway tunnel_gate_' + tunnel_id + '"  data-pos="0" type="data" data-toggle="tooltip" title="Yes" data-cast="' + user_id + '" data-val="1" data-id="' + tunnel_id + '"><i class="fa fa-check" style="color:#1D9E74"></i></div>';
            /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
             $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
        }
    }
    /*
     if(stat==0){
     html+='<input type="hidden" class="edit_gateway_s" name="gateway" value=0>';
     html+='<div class="gateway tunnel_gate_'+tunnel_id+'" type="data" data-toggle="tooltip" title="No" data-cast="'+user_id+'" data-val="0" data-id="'+tunnel_id+'"><i class="fa fa-times" style="color:#DA3838"></i></div>';

     }else if(stat==1){
     html+='<input type="hidden" class="edit_gateway_s" name="gateway" value=1>';
     html+='<div class="gateway tunnel_gate_'+tunnel_id+'" type="data" data-toggle="tooltip" title="Yes" data-cast="'+user_id+'" data-val="1" data-id="'+tunnel_id+'"><i class="fa fa-check" style="color:#1D9E74"></i></div>';

     }*/
    return html;
}

//bidirection change php section
function biderection (stat, tunnel_id, user_id){
    var html="";
    if(stat==0){
        html+='<input type="hidden" id="edit_biderection_s" name="biderection" value=0>';
        html+='<div class="biderection tunnel_bi_'+tunnel_id+'" type="data" data-toggle="tooltip" title="Mode 1" data-ctrl="0" data-cast="'+user_id+'" data-val="0" data-id="'+tunnel_id+'" data-url="change_biderection">';
        html+='<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i></div>';
        /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
         $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
    }
    if(stat==1){
        html+='<input type="hidden" id="edit_biderection_s" name="biderection" value=1>';
        html+='<div class="biderection tunnel_bi_'+tunnel_id+'" type="data" data-toggle="tooltip" title="Mode 2" data-ctrl="0" data-cast="'+user_id+'" data-val="1" data-id="'+tunnel_id+'" data-url="change_biderection">';
        html+='<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i></div>';
        /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
         $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
    }
    if(stat==2){
        html+='<input type="hidden" id="edit_biderection_s" name="biderection" value=2>';
        html+='<div class="biderection tunnel_bi_'+tunnel_id+'" type="data" data-toggle="tooltip" title="Mode 3" data-ctrl="0" data-cast="'+user_id+'" data-val="2" data-id="'+tunnel_id+'" data-url="change_biderection">';
        html+='<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right"></i></div>';
        /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
         $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
    }
    if(stat==3){
        html+='<input type="hidden" id="edit_biderection_s" name="biderection" value=3>';
        html+='<div class="biderection tunnel_bi_'+tunnel_id+'" type="data" data-toggle="tooltip" title="Mode 4" data-ctrl="0" data-cast="'+user_id+'" data-val="2" data-id="'+tunnel_id+'" data-url="change_biderection">';
        html+='<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i></div>';
        /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
         $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
    }
    return html;
}
function gstatus(stat, tunnel_id, usr_id){
    $html="";
    /*  if($stat >= 0 && $stat < 2) {
     $html .= '<input type="hidden" class="edit_status_s" name="status" value=' . $stat . '>';
     $html .= '<div class="status st_ch_status_' . $stat . ' tunnel_stat_' . $tunnel_id . '" type="data" data-toggle="tooltip" title="Active" data-cast="' . $_SESSION['user_id'] . '" data-val="' . $stat . '" data-id="' . $tunnel_id . '"><i class="fa fa-fw fa-circle"></i></div>';
     }*/
    if(stat==1){
        $html+='<input type="hidden" class="edit_status_s" name="status" value=1>';
        $html+='<div class="status tunnel_stat_' + tunnel_id + '" type="data" data-toggle="tooltip" title="Active" data-cast="' + usr_id + '" data-val="1" data-id="' + tunnel_id + '"><i class="fa fa-fw fa-circle" style="color:#1D9E74"></i></div>';

    }else if(stat==0){
        $html+='<input type="hidden" class="edit_status_s" name="status" value=0>';
        $html+='<div class="status tunnel_stat_' + tunnel_id + '" type="data" data-toggle="tooltip" title="Inactive" data-cast="' + usr_id + '" data-val="0" data-id="' + tunnel_id + '"><i class="fa fa-fw fa-circle"  style="color:#DA3838"></i></div>';
    }else{
        $html+='<input type="hidden" class="edit_status_s" name="status" value=0>';
        $html+='<div class="status tunnel_stat_' + tunnel_id + '" type="data" data-toggle="tooltip" title="Inactive" data-cast="' + usr_id + '" data-val="0" data-id="' + tunnel_id + '"><i class="fa fa-fw fa-circle"  style="color:#DA3838"></i></div>';
    }
    return $html;
}
function tunnels(data, is_shared=false){
    console.log('tunnel_data');
    console.log(data);
    // return "";
    var group_arr = ['<span style="color: #ea4335;"><strong>A</strong></span>', '<span style="color: #839D1C;"><strong>B</strong></span>', '<span style="color: #00A998;"><strong>C</strong></span>', '<span style="color: #F6AE00;"><strong>D</strong></span>', '<span style="color: #4285F4;"><strong>E</strong></span>', '<span style="color: #330033;"><strong>F</strong></span>', '<span style="color: #FF404E;"><strong>G</strong></span>', '<span style="color: #FFFF00;"><strong>H</strong></span>', '<span style="color: #FF3300;"><strong>I</strong></span>', '<span style="color: #CC6600;"><strong>J</strong></span>', '<span style="color: #9999CC;"><strong>K</strong></span>', '<span style="color: #0000CC;"><strong>L</strong></span>', '<span style="color: #FF0000;"><strong>M</strong></span>', '<span style="color: #003366;"><strong>N</strong></span>', '<span style="color: #003333;"><strong>0</strong></span>', '<span style="color: #FF00CC;"><strong>P</strong></span>', '<span style="color: #FF0066;"><strong>Q</strong></span>', '<span style="color: #CC0000;"><strong>R</strong></span>', '<span style="color: #CC6600;"><strong>S</strong></span>', '<span style="color: #666666;"><strong>T</strong></span>', '<span style="color: #330066;"><strong>U</strong></span>', '<span style="color: #CC99CC;"><strong>V</strong></span>', '<span style="color: #FFCC66;"><strong>W</strong></span>', '<span style="color: #FF3399;"><strong>X</strong></span>', '<span style="color: #99CCFF;"><strong>Y</strong></span>', '<span style="color: #0099FF;"><strong>Z</strong></span>'];
    var btn_arr=['#393333', '#1D9E74'];
    //var route_arr=['<i class="fa fa-road" style="color:#393333"></i>', '<i class="fa fa-road" style="color:#1D9E74"></i>'];
    var html='';
    var x="tunnel";
    if(typeof data[x] === 'undefined') {
        x=0;
    }

    html +='<div class="p_div">';
    html +='<div id="p_div_' + data[x].tunnel_id + '">';

    var dev_class = 'dev-disconnect'; //GET DEV STATUS HERE TODO!!!
    var dev_message = 'Disconnected';
    var icon='<i class="fa fa-share-square-o" aria-hidden="true"></i>';
    if(data[x].dev_status == 1){
        dev_class = 'dev-connected';
        dev_message = data[x].DeV;
        icon='<i class="fa fa-times" aria-hidden="true"></i>';
    }
    else if(data[x].dev_status == 0){
        dev_class = 'dev-connecting';
        dev_message = 'Initiating';
        icon='<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>';
    }
    else if(data[x].dev_status == -1){
        dev_class = 'dev-disconnected';
        dev_message = 'Disconnected';
        icon='<i class="fa fa-share-square-o" aria-hidden="true"></i>';
    }

    html += '<a class="btn holbol dev_status '+dev_class+'" data-tid="'+data[x].tunnel_id+'" data-val="-1" data-toggle="tooltip" data-placement="bottom" style="margin-left: 0px; background-color:transparent; color: black; width:24px!important; margin-right:0px!important; margin-left: 0px!important;">'+icon+'</a>';

    html += '<a class="holbol dev-status-label dev-status-label_'+data[x].tunnel_id+'" data-id="'+data[x].tunnel_id+'" data-val="-1" data-toggle="tooltip" data-placement="bottom" style="text-align: center; margin-left: 0px; background-color:transparent; color: black; border-left:none;width: 150px!important;">'+dev_message+'</a>';

    //if($data['plan_id']!=1){
    html += '<a class="btn holbol acc_type cursor acc_type_' + data[x].tunnel_id + '" data-id="' + data[x].tunnel_id + '" data-val="' + data[x].plan_id + '" data-toggle="tooltip" data-placement="bottom" ' + ((data[x].plan_id != 1 && data[x].plan_id != undefined) ? "style='margin-left: 0px; background-color:transparent;  color: black; opacity:0.25'" : "style='margin-left: 0px; background-color:#b9c3c8;'") + '>Premium</a>';

    //}
    //if($data['route']==1){
    html +='<a data-val="' + data[x].route + '" class="btn holbol route_change cursor tunnel_route_' + data[x].tunnel_id + '" type="data" data-pos="0" data-id="' + data[x].tunnel_id + '" ' + ( data[x].route == 1 ? "style='background-color:#b9c3c8'":"style='background-color:transparent;  color: black; opacity:0.25'") + '>Route</a>';
    //}

    //if($data['internet']==1){
    html +='<a data-val="' +  data[x].internet + '" class="btn holbol internet_change cursor tunnel_internet_' + data[x].tunnel_id + '" type="data" data-pos="0" data-id="' + data[x].tunnel_id + '" ' + (data[x].internet == 1?"style='background-color:#b9c3c8'":"style='background-color:transparent; color: black; opacity:0.25'") + '>Internet</a>';
    //}
    var opacity = '';
    if(is_shared == false){
        opacity = 'opacity:0.25; color: black; background-color: transparent;';
    }
    html +='<a data-val="" class="btn holbol sponsore sponsored_' + data[x].tunnel_id + '" type="data" data-pos="0" data-tid="' + data[x].tunnel_id + '"  data-cloud="' + data[x].cloud_id + '" data-u="' + data[x].customer_id + '" style="background-color:#1D9E74;' + opacity + '">Sponsored</a>';

    html +='<a class="btn holbol change_tunnel change_tunnel_' + data[x].tunnel_id + '" data-id="' + data[x].tunnel_id + '" data-type="' + (data[x].tunnel_type != "client"?"server":"client") + '" href="javascript:void(0)" ' + (data[x].tunnel_type != "client"?"style='background-color:#b9c3c8'":"style='background-color:transparent;  color: black; opacity:0.25'") + '>Server';

    html +='</a>';

    html +='</div>';

    if(data[x].tunnel_type=="client"){
        if(data[x].status != 0){
            html +='<div class="list_body bg_yellow tunnel_body tunnel_body_' + data[x].tunnel_id + '">';
        }else if(data[x].status == 0){
            html +='<div class="list_body bg_yellow tunnel_body tunnel_body_' + data[x].tunnel_id + '" style="background-color:#cecece">';
        }
    }else{
        if(data[x].status != 0){
            html +='<div class="list_body bg_green tunnel_body tunnel_body_' + data[x].tunnel_id + '">';
        }else if(data[x].status == 0){
            html +='<div class="list_body bg_green tunnel_body tunnel_body_' + data[x].tunnel_id + '" style="background-color:#cecece">';
        }
    }

    html +='<div class="meta">';
    html +='<a href="javascript:void(0)" class="showACL" data-toggle="tooltip" data-placement="right" title="ACL view" data-cloud="' + data[x].cloud_id + '" data-type="' + data[x].tunnel_type + '" data-id="' + data[x].tunnel_id + '"><i class="fa fa-eye"></i></a>';
    html +='</div>';

    html +='<div class="meta">';
    html +='<a href="javascript:void(0)" class="btn_add_acl btn_add_acl_' + data[x].tunnel_id + '" data-toggle="tooltip" data-placement="right" title="Create ACL" data-id="' + data[x].tunnel_id + '"><i class="fa fa-fw fa-wrench"></i></a>';
    html +='</div>';

    //html +='<div class="meta" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"Downgrade to client":"Upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_' + data[x].tunnel_id + '" data-type="'.$data['tunnel_type'].'" data-id="' + data[x].tunnel_id + '">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

    html +='<div class="meta" data-toggle="tooltip" title="Add clone"><a href="javascript:void(0)" class="add_clone" data-type="' + data[x].tunnel_type + '" data-id="' + data[x].tunnel_id + '"><i class="fa fa-fw fa-plus"></i></a></div>';

    html +='<div class="meta" data-toggle="tooltip" title="Save this"><a href="javascript:void(0)" class="save_this_client" data-type="' + data[x].tunnel_type + '" data-id="' + data[x].tunnel_id + '"><i class="fa fa-floppy-o"></i></a></div>';

    html +='<div class="meta cursor tunnel_chk tunnel_' + data[x].tunnel_id + ' tunnel_grp_chk_' + data[x].group_id + '" data-val="0" data-id="' + data[x].tunnel_id + '" data-toggle="tooltip" title="Select tunnel"><i class="fa fa-fw fa-square-o"></i></div>';

    html +='<div class="meta cursor tunnel_grp" data-toggle="tooltip" data-gid="' + data[x].group_id + '" title="' + data[x].group_id + '"><div class="group tunnel_grp_' + data[x].tunnel_id + '" type="data" data-cast="' + data[x].customer_id +'" data-val="' + data[x].group_id + '" data-id="' + data[x].tunnel_id + '" data-pos="0">'+((data[x].group_id in group_arr)?group_arr[data[x].group_id]:"")+'</div></div>';
    //   html+='<div class="meta cursor tunnel_grp" data-toggle="tooltip" data-gid="'+data[x].group_id+'" title="'+data[x].group_id+'"><div class="group tunnel_grp_'+data[x].tunnel_id+'" type="data" data-cast="'+data[x].customer_id+'" data-val="'+data[x].group_id+'" data-id="'+data[x].tunnel_id+'">'+((data[x].group_id in group_arr)?group_arr[data[x].group_id]:"")+'</div></div>';

    //html +='<div class="meta width-140 tunnel_email_' + data[x].tunnel_id + '" data-toggle="tooltip" data-placement="bottom" title="'.$data['email'].'">'.$data['email'].'</div>';
    html +='<div class="meta width-120 tunnel_display_' + data[x].tunnel_id + '" data-toggle="tooltip" data-placement="bottom" title="' + data[x].display_name + '"><a href="javascript:void(0);" class="display display_' + data[x].tunnel_id + ' tunnel_editable" data-type="text" data-pk="' + data[x].tunnel_id + '" data-title="Enter display name">' + (data[x].display_name!=""?data[x].display_name:"Tunnel " + data[x].tunnel_id) + '</a></div>';

    html +='<div class="meta cursor">' + biderection(data[x].bidirectional_mode, data[x].tunnel_id, data[x].customer_id) + '</div>';
    html +='<div class="meta width-80 tunnel_location_' + data[x].tunnel_id + '" data-toggle="tooltip" title=""><a href="javascript:void(0);" class="change_location location_' + data[x].tunnel_id + ' tunnel_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="' + data[x].tunnel_id + '">' + ((data[x].location!=null && data[x].location!="")?data[x].location:"Select Location") + '</a></div>';

    if(data[x].tunnel_type=="client"){
        html +='<div class="meta width-80 subnet_' + data[x].tunnel_id + '" data-toggle="tooltip" title="' + data[x].cloud_ip + '">Auto</div>';
    }else{
        html +='<div class="meta width-80 subnet_' + data[x].tunnel_id + '" data-toggle="tooltip" title="' + data[x].cloud_ip + '">' + data[x].cloud_ip + '</div>';
    }
    /*$tunnel_cost = packages($data['tunnel_type'], $data['plan_id'], $data['tunnel_id']);
     html +='<div class="meta plan_cost_' + data[x].tunnel_id + '" data-toggle="tooltip" title="Tunnel points '.$tunnel_cost*cash_to_point().'">'.$tunnel_cost*cash_to_point().'</div>';*/
    html+='<div class="meta plan_cost_'+data[x].tunnel_id+' width-60" data-toggle="tooltip" title="Tunnel points ' + +data[x].cost * 10 + '">' + data[x].cost * 10 + '</div>';
    html +='<span class="not_client_' + data[x].tunnel_id + '">';
    if(data[x].tunnel_type!="client"){
        html +='<div class="meta width-140" data-toggle="tooltip" title="' + ((data[x].real_ip!=null && data[x].real_ip!="" && data[x].real_ip!= "Request real ip")?data[x].real_ip:"Not assigned") + '"><a href="javascript:void(0);" class="real_ip real_ip_' + data[x].tunnel_id + '" style="' + (data[x].active==0?"color:#1B1E24":"color:#1D9E74") + '" data-val="' + ((data[x].active!=null && data[x].active!="")?data[x].active:-1) + '" data-id="' + data[x].tunnel_id + '">' + ((data[x].real_ip!=null && data[x].real_ip!="" && data[x].real_ip!= "Request real ip")?data[x].real_ip:"Not assigned") + '</a></div>';
        html+='<div class="meta cursor width-60">'+gateway(data[x].gateway_mode, data[x].tunnel_id, data[x].customer_id, data[x].tunnel_type)+'</div>';
    } else {
        html +='<div class="meta width-140" data-toggle="tooltip" data-placement="right" title="' + (data[x].tunnel_type!="client"?"":"To activate this field upgrade to server") + '"><a href="javascript:void(0)" class="change_tunnel change_tunnel_' + data[x].tunnel_id + '" data-type="' + data[x].tunnel_type + '" data-id="' + data[x].tunnel_id + '">' + (data[x].tunnel_type!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>") + '</a></div>';

        html +='<div class="meta width-60" data-toggle="tooltip" data-placement="right" title="' + (data[x].tunnel_type != "client"?"":"To activate this field upgrade to server") + '"><a href="javascript:void(0)" class="change_tunnel change_tunnel_' + data[x].tunnel_id + '" data-type="' + data[x].tunnel_type + '" data-id="' + data[x].tunnel_id + '">' + (data[x].tunnel_type!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>") + '</a></div>';
    }

    html+='<div class="tunnel_searchable_switch_block">';
    html+='<input id="cmn-toggle-tunnel-' + data[x].tunnel_id + '" class="cmn-toggle cmn-toggle-round tunnel_searchable_switch" data-tunnel_id="' + data[x].tunnel_id + '" type="checkbox" '+( data[x].is_searchable!=0?"checked":"")+'>';
    html+='<label for="cmn-toggle-tunnel-' + data[x].tunnel_id + '"></label>';
    html+='</div>';

    html +='<div class="meta cursor float-right">' + gstatus(data[x].status, data[x].tunnel_id, data[x].customer_id) + '</div>';
    html +='</span><div class="meta float-right" data-toggle="tooltip" title="Delete this tunnel" ><a href="javascript:void(0);" data-id="' + data[x].tunnel_id + '" class="delete_tunnel delete_tunnel_' + data[x].tunnel_id + '" data-type="' + data[x].tunnel_type + '"><i class="fa fa-fw fa-trash" style="color:#DA3838"></i></a></div>';
    html +='</div>';
    html +='</div>';
    html +='<div class="tunnel_acl_div_' + data[x].tunnel_id + ' tunnel_acl_div" data-id="'+data[x].tunnel_id+'" style="display:none;">';
    html +='<label style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 20px;">Source base<span class="source_no_data_p_' + data[x].tunnel_id + '" style="color: #ea4335; font-size: 15px;"></span>&nbsp;&nbsp;<input type="button" class="btn btn-xs btn-primary acl_destination_search_btn" value="Search ACL" data-tid="' + data[x].tunnel_id + '" style="margin-bottom: 3px;"/></label>';
    html +='<div class="source_acl_content_' + data[x].tunnel_id + '"></div>';
    html +='<label  style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 20px;margin-top: 5px;">Destination base<span class="destination_no_data_p_' + data[x].tunnel_id + '" style="color: #ea4335; font-size: 15px;"></span></label>';
    var deststate = '';
    if(data[x].tunnel_type == 'client')
    {
        deststate = 'disabled';
    }
    html +='<div class="destination_acl_content destination_acl_content_' + data[x].tunnel_id + ' ' + deststate + '"></div>';
    html +='</div>';

    /*
     // if(data[x].tunnel_type=="client"){
     html+='<div class="list_body tunnel_body tunnel_body_'+data[x].tunnel_id+'">';
     // }else{
     //html+='<div class="list_body bg_green tunnel_body tunnel_body_'+data[x].tunnel_id+'">';
     // }
     html+='<div class="meta">';
     html+='<a href="javascript:void(0)" class="showACL" data-toggle="tooltip" data-placement="right" title="ACL view" data-cloud="'+data[x].cloud_id+'" data-type="'+data[x].tunnel_type+'" data-id="'+data[x].tunnel_id+'"><i class="fa fa-wrench"></i></a>';
     html+='<a href="javascript:void(0)" class="btn_add_acl btn_add_acl_'+data[x].tunnel_id+'" data-toggle="tooltip" data-placement="right" title="Create ACL" data-id="'+data[x].tunnel_id+'"><i class="fa fa-fw fa-plus" style="margin-left: 5px; font-size: 10px;"></i></a>';
     html+='</div>';

     html+='<div class="meta" data-toggle="tooltip" data-placement="right" title="'+(data[x].tunnel_type!="client"?"Downgrade to client":"Upgrade to server")+'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'+data[x].tunnel_id+'" data-type="'+data[x].tunnel_type+'" data-id="'+data[x].tunnel_id+'">'+(data[x].tunnel_type!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>")+'</a></div>';

     html+='<div class="meta" data-toggle="tooltip" title="Add clone"><a href="javascript:void(0)" class="add_clone" data-type="'+data[x].tunnel_type+'" data-id="'+data[x].tunnel_id+'"><i class="fa fa-fw fa-plus"></i></a></div>';
     html+='<div class="meta" data-toggle="tooltip" title="Save this"><a href="javascript:void(0)" class="save_this_client" data-type="'+data[x].tunnel_type+'" data-id="'+data[x].tunnel_id+'"><i class="fa fa-floppy-o"></i></a></div>';
     html+='<div class="meta cursor tunnel_chk tunnel_'+data[x].tunnel_id+' tunnel_grp_chk_'+data[x].group_id+'" data-val="0" data-id="'+data[x].tunnel_id+'" data-toggle="tooltip" title="Select tunnel"><i class="fa fa-fw fa-square-o"></i></div>';
     html+='<div class="meta cursor tunnel_grp" data-toggle="tooltip" data-gid="'+data[x].group_id+'" title="'+data[x].group_id+'"><div class="group tunnel_grp_'+data[x].tunnel_id+'" type="data" data-cast="'+data[x].customer_id+'" data-val="'+data[x].group_id+'" data-id="'+data[x].tunnel_id+'">'+((data[x].group_id in group_arr)?group_arr[data[x].group_id]:"")+'</div></div>';

     //html+='<div class="meta width-140 tunnel_email_'+data[x].tunnel_id+'" data-toggle="tooltip" data-placement="bottom" title="'+data[x].email+'">'+data[x].email+'</div>';

     html+='<div class="meta width-140 tunnel_display_'+data[x].tunnel_id+'" data-toggle="tooltip" data-placement="bottom" title="'+data[x].display_name+'"><a href="#" class="display display_'+data[x].tunnel_id+' tunnel_editable" data-type="text" data-pk="'+data[x].tunnel_id+'" data-title="Enter display name">'+data[x].display_name+'</a></div>';
     //html+='<div class="meta cursor"></div>';
     html+='<div class="meta cursor">'+biderection(data[x].bidirectional_mode, data[x].tunnel_id, data[x].customer_id)+'</div>';
     html+='<div class="meta width-80 tunnel_location_'+data[x].tunnel_id+'" data-toggle="tooltip" title=""><a href"javascript:void(0);" class="change_location location_'+data[x].tunnel_id+' tunnel_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="'+data[x].tunnel_id+'">Select Location</a></div>';
     //new
     html+='<div class="meta width-77"><div class="" id="DeV_'+data[x].tunnel_id+'" data-toggle="tooltip" data-placement="top" title=""></div></div>';
     html+='<div class="meta width-77"><div class="acc_type acc_type_'+data[x].tunnel_id+'" data-id="'+data[x].tunnel_id+'" data-val="'+data[x].plan_id+'" data-toggle="tooltip" data-placement="bottom" title="'+(data[x].plan_id==1?"Premium":"Premium")+'">'+(data[x].plan_id==1?"Premium":"Premium")+'</div></div>';
     //!new
     html+='<div class="meta width-80 subnet_'+data[x].tunnel_id+'" data-toggle="tooltip" data-placement="bottom" title="'+data[x].cloud_ip+'">'+(data[x].cloud_ip!=undefined || data[x].cloud_ip!=""?data[x].cloud_ip:"&nbsp;")+'</div>';
     html+='<div class="meta plan_cost_'+data[x].tunnel_id+'" data-toggle="tooltip" title="">'+data[x].cost+'</div>';
     html+='<div class="meta" data-toggle="tooltip" title=""><div data-val="'+data[x].internet+'" class="internet_change cursor tunnel_internet_'+data[x].tunnel_id+'" type="data" data-pos="0" data-id="'+data[x].tunnel_id+'">'+(data[x].internet==1?"<i class='fa fa-fw fa-globe' style='color:#1D9E74'></i>":"<i class='fa fa-fw fa-globe' style='color:#393333'></i>")+'</div></div>';

     html+='<div class="meta" data-toggle="tooltip" title=""><div data-val="'+data[x].route+'" class="route_change cursor tunnel_route_'+data[x].tunnel_id+'" type="data" data-pos="0" data-id="'+data[x].tunnel_id+'">'+(data[x].route==1?"<i class='fa fa-fw fa-road' style='color:#1D9E74'></i>":"<i class='fa fa-fw fa-road' style='color:#393333'></i>")+'</div></div>';
     html+='<span class="not_client_'+data[x].tunnel_id+'">';
     if(data[x].tunnel_type!="client"){
     html+='<div class="meta width-100" data-toggle="tooltip" title="'+(data[x].real_ip!=null?data[x].real_ip:"Not assigned")+'"><a href="javascript:void(0);" data class="real_ip real_ip_'+data[x].tunnel_id+'" style="'+(data[x].active==0||data[x].active==""?"color:#1B1E24":"color:#1D9E74")+'" data-val="'+(data[x].active!=null?data[x].active:-1)+'" data-id="'+data[x].tunnel_id+'">'+(data[x].real_ip!=undefined || data[x].real_ip!=""?data[x].real_ip:"Not assigned")+'</a></div>';
     html+='<div class="meta cursor">'+gateway(data[x].gateway_mode, data[x].tunnel_id, data[x].customer_id)+'</div>';
     }
     html+='</span>';
     html+='<div class="meta" data-toggle="tooltip" title="Delete this tunnel" ><a href="javascript:void(0);" data-id="'+data[x].tunnel_id+'" class="delete_tunnel_'+data[x].tunnel_id+' delete_tunnel" data-type="'+data[x].tunnel_type+'"><i class="fa fa-fw fa-trash" style="color:#DA3838"></i></a></div>';
     html+='</div>';
     html += '<div class="tunnel_acl_div_'+data[x].tunnel_id+' tunnel_acl_div" style="display:none;"></div>';*/

    return html;
}

function packages(type, plan, id){
    $.ajax({
        url:"request.php?request=packages&type="+type+"&p_id="+plan+"&id="+id,
        success:function(resp){
            return resp;
        }
    });
}
var color_box_class="color-box";

function acl(data, res_type, cur_tunnel){
    console.log(data);
    $(".destination_acl_content_"+cur_tunnel).html("");
    $(".source_acl_content_"+cur_tunnel).html("");

    var tunnel="";
    //var html="";
    var xyz=0;
    var source="";
    var destination="";
    var source_count=0;
    var destination_count=0;
    $.each(data, function(key, value){
        var html="";
        var id = key;
        xyz++;
        tunnel = value.tunnel_id;
        new_class='';//" acl_installed ";

        if(value.is_installed==1){
            color_box_class="disabled_color_box";
        }else{
            color_box_class="color-box";
        }

        html+='<div class="acl_upper_div">';
        html += '<div class="acl_div_'+id+' acl_div" style="display:block;">';
        html+='<div class="soumya_btn">';
        html+='<a href="javascript:void(0);" class="font-awesome" btn-type="clone" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-toggle="tooltip" title="Create ACL clone"><i class="fa fa-fw fa-copy"></i></a>';
        html+='<a href="javascript:void(0);" class="font-awesome" btn-type="save" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-toggle="tooltip" title="Save"><i class="fa fa-fw fa-floppy-o"></i></a>';
        html+='<a href="javascript:void(0);" class="font-awesome" btn-type="clear" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-toggle="tooltip" title="Clear"><i class="fa fa-fw fa-cut"></i></a>';

        if(cur_tunnel == parseInt(value.destination.specific_tunnel.value)){
            html+='<a href="javascript:void(0);" class="" style="opacity:0.2;" btn-type="change" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-val="destination" data-toggle="tooltip" title="Change ACL base"><i class="fa fa-fw fa-arrow-right"></i></a>';
        }else if(cur_tunnel == parseInt(value.source.specific_tunnel.value)){
            if(value.default_acl_id!=id){
                html+='<a href="javascript:void(0);" class="font-awesome" btn-type="change" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-val="source" data-toggle="tooltip" title="Change ACL base"><i class="fa fa-fw fa-arrow-left"></i></a>';
            }else{
                html+='<a href="javascript:void(0);" style="opacity: 0.3;" btn-type="change" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-val="source" data-toggle="tooltip" title="Change ACL base"><i class="fa fa-fw fa-arrow-left"></i></a>';
            }
        }else{
            html+='<a href="javascript:void(0);" style="opacity: 0.25;" btn-type="change" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-val="source" data-toggle="tooltip" title="Change ACL base"><i class="fa fa-fw fa-arrow-left"></i></a>';
        }

        if(value.default_acl_id!=id){
            if(cur_tunnel != parseInt(value.destination.specific_tunnel.value)){
                html+='<a href="javascript:void(0);" class="font-awesome" btn-type="set_default_acl" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-toggle="tooltip" title="Set default ACL"><i class="fa fa-fw fa-home"></i></a>';
            }
            if(cur_tunnel == parseInt(value.destination.specific_tunnel.value)){
                var checked_val=(value.is_searchable==1 ? "checked":"");
                html+='<div class="acl_searchable_switch_block" >';
                html+='<input id="cmn-toggle-acl-'+id+'" class="cmn-toggle cmn-toggle-round acl_searchable_switch" data-acl_id="'+id+'" type="checkbox" '+checked_val+'>';
                html+='<label for="cmn-toggle-acl-'+id+'"></label>';
                html+='</div>';
            }

            html+='<a href="javascript:void(0);" class="font-awesome" btn-type="delete" data-tid="'+value.tunnel_id+'" data-id="'+id+'" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash-o red"></i></a>';
        }else{
            html+='<a href="javascript:void(0);" data-toggle="tooltip" title="Set default ACL" style="opacity: 0.3;"><i class="fa fa-fw fa-home"></i></a>';

            html+='<a href="javascript:void(0);" style="opacity: 0.3;" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash-o red"></i></a>';
        }

        html+='<div class="onoffswitch">';
        html+='<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch-'+value.tunnel_id+'-'+id+'" checked>';
        html+='<label class="onoffswitch-label" for="myonoffswitch-'+value.tunnel_id+'-'+id+'">';
        html+='<span class="onoffswitch-inner"></span>';
        html+='<span class="onoffswitch-switch"></span>';
        html+='</label>';
        html+='</div>';

        html+='<div class="acl_name_div">';
        html+='<input type="text" name="acl_name" class="acl_name acl_name_'+id+'" data-tid="'+value.tunnel_id+'" data-id="'+id+'" size="10" placeholder="Name of ACL" data-value="'+value.acl_name+'" value="'+value.acl_name+'" '+(value.is_installed==1?"disabled":"")+'>';
        html+='</div>';
        html+='<div class="acl_description_div">';
        html+='<input type="text" name="acl_description" class="acl_description acl_description_'+id+'" data-tid="'+value.tunnel_id+'" data-id="'+id+'" size="33" placeholder="Description of ACL" data-value="'+value.acl_description+'" value="'+value.acl_description+'" '+(value.is_installed==1?"disabled":"")+'>';
        html+='</div>';
        var soumya_class="";
        if(value.default_acl_id==id){
            html+='<div class="is_default">';
            html+="DEF";
            html+='</div>';
            soumya_class="default_acl ";
        }
        if(value.is_installed==1){
            html+='<div class="is_installed">';
            html+="INST";
            html+='</div>';
            soumya_class+="installed_acl ";
        }else{
            html+='<div class="not_installed">';
            html+='</div>';
        }
        if(value.is_subscribed==1){
            html+='<div class="is_subscribed">';
            html+="SUB";
            html+='</div>';
            soumya_class+="subscribed_acl";
        }else{
            html+='<div class="not_subscribed">';
            html+='</div>';
        }

        html+='</div>';
        html+='<div>';

        if(cur_tunnel == parseInt(value.source.specific_tunnel.value)){
            source = parseInt(value.source.specific_tunnel.value);
            // html+='<p>Source base</p>';
        } else if(cur_tunnel == parseInt(value.destination.specific_tunnel.value)){
            destination = parseInt(value.destination.specific_tunnel.value);
            // html+='<p>Destination base</p>';
        }
        html+='</div>';
        html+='<div class="clearfix"></div>';


        html+='<div class="soumya '+(value.status==0?"disabled_soumya":"")+' '+soumya_class+'">';
        html+='<div class="box-holder">';
        html+='<span>then</span>';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-id="'+id+'" data-type="d_final" data-toggle="confirmation">'+show_updated_value(value.d_final, "d_final", id, tunnel , cur_tunnel," ")+'</div>';
        html+='<label>Final Dst</label>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        if(cur_tunnel != parseInt(value.destination.specific_tunnel.value)){ //if this acl is source acl
            html+='<div class="box-holder">';
            html+='<span>If</span>';
            html+='<div class="box box-con box_'+id+' destination_'+id+'  '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="destination" data-id="'+id+'" style="border:3px solid #000">'+show_updated_value(value.destination, "destination", id, tunnel, cur_tunnel ," ")+'</div>';
            html+='<label>Destination</label>';
        } else { //if this acl is destination acl
            destination_count++;
            html+='<div class="box-holder" style="width: 41px;">';
            html+='<span style="margin-left: 3px;">If</span>';
            html+='<div class="box box-con box_'+id+' destination_'+id+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="destination" data-id="'+id+'" style="border:3px solid #000;background-color:#00A998">'+show_updated_value(value.destination, "destination", id, tunnel, cur_tunnel ," ")+'</div>';
            html+='<label style="margin-left: -12px;">Destination</label>';
        }

        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left green"></i>';
        html+='</div>';

        html+='<div class="box-holder" style="width: 34px;">';
        html+='<span style="margin-left: 3px;">then</span>';
        html+='<div style="min-width: 36px;" class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="c_forwarding" data-id="'+id+'">'+show_updated_value(value.c_forwarding, "c_forwarding", id, tunnel, cur_tunnel ," ")+'</div>';
        html+='<label style="margin-left: 2px;">FWD</label>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        html+='<div class="box-holder" style="width: 25px;">';
        html+='<span>then</span>';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="c_qos" data-id="'+id+'">'+show_updated_value(value.c_qos, "c_qos", id, tunnel, cur_tunnel ," ")+'</div>';
        html+='<label>QOS</label>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        html+='<div class="box-holder" style="width:25px;">';
        html+='<span style="margin-left:-3px;">then</span>';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="c_routing" data-id="'+id+'">'+show_updated_value(value.c_routing, "c_routing", id, tunnel, cur_tunnel ," ")+'</div>';
        html+='<label style="margin-left: -13px;">Routing</label>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        var box_status="disabled";
        if(value.s_aliasing.new_dst.value=="" || value.s_aliasing.new_dst.value==0){
            box_status="enabled";
        }

        html+='<div class="box-holder">';
        html+='<span>If</span>';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="c_firewall" data-id="'+id+'">'+show_updated_value(value.c_firewall, "c_firewall", id, tunnel, cur_tunnel, box_status)+'</div>';

        html+='<span>Firewall</span>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left green"></i>';
        html+='</div>';

        html+='<div class="box-holder" style="width: 25px;">';
        html+='<span>If</span>';
        if(value.c_forwarding.specific_tunnel.value=="" || value.c_forwarding.specific_tunnel.value=="0"){
            box_status="disabled";
        }else{
            box_status="enabled";
        }
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="s_aliasing" data-id="'+id+'">'+show_updated_value(value.s_aliasing, "s_aliasing", id, tunnel, cur_tunnel ,box_status)+'</div>';
        html+='<label style="margin-left: -12px;">Aliasing</label>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        html+='<div class="box-holder">';
        html+='<span>If</span>';
        if(cur_tunnel == parseInt(value.destination.specific_tunnel.value)){ //if(cur_tunnel != parseInt(value.source.specific_tunnel.value)){
            html+='<div class="box box-con box_'+id+' source_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="source" data-id="'+id+'" style="border:3px solid #000">'+show_updated_value(value.source, "source", id, tunnel, cur_tunnel ," ")+'</div>';
        } else {
            source_count++;
            html+='<div class="box box-con box_'+id+' source_'+id+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="source" data-id="'+id+'" style="border:3px solid #000; background-color:#00A998">&nbsp;</div>';
        }
        html+='<span>Source</span>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        html+='<div class="box-holder" style="width: 25px;">';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="s_qos" data-id="'+id+'">'+show_updated_value(value.s_qos, "s_qos", id, tunnel, cur_tunnel ," ");

        //html+='<i class="fa  fa-close lg_close"></i>';
        html+='</div>';
        html+='<span>QOS</span>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        html+='<div class="box-holder">';
        html+='<span>If</span>';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="s_firewall" data-id="'+id+'">'+show_updated_value(value.s_firewall, "s_firewall", id, tunnel, cur_tunnel ," ")+'</div>';

        html+='<span>Firewall</span>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';

        html+='<div class="arroww">';
        html+='<i class="fa fa-long-arrow-left"></i>';
        html+='</div>';

        html+='<div class="box-holder" style="width: 81px;">';
        html+='<span>If</span>';
        html+='<div class="box box-con box_'+id+' '+new_class+'" data-tid="'+value.tunnel_id+'" data-cloud="" data-type="s_tos" data-id="'+id+'">'+show_updated_value(value.s_tos, "s_tos", id, tunnel, cur_tunnel ," ")+'</div>';

        html+='<span style="margin-left: 0px;">Binding</span>';
        html+='<div class="add_div">';
        html+='</div>';
        html+='</div>';
        html+='</div>';
        html+='</div>';
        html+='</div>';

        if(cur_tunnel == parseInt(value.source.specific_tunnel.value)){
            $(".source_acl_content_"+value.tunnel_id).prepend(html);
        } else if(cur_tunnel == parseInt(value.destination.specific_tunnel.value)){
            $(".destination_acl_content_"+value.tunnel_id).prepend(html);
        } else{
            $(".source_acl_content_"+cur_tunnel).prepend(html);
        }

        if(source_count==0){
            $(".source_no_data_p_"+cur_tunnel).html("  (Source ACL not found)");
        } else {
            $(".source_no_data_p_"+cur_tunnel).html("");
        }
        if(destination_count==0){
            $(".destination_no_data_p_"+cur_tunnel).html("  (Destination ACL not found)");
        } else {
            $(".destination_no_data_p_"+cur_tunnel).html("");
        }

        if(destination_count==0 && source_count==0){
            $(".source_"+id).attr("style", "border:3px solid #000; background-color:#00A998");
            $(".source_"+id).html("&nbsp;");
        }
    });
}
function update_tunnel_acl(tunnel_id){
    $.ajax({
        url:"request.php?request=get_acl_info&id="+tunnel_id,
        success:function(resp){
            var tunnel_acl_data = $.parseJSON(resp);
            box_btn_val = tunnel_acl_data;
            console.log('show_acl_data');
            console.log(tunnel_acl_data.data);
            acl(tunnel_acl_data.data, null, tunnel_id);
            $(".box").trigger("mouseover");
        }
    });
}
function share_acl(data, tunnel_id, cur_tunnel_acls){ //tunnel_id for where aci is searched
    console.log("acl_data");
    console.log(data);
    console.log("cur_tunnel_acls");
    console.log(cur_tunnel_acls);

    var cur_tunnel = null;
    var tunnel="";
    //var html="";
    var xyz=0;
    var source="";
    var destination="";
    var source_count=0;
    var destination_count=0;
    var html="";
    $(".acl_search_result").html("");
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

            html += '<div class="box-install">';
            if(jQuery.inArray(parseInt(id), cur_tunnel_acls)==-1){
                html += '<input type="button" class="btn btn-primary btn-small install_acl" data-acl="' + id + '" data-tunnel="' + tunnel_id + '" value="Install"/>';
            }else{
                html += '<input type="button" class="btn btn-primary btn-small installed_acl" disabled data-acl="' + id + '" data-tunnel="' + tunnel_id + '" value="Installed"/>';
            }
            html += '</div>';

            html+='<div class="not_subscribed"></div>';
            html+='<div class="clearfix"></div>';
            html += '</div>';



            html += '<div class="soumya">';
            html += '<div class="box-holder">';
            html += '<span>then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-id="' + id + '" data-type="d_final" data-toggle="confirmation">' + show_updated_value(value.d_final, "d_final", id, tunnel, cur_tunnel ," ") + '</div>';
            html += '<label>Final Dst</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 41px;">';
            if (cur_tunnel != parseInt(value.destination.specific_tunnel.value)) {
                html+='<span>If</span>';
                html += '<div class="box box-con box_' + id + ' destination_' + id + '  ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="destination" data-id="' + id + '" style="border:3px solid #000">' + show_updated_value(value.destination, "destination", id, tunnel, cur_tunnel ," ") + '</div>';
            } else {
                destination_count++;
                html+='<span style="margin-left: 3px;">If</span>';
                html += '<div class="box box-con box_' + id + ' destination_' + id + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="destination" data-id="' + id + '" style="border:3px solid #000;background-color:#00A998">&nbsp;</div>';
            }
            html += '<label style="margin-left:-12px;">Destination</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left green"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 34px;">';
            html += '<span>then</span>';
            html += '<div  style="min-width: 36px;" class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_forwarding" data-id="' + id + '">' + show_updated_value(value.c_forwarding, "c_forwarding", id, tunnel, cur_tunnel ," ") + '</div>';
            html += '<label style="margin-left: 2px;">FWD</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 25px;">';
            html += '<span>then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_qos" data-id="' + id + '">' + show_updated_value(value.c_qos, "c_qos", id, tunnel ,cur_tunnel," ") + '</div>';

            html += '<span>QOS</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 25px;">';
            html += '<span style="margin-left: -3px;">then</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_routing" data-id="' + id + '">' + show_updated_value(value.c_routing, "c_routing", id, tunnel, cur_tunnel, " ") + '</div>';

            html += '<label style="margin-left: -13px;">Routing</label>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="c_firewall" data-id="' + id + '">' + show_updated_value(value.c_firewall, "c_firewall", id, tunnel, cur_tunnel ," ") + '</div>';

            html += '<span>Firewall</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left green"></i>';
            html += '</div>';

            html += '<div class="box-holder"  style="width: 25px;">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_aliasing"  data-id="' + id + '">' + show_updated_value(value.s_aliasing, "s_aliasing", id, tunnel, cur_tunnel ," ") + '</div>';
            html += '<label style="margin-left: -12px;">Aliasing</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder">';
            html += '<span>If</span>';
            if (cur_tunnel != parseInt(value.source.specific_tunnel.value)) {
                html += '<div class="box box-con box_' + id + ' source_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="source" data-id="' + id + '" style="border:3px solid #000">' + show_updated_value(value.source, "source", id, tunnel, cur_tunnel ," ") + '</div>';
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

            html += '<div class="box-holder" style="width: 25px;">';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_qos" data-id="' + id + '">' + show_updated_value(value.s_qos, "s_qos", id, tunnel, cur_tunnel ," ");

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
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_firewall" data-id="' + id + '">' + show_updated_value(value.s_firewall, "s_firewall", id, tunnel , cur_tunnel," ") + '</div>';

            html += '<span>Firewall</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="arroww">';
            html += '<i class="fa  fa-long-arrow-left"></i>';
            html += '</div>';

            html += '<div class="box-holder" style="width: 81px;">';
            html += '<span>If</span>';
            html += '<div class="box box-con box_' + id + ' ' + new_class + '" data-tid="' + value.tunnel_id + '" data-cloud="" data-type="s_tos" data-id="' + id + '">' + show_updated_value(value.s_tos, "s_tos", id, tunnel, cur_tunnel ," ") + '</div>';

            html += '<span style="margin-left: 0px;">Binding</span>';
            html += '<div class="add_div">';
            html += '</div>';
            html += '</div>';

            html += '<div class="clearfix">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

    });
    $(".acl_search_result").prepend(html);
}

function check_blank(data){
    var i = 0;
    $.each(data, function(key, val){
        if(val.value!=0){
            i = 1;
        }
    });
    return i;
}
function show_destination_for_default(data, database_name, id, tunnel_id, cur_tunnel){
    var items = ["black", "black", "black", "black", "black"];//["#996600", "#003366", "#336699", "#00cc66", "#ff6666"];
    var item = items[Math.floor(Math.random()*items.length)];
    var database="";
    var html="";
    var i = 0;
    //console.log(data);
    $.each(data, function(key, val){

        if(!(key=="every_cloud" || key=="my_clouds" || key=="real_ip" || key=="this_tunnel")) {
            if (val.value != 0) {
                if (database_name == "destination" || database_name == "source") {
                    if (cur_tunnel == parseInt(val.value) && key == "specific_tunnel") {
                        database = database_name;
                        html += '<div class="' + color_box_class + ' ' + key + '-' + database_name + '-' + id + '" style="background-color:transparent; color: white; opacity:1;" data-tid="' + tunnel_id + '" data-toggle="tooltip" title="' + val.label.full + '">' + val.label.short + '</div>';
                    } else if (database != database_name) {
                        html += '<div class="' + color_box_class + ' ' + key + '-' + database_name + '-' + id + '" style="background-color:' + item + '"; data-tid="' + tunnel_id + '" data-toggle="tooltip" title="' + val.label.full + '">' + val.label.short + '</div>';
                    }
                } else {
                    html += '<div class="' + color_box_class + ' ' + key + '-' + database_name + '-' + id + '" style="background-color:' + item + '" data-tid="' + tunnel_id + '" data-toggle="tooltip" title="' + val.label.full + '">' + val.label.short + '</div>';
                }
            } else if (val.value == 0) {
                if (database != database_name) {
                    html += '<div class="' + color_box_class + ' ' + key + '-' + database_name + '-' + id + '" style="background-color:transparent; color: black; opacity:0.35;" data-tid="' + tunnel_id + '" data-toggle="tooltip" title="' + val.label.full + '">' + val.label.short + '</div>';
                }
            }
        }
    });
    return html;
}

function show_updated_value(data, database_name, id, tunnel_id, cur_tunnel, box_status){
    var items = ["black", "black", "black", "black", "black"];//["#996600", "#003366", "#336699", "#00cc66", "#ff6666"];
    var item = items[Math.floor(Math.random()*items.length)];
    var database="";
    var html="";
    var i = 0;
    //console.log(box_status);
    var color_box_class_name=color_box_class;
    if(box_status=="disabled"){
        color_box_class_name="disabled_color_box";
    }

    $.each(data, function(key, val){
        if(key=="deny_allow_all"){
            if(val.value==0){
                html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="color: black; opacity:0.3; background-color:#808080 !important;" data-tid="'+tunnel_id+'" data-toggle="tooltip" data-avl_attr="'+val.value+'" title="'+val.label.full+'">'+val.label.short+'</div>';
            }else if(val.value==1){ //allow_all
                html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="color: #00ff00; opacity:1;background-color:#ffffff !important;" data-tid="'+tunnel_id+'" data-toggle="tooltip" data-avl_attr="'+val.value+'" title="'+val.label.full+'">'+val.label.short+'</div>';
            }else{ //deny_all
                html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="color: #ff0000; opacity:1;background-color:#ffffff !important;" data-tid="'+tunnel_id+'" data-toggle="tooltip" data-avl_attr="'+val.value+'" title="'+val.label.full+'">'+val.label.short+'</div>';
            }
        }
        else if(database_name=="destination" && cur_tunnel == parseInt(data.specific_tunnel.value)){////////////////////if destinaiton acl then
            if(key=="real_ip" || key=="this_tunnel"){
                if(val.value!=0){
                    html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: white; opacity:1;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                }else{
                    html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: '+item+'; opacity:0.35;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                }
            }
        }else{
            if(database_name == "destination"){
                if(key=="every_cloud" || key=="internet"){
                    if(val.value!=0){
                        html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: white; opacity:1;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }else{
                        html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: '+item+'; opacity:0.35;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }
                }
                else if(key=="specific_tunnel"){
                    sub_color_box_class_name="color-box";
                    if(val.value!=0){
                        html+='<div class="'+sub_color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: white; opacity:1;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }else{
                        html+='<div class="'+sub_color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: '+item+'; opacity:0.35;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }
                }
                else if(key=="my_cloud" || key=="my_clouds" || key=="specific_group"){
                    var sub_color_box_class_name=color_box_class_name;
                    if(data.every_cloud.value=="1"){
                        sub_color_box_class_name="disabled_color_box";
                    }
                    if(val.value!=0){
                        html+='<div class="'+sub_color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: white; opacity:1;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }else{
                        html+='<div class="'+sub_color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: '+item+'; opacity:0.35;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }
                }
            }else {
                if(val.value!=0){
                    if(database_name == "destination" || database_name == "source"){
                        if(cur_tunnel == parseInt(val.value) && key == "specific_tunnel"){
                            database = database_name;
                            html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: white; opacity:1;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                        } else if(database != database_name){
                            html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:'+item+'"; data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                        }
                    } else {
                        html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:'+item+'" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }
                } else if(val.value==0){
                    if(database != database_name){
                        html+='<div class="'+color_box_class_name+' '+key+'-'+database_name+'-'+id+'" style="background-color:transparent; color: black; opacity:0.35;" data-tid="'+tunnel_id+'" data-toggle="tooltip" title="'+val.label.full+'">'+val.label.short+'</div>';
                    }
                }
            }
        }
        //alert(html);
    });
    return html;
}

function notify_msg (status, msg) {
    notify({
        type: status,
        title: status,
        message: msg,
        position: {
            x: "left",
            y: "bottom"
        },
        icon: '',
        size: "small",
        overlay: false,
        closeBtn: true,
        overflowHide: false,
        spacing: 20,
        theme: "default",
        autoHide: true,
        delay: 3000,
        onShow: null,
        onClick: null,
        onHide: null,
        template: '<div class="notify"><div class="notify-text"></div></div>'
    });
}

function ipv4addr(value) {
    var ip = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
    var res;
    if (value.match(ip)) {
        arr=[];
        arr=value.split('.');
        //alert(arr);
        if(parseInt(arr[0]) <= 255){
            if(parseInt(arr[1]) <= 255){
                if(parseInt(arr[2]) <= 255){
                    if(parseInt(arr[3]) <= 255){
                        return true;
                    }
                }
            }
        }else{
            return false;
        }
    }
    else {
        return false;
    }
}

function isNumber(n, length_check) {
    if(length_check > 0){
        return !isNaN(parseFloat(n)) && isFinite(n) && (n.length==4);
    }
    return !isNaN(parseFloat(n)) && isFinite(n);
}


///////////////////////////////////////////////this is for socket////////////////////////////////////////////////////////////////////
function connectivity_page_content(data){
    console.log(data.cloud_tunnels_data);
    var html="";
    var cloud_tunnels_data=data.cloud_tunnels_data;

    html+='<div class="content-block">';
        html+='<div class="filter-action-block">';
            html+='<div class="filter-block">';
                html+='<label for="cloud_selector" class="cloud_selector_label">Filter: &nbsp;';
                    html+='<select id="cloud_selector" name="cloud_selector" class="cloud_select" data-token="'+token+'">';
                        html+='<option value="0">All clouds</option>';
                            $.each(cloud_tunnels_data,function(index,data){
                                html+='<option value="'+data.cloud_id+'">'+data.cloud_name+'</option>';
                            });
                    html+='</select>';
                html+='</label>';
            html+='</div>';
            html+='<div class="add-cloud-block">';
                html+='<a href="javascript:;" class="btn btn-rounded btn-primary" data-toggle="modal" data-target="#add_cloud"> Create cloud </a>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
        html+='</div>';
        html+='<div class="filter-result-block">';

    $.each(cloud_tunnels_data,function(index,cloud_data){
        html+=cloud_tunnels(cloud_data);
    });

        html+='</div>';
    html+='</div>';

    return html;
}
function cloud_tunnels(data){
    var html="";
    html+='<div class="cloud-row cloud-row-'+data.cloud_id+'" data-cid="'+data.cloud_id+'">';
    html+=cloud_tunnels_content(data);
    html+='</div>';
    return html;
}
function cloud_tunnels_content(data){
    var html="";
    html+='<div class="cloud-tunnels cloud-tunnels-'+data.cloud_id+'">';
    html+='<div class="page-content cloud-content">';
    html+='<div class="content" style="padding-top: 0px;">';
    html+='<div class="page-title">';
    html+='<div class="cloud-name cloud-name-'+data.cloud_id+'">';
    html+=data.cloud_name;
    html+='<span class="cloud-cost cloud-cost-'+data.cloud_id+'"> &nbsp;( Total cost = '+data.cost+' )</span>';
    html+='</div>';
    html+='<span class="delete_cloud" data-val="'+data.cloud_id+'"><i class="fa fa-trash pull-right cursor" data-toggle="tooltip" data-placement="top" title="Delete this Cloud"></i></span>';

        var switch_checked_val=(data.is_searchable==1?"checked":"");

    html+='<div class="cloud_searchable_switch_block">';
    html+='<input id="cmn-toggle-cloud-'+data.cloud_id+'" class="cmn-toggle cmn-toggle-round cloud_searchable_switch" data-cloud_id="'+data.cloud_id+'" type="checkbox" '+switch_checked_val+'>';
    html+='<label for="cmn-toggle-cloud-'+data.cloud_id+'"></label>';
    html+='</div>';
    html+='</div>';
    html+='<div class="clearfix"></div>';
    html+='<div data-uid="'+current_customer_id+'" class="just list Parks">';

    html+='<div class="list_header">';
    html+='<div class="meta" data-toggle="tooltip" data-placement="right" title="ACL"><i class="fa fa-eye"></i></div>';
    html+='<div class="meta" data-toggle="tooltip" data-placement="right" title="Create ACL"><i class="fa fa-cogs"></i></div>';

    html+='<div class="meta" id="SortByName" data-toggle="tooltip" data-placement="right" title="Add tunnels"><a href="javascript:void(0);" data-val="'+data.cloud_id+'" data-mail="'+current_customer_email+'" data-count="0" class="tunnel_add_form_btn"><i class="fa fa-fw fa-plus-circle"></i></a></div>';

    html+='<div class="meta" id="" data-toggle="tooltip" data-placement="right" title="Save all"><a href="javascript:void(0);" data-val="'+data.cloud_id+'" data-count="0" class="all_tunnel_save_btn"><i class="fa fa-floppy-o"></i></a></div>';

    html+='<div class="meta width-30"><div class="cursor chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa fa-square-o"></i></div></div>';

    html+='<div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_grp" data-cloud="'+data.cloud_id+'" data-dif="asc"><i class="fa fa-sort"></i></a></div>';

    html+='<div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i></div>';
    html+='<div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="'+data.cloud_id+'" data-dif="asc"><i class="fa fa-sort"></i></a></div>';
    html+='<div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>';
    html+='<div class="meta width-100" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>';
    html+='<div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>';
    html+='<div class="meta width-140" style="width: 137px!important;" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>';
    html+='<div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway</div>';
    html+='<div class="meta"></div>';
    html+='</div>';
    html+='<form id="tunnels_form_field" style="display:none;">';
    html+='<input type="button" class="btn btn-sm btn-primary btn_add_tunnel" data-cloud="'+data.cloud_id+'" value="Submit">';
    html+='<input type="reset" class="btn btn-sm btn-warning" id="tunnel_form_close_btn" value="Cancel">';
    html+='</form>';
    html+='<div class="tunnel_body tunnel_body_'+data.cloud_id+'">';

    html+='</div>';
    html+='<div id="tunnel_body_pagenation_'+data.cloud_id+'">';

    html+='</div>';
    html+='<script>';

    var tunnel_ids="";
    $.each(data.tunnels,function(index,tunnel){
        tunnel_ids+=tunnel.tunnel_id;
        tunnel_ids+=",";
    });
    if(tunnel_ids.length>0){
        tunnel_ids=tunnel_ids.substring(0,tunnel_ids.length-1);
    }

    html+='$("#tunnel_body_pagenation_"+'+data.cloud_id+').pagination({';
    html+='dataSource: ['+tunnel_ids+'],';
    html+='pageSize: 5,';
    html+='autoHidePrevious: true,';
    html+='autoHideNext: true,';
    html+='callback: function(page_data, pagination) {';
    html+='console.log(page_data);';
    html+='tunnel_template(page_data,".tunnel_body_"+'+data.cloud_id+');';
    html+='}';
    html+='});';
    html+='</script>';
    html+='</div>';
    html+='</div>';
    html+='</div>';
    html+='</div>';

    return html;
}
function update_cloud(cloud_id){
    $.ajax({
        url:'request.php?request=get_tunnels_for_cloud&cloud_id='+cloud_id,
        success:function(res){
            console.log(res);
            var data= $.parseJSON(res);
            var html=cloud_tunnels_content(data);
            $(".cloud-row-"+cloud_id).html(html);
        }
    });
}
function tunnel_template(data,target){
    console.log(data);
    var template_data="";
    var field_data={
        tunnel_ids:data
    };
    $.ajax({
        url : "request.php?request=get_tunnels_from_ids",
        type : "POST",
        data : field_data,
        success : function(resp){
            $(target).html(resp);
        },
        error : function(){
        }
    });
    return true;
}
function sponsor_tunnel_template(data,target){
    console.log(data);
    var template_data="";
    var field_data={
        tunnel_ids:data
    };
    $.ajax({
        url : "request.php?request=get_sponsor_tunnels_from_ids",
        type : "POST",
        data : field_data,
        success : function(resp){
            $(target).html(resp);
        },
        error : function(){
        }
    });
    return true;
}
function profile_info_page_content(data){
    var html="";
    html+='<div class="page-content">';
    html+='<div class="profile-page-content">';
    html+='<div class="row">';
    html+='<div class="alert alert-success" role="alert" style="display: none;"></div>';
    html+='<div class="col-lg-6 col-md-6 col-sm-6">';
    html+='<div class="alert alert-success" role="alert" id="manual_add_success_message" style="display: none;"></div>';
    html+='<div class="alert alert-danger" role="alert" id="manual_add_error_message" style="display: none;"></div>';
    html+='<div id="edtprofile-response-message"></div>';
    html+='<h4 class="pr_heading">Edit your profile</h4>';
    html+='<form id="profile_pic_change" method="post" enctype="multipart/form-data">';
    html+='<div id="crop-avatar-user custom_row">';
    html+='<div class="profile_image_div">';
    html+='<img src="'+(data.profile_image!="" ? data.profile_image : ROOT_URL+"/assets/img/profiles/demo-user.jpg")+'" class="profile_image_viewers" id="profile_image_viewer">';
    html+='</div>';
    html+='<div>';
    html+='<input id="profile_image" type="file" name="profile_image">';
    html+='</div>';
    html+='</div>';

    html+='<input type="hidden" name="get_cus_id" id="get_cus_id" value="'+data.customer_id+'">';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Name: </label>';
    html+='<input class="form-control" id="name" type="text" value="'+data.name+'" name="name">';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Display Name: </label>';
    html+='<input class="form-control" id="display_name" type="text" value="'+data.display_name+'" name="display_name">';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Phone number: </label>';
    html+='<input class="form-control" id="phone" type="text" value="'+data.phone+'" pattern= "[7-9]{1}[0-9]{9}" name="phone">';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Your mail address: </label>';
    html+='<input class="form-control" id="email" type="text" value="'+data.email+'" name="email" disabled>';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Your Recovery Email address: </label>';
    html+='<input class="form-control" id="remail" type="text" value="'+data.remail+'" name="reemail">';
    html+='</div>';

    html+='<div class="custom_row">';
    html+='<input type="submit" class="btn btn-primary btn-success btn-recovery" id="update-profile-btn" name="update-profile-btn" value="Save">';
    html+='</div>';

    html+='</form>';

    html+='</div>';
    html+='<div class="col-lg-6 col-md-6 col-sm-6">';
    html+='<h4 class="pr_heading">Change your password</h4>';
    html+='<div id="edtpass-response-message"></div>';
    html+='<form id="change_password_form">';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Old password: </label>';
    html+='<input type="password" class="form-control" id="opassword" type="text" name="opassword">';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<label for="ex3">New password: </label>';
    html+='<input type="password" class="form-control" id="password" type="text" value="" name="password">';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<label for="ex3">Confirm password: </label>';
    html+='<input type="password" class="form-control" id="cfmPassword" type="text" name="cfmPassword" required>';
    html+='</div>';
    html+='<div class="custom_row">';
    html+='<button type="submit" name="submit" value="Submit" class="btn btn-primary btn-success btn-cpassword" href="javascript:void(0)" id="btn-editpass-profile">Update Password</button>';
    html+='</div>';
    html+='</form>';
    html+='</div>';
    html+='</div>';
    html+='</div>';

    html+='<div class="addNewRow"></div>';
    html+='</div>';
    return html;
}

function get_home_info_page_content(data){
    var html="";

    return html;
}

function get_social_info_page_content(data){
    var html="";

    return html;
}

function get_friend_list_page_for_dialog(friend_list_data){
    var html="";
    var friend_list=friend_list_data.friend_list;
    html+='<div class="current-friends-box">';

    var i=0;
    $.each(friend_list, function(index, friend_data){
        console.log(friend_data);
        i++;
        var last_class = "";
        if (i==friend_list.length){
            last_class = "left-friend-list-content-row-last";
        }
        html+='<div class="left-friend-list-content-row '+last_class+'" data-friend_id="'+friend_data.customer_id+'" data-customer_name="'+friend_data.name+'">';
        html+='<div class="customer-profile-info-box">';
        html+='<div style="float: left">';
        var profile_image=(friend_data.profile_image!="" ? friend_data.profile_image : ROOT_URL+'/assets/img/profiles/demo-user.jpg');
        html+='<img class="friend_short_image" src="'+profile_image+'" alt="'+profile_image+'">';
        html+='</div>';
        html+='<div class="friend_info" style="float: left;">';
        html+='<div class="friend_name">'+friend_data.display_name+'</div>';
        html+='<div class="friend_tag_id">'+friend_data.tag_id+': '+friend_data.shared_acl_cnt+'</div>';
        html+='</div>';
        html+='<div class="clearfix"></div>';
        html+='</div>';
        html+='</div>';
    });
    html+='</div>';
    return html;
}

function get_friend_list_page_for_sidebar(friend_list_data){
    var html="";
    var friend_list=friend_list_data.friend_list;
    html+='<div class="all-customers-box"></div>';
    html+='<div class="current-friends-box">';

    var i=0;
    $.each(friend_list, function(index, friend_data){
        console.log(friend_data);
        i++;
        var last_class = "";
        if (i==friend_list.length){
            last_class = "left-friend-list-content-row-last";
        }
        html+='<div class="left-friend-list-content-row left-friend-list-content-row-'+friend_data.customer_id+' '+last_class+' custom_popup_context_item" data-friend_id="'+friend_data.customer_id+'" data-friend_name="'+friend_data.name+'">';
            html+='<div class="profile-info-box" style="float: left;">';
                html+='<div style="float: left">';
                var profile_image=(friend_data.profile_image!="" ? friend_data.profile_image : ROOT_URL+'/assets/img/profiles/demo-user.jpg');
                    html+='<img class="friend_short_image" src="'+profile_image+'" alt="'+profile_image+'">';
                html+='</div>';
                html+='<div class="friend_info" style="float: left;">';
                    html+='<div class="friend_name">'+friend_data.display_name+'</div>';
                    html+='<div class="friend_tag_id">'+friend_data.tag_id+': '+friend_data.shared_acl_cnt+'</div>';
                html+='</div>';
                html+='<div class="clearfix"></div>';
            html+='</div>';
            html+='<div class="friend-action-box">';
                html+='<span class="friend-action delete-action" data-friend_id="'+friend_data.customer_id+'">';
                    html+='<i class="fa fa-trash-o" aria-hidden="true"></i>';
                html+='</span>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
        html+='</div>';
    });
    html+='</div>';
    return html;

}

function get_request_friend_list_page_for_sidebar(friend_list_data){
    var html="";
    var friend_list=friend_list_data.friend_list;
    var i=0;
    if(friend_list.owner_list!==undefined){
        console.log('not undefined');
        $.each(friend_list.owner_list, function(index, friend_data){
            console.log(friend_data);
            i++;
            var last_class = "";
            if (i==friend_list.owner_list.length){
                last_class = "left-friend-list-content-row-last";
            }
            html+='<div class="left-friend-list-content-row left-friend-list-content-row-'+friend_data.customer_id+' '+last_class+'" data-friend_id="'+friend_data.customer_id+'" data-friend_name="'+friend_data.name+'">';
            html+='<div class="profile-info-box" style="float: left;">';
            html+='<div style="float: left">';
            var profile_image=(friend_data.profile_image!="" ? friend_data.profile_image : ROOT_URL+'/assets/img/profiles/demo-user.jpg');
            html+='<img class="friend_short_image" src="'+profile_image+'" alt="'+profile_image+'">';
            html+='</div>';
            html+='<div class="friend_info" style="float: left;">';
            html+='<div class="friend_name">'+friend_data.display_name+'</div>';
            html+='<div class="friend_tag_id">'+friend_data.tag_id+': '+friend_data.shared_acl_cnt+'</div>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
            html+='<div class="friend-action-box">';
            html+='<span class="friend-action accept-action" data-friend_id="'+friend_data.customer_id+'">';
            html+='<i class="fa fa-check" aria-hidden="true"></i>';
            html+='</span>';
            html+='<span class="friend-action reject-action" data-friend_id="'+friend_data.customer_id+'">';
            html+='<i class="fa fa-ban" aria-hidden="true"></i>';
            html+='</span>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
        });
    }

    i=0;
    if(friend_list.other_list!=undefined){
        console.log('not undefined1');
        $.each(friend_list.other_list, function(index, friend_data){
            console.log(friend_data);
            i++;
            var last_class = "";
            if (i==friend_list.other_list.length){
                last_class = "left-friend-list-content-row-last";
            }
            html+='<div class="left-friend-list-content-row left-friend-list-content-row-'+friend_data.customer_id+' '+last_class+'" data-friend_id="'+friend_data.customer_id+'" data-friend_name="'+friend_data.name+'">';
            html+='<div class="profile-info-box" style="float: left;">';
            html+='<div style="float: left">';
            var profile_image=(friend_data.profile_image!="" ? friend_data.profile_image : ROOT_URL+'/assets/img/profiles/demo-user.jpg');
            html+='<img class="friend_short_image" src="'+profile_image+'" alt="'+profile_image+'">';
            html+='</div>';
            html+='<div class="friend_info" style="float: left;">';
            html+='<div class="friend_name">'+friend_data.display_name+'</div>';
            html+='<div class="friend_tag_id">'+friend_data.tag_id+': '+friend_data.shared_acl_cnt+'</div>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
            html+='<div class="friend-action-box">';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
        });
    }
    return html;
}

function get_rejected_friend_list_page_for_sidebar(friend_list_data){
    var html="";
    var friend_list=friend_list_data.friend_list;
    var i=0;
    if(friend_list.owner_list!==undefined) {
        console.log('not undefined');
        $.each(friend_list.owner_list, function(index, friend_data){
            console.log(friend_data);
            i++;
            var last_class = "";
            if (i==friend_list.owner_list.length){
                last_class = "left-friend-list-content-row-last";
            }
            html+='<div class="left-friend-list-content-row left-friend-list-content-row-'+friend_data.customer_id+' '+last_class+'" data-friend_id="'+friend_data.customer_id+'" data-friend_name="'+friend_data.name+'">';
            html+='<div class="profile-info-box" style="float: left;">';
            html+='<div style="float: left">';
            var profile_image=(friend_data.profile_image!="" ? friend_data.profile_image : ROOT_URL+'/assets/img/profiles/demo-user.jpg');
            html+='<img class="friend_short_image" src="'+profile_image+'" alt="'+profile_image+'">';
            html+='</div>';
            html+='<div class="friend_info" style="float: left;">';
            html+='<div class="friend_name">'+friend_data.display_name+'</div>';
            html+='<div class="friend_tag_id">'+friend_data.tag_id+': '+friend_data.shared_acl_cnt+'</div>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
            html+='<div class="friend-action-box">';
            html+='<span class="friend-action delete-action" data-friend_id="'+friend_data.customer_id+'">';
            html+='<i class="fa fa-trash-o" aria-hidden="true"></i>';
            html+='</span>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
        });
    }
    i=0;
    if(friend_list.other_list!=undefined){
        console.log('not undefined1');
        $.each(friend_list.other_list, function(index, friend_data){
            console.log(friend_data);
            i++;
            var last_class = "";
            if (i==friend_list.other_list.length){
                last_class = "left-friend-list-content-row-last";
            }
            html+='<div class="left-friend-list-content-row left-friend-list-content-row-'+friend_data.customer_id+' '+last_class+'" data-friend_id="'+friend_data.customer_id+'" data-friend_name="'+friend_data.name+'">';
            html+='<div class="profile-info-box" style="float: left;">';
            html+='<div style="float: left">';
            var profile_image=(friend_data.profile_image!="" ? friend_data.profile_image : ROOT_URL+'/assets/img/profiles/demo-user.jpg');
            html+='<img class="friend_short_image" src="'+profile_image+'" alt="'+profile_image+'">';
            html+='</div>';
            html+='<div class="friend_info" style="float: left;">';
            html+='<div class="friend_name">'+friend_data.display_name+'</div>';
            html+='<div class="friend_tag_id">'+friend_data.tag_id+': '+friend_data.shared_acl_cnt+'</div>';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
            html+='<div class="friend-action-box">';
            html+='</div>';
            html+='<div class="clearfix"></div>';
            html+='</div>';
        });
    }
    return html;
}