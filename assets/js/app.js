
require('../css/custom.css');
require('../scss/color-monpro.scss');
require('materialize-css/sass/materialize.scss');
import('core-js');

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

    const fixe_fiche_user = $('input#fiche_prospection_user_telephoneFixeEntreprise');
    const portable_fiche_user = $('input#fiche_prospection_user_telephonePortableEntreprise');
    $("body").on('change',"form[name=fiche_prospection_user]",function(){
        if(fixe_fiche_user.val() !== "")
        {
            portable_fiche_user.removeAttr('required');
            verifTel(fixe_fiche_user);
        }
        else
        {
            portable_fiche_user.attr('required',true);
        }

        if(portable_fiche_user.val() !== "")
        {
            fixe_fiche_user.removeAttr('required');
            verifTel(portable_fiche_user);
        }
        else
        {
            fixe_fiche_user.attr('required',true);
        }

        if(portable_fiche_user.val() === "" && fixe_fiche_user.val() === "")
        {
            fixe_fiche_user.attr('required',true);
            portable_fiche_user.attr('required',true);
        }
    });
    fixe_fiche_user.on('focusout',function () {
        verifTel(fixe_fiche_user);
    });
    portable_fiche_user.on('focusout',function () {
        verifTel(portable_fiche_user);
    });

    $('a.change_responsable').on('click', function (e) {
        e.preventDefault();
        const id_fiche = $(this).data('identification');
        const path_users = $(this).data('path');
        $.ajax({
            type: "POST",
            url: path_users
        }).done(function (response) {
            /* Partie changement actions */
            const valider_action = $('<a href="" class="btn-floating monpro btn_responsable valider_action"><i class="material-icons large" title="Valider">done</i></a>').hide();
            const annuler_action = $('<a href="" class="btn-floating monpro btn_responsable"><i class="material-icons large" title="Annuler">close</i></a>').hide();
            const current_value_user = $("td#responsable_"+id_fiche).html().trim();
            $("td#actions_"+id_fiche+" > a").hide(200);
            $("td#actions_"+id_fiche).append(valider_action).append(annuler_action);
            valider_action.show(200);annuler_action.show(200);
            annuler_action.on('click',function(e){
                e.preventDefault();
                $("td#actions_"+id_fiche+" > a.btn_responsable").hide(200, function(){
                    $(this).remove();
                    $("td#actions_"+id_fiche+" > a").show(200);
                    $("td#responsable_"+id_fiche+" form").hide(200,function(){
                        $("td#responsable_"+id_fiche).html('').append(current_value_user);
                        current_value_user.show(200);
                    });
                });
            });
            /* ---------------------------------- */
            /* Partie Création select users */
            const array_users = $.parseJSON(response.users);
            const select_users = '<form><div class="input-field col s12"><select id="select_responsable_'+id_fiche+'"><option value="" disabled selected>Choisir un responsable</option><option value="NULL">Aucun responsable</option></select><label>Responsable</label></div></form>';
            $("td#responsable_"+id_fiche).html(select_users);
            $.each(array_users, function(key, value){
                const id_user = value.id;
                const nom_user = value.nom;
                const prenom_user = value.prenom;
                const option_select = '<option value="'+id_user+'">'+prenom_user+' '+nom_user+'</option>';
                $("table td select#select_responsable_"+id_fiche).append(option_select);
            });
            materialize.FormSelect.init($("select#select_responsable_"+id_fiche));
            /* ---------------------------------- */
            /* Partie Validation du choix */
            $("td#actions_"+id_fiche+" a.btn_responsable.valider_action").on('click', function(e){
                e.preventDefault();
                /* Verification si le select à une valeur autre que vide */
                const val_select_responsable = $('select#select_responsable_'+id_fiche).find(":selected").val();
                const text_select_responsable = $('select#select_responsable_'+id_fiche).find(":selected").text();
                if(val_select_responsable === "")
                {
                    const div_flash = '<div class="notifications flash-error p-2 center-align">Attention ! Veuillez sélectionner une option valide dans la liste déroulante !</div>';
                    $('main').prepend(div_flash);
                    $('main > div.notifications').delay(5000).fadeOut(1000);
                }
                else
                {
                    $.ajax({
                        type: "POST",
                        url: "/admin/modification-responsable",
                        data: {'id_fiche':id_fiche,'id_responsable':val_select_responsable},
                        dataType: "json"
                    }).done(function(){
                        $("td#actions_"+id_fiche+" > a.btn_responsable").hide(200, function(){
                            $(this).remove();
                            $("td#actions_"+id_fiche+" > a").show(200);
                            $("td#responsable_"+id_fiche+" form").hide(200,function(){
                                $("td#responsable_"+id_fiche).html('').append(text_select_responsable);
                                text_select_responsable.show(200);
                            });
                        });
                        const div_flash = '<div class="notifications flash-success p-2 center-align">Modification du responsable réussie !</div>';
                        $('main').prepend(div_flash);
                        $('main > div.notifications').delay(3000).fadeOut(1000);
                    });
                }
            });
            /* ---------------------------------- */
        });
    });
});
