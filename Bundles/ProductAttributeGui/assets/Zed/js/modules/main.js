/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

require('ZedGui');
require('../../sass/main.scss');

function AttributeManager() {
    var _attributeManager = {
        attributesValues: {},
        metaAttributes: {},
        locales: {},
        removedKeys: []
    };

    var jsonLoader = {};

    jsonLoader.load = function(input) {
        var json = $(input).html();
        return JSON.parse(json);
    };

    _attributeManager.init = function() {
        _attributeManager.attributesValues = jsonLoader.load($('#productAttributeValuesJson'));
        _attributeManager.metaAttributes = jsonLoader.load($('#metaAttributesJson'));
        _attributeManager.locales = jsonLoader.load($('#localesJson'));
    };

    _attributeManager.getLocaleCollection = function() {
        return _attributeManager.locales;
    };

    _attributeManager.extractKeysFromTable = function() {
        var keys = [];
        $('#productAttributesTable tr').each(function(){
            keys.push($(this).find("td:first").text()); //put elements into array
        });

        return keys;
    };

    _attributeManager.validateKey = function(key) {
        var currentKeys = _attributeManager.extractKeysFromTable();

        if ($.inArray(key, currentKeys) > -1) {
            alert('Attribute "'+ key +'" already defined');
            return false;
        }

        return true;
    };

    _attributeManager.hasKeyBeenUsed = function(key) {
        var currentKeys = _attributeManager.extractKeysFromTable();

        return ($.inArray(key, currentKeys) > 0);
    };

    _attributeManager.generateDataToAdd = function(key, idAttribute, attributeMetadata) {
        var dataToAdd = [];
        var locales = _attributeManager.getLocaleCollection();

        dataToAdd.push(key);

        for (var localeCode in locales) {
            var readOnly = 'readonly="true"';

            if (attributeMetadata.is_super) {
                readOnly = '';
            }

            var item = '<input type="text"' +
                ' class="spryker-form-autocomplete form-control ui-autocomplete-input kv_attribute_autocomplete" ' +
                ' data-allow_input="' + attributeMetadata.allow_input + '"' +
                ' data-is_super="' + attributeMetadata.is_super + '"' +
                ' data-is_attribute_input ' +
                ' data-attribute_key="' + key + '" ' +
                ' value="" ' +
                ' data-id_attribute="' + idAttribute + '" ' +
                ' data-locale_code="' + localeCode + '"' +
                readOnly +
                '>' +
                '<span style="display: none"></span>';

            dataToAdd.push(item);
        }

        dataToAdd.push('<a data-key="' + key + '" href="#" class="btn btn-xs btn-outline btn-danger remove-item">Remove</a>');

        return dataToAdd;
    };

    _attributeManager.addKey = function(key, idAttribute, dataTable) {
        key = key.replace(/([^a-z0-9\_\-\:]+)/gi, '').toLowerCase();

        if (key === '' || !idAttribute) {
            alert('Please select attribute key first');
            return false;
        }

        if (!_attributeManager.validateKey(key)) {
            return false;
        }

        var keyInput = $('#attribute_form_key');
        var attributeMetadata = {
            'key': keyInput.attr('data-key'),
            'id': keyInput.attr('data-value'),
            'allow_input': keyInput.attr('data-allow_input'),
            'is_super': keyInput.attr('data-is_super'),
            'input_type': keyInput.attr('data-input_type')
        };

        _attributeManager.resetRemovedKey(key);

        var dataToAdd = _attributeManager.generateDataToAdd(key, idAttribute, attributeMetadata);

        dataTable.DataTable().
            row.
            add(dataToAdd)
            .draw();
    };

    _attributeManager.addRemovedKey = function(key) {
        _attributeManager.removedKeys.push(key)
    };

    _attributeManager.resetRemovedKey = function(key) {
        delete _attributeManager.removedKeys[key];
    };

    _attributeManager.resetRemovedKeysCache = function () {
        _attributeManager.removedKeys = [];
    };

    _attributeManager.save = function() {
        var locales = _attributeManager.getLocaleCollection();
        var form = $('form#attribute_values_form');
        var idProductAbstract = $('#attribute_values_form_hidden_product_abstract_id').val();
        var formData = [];

        $('[data-is_attribute_input]').each(function(index, value) {
            var input = $(value);
            var attributeValue = input.val();
            var idAttribute = input.attr('data-id_attribute') || null;
            var locale_code = input.attr('data-locale_code') || null;
            var key = input.attr('data-attribute_key') || null;

            formData.push({
                'key': key,
                'id': idAttribute,
                'locale_code': locale_code,
                'value': attributeValue
            });
        });


        $(_attributeManager.removedKeys).each(function(index, removedKey) {
            for (var localeCode in locales) {
                var locale = locales[localeCode];
                var idLocale = locale['id_locale'];
                if (!idLocale) {
                    idLocale = '_';
                }

                formData.push({
                    'key': removedKey,
                    'id': null,
                    'locale_code': idLocale,
                    'value': ''
                });
            }
        });

        var formDataJson = JSON.stringify(formData);

        $.ajax({
            url: '/product-attribute-gui/save',
            type: 'POST',
            dataType: 'application/json',
            data: 'json=' + formDataJson + '&id-product-abstract=' + idProductAbstract,
            complete: function(jqXHR) {
                if(jqXHR.readyState === 4) {
                    _attributeManager.resetRemovedKeysCache();

                    $("#saveButton")
                        .prop('disabled', false)
                        .val('Save');

                    var message = 'An error has occurred';
                    var responseData = JSON.parse(jqXHR.responseText);
                    if (responseData.hasOwnProperty('message')) {
                        message = responseData.message;
                    }

                    window.sweetAlert({
                        title: jqXHR.status === 200 ? 'Success' : 'Error',
                        text: message,
                        type: jqXHR.status === 200 ? 'success' : 'error'
                    });

                    location.reload();
                }
            },
            beforeSend: function() {
                $("#saveButton")
                    .prop('disabled', true)
                    .val('Saving');
            }
        });
    };

    _attributeManager.init();

    return _attributeManager;
}

