<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>

        <style>


            .tx-chatHolder{
                width:100%;
                height:500px;
                border:1px black solid;
                min-height:75%;
                float:left;
                clear:both;
                display:table-cell;
                overflow:scroll;

            }
            .tx-userInput{

                width:100%;
                height:25%;
                border:1pt black solid;
                float:left;
                clear:both;
                display:table-cell;
            }
            .tx-Holder{
                display:table;
                width:100%;
                height:100%;
                margin:0pt;
                padding:0pt;
            }
            .tx-ChatBox{
                border:1pt black solid;
                width:85%;
            }
            .tx-chatSender{
                border:1pt black solid;
                cursor:pointer;
            }
            .tx-chatSender:hover{
                background:blue;
            }
            .tx-chatSpan {
                width:100%;
                float:left;
                clear:both;
            }
            .tx-UserHolder{
                font-weight:bold;
                color:red;
            }
        </style>

        <script type ="text/javascript">
            var cText = 0;
            
            //Holds all the options for the game =)
            var options = 
                {
                "player":[{"room":"r1", "inventory":["rock"]}],
                "rooms":[
                    {"id":"r1",
                        "name":"Dark scary room",
                        "description":"This room scares you, its cold, damp, and unfourtantly empty.",
                        "up":"r2",
                        "right":"r3"
                    },
                    {"id":"r2",
                        "name":"A clean room",
                        "description":"A clean room that is way better then the scary room but not that amazing..",
                        "down":"r1",
                        "items":["rock"]
                    },
                    {"id":"r3",
                        "name":"A Purple Room",
                        "description":"I think this is a dirty room...",
                        "left":"r1",
                        "items":["rock"]
                    }
                ],
                "items":[
                    {
                        "item":"rock", 
                        "description":"A lowly rock looking at you oddly"
                    }
                ],
                "commands":["move", "hello", "look", "clear", "inventory", "get"],
                "moveDirections":["up","down","left","right"],
                "moveText":"Possible Moves",
                "moveError":"There doesn't seem to be anything that way!",
                move: function(t){
                    //Find out the command
                    var cmd = gameLogic._text("move", t);
                    //Find the allowed movement areas.
                    var loc = options.player[0].room;
                    var aLoc = gameLogic.findJSONLocation(options.rooms, 'id', loc);
                    var allowed = gameLogic.findAllowedMoves(aLoc);
                    
                    //This code actually moves the player if the command is within the text
                    if(allowed.indexOf(cmd) != -1){
                        options.player[0].room = options.rooms[aLoc][cmd];
                        options.clear();
                        //Trigger the look command after you move!
                        options.look();
                    } else {
                        //Write the error if they cant do that.
                        gameLogic.writeText(options.moveError);
                    }
                },
                hello: function(t){
                    //Example of how to get a function and write text
                    gameLogic.writeText("Welcome to this game");
                },
                clear: function(t){
                    //Clears the screen  
                    $('.tx-chatSpan').remove();
                },
                look: function(t){
                    var loc = options.player[0].room;
                    var aLoc = gameLogic.findJSONLocation(options.rooms, 'id', loc);
                    gameLogic.writeText('-------------------------------');
                    gameLogic.writeText(options.rooms[aLoc].name);
                    gameLogic.writeText('-------------------------------');
                    gameLogic.writeText(options.rooms[aLoc].description);
                    //Find the allowed moves
                    var allowed = gameLogic.findAllowedMoves(aLoc);
                    gameLogic.displayMoveLocal(allowed);
                    gameLogic.displayInventoryLocal(aLoc);
                },
                help: function(t){
                    //Help text
                },
                inventory: function(t){
                    //Look at your inventory
                    gameLogic.writeText("Your Inventory");
                    gameLogic.writeText("---------------");
                    for(var x=0; x <= options.player[0].inventory.length - 1; x++){
                        var outText;
                        var id = gameLogic.findJSONLocation(options.items, 'item', options.player[0].inventory[x]);
                        outText = options.player[0].inventory[x] + " : " + options.items[id].description;
                        gameLogic.writeText(outText);
                    }
                },
                get: function(t){
                    var cmd = gameLogic._text("get", t);
                    
                    //Does this item exist here?
                    var aLoc = gameLogic.findJSONLocation(options.rooms.items, 'items', cmd);
                    alert(aLoc);
                    
                }
            
            };
            
            
            //Game logic holds the logic for the game... ;)
            var gameLogic = {
                //Finds the allowed moves for a user at a location.
                findAllowedMoves: function(loc){
                    var allowedMoves = [];
                    for(var x in options.moveDirections){
                        if(options.rooms[loc][options.moveDirections[x]]){
                            Array.push(allowedMoves, options.moveDirections[x]);
                        }
                    }
                    return allowedMoves;                    
                },
                //Displays the avialible moves for a user at a location.
                displayMoveLocal: function(l){
                    gameLogic.writeText(options.moveText);
                    var outText = "";
                    for(var x in l){
                        outText += l[x] + " ";
                    }
                
                    gameLogic.writeText(outText);
                },
                //finds the location of an item in an obj.
                findJSONLocation: function(obj, idlookup, lookup){
                    var l = 0;
                
                    for(var x in obj){
                        if(obj[x][idlookup] == lookup){
                            l=x;
                        }
                    }
              
                    return l;
                },
                //Write text to the 'screen
                writeText: function(text){
                    cText++;
                    var id = 'chatText'+cText;
                    var span = '<span class="tx-chatSpan" id="chatText'+cText+'"><span id="userHolder" class="tx-UserHolder">>  </span>'+ text +'</span>'
                    $("#chatlocation").append(span);
                
                    //Get the newest position and scroll to it.
                    var t = $("#" + id).position(); 
                    $("#chatlocation").scrollTop(t.top);
                    utility.divToEnd("chatlocation");
                },
                //Find text after a command.
                _text: function(cmd, str){
                    var l = cmd.length;
                    var text = $.trim(str.substr(l));
                    return text;
                },
                //Sees if the first word is a legal command.
                testText: function(t){
                    t =  t.toLowerCase();
                    for(var x in options.commands){
                        //Looks for a command
                        if (t.indexOf(options.commands[x]) == 0){
                            //Calls related command.
                            options[options.commands[x]](t);   
                        }
                    }
                },
                displayInventoryLocal: function(t){
                    if(!options.rooms[t].items){}
                    else {
                    gameLogic.writeText("");
                    gameLogic.writeText("Items in this location");
                    gameLogic.writeText("----------------------");
                    for(x in options.rooms[t].items){
                        gameLogic.writeText(options.rooms[t].items[x]);
                        
                    }
                }
                    
                    
            }
               
        };
            
        //Utility functions on the page.
        var utility = {
            divToEnd: function(divId){
                var theDiv = document.getElementById( divId );

                if( theDiv )
                    theDiv.scrollTop = theDiv.scrollHeight;
            }
               
        };
            
            
        //Main callback
        function chat(text){
            var text = $("#chatBox").val();
            $("#chatBox").val('');
            $("#chatBox").focus();
            gameLogic.writeText(text);

            //Evaluate it for future action
            gameLogic.testText(text);
        }           
            
        </script>
    </head>
    <body>
        <div class="tx-Holder">
            <div id="chatlocation" class="tx-chatHolder"></div>
            <div id ="inputLocation" class="tx-userInput">
                <input type ="text" id="chatBox" class="tx-ChatBox">
                <button id="chatSend" class="tx-chatSender" onClick ="chat();">Send</button>
            </div>
        </div>
    </body>
</html>
