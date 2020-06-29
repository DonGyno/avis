
require('../css/custom.css');
require('../scss/color-monpro.scss');
require('materialize-css/sass/materialize.scss');

const $ = require('jquery');

const materialize = require('materialize-css');

$(document).ready(function () {
    materialize.AutoInit();
});

$(document).ready(function () {
    if($('tbody input.filled-in').is(':checked'))
    {
        $("a#btn_export").removeClass('disabled');
    }
    else
    {
        $("a#btn_export").addClass('disabled');
    }

    $("body").on('click',"input#select_all",function(){
        if($("input#select_all").is(':checked'))
        {
            $('tbody input.filled-in').prop('checked',true);
            $("a#btn_export").removeClass('disabled');
        }
        else
        {
            $('tbody input.filled-in').prop('checked',false);
            $("a#btn_export").addClass('disabled');
        }
    });

    $("body").on('change',"tbody input.filled-in",function(){
        if($('tbody input.filled-in').is(':checked'))
        {
            $("a#btn_export").removeClass('disabled');
        }
        else
        {
            $("a#btn_export").addClass('disabled');
        }
    });

    $("body").on('click',"a#btn_export",function(e){
        e.preventDefault();
        const checkbox_avis = $('tbody input.filled-in');
        const array_id = [];
        $.each(checkbox_avis, function (key, valeur) {
            if($(this).is(':checked'))
            {
                array_id.push(valeur.value)
            }
        });

        const path = $("a#btn_export").attr("data-path");
        console.log(path);
        $.ajax({
            type: "POST",
            url: path,
            data: { array_id: array_id },
            dataType: "json"
        });
    });
});