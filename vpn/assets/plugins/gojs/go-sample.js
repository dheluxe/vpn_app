var myDiagram = null;
$(document).ready(function(){
    gojs_init();
});

function gojs_init(){
    if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
    var GT = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
        GT(go.Diagram, "myDiagramDiv",  // must name or refer to the DIV HTML element
            {
                // start everything in the middle of the viewport
                initialContentAlignment: go.Spot.Center,
                // have mouse wheel events zoom in and out instead of scroll up and down
                "toolManager.mouseWheelBehavior": go.ToolManager.WheelZoom,
                // support double-click in background creating a new node
                "clickCreatingTool.archetypeNodeData": { text: "RS" },
                // enable undo & redo
                "undoManager.isEnabled": true
            });
    // when the document is modified, add a "*" to the title and enable the "Save" button
    myDiagram.addDiagramListener("Modified", function(e) {
        var button = document.getElementById("SaveButton");
        if (button) button.disabled = !myDiagram.isModified;
        var idx = document.title.indexOf("*");
        if (myDiagram.isModified) {
            if (idx < 0) document.title += "*";
        } else {
            if (idx >= 0) document.title = document.title.substr(0, idx);
        }
    });

    // define the Node template
    myDiagram.nodeTemplate =
        GT(go.Node, "Auto",
            { contextMenu: GT(go.Adornment) },
            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
            // define the node's outer shape, which will surround the TextBlock
            GT(go.Shape, "RoundedRectangle",
                new go.Binding("fill", "color"),
                {
                    parameter1: 5,  // the corner has a large radius
                    height:40,
                    fill: GT(go.Brush, "Linear", { 0: "rgb(254, 201, 0)", 1: "rgb(254, 162, 0)" }),
                    stroke: null,
                    portId: "",  // this Shape is the Node's port, not the whole Node
                    fromLinkable: true, fromLinkableDuplicates: true,
                    toLinkable: true, toLinkableDuplicates: true
                }),
            GT(go.TextBlock,
                {
                    margin:10,
                    font: "bold 11pt helvetica, bold arial, sans-serif",
                    editable: false,  // editing the text automatically updates the model data
                    cursor: "pointer"
                },
                new go.Binding("text").makeTwoWay())
        );

    // unlike the normal selection Adornment, this one includes a Button
    myDiagram.nodeTemplate.selectionAdornmentTemplate =
        GT(go.Adornment, "Spot",
            GT(go.Panel, "Auto",
                GT(go.Shape, { fill: null, stroke: "blue", strokeWidth: 2 }),
                GT(go.Placeholder)  // a Placeholder sizes itself to the selected Node
            )
        ); // end Adornment
    // replace the default Link template in the linkTemplateMap
    myDiagram.linkTemplate =
        GT(go.Link,  // the whole link panel
            /*{ contextMenu: GT(go.Adornment) },*/
            {
                curve: go.Link.Bezier, adjusting: go.Link.Stretch,
                reshapable: true, relinkableFrom: true, relinkableTo: true,
                toShortLength: 3
            },
            new go.Binding("points").makeTwoWay(),
            new go.Binding("curviness"),
            GT(go.Shape,  // the link shape
                new go.Binding("stroke", "color")
                /*{
                    strokeWidth: 1.5,
                    fill: GT(go.Brush, "Linear", { 0: "rgb(254, 201, 0)", 1: "rgb(254, 162, 0)" }),
                    stroke: GT(go.Brush, "Linear", { 0: "rgb(254, 201, 0)", 1: "rgb(254, 162, 0)" })
                }*/),
            GT(go.Shape,  // the arrowhead
                new go.Binding("stroke", "color"),
                new go.Binding("fill", "color"),
                { fromArrow:"diamond" }),
            GT(go.Shape,  // the arrowhead
                new go.Binding("stroke", "color"),
                new go.Binding("fill", "color"),
                { toArrow:"diamond" })
        );



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // This is a dummy context menu for the whole Diagram:
    //myDiagram.contextMenu = GT(go.Adornment);

    // Override the ContextMenuTool.showContextMenu and hideContextMenu methods
    // in order to modify the HTML appropriately.
    var cxTool = myDiagram.toolManager.contextMenuTool;

    // This is the actual HTML context menu:
    var cxElement = document.getElementById("gojs-contextMenu");

    // We don't want the div acting as a context menu to have a (browser) context menu!
    cxElement.addEventListener("contextmenu", function(e) {
        this.focus();
        e.preventDefault();
        return false;
    }, false);
    cxElement.addEventListener("blur", function(e) {
        cxTool.stopTool();

        // maybe start another context menu
        if (cxTool.canStart()) {
            myDiagram.currentTool = cxTool;
            cxTool.doMouseUp();
        }

    }, false);
    cxElement.tabIndex = "1";

    // This is the override of ContextMenuTool.showContextMenu:
    // This does not not need to call the base method.
    cxTool.showContextMenu = function(contextmenu, obj) {
        var diagram = this.diagram;
        if (diagram === null) return;

        // Hide any other existing context menu
        if (contextmenu !== this.currentContextMenu) {
            this.hideContextMenu();
        }

        // Show only the relevant buttons given the current state.
        var cmd = diagram.commandHandler;
        var objExists = obj !== null;
        document.getElementById("control").style.display = "block";
        document.getElementById("edit").style.display = "block";
        document.getElementById("delete").style.display = objExists && cmd.canDeleteSelection() ? "block" : "none";

        // Now show the whole context menu element
        cxElement.style.display = "block";
        // we don't bother overriding positionContextMenu, we just do it here:
        var mousePt = diagram.lastInput.viewPoint;
        cxElement.style.left = mousePt.x + "px";
        cxElement.style.top = mousePt.y + "px";

        // Remember that there is now a context menu showing
        this.currentContextMenu = contextmenu;
    }

    // This is the corresponding override of ContextMenuTool.hideContextMenu:
    // This does not not need to call the base method.
    cxTool.hideContextMenu = function() {
        if (this.currentContextMenu === null) return;
        cxElement.style.display = "none";
        this.currentContextMenu = null;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // read in the JSON data from the "mySavedModel" element
    load();
}

function cxcommand(val) {
    var diagram = myDiagram;
    if (!(diagram.currentTool instanceof go.ContextMenuTool)) return;

    console.log(val);
    switch (val) {
        case "Run OSPF":
            console.log(selected_node);
            var id=selected_node.id;
            alert(val);
            $('html, body').animate({
                scrollTop: $(document).scrollTop()
            }, 1);
            break;
        case "Stop OSPF":
            console.log(selected_node);
            var id=selected_node.id;
            alert(val);
            $('html, body').animate({
                scrollTop: $(document).scrollTop()
            }, 1);
            break;
        case "Control":
            console.log(selected_node);
            diagram.commandHandler.copySelection();
            $('html, body').animate({
                scrollTop: $(document).scrollTop()
            }, 1);
            var id=selected_node.id;
            if(id==0){

            }else if(id!="" && id!=undefined){
                $.ajax({
                    url:'../request.php?request=remote_server_edit',
                    type:"POST",
                    data:{"id": id},
                    success:function(resp){
                        var val=$.parseJSON(resp);
                        console.log(val);
                        $("#remote_ip").val(val.remote_ip);
                        $("#server_name").val(val.server_name);
                        $("#script_status").val(val.current_status);
                        $("#number_successful_commands").val(val.number_successful_commands);
                        $("#resource_usage").val(val.ressnap);
                        $("#server_id").val(val.id);

                        $("#remote_server_control .id").val(val.id);
                        $("#remote_server_control .ip").val(val.remote_ip);
                        $("#remote_server_control .monstat").val(val.is_monitored);
                        if(val.is_monitored==1){
                            $("#remote_server_control .command").val("stop");
                        }else{
                            $("#remote_server_control .command").val("start");
                        }
                        $("#remote_server_control_modal").modal("show");
                    }
                });
            }else{

            }

            break;
        case "Edit":
            $('html, body').animate({
             scrollTop: $(document).scrollTop()
             }, 1);
            console.log(selected_node);
            var id=selected_node.id;
            if(id==0){
                alert("You can not edit main server.");
            }else if(id!="" && id!=undefined){
                $.ajax({
                    url:'../request.php?request=remote_server_edit',
                    type:"POST",
                    data:{"id": id},
                    success:function(resp){
                        var val=$.parseJSON(resp);
                        console.log(val);
                        $("#add_remote_server_modal .modal-title").html("Update Remote Server");
                        $(".btn_install").html("Update");
                        $("#remote_id").val(val.remote_ip);
                        $("#ssh_username").val(val.ssh_username);
                        $("#ssh_password").val(val.ssh_password);
                        $("#sname").val(val.server_name);
                        $("#email").val(val.email);
                        $("#sel1").val(val.remote_group);
                        $("#add_remote_server_modal").modal("show");
                    }
                });
            }else{

            }
            break;
        case "Delete":
            console.log(selected_node);
            var id=selected_node.id;
            if(id==0){
                alert("You can not remove main server.");
            }else{
                diagram.commandHandler.deleteSelection();
            }

            $('html, body').animate({
                scrollTop: $(document).scrollTop()
            }, 1);
            break;
    }
    diagram.currentTool.stopTool();
}

// A custom command, for changing the color of the selected node(s).
function changeColor(diagram, color) {
    // Always make changes in a transaction, except when initializing the diagram.
    diagram.startTransaction("change color");
    diagram.selection.each(function(node) {
        if (node instanceof go.Node) {  // ignore any selected Links and simple Parts
            // Examine and modify the data, not the Node directly.
            var data = node.data;
            // Call setDataProperty to support undo/redo as well as
            // automatically evaluating any relevant bindings.
            diagram.model.setDataProperty(data, "color", color);
        }
    });
    diagram.commitTransaction("change color");
}
// Show the diagram's model in JSON format
function save() {
    var diagram_data=myDiagram.model.toJson();
    document.getElementById("mySavedModel").value = diagram_data;
    $.ajax({
        url:"../request.php?request=save_diagram_data",
        type: "POST",
        data: {"diagram_data":diagram_data},
        success: function(resp){
            console.log(resp);
            /*$("#diagram_save_success_message").html("saved successfully.");
            $("#diagram_save_success_message").css("display","block");
            setTimeout(function(){
                $("#diagram_save_success_message").css("display","none");
            },3000);*/
            //load();
            location.reload(true);
        }
    });

}
function load() {
    var load_data=document.getElementById("mySavedModel").value;
    //console.log($.parseJSON(load_data));
    console.log(go.Model.fromJson(load_data));
    myDiagram.model = go.Model.fromJson(load_data);
}