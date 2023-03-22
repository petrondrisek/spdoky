$("[name=search]").keyup(function(e){
    if($(this).val().length < 2){
        $(".resultArea").removeClass("active").html("");
        return;
    }

    if($(this).val().length === 2){
        $(".resultArea").html("Vyhledávání");
    }

    $.ajax({
        url: "/detail/whisperer?like="+$(this).val(),
        success: (data) => {
            if(data.length === 0){
                $(".resultArea").html("Žádné výsledky");
                return;
            }
            $(".resultArea").html("");
            for(var i = 0; i < data.length; i ++){
                $(".resultArea").append("<li class=\"resultArea__item\" onclick=\"detail(this)\" data-identifier=\""+data[i].title.replaceAll(" ", "+")+"\"><img height=\"50px\" src=\"/assets/cars/"+data[i].title+".webp\" alt=\"Small IMG\"> "+data[i].title+"</li>");
            }
        },
        error: (error) => {
            console.error(error);
        }
    });

    $(".resultArea").addClass("active");
});

function detail(element){
    $.ajax({
        url: '/detail/detail?title='+$(element).attr("data-identifier"),
        success: (data) => {
            if(data.length === 0) return;
            $(".resultContent").html("");
            $(".resultArea").html("").removeClass("active");

            $(".resultContent").append("<h1 class=\"text-center mt-3 h1-lines\"><img src=\"/assets/cars/"+data[0].title+".webp\" alt=\"Small image\" height=\"50px\"> "+data[0].title.split(" - ")[1]+"</h1>");
            $(".resultContent").append("<div class=\"resultContent__map text-center mt-3 mb-3\"><img src=\"/assets/map_clear.webp\" width=\"100%\" alt=\"Mapa\"></div>");
            
            for (var i = 0; i < data.length; i++){
                var vehicleData = JSON.parse(data[i].data);
                console.log(vehicleData);
                if(vehicleData.map_top === undefined || vehicleData.map_left === undefined || vehicleData.image === undefined){
                    console.warn("Ignorováno ID "+i+", neobsahuje potřebné informace ("+vehicleData.map_top+", "+vehicleData.map_left+", "+vehicleData.image+")");
                    continue;
                }

                $(".resultContent__map").append("<div class=\"map-pointer\" style=\"top: "+vehicleData.map_top+"%; left: "+vehicleData.map_left+"%\"></div>");
                $(".resultContent").append("<div class\"vehicle-about mt-3\"><h2 class=\"mt-1 mb-1\">Lokace #"+data[i].id+"</h2><p>"+(vehicleData.desc !== "" ? "<span class=\"font-weight-bold\">Poznámka:</span> "+vehicleData.desc : "")+"</p><img src=\""+vehicleData.image+"\" width=\"100%\" class=\"mb-3\" alt=\"Obrazek lokace s vozidlem\"></div>");
            }
            console.log(data);
        },
        error: (error) => {
            console.error(error);
        }
    });
}

function addVehicle(element, e){
    $(".resultContent").html("<form enctype=\"multipart/form-data\" method=\"post\" id=\"addVehicle\"></html>");
    //Nadpis
    $("#addVehicle").append("<h1 class=\"mt-3 h1-lines text-center\">Přidávání vozidla</h1>");

    //Informace o vozidlu
    $("#addVehicle").append("<input type=\"number\" class=\"text-input mb-2\" name=\"vehicleID\" placeholder=\"ID vozidla\"><input type=\"text\" class=\"text-input mb-2\" name=\"vehicleName\" placeholder=\"Název vozidla\">");
    //Popis lokace
    $("#addVehicle").append("<input type=\"text\" class=\"text-input mb-2\" name=\"vehicleDesc\" placeholder=\"Krátký popis lokace, např. část / ulice\">");
    
    //fotky
    $("#addVehicle").append("<div class=\"mt-2 mb-2\"><label for=fotka>Fotka lokace s vozidlem: </label><input type=\"file\" accept=\"image/webp\" name=\"vehicleImage\" id=\"Fotka\"></div>");

    //Mapa
    $("#addVehicle").append("<p>Vyberte prosím lokaci, kde se vozidlo na mapě nachází.</p><div class=\"resultContent__map text-center mt-3\"><img src=\"/assets/map_clear.webp\" onclick=\"setPointer(this, event)\" width=\"100%\" alt=\"Mapa\"><div data-top=0 data-left=0 class=\"map-pointer\" style=\"top: 0; left: 0;\"></div></div><input type=\"hidden\" name=\"mapLeft\"><input type=\"hidden\" name=\"mapTop\">");

    $("#addVehicle").append("<input type=\"submit\" class=\"btn btn-warning mt-3 w-100\" value=\"Přidat vozidlo\"><script>addVehicleForm();</script>");
}

function setPointer(element, e){
    var parentOffset = $(element).parent().offset();
    
    //Left
    var relX = e.pageX - parentOffset.left;
    var percX = relX / $(element).width() * 100;

    //Top
    var relY = e.pageY - parentOffset.top;
    var percY = relY / $(element).height() * 100;

    $(".map-pointer").css({'top':percY+"%", 'left':percX+"%"}).attr({'data-top':percY, 'data-left':percX});
    $("[name=mapLeft]").val(percX);
    $("[name=mapTop").val(percY);
}

function addVehicleForm(){
    var blockAdd = false;
    $("#addVehicle").submit(function(e){
        e.preventDefault();

        if(blockAdd === true){
            $("body").append("<div class=\"flash flash-addVehicle\">Vyčkejte prosím, odfiltrováno jako spam.</div>");
            setTimeout(()=>{ $(".flash-addVehicle").remove(); }, 2000);
            return;
        }

        var formData = new FormData(this);
        formData.append("vehicleIMG", document.querySelector("[name=vehicleImage]").files.length > 0 ? document.querySelector("[name=vehicleImage]").files[0] : []);
        
        $("body").append("<div class=\"flash flash-addVehicle\">Odesílání dat ...</div>");

        blockAdd = true;

        $.ajax({
            type: 'POST',
            url: '/detail/addnew',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $(".flash-addVehicle").remove();
                $("body").append("<div class=\"flash flash-addVehicle\">"+data["message"]+"</div>");
                setTimeout(()=>{ $(".flash-addVehicle").remove(); }, 2000);
                if(data["success"] === 1) $(".resultContent").html("");
                blockAdd = false;
            }
        }
    );
    });
}