jQuery(document).ready(function(){
    $("body").on("mousedown",".active_option .display_value",function(){
        if(event.which == 1){
            var select_box_class=jQuery(this).closest(".custom_select_box").attr("class").split(" ")[0];
            if($("."+select_box_class+" .inactive_option").hasClass("hidden")){
                $("."+select_box_class+" .inactive_option").removeClass("hidden");
            }else{
                $("."+select_box_class+" .inactive_option").addClass("hidden");
            }
        }
    });

    $("body").on("mouseup",".active_option .action_btn",function(){
        if(event.which == 1){
            var empty_txt="Not assigned";
            var select_box_class=jQuery(this).closest(".custom_select_box").attr("class").split(" ")[0];
            var value=$("."+select_box_class+" .active_option .display_value").attr("data-value");
            if(value!=empty_txt){
                $("."+select_box_class+" .active_option .display_value").html(empty_txt);
                $("."+select_box_class+" .active_option .display_value").attr("data-value","");
                $("."+select_box_class+" .active_option .action_btn").addClass("assign_action_btn");
                $("."+select_box_class+" .active_option .assign_action_btn").removeClass("action_btn");
                $("."+select_box_class+" .active_option .assign_action_btn i").attr("class","fa fa-fw fa-plus");
                $("."+select_box_class+" .active_option").trigger("clear");

            }
        }
    });

    $("body").on("mouseup",".inactive_option .display_value",function(){
        if(event.which == 1){
            var value=$(this).attr("data-value");
            //var display_value=$(this).html();
            var select_box_class=jQuery(this).closest(".custom_select_box").attr("class").split(" ")[0];
            $("."+select_box_class+" .inactive_option").addClass("hidden");
            if(value!=$("."+select_box_class+" .active_option .display_value").attr("data-value")){
                if(value==""){
                    var empty_txt="Not assigned";
                    $("."+select_box_class+" .active_option .display_value").html(empty_txt);
                }else{
                    $("."+select_box_class+" .active_option .display_value").html(value);
                }
                $("."+select_box_class+" .active_option .display_value").attr("data-value",value);
                $("."+select_box_class+" .active_option").trigger("change");
            }
        }
    });

    $("body").on("mouseup",".inactive_option .action_btn",function(event){
        if(event.which == 1){
            jQuery(this).closest(".inactive_option").addClass("deleted");
            jQuery(this).closest(".inactive_option").trigger("clear");
        }
    });

    $("body").on("click",".custom_select_box", function(){
        var select_box_class=jQuery(this).attr("class").split(" ")[0];
        jQuery(".custom_select_box").each(function(){
            var box_class=jQuery(this).attr("class").split(" ")[0];
            if(select_box_class!=box_class){
                $("."+box_class+" .inactive_option").addClass("hidden");
            }
        });
    });

/*

    $("body").on("change",".active_option", function(){
        alert($(this).children(".display_value").html());
    });
    $("body").on("clear",".active_option", function(){
        alert($(this).children(".display_value").html());
    });
    $("body").on("clear",".inactive_option", function(){
        alert($(this).children(".display_value").html());
    });

    */
});
