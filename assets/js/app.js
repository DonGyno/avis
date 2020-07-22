
require('../css/custom.css');
require('../scss/color-monpro.scss');
require('materialize-css/sass/materialize.scss');

const $ = require('jquery');

const materialize = require('materialize-css');

function verifTel(selector)
{
    const regex = /^([0-9]{2}[\s\.])([0-9]{2}[\s\.]){3}([0-9]{2})/gm;
    test = regex.test(selector.val());
    if(test === false)
    {
        const msg = "<div class=\"notifications flash-error p-2 center-align\">Attention la syntaxe du numéro de téléphone n'est pas correcte !</div>";
        $('main').prepend(msg);
        $('main > div.notifications').delay(5000).fadeOut(1000);
    }
}

$(document).ready(function () {
    materialize.AutoInit();
});

document.addEventListener('DOMContentLoaded', function () {
    var textNeedCount = document.querySelectorAll('textarea#enquete_recommanderCommentaireAEntreprise');
    materialize.CharacterCounter.init(textNeedCount);
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
        $.ajax({
            type: "POST",
            url: path,
            data: { array_id: array_id },
            dataType: "json"
        }).always(function () {
            const div_flash = '<div class="notifications flash-success p-2 center-align">Export Réussie avec succès ! Le téléchargement du fichier est immiment !</div>';
            $('main').prepend(div_flash);
            $('main > div.notifications').delay(5000).fadeOut(1000);
        });
    });

    const fixe = $('input#entreprise_telephoneFixe');
    const portable = $('input#entreprise_telephonePortable');
    $("body").on('change',"form[name=entreprise]",function(){
        if(fixe.val() !== "")
        {
            portable.removeAttr('required');
            verifTel(fixe);
        }
        else
        {
            portable.attr('required',true);
        }

        if(portable.val() !== "")
        {
            fixe.removeAttr('required');
            verifTel(portable);
        }
        else
        {
            fixe.attr('required',true);
        }

        if(portable.val() === "" && fixe.val() === "")
        {
            fixe.attr('required',true);
            portable.attr('required',true);
        }
    });
    fixe.on('focusout',function () {
        verifTel(fixe);
    });
    portable.on('focusout',function () {
        verifTel(portable);
    });

    const input_telephone_dest = $("input#enquete_telephoneDestinataire");
    input_telephone_dest.parent().hide();

    const checkbox_temoignage = $("input#enquete_temoignageVideo_1");
    $("body").on('change',"form[name=enquete]", function () {
        if(checkbox_temoignage.is(':checked'))
        {
            input_telephone_dest.parent().show(1000, function () {
                input_telephone_dest.parent().parent().addClass('flex-end');
            });
        }
        else
        {
            input_telephone_dest.parent().hide(1000);
            input_telephone_dest.parent().parent().removeClass('flex-end');
        }
    });


    const fixe_fiche = $('input#fiche_prospection_telephoneFixeEntreprise');
    const portable_fiche = $('input#fiche_prospection_telephonePortableEntreprise');
    $("body").on('change',"form[name=fiche_prospection]",function(){
        if(fixe_fiche.val() !== "")
        {
            portable_fiche.removeAttr('required');
            verifTel(fixe_fiche);
        }
        else
        {
            portable_fiche.attr('required',true);
        }

        if(portable_fiche.val() !== "")
        {
            fixe_fiche.removeAttr('required');
            verifTel(portable_fiche);
        }
        else
        {
            fixe_fiche.attr('required',true);
        }

        if(portable_fiche.val() === "" && fixe_fiche.val() === "")
        {
            fixe_fiche.attr('required',true);
            portable_fiche.attr('required',true);
        }
    });
    fixe_fiche.on('focusout',function () {
        verifTel(fixe_fiche);
    });
    portable_fiche.on('focusout',function () {
        verifTel(portable_fiche);
    });

});
