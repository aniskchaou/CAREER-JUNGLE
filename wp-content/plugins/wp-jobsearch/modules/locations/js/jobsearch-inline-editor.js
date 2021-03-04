"use strict";
//Global variables
var $ = jQuery;
var params = null;  		//Parameters
var colsEdi = null;
var newColHtml = '<div class="jobsearch-btn-group">' +
    '<button id="bEdit" type="button"  onclick="rowEdit(this);">' +
    '<i class="dashicons dashicons-edit"></i>' +
    '</button>' +
    '<button id="bElim" type="button"   onclick="rowElim(this);">' +
    '<i class="dashicons dashicons-trash" aria-hidden="true"></i>' +
    '</button>' +
    '<button id="bAcep" type="button"  class="save-check" style="display:none;" onclick="rowAcep(this);">' +
    '<i class="dashicons dashicons-yes"></i>' +
    '</button>' +
    '<button id="bCanc" type="button" class="btn btn-sm btn-default" style="display:none;"  onclick="rowCancel(this);">' +
    '<i class="dashicons dashicons-dismiss" aria-hidden="true"></i>' +
    '</button>' +
    '</div>';

var saveColHtml = '<div class="jobsearch-btn-group">' +
    '<button id="bEdit" type="button"  style="display:none;" onclick="rowEdit(this);">' +
    '<i class="dashicons dashicons-edit"></i>' +
    '</button>' +
    '<button id="bElim" type="button"  style="display:none;" onclick="rowElim(this);">' +
    '<i class="dashicons dashicons-trash" aria-hidden="true"></i>' +
    '</button>' +
    '<button id="bAcep" type="button" class="save-check"   onclick="rowAcep(this);">' +
    '<i class="dashicons dashicons-yes"></i>' +
    '</button>' +
    '<button id="bCanc" type="button"  onclick="rowCancel(this);">' +
    '<i class="dashicons dashicons-dismiss" aria-hidden="true"></i>' +
    '</button>' +
    '</div>';

var colEdicHtml = '<td name="buttons">' + newColHtml + '</td>';
var colSaveHtml = '<td name="buttons">' + saveColHtml + '</td>';


$.fn.SetEditable = function (options) {

    var defaults = {
        columnsEd: null,         //Index to editable columns. If null all td editables. Ex.: "1,2,3,4,5"
        $addButton: null,        //Jquery object of "Add" button
        onEdit: function () {
        },   //Called after edition
        onBeforeDelete: function () {
        }, //Called before deletion
        onDelete: function () {
        }, //Called after deletion
        onAdd: function () {
        }     //Called when added a new row
    };
    params = $.extend(defaults, options);

    this.find('thead tr').append('<th name="buttons"></th>');  //encabezado vacío
    this.find('tbody tr').append(colEdicHtml);
    var $tabedi = this;   //Read reference to the current table, to resolve "this" here.
    //Process "addButton" parameter
    if (params.$addButton != null) {
        //Se proporcionó parámetro
        params.$addButton.click(function () {
            rowAddNew($tabedi.attr("id"));
        });
    }
    //Process "columnsEd" parameter
    if (params.columnsEd != null) {
        //Extract felds
        colsEdi = params.columnsEd.split(',');
    }
};

function IterarCamposEdit($cols, tarea) {
//Itera por los campos editables de una fila
    var n = 0;
    $cols.each(function () {
        n++;
        if ($(this).attr('name') == 'buttons') return;  //excluye columna de botones
        if (!EsEditable(n - 1)) return;   //noe s campo editable
        tarea($(this));
    });

    function EsEditable(idx) {
        //Indica si la columna pasada está configurada para ser editable
        if (colsEdi == null) {  //no se definió
            return true;  //todas son editable
        } else {  //hay filtro de campos
//alert('verificando: ' + idx);
            for (var i = 0; i < colsEdi.length; i++) {
                if (idx == colsEdi[i]) return true;
            }
            return false;  //no se encontró
        }
    }
}

function FijModoNormal(but) {
    $(but).parent().find('#bAcep').hide();
    $(but).parent().find('#bCanc').hide();
    $(but).parent().find('#bEdit').show();
    $(but).parent().find('#bElim').show();
    var $row = $(but).parents('tr');  //accede a la fila
    $row.removeClass('editing');  //quita marca
    //$row.attr('class', '');  //quita marca
}