/**
 * @param data
 * @param params
 * @returns {{results: *, pagination: {more: (boolean|number)}}}
 */
function processAjaxResult(data, params) {
    //{"id_attribute":1,"values":[{"id_product_management_attribute_value":1,"fk_locale":66,"value":"intel-atom-quad-core","translation":"Intel Atom Z3560 Quad-Core US"}]}
    // parse the results into the format expected by Select2
    // since we are using custom formatting functions we do not need to
    // alter the remote JSON data, except to indicate that infinite
    // scrolling can be used
    params.page = params.page || 1;

    return {
        results: data.values,
        pagination: {
            more: (params.page * 30) < data.total || 0
        }
    };
}

function removeActionHandler() {
    var $link = $(this);
    var dataTable = $('#productAttributesTable').DataTable();

    /*$link.parents('tr').find("td input").each(function(index, input) {
        $(input).val('');
    });
    $link.parents('tr').hide();*/

    dataTable.row($link.parents('tr')).remove().draw();

    return false;
}

function updateAttributeInputsWithAutoComplete() {
    $('[data-is_attribute_input]').each(function(key, value) {
        var input = $(value);
        var id = input.attr('data-id_attribute') || null;
        var locale_code = input.attr('data-locale_code') || null;

        input.autocomplete({
            minLength: 0,
            source: function(request, response) {
                $.ajax({
                    url: '/product-management/attribute/suggest/',
                    dataType: 'json',
                    data: {
                        q: request.term,
                        id: id,
                        locale_code: locale_code
                    },
                    success: function(data) {
                        response($.map(data.values, function (item) {
                            return {
                                label: item.text,
                                value: item.id
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                var input = $(this);
                input.val(ui.item.label);
                input.attr('data-value', ui.item.value);

                return false;
            },
            focus: function(event, ui) {
                var input = $(this);
                input.val(ui.item.label);
                input.attr('data-value', ui.item.value);

                return false;
            }
        });
    });
}


$(document).ready(function() {

    var attributeManager = new AttributeManager();

    $('#addButton').on('click', function() {
        var input = $('#attribute_form_key');
        var dataTable = $('#productAttributesTable');
        var idAttribute = input.attr('data-value');
        var key = input.val().trim();

        attributeManager.addKey(key, idAttribute, dataTable);

        $('.remove-item')
            .off('click')
            .on('click', function(event, element) {
                var key = $(this).attr('data-key');
                attributeManager.addRemovedKey(key);
                removeActionHandler.call($(this))
            });

        updateAttributeInputsWithAutoComplete();

        return false;
    });

    $('.remove-item')
        .off('click')
        .on('click', function(event, element) {
            var key = $(this).attr('data-key');
            attributeManager.addRemovedKey(key);
            removeActionHandler.call($(this))
        });

    updateAttributeInputsWithAutoComplete();

    $('#attribute_form_key').autocomplete({
        minLength: 0,
        source: function(request, response) {
            $.ajax({
                url: '/product-attribute-gui/suggest/keys',
                dataType: 'json',
                data: {
                    q: request.term,
                },
                success: function(data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.key,
                            value: item.attribute_id,
                            allow_input: item.allow_input,
                            is_super: item.is_super,
                            input_type: item.input_type,
                        };
                    }));
                }
            });
        },
        select: function(event, ui) {
            console.log('ui.item',ui.item);

            var input = $(this);
            input.val(ui.item.label);

            input.attr('data-key', ui.item.label);
            input.attr('data-value', ui.item.value);
            input.attr('data-allow_input', ui.item.allow_input);
            input.attr('data-is_super', ui.item.is_super);
            input.attr('data-input_type', ui.item.input_type);

            return false;
        },
        focus: function(event, ui) {
            console.log('ui.item',ui.item);

            var input = $(this);
            input.val(ui.item.label);

            input.attr('data-key', ui.item.label);
            input.attr('data-value', ui.item.value);
            input.attr('data-allow_input', ui.item.allow_input);
            input.attr('data-is_super', ui.item.is_super);
            input.attr('data-input_type', ui.item.input_type);

            return false;
        }
    });

    $('#saveButton').on('click', function(event, ui) {
        attributeManager.save();
    });

    $("#attribute_values_form").submit(function(e) {
        e.preventDefault();
        return false;
    });


    $('#productAttributesTable').DataTable({
        'columnDefs': [{
            'targets': -1,
            'orderable': false
        }],
        destroy: true
    });

    $('#attribute_form').on('keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            $('#addButton').trigger('click');
            return false;
        }
    });
});