function FijModoEdit(but) {

    $(but).parent().find('#bAcep').show();
    $(but).parent().find('#bCanc').show();
    $(but).parent().find('#bEdit').hide();
    $(but).parent().find('#bElim').hide();
    var $row = $(but).parents('tr');
    $row.addClass('editing');
    //$row.attr('class', 'editing');
}

function ModoEdicion($row) {
    if ($row.hasClass('editing')) {
        return true;
    } else {
        return false;
    }
}

var _html = '<span class="file-loader"><i class="fa fa-refresh fa-spin"></i></span>';

function rowAcep(but) {
    var $row = $(but).parents('tr');  //accede a la fila
    var $tablename = $(but).parents('table').attr('class');
    var $cols = $row.find('td.editable');  //lee campos
    if (!ModoEdicion($row)) return;  //Ya está en edición
    var _flag = true;
    var _counter = 0;
    IterarCamposEdit($cols, function ($td) {
        var cont = $td.find('input').val();
        if (_counter == 0) {
            if (cont == '') {
                _flag = false;
                $row.addClass('loc-error');
            } else {
                _flag = true;
                $row.removeClass('loc-error');
            }
        }

        if (_counter == 1 && $tablename == 'table country-table-detail' && _flag == true) {
            if (cont == '') {
                _flag = false;
                $row.addClass('loc-error');
                alert(jobsearch_location_common_text.req_cntry);
            } else if (isNaN(cont) == false) {
                _flag = false;
                $row.addClass('loc-error');
                alert(jobsearch_location_common_text.req_num)
            } else if (cont.length > 3) {
                _flag = false;
                $row.addClass('loc-error');
                alert(jobsearch_location_common_text.req_chars);
            } else {
                _flag = true;
                $row.removeClass('loc-error');

            }

            if (cont != cont.toUpperCase()) {
                _flag = false;
                $row.addClass('loc-error');
                alert(jobsearch_location_common_text.req_cntry_code_uppercase)
            }

            if (_flag == true) {
                var request = jQuery.ajax({
                    url: ajaxurl,
                    method: "POST",
                    data: {
                        country_code: cont.toUpperCase(),
                        action: 'jobsearch_check_state_dir',
                    },
                    dataType: "json"
                });
                request.done(function (response) {
                    if ('undefined' !== typeof response.country_code) {
                        _flag = false;
                        $row.addClass('loc-error');
                        alert("Country code '" + response.country_code + "'  already exists.")
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    //alert(textStatus)
                });
            }
        }

        if (_flag == true) {
            if (_counter == 2 && $tablename == 'table country-table-detail') {
                if (isNaN(cont) == true || cont == '') {
                    alert(jobsearch_location_common_text.req_poplation);
                    $row.addClass('loc-error');
                } else {
                    $row.removeClass('loc-error')
                }
            }
        }
        $td.html(cont);
        _counter++;
    });
    if ($row.hasClass('loc-error')) {
        if ($tablename == 'table country-table-detail') {
            jQuery('#submit_country_detail').prop('disabled', true);
            jQuery('#submit_country_detail').addClass('loc-disabled');
        } else if ($tablename == 'table state-table-detail') {
            jQuery('#submit_states_detail').prop('disabled', true);
            jQuery('#submit_states_detail').addClass('loc-disabled');
        } else if ($tablename == 'table cities-table-detail') {
            jQuery('#submit_cities_detail').prop('disabled', true);
            jQuery('#submit_cities_detail').addClass('loc-disabled');
        }
    } else {
        if ($tablename == 'table country-table-detail') {
            jQuery('#submit_country_detail').prop('disabled', false);
            jQuery('#submit_country_detail').removeClass('loc-disabled');
        } else if ($tablename == 'table state-table-detail') {
            jQuery('#submit_states_detail').prop('disabled', false);
            jQuery('#submit_states_detail').removeClass('loc-disabled');
        } else if ($tablename == 'table cities-table-detail') {
            jQuery('#submit_cities_detail').prop('disabled', false);
            jQuery('#submit_cities_detail').removeClass('loc-disabled');
        }
    }
    FijModoNormal(but);
    params.onEdit($row);
}

function rowCancel(but) {
//Rechaza los cambios de la edición
    var $row = $(but).parents('tr');  //accede a la fila
    var $cols = $row.find('td');  //lee campos
    if (!ModoEdicion($row)) return;  //Ya está en edición
    //Está en edición. Hay que finalizar la edición
    IterarCamposEdit($cols, function ($td) {  //itera por la columnas
        var cont = $td.find('div').html(); //lee contenido del div
        $td.html(cont);  //fija contenido y elimina controles
    });
    FijModoNormal(but);
}

function rowEdit(but) {

    var $td = $("tr[class='editing'] td.editable");
    rowAcep($td);
    var $row = $(but).parents('tr');
    var $cols = $row.find('td.editable');
    $row.addClass('new-row');
    if (ModoEdicion($row)) return;

    IterarCamposEdit($cols, function ($td) {
        var cont = $td.html();
        var div = '<div style="display: none;">' + cont + '</div>';
        var input = '<input  class="form-control input-sm"  value="' + cont + '">';
        $td.html(div + input);
    });
    //
    FijModoEdit(but);
}

function rowElim(but, data_id = '', country_id = '') {  //Elimina la fila actual
    var $row = $(but).parents('tr');  //accede a la fila
    var $tablename = $(but).parents('table').attr('class');

    if ($tablename == 'table country-table-detail') {
        if (data_id != '') {
            jQuery.ajax({
                url: jobsearch_plugin_vars.ajax_url,
                method: "POST",
                data: {
                    cntry_id: data_id,
                    action: 'jobsearch_delete_country',
                },
                dataType: 'json',
                success: function (res) {
                    location.reload(true);

                }
            });
        }
        jQuery('#submit_country_detail').prop('disabled', false);
        jQuery('#submit_country_detail').removeClass('loc-disabled');
    } else if ($tablename == 'table state-table-detail') {
        if (data_id != '') {
            jQuery.ajax({
                url: jobsearch_plugin_vars.ajax_url,
                method: "POST",
                data: {
                    state_id: data_id,
                    action: 'jobsearch_delete_state',
                },
                dataType: 'json',
                success: function (res) {
                    location.reload(true);

                }
            });
        }
        jQuery('#submit_states_detail').prop('disabled', false);
        jQuery('#submit_states_detail').removeClass('loc-disabled');
    } else if ($tablename == 'table cities-table-detail') {
        if (data_id != '') {

            jQuery.ajax({
                url: jobsearch_plugin_vars.ajax_url,
                method: "POST",
                data: {
                    city_id: data_id,
                    action: 'jobsearch_delete_city',
                },
                dataType: 'json',
                success: function (res) {
                    location.reload(true);
                }
            });
        }
        jQuery('#submit_cities_detail').prop('disabled', false);
        jQuery('#submit_cities_detail').removeClass('loc-disabled');
    }
    params.onBeforeDelete($row);
    $row.remove();
    params.onDelete();
}

function rowAddNew(tabId) {
    var $tab_en_edic = $("#" + tabId);//Table to edit
    var $filas = $tab_en_edic.find('tbody tr');

    if ($filas.length == 0) {
        var $row = $tab_en_edic.find('thead tr');
        var $cols = $row.find('th');  //lee campos
        //construye html
        var htmlDat = '';
        $cols.each(function (i) {
            console.info(i);
            var _class_editable = i == 1 ? 'editable' : '';
            if ($(this).attr('name') == 'buttons') {
                //Es columna de botones
                htmlDat = htmlDat + colEdicHtml;
            } else {
                htmlDat = htmlDat + '<td class="'+_class_editable+'"></td>';
            }
        });

        $tab_en_edic.find('tbody').append('<tr class="new-row">' + htmlDat + '</tr>');

    } else {

        var $ultFila = $tab_en_edic.find('tr:last');
        $ultFila.clone().appendTo($ultFila.parent());
        $tab_en_edic.find('tr:last').addClass('editing new-row');
        $ultFila = $tab_en_edic.find('tr:last');
        var $cols = $ultFila.find('td.editable');
        $cols.each(function () {
            if ($(this).attr('name') == 'buttons') {

            } else {
                var div = '<div style="display: none;"></div>';
                var input = '<input  class="form-control input-sm"  value="">';
                jQuery(this).html(div + input);
            }
        });

        $ultFila.find('td:last').html(saveColHtml);
    }
    params.onAdd();

    jQuery(document).find('.editing').each(function () {
        jQuery(this).find("input[type=checkbox]").first().remove();
        jQuery(this).find("td:nth-child(3)").text('');
    })
}

function TableToCSV(tabId, separator) {
    var datFil = '';
    var tmp = '';
    var $tab_en_edic = jQuery("#" + tabId);
    $tab_en_edic.find('tbody tr.new-row').each(function () {

        if (ModoEdicion(jQuery(this))) {
            jQuery(this).find('#bAcep').click();  //acepta edición
        }
        var $cols = jQuery(this).find('td');  //lee campos
        datFil = '';
        $cols.each(function () {
            if ($(this).attr('name') == 'buttons') {
            } else {
                datFil = datFil + $(this).html() + separator;
            }
        });
        if (datFil != '') {
            datFil = datFil.substr(0, datFil.length - separator.length);
        }
        tmp = tmp + datFil + '\n';
    });
    return tmp;
}

///////////////// Jobsearch location functions///////////////////////

var $ = jQuery;
jQuery(function () {
    var $ = jQuery, country_id, country_name;
    jQuery('#editor-country').on('change', function () {
        var _this = jQuery(this);
        if (_this.val() != 0) {
            jQuery(".cities-table-detail").find('tbody').html('');
            country_id = _this.val();
            var element = _this.find('option:selected');
            country_name = element.attr('data-country-name');
            editor_functions.readsingleCountryData(country_id);
            jQuery(".state-wrapper").removeClass('loc-hidden');
            jQuery(".cities-wrapper").removeClass('loc-hidden');
            jQuery("#stateId").removeClass('loc-hidden');
            jQuery(".jobsearch-load-states-cities-name").find('h3').text('');
            editor_functions.readSingleCityState(country_id, jQuery('.state-table'));
            jQuery('.jobsearch-load-state-name').find('h3').html(country_name + ", States");
        } else {
            jQuery(".jobsearch-load-state-name").find('h3').text('');
            jQuery(".jobsearch-load-states-cities-name").find('h3').text('');
            jQuery(".country-table-detail").find('tbody').html('');
            jQuery(".state-table-detail").find('tbody').html('');
            jQuery(".cities-table").find('tbody').html('');
            jQuery("#stateId").addClass('loc-hidden');
        }
    });

    jQuery(document).on('change', '#editor-state', function () {
        var _this = jQuery(this), state_name, state_id;

        if (_this.val() != 0 && _this.val() != undefined) {
            var element = _this.find('option:selected');
            state_name = element.attr('data-state-name');
            state_id = _this.val();
            editor_functions.readSingleCityStatedata(state_id, jQuery('.cities-table'));
            jQuery('.jobsearch-load-states-cities-name').find('h3').html(state_name + ", Cities")
        } else {
            jQuery(".jobsearch-load-states-cities-name").find('h3').text('');
            jQuery(".cities-table-detail").find('tbody').html('');
        }
    });
});

var selector;

var editor_functions = {
    readSingleCityStatedata: function (state_id, table_selector) {
        var $ = jQuery, selector = table_selector.find("table tbody");
        jQuery(".jobsearch-loc-city-loader").html('<span class="spinner is-active"></span>');
        jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                state_id: state_id,
                action: 'jobsearch_load_single_state_cities_data',
            },
            dataType: 'json',
            success: function (res) {
                selector.html('');
                jQuery(".jobsearch-loc-city-loader").find('span').remove();
                $.each(res.result, function (index, element) {
                    var _newColHtml = '<tr><td><input type="checkbox" name="jobsearch_loc_all_cities[]"  value="' + element.city_id + '"></td><td class="editable">' + element.city_name + '</td><td style="display: none;">' + element.city_id + '</td>' +
                        '<td name="buttons"><div class="jobsearch-btn-group">' +
                        '<button id="bEdit" type="button"  onclick="rowEdit(this);">' +
                        '<i class="dashicons dashicons-edit"></i>' +
                        '</button>' +
                        '<button id="bElim" type="button"  onclick="rowElim(this,' + element.city_id + ');">' +
                        '<i class="dashicons dashicons-trash" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button id="bAcep" type="button" class="save-check"  style="display:none;" onclick="rowAcep(this);">' +
                        '<i class="dashicons dashicons-yes"></i>' +
                        '</button>' +
                        '<button id="bCanc" type="button" class="btn btn-sm btn-default" style="display:none;"  onclick="rowCancel(this);">' +
                        '<i class="dashicons dashicons-dismiss" aria-hidden="true"></i>' +
                        '</button>' +
                        '</div></td></tr>';
                    selector.append(_newColHtml);
                })

            }
        });
    }, readSingleCityState: function (country_id, table_selector) {
        var $ = jQuery, selector = table_selector.find("table tbody");
        jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                country_id: country_id,
                action: 'jobsearch_load_single_state_data',
            },
            dataType: 'json',
            success: function (res) {
                selector.html('');
                jQuery("#editor-state").html('');
                jQuery("#editor-state").append(jQuery("<option></option>").attr("value", "").text(jobsearch_location_common_text.state_select));
                $.each(res.result, function (index, element) {
                    var _newColHtml = '<tr><td><input type="checkbox" name="jobsearch_loc_all_states[]"  value="' + element.state_id + '"></td><td class="editable">' + element.state_name + '</td><td style="display: none">' + element.state_id + '</td>' +
                        '<td name="buttons"><div class="jobsearch-btn-group">' +
                        '<button id="bEdit" type="button"  onclick="rowEdit(this);">' +
                        '<i class="dashicons dashicons-edit"></i>' +
                        '</button>' +
                        '<button id="bElim" type="button"   onclick="rowElim(this, ' + element.state_id + ' , ' + element.country_id + ');">' +
                        '<i class="dashicons dashicons-trash" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button id="bAcep" type="button" class="save-check"  style="display:none;" onclick="rowAcep(this);">' +
                        '<i class="dashicons dashicons-yes"></i>' +
                        '</button>' +
                        '<button id="bCanc" type="button" class="btn btn-sm btn-default" style="display:none;"  onclick="rowCancel(this);">' +
                        '<i class="dashicons dashicons-dismiss" aria-hidden="true"></i>' +
                        '</button>' +
                        '</div></td></tr>';

                    selector.append(_newColHtml);
                    if (res.result[0].state_name == jobsearch_location_common_text.any_state_text) {
                        jQuery("#editor-state").append(jQuery("<option></option>").attr("value", 0).text(jobsearch_location_common_text.state_text));
                        return;
                    }
                    jQuery("#editor-state").append(jQuery("<option></option>").attr("data-state-name", element.state_name).attr("value", element.state_id).text(element.state_name));
                    //
                })
            }
        });
    },
    UpdateStates: function (country_id, selector) {
        jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                country_id: country_id,
                action: 'jobsearch_load_single_state_data',
            },
            dataType: 'json',
            success: function (res) {
                selector.html('');
                selector.append(jQuery("<option></option>").attr("value", 0).text(jobsearch_location_common_text.state_select));
                $.each(res.result, function (index, element) {
                    selector.append(jQuery("<option></option>").attr("data-state-name", element.state_name).attr("value", element.state_id).text(element.state_name));
                })
            }
        });
    },
    readCountryFileForEditor: function (selector) {

        var request = jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                action: 'jobsearch_location_load_countries_data',
            },
            dataType: "json"
        });
        request.done(function (response) {

            jQuery.each(response, function (index, element) {
                selector.append(jQuery("<option></option>")
                    .attr("data-country-name", element.name)
                    .attr("value", element.cntry_id)
                    .text(element.name));
            });

        });
        request.fail(function (jqXHR, textStatus) {

        });
    },
    readsingleCountryData: function (country_id) {
        var $ = jQuery;
        selector = jQuery(".country-table").find("table tbody");
        jQuery(".jobsearch-loc-state-loader").html('<span class="spinner is-active"></span>');
        jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                country_id: country_id,
                action: 'jobsearch_load_single_country_data',
            },
            dataType: 'json',
            success: function (res) {
                jQuery('.country-table').removeClass('loc-hidden');
                jQuery('.jobsearch-loc-state-loader').find('span').remove();
                $.each(res.result, function (index, element) {
                    var _newColHtml = '<tr><td class="editable">' + element.name + '</td><td class="editable">' + element.code + '</td>' +
                        '<td><div class="jobsearch-btn-group">' +
                        '<button id="bEdit" type="button"  onclick="rowEdit(this);">' +
                        '<i class="dashicons dashicons-edit"></i>' +
                        '</button>' +
                        '<button id="bElim" type="button"   onclick="rowElim(this,' + element.cntry_id + ');">' +
                        '<i class="dashicons dashicons-trash" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button id="bAcep" type="button" class="save-check"  style="display:none;" onclick="rowAcep(this);">' +
                        '<i class="dashicons dashicons-yes"></i>' +
                        '</button>' +
                        '<button id="bCanc" type="button" class="btn btn-sm btn-default" style="display:none;"  onclick="rowCancel(this);">' +
                        '<i class="dashicons dashicons-dismiss" aria-hidden="true"></i>' +
                        '</button>' +
                        '</div></td></tr>';
                    selector.html('');
                    selector.append(_newColHtml);
                });

            }
        });
    }
}

jQuery(document).on('click', '.select-all-states', function () {
    var _this = jQuery(this);
    if (_this.is(':checked')) {
        jQuery('.state-wrapper input[type="checkbox"][name^="jobsearch_loc_all_states[]"]').prop('checked', true);
    } else {
        jQuery('.state-wrapper input[type="checkbox"][name^="jobsearch_loc_all_states[]"]').prop('checked', false);
    }
});

jQuery(document).on('click', '.select-all-cities', function () {
    var _this = jQuery(this);
    if (_this.is(':checked')) {
        jQuery('.cities-wrapper input[type="checkbox"][name^="jobsearch_loc_all_cities[]"]').prop('checked', true);
    } else {
        jQuery('.cities-wrapper input[type="checkbox"][name^="jobsearch_loc_all_cities[]"]').prop('checked', false);
    }
})

jQuery(function () {

    var $ = jQuery, request, ar_lines, each_data_value, td, i;

    ////////// Countries Editable ////////////////////
    jQuery('#makeEditableCountries').SetEditable({$addButton: jQuery('#add_country')});
    jQuery('#submit_country_detail').on('click', function () {

        selector = jQuery('#editor-country');
        td = TableToCSV('makeEditableCountries', ',');
        ar_lines = td.split("\n");
        each_data_value = [];

        for (i = 0; i < ar_lines.length; i++) {
            var _countries_detail = ar_lines[i].split(",");

            if (ar_lines[i] != "") {
                each_data_value.push({
                    "name": _countries_detail[0],
                    "code": _countries_detail[1],
                })
            }
        }

        if (each_data_value.length == 0) {
            alert("No country to add");
            return false;
        }

        jQuery("#submit_country_detail").html(_html);
        selector.html('');
        selector.append('<option>' + jobsearch_location_common_text.pls_wait + '</option>');

        request = jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                countries_list: each_data_value,
                action: 'jobsearch_update_country',
            },
            dataType: "json"
        });
        request.done(function (response) {
            if ('undefined' !== typeof response.status && response.status == 'data_updated') {
                jQuery("#submit_country_detail").remove('span').text(jobsearch_location_common_text.cntry_success);
                setTimeout(function () {
                    jQuery("#submit_country_detail").text('').text(jobsearch_location_common_text.sav_contry);
                    selector.html('');
                    editor_functions.readCountryFileForEditor(selector);
                }, 1500)
            } else {
                jQuery("#submit_country_detail").remove('span').text(jobsearch_location_common_text.sav_contry);
                setTimeout(function () {
                    selector.html('');
                    editor_functions.readCountryFileForEditor(selector);
                    jQuery(".state-wrapper").addClass('loc-hidden');
                    jQuery(".cities-wrapper").addClass('loc-hidden');
                }, 1500)
            }
        });
        request.fail(function (jqXHR, textStatus) {
            //alert(textStatus)
        });

    });
    ////////////////States Editable////////////////////
    jQuery('#makeEditableStates').SetEditable({$addButton: jQuery('#add_state')});
    jQuery('#submit_states_detail').on('click', function () {
        jQuery("#submit_states_detail").html(_html);

        selector = jQuery("#stateId");
        var _country_id = jQuery('#editor-country option:selected').val();

        td = TableToCSV('makeEditableStates', ',');
        if ($.trim(td) == 'Enter Any State') {
            alert(jobsearch_location_common_text.req_state)
            jQuery("#submit_states_detail").remove('span').text(jobsearch_location_common_text.save_states);
            return false;
        }
        ar_lines = td.split("\n");
        each_data_value = [];
        for (i = 0; i < ar_lines.length; i++) {
            var _state_detail = ar_lines[i].split(",");

            if (ar_lines[i] != "") {
                each_data_value.push({
                    "state_name": _state_detail[1],
                    "state_id": _state_detail[2],
                })
            }
        }


        request = jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                states_list: each_data_value,
                country_id: _country_id,
                action: 'jobsearch_add_new_states',
            },
            dataType: "json"
        });
        request.done(function (response) {
            if ('undefined' !== typeof response.status && response.status == 'data_updated') {
                jQuery("#submit_states_detail").text('').text(jobsearch_location_common_text.state_success);
                setTimeout(function () {
                    jQuery("#submit_states_detail").remove('span').text(jobsearch_location_common_text.save_states);
                    selector.html('');
                    editor_functions.UpdateStates(_country_id, jQuery('#editor-state'));
                }, 1600)
            }
        });
        request.fail(function (jqXHR, textStatus) {

        });
    });

    jQuery('.jobsearch-loc-delete-states').on('click', function () {

        var jobsearch_state_ids = [];
        jQuery.each(jQuery(".state-wrapper input[name='jobsearch_loc_all_states[]']:checked"), function () {
            jobsearch_state_ids.push(jQuery(this).val());
        });

        if (jobsearch_state_ids.length == 0) {
            alert(jobsearch_location_common_text.state_select);
            return false;
        }
        //
        request = jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                jobsearch_state_ids: jobsearch_state_ids,
                action: 'jobsearch_delete_states',
            },
            dataType: "json"
        });
        request.done(function (response) {
            if ('undefined' !== typeof response.status && response.status == 'data_deleted') {
                location.reload(true);
            }
        });
        request.fail(function (jqXHR, textStatus) {

        });
    });
    ////////////////Cities Editable////////////////////
    jQuery('#makeEditableCities').SetEditable({$addButton: jQuery('#add_cities')});

    jQuery('#submit_cities_detail').on('click', function () {
        jQuery("#submit_cities_detail").html(_html);
        var _state_id = jQuery("#editor-state option:selected").val();
        var _country_id = jQuery("#editor-country option:selected").val();
        td = TableToCSV('makeEditableCities', ',');
        if (jQuery.trim(td) == 'Enter Any City') {
            alert(jobsearch_location_common_text.req_city);
            jQuery("#submit_cities_detail").remove('span').text(jobsearch_location_common_text.sav_city);
            return false;
        }
        ar_lines = td.split("\n");
        each_data_value = [];
        for (i = 0; i < ar_lines.length; i++) {
            var _cities_detail = ar_lines[i].split(",");
            if (ar_lines[i] != '') {
                each_data_value.push({
                    "city_name": _cities_detail[1],
                    "city_id": _cities_detail[2],
                })
            }
        }

        request = jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                cities_list: each_data_value,
                state_id: _state_id,
                country_id: _country_id,
                action: 'jobsearch_add_new_cities',
            },
            dataType: "json"
        });
        request.done(function (response) {
            if ('undefined' !== typeof response.status && response.status == 'data_updated') {
                jQuery("#submit_cities_detail").remove('span').text(jobsearch_location_common_text.city_success);

                setTimeout(function () {
                    jQuery("#submit_cities_detail").remove('span').text(jobsearch_location_common_text.sav_city);
                }, 1600)
            }
        });
        request.fail(function (jqXHR, textStatus) {

        });
    });

    jQuery('.jobsearch-loc-delete-cities').on('click', function () {

        var jobsearch_city_ids = [];
        jQuery.each(jQuery(".cities-wrapper input[name='jobsearch_loc_all_cities[]']:checked"), function () {
            jobsearch_city_ids.push(jQuery(this).val());
        });
        //
        if (jobsearch_city_ids.length == 0) {
            alert(jobsearch_location_common_text.state_city);
            return false;
        }
        request = jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                jobsearch_city_ids: jobsearch_city_ids,
                action: 'jobsearch_delete_cities',
            },
            dataType: "json"
        });
        request.done(function (response) {
            if ('undefined' !== typeof response.status && response.status == 'data_deleted') {
                location.reload(true);
            }
        });
        request.fail(function (jqXHR, textStatus) {

        });
    });
    jQuery('.jobsearch-loc-reset-data').on('click', function () {
        var r = confirm(jobsearch_location_common_text.reset_data);
        if (r == true) {
            request = jQuery.ajax({
                url: ajaxurl,
                method: "POST",
                data: {
                    action: 'jobsearch_locations_reset_data',
                },
                dataType: "json"
            });
            request.done(function (response) {
                if ('undefined' !== typeof response.status && response.status == 'folder_deleted') {
                    location.reload(true);
                }
            });
            request.fail(function (jqXHR, textStatus) {

            });
        }
    })
});