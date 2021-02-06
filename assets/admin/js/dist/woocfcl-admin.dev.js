"use strict";

var commHelper = {
  // encriptacion
  isInternetExplorer: function isInternetExplorer() {
    return navigator.userAgent.toLowerCase().indexOf('msie') >= 0;
  },
  isUndefined: function isUndefined(o) {
    return typeof o === 'undefined';
  },
  isNumber: function isNumber(o) {
    return !Number.isNaN(o);
  },
  isString: function isString(o) {
    return typeof o === 'string';
  },
  isDate: function isDate(o) {
    return o && toString.call(o) === '[object Date]' && !Number.isNaN(o);
  },
  isDateString: function isDateString(o) {
    var dateWrapper = new Date(o);
    return !Number.isNaN(dateWrapper.getDate());
  },
  isFunction: function isFunction(o) {
    return typeof o === 'function';
  },
  isJSON: function isJSON(o) {
    return Object.prototype.toString.call(o) === '[object Object]';
  },
  isArray: function isArray(obj) {
    if (typeof Array.isArray === 'undefined') {
      Array.isArray = function (obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
      };
    }

    ;
  },
  isEmpty: function isEmpty(o) {
    return typeof o === 'undefined' || o === null || o === '';
  },
  isEmptyArray: function isEmptyArray(o) {
    if (this.isArray(o) === true) return o.length === 0;
    return false;
  },
  isEmptyLookup: function isEmptyLookup(o) {
    return o === -1 || o === 0 || o === '-1' || o === '0' || this.isEmpty(o);
  },
  isPhonenumberCl: function isPhonenumberCl(o) {
    var phoneno = /^(\+?56)?(\s?)(0?9)(\s?)[9876543]\d{7}$/;
    if (this.isEmpty(o)) return false;
    return !!o.match(phoneno);
  },
  isEmail: function isEmail(o) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(o).toLowerCase());
  },
  isStringValid: function isStringValid(o, minlength, maxlength) {
    var str = this.getString(o);
    return !(str.length < minlength || str.length > maxlength);
  },
  getValue: function getValue(o) {
    var defaultvalue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    return this.isEmpty(o) ? defaultvalue : o;
  },
  getString: function getString(o, defaultvalue) {
    defaultvalue = this.isEmpty(defaultvalue) ? '' : defaultvalue;
    return this.isEmpty(o) ? defaultvalue : o;
  },
  getBool: function getBool(o) {
    return this.isEmpty(o) ? false : !!(o === 'on' || o === 'true' || o === true || Number(o) === 1);
  },
  getDate: function getDate(o) {
    var $o = this.isEmpty(o) ? null : o;

    if ($o !== null) {
      $o = this.isString($o) ? this.isDateString($o) ? new Date($o) : null : $o;
      $o = $o !== null && this.isDate($o) ? $o : null;
    }

    return $o;
  },
  getInteger: function getInteger(o, value) {
    if (this.isEmpty(o)) return value;
    return this.getNumber(o, value, 0);
  },
  getNumber: function getNumber(o, value, decimals) {
    var $value = this.isEmpty(value) ? 0 : value;
    var $decimals = this.isEmpty(decimals) ? 2 : decimals;
    var $o = this.isEmpty(o) ? $value : o.toString().replace(/\$|,/g, '');
    $o = Number.isNaN($o) ? $value : $o;
    $o = Number(parseInt($o, 10).toFixed($decimals));
    return $o;
  },
  getNumberFromString: function getNumberFromString(o) {
    if (typeof o === 'string') {
      var match = o.match(/[0-9,.]+/g); // commas to delimit thousands need to be removed

      if (match !== null) {
        o = match[0].replace(/,/g, '');
        o = parseFloat(o);
      }
    }

    return o;
  },
  getDateStringfromISO: function getDateStringfromISO(o) {
    var date = !this.isEmpty(o) ? new Date(o) : '';
    if (date === '') return date;
    return "".concat(this.appendLeadingZeroes(date.getDate()), "-").concat(this.appendLeadingZeroes(date.getMonth() + 1), "-").concat(date.getFullYear());
  },
  getDatefromISO: function getDatefromISO(o) {
    return !this.isEmpty(o) ? new Date(o) : null;
  },
  setDateToISO: function setDateToISO(date, strhour) {
    strhour = this.isEmpty(strhour) ? 'T12:00:00Z' : strhour;
    return "".concat(date.getFullYear(), "-").concat(this.appendLeadingZeroes(date.getMonth() + 1), "-").concat(this.appendLeadingZeroes(date.getDate())).concat(strhour);
  },
  setDateToISOFilter: function setDateToISOFilter(date) {
    var self = this;
    var strhour = {
      ini: self.setDateToISO(date, 'T00:00:00Z'),
      end: self.setDateToISO(date, 'T23:59:59Z')
    };
    return strhour;
  },
  // + Jonas Raoni Soares Silva
  // @ http://jsfromhell.com/number/fmt-money [rev. #2]
  // Modified to pass JSLint
  // n = the number to format
  // c = # of floating point decimal places, default 2
  // d = decimal separator, default "."
  // t = thousands separator, default ","
  getMoneyformat: function getMoneyformat(n, c, d, t) {
    c = Number.isNaN(c = Math.abs(c)) ? 2 : c;
    d = d === undefined ? '.' : d;
    t = t === undefined ? ',' : t;
    var s = n < 0 ? '-' : '';
    var i = "".concat(parseInt(n = Math.abs(+n || 0).toFixed(c), 10));
    var j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1".concat(t)) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
  },
  getUrlParameter: function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp("[\\?&]".concat(name, "=([^&#]*)"));
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
  },
  urlformat: function urlformat(url, param) {
    var paramlg = langsupport === true ? "?lg=".concat(APP_PAGE.lang) : '';
    var munion = url.indexOf('?') >= 0 ? '&' : '?';
    param = this.isEmpty(param) ? '' : munion + param;
    return url + param;
  },
  redirect: function redirect(url, param) {
    var self = this;
    window.location.replace(self.urlformat(url, param));
  },
  redirectTime: function redirectTime(url, param) {
    var self = this;
    if (!this.isEmpty(url)) setTimeout(function () {
      window.location.replace(self.urlformat(url, param));
    }, 500);
  },
  redirectNewWindow: function redirectNewWindow(url) {
    var win = window.open(url, '_blank').focus();
  },
  appendLeadingZeroes: function appendLeadingZeroes(n) {
    return n <= 9 ? "0".concat(n) : n;
  },
  findInArray: function findInArray(o, value, key, subkey) {
    var self = this;
    if (self.isEmpty(o) || !self.isArray(o)) return null;
    if (self.isEmpty(value)) return null;
    var resultArray = $.grep(o, function (item, i) {
      if (self.isEmpty(subkey)) return item[key] === value;
      if (this.isEmpty(item[key][subkey])) return null;
      return item[key][subkey] === value;
    }, false);
    return resultArray.length > 0 ? resultArray : null;
  },
  getValueFromObject: function getValueFromObject(obj, arrkey) {
    try {
      if (this.isEmpty(obj)) return null;
      var keys = null;
      var objx = obj;
      $.each(arrkey, function (ix, itemkey) {
        keys = Object.keys(objx); // console.log("objx", objx, keys);

        if (keys.indexOf(itemkey) !== -1) {
          objx = objx[itemkey];
        } else return null;
      });
      return objx;
    } catch (ex) {
      return '';
    }
  },
  getjsonfromUrl: function getjsonfromUrl(url) {
    var d = jQuery.ajax({
      type: 'GET',
      url: url,
      cache: false,
      async: false
    }).responseText;
    return JSON.parse(d);
  },
  replaceStringFromArray: function replaceStringFromArray() {
    var s = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    var o = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
    var i = 0;
    o.forEach(function (item) {
      s = s.replace("{".concat(i, "}"), item);
      i += 1;
    });
    return s;
  },
  isHtmlIdValid: function isHtmlIdValid(id) {
    var re = /^[a-z\_]+[a-z0-9\_]*$/;
    return re.test(id.trim());
  },
  decodeHtml: function decodeHtml(str) {
    if (jQuery.type(str) === 'string') {
      var map = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
      };
      return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function (m) {
        return map[m];
      });
    }

    return str;
  },
  transformJsonToArray: function transformJsonToArray(jso) {
    var o = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
    if (this.isEmptyArray(o)) return [];
    if (this.isEmpty(jso)) return [];
    var r = [];
    o.forEach(function (item) {
      r.push(jso[item]);
    });
    return r;
  }
};

var woocfcl_settings = function ($, window, document, undefined) {
  var countries = typeof woocfcl_countries !== 'undefined' ? woocfcl_countries : null;
  var states = typeof woocfcl_states !== 'undefined' ? woocfcl_states : null;
  var options = typeof woocfcl_options !== 'undefined' ? woocfcl_options : null;
  var config = typeof woocfcl_config !== 'undefined' ? woocfcl_config : null;
  var currentLang = config.lang.shortlang;
  var jsonTranslate = commHelper.getjsonfromUrl("/wp-content/plugins/woo-chile-custom/assets/admin/js/i18n/".concat(currentLang, ".json")); // dom object;

  var container = $('.container');
  var loader = $('#loader');
  var $modalRowEdit = $('#EditFieldModal').modal();
  var $formRowEdit = $('#woocfcl-form-row-edit');
  var $formFields = $('#woocfcl-form-field-options');
  var $action_changes = [];
  var table;
  var select_pagination;
  var select_country;
  var MSG_INVALID_NAME = 'NAME/ID must begin with a lowercase letter ([a-z]) and may be followed by any number of lowercase letters, digits ([0-9]) and underscores ("_")';
  var OPTION_ROW_HTML = '<tr>';
  OPTION_ROW_HTML += '<td style="width:150px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:140px;"/></td>';
  OPTION_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>';
  OPTION_ROW_HTML += '<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>';
  OPTION_ROW_HTML += '<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>';
  OPTION_ROW_HTML += '<td class="action-cell sort ui-sortable-handle"></td>';
  OPTION_ROW_HTML += '</tr>';
  var contextState = {
    tableId: '#options-datatable',
    toolbar: '<div class="input-field col s4"><select id="countries-dropdown" name="countries"></select><label>{0}</label></div>',
    toolbarNames: ['country'],
    columnNames: ['RowOrder', 'CheckboxSelect', 'ID', 'country', 'Name', 'AdditionalCode', 'NumberCode', 'enabled', 'Button'],
    datatable: {
      dom: '<"toolbar"> lfrtip',
      data: options,
      language: {
        url: ''
      },
      deferLoading: true,
      columns: [{
        data: 'RowOrder',
        className: 'reorder',
        render: function render(data, type, row, meta) {
          return dataTableHelper.appendsHideData(data, type, row, meta, contextState.columnNames);
        }
      }, {
        targets: 1,
        searchable: false,
        orderable: false,
        className: 'select-checkbox',
        checkboxes: {
          selectRow: true
        },
        render: function render(data, type, row, meta) {
          return dataTableHelper.appendsCheckboxselection(data, type, row, meta, contextState.columnNames);
        }
      }, {
        data: 'ID'
      }, {
        data: 'country',
        visible: false
      }, {
        data: 'Name'
      }, {
        data: 'AdditionalCode'
      }, {
        data: 'NumberCode'
      }, {
        data: 'enabled',
        render: function render(data, type, row, meta) {
          return dataTableHelper.appendswitch(data, type, row, meta, contextState.columnNames);
        }
      }, {
        data: null,
        visible: true,
        targets: 8,
        searchable: false,
        orderable: false,
        className: 'text-center',
        render: function render(data, type, row, meta) {
          return dataTableHelper.appendsButtonEdit(data, type, row, meta, contextState.columnNames);
        }
      }],
      columnDefs: [{
        orderable: false,
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
      }],
      rowReorder: {
        dataSrc: 'RowOrder'
      },
      select: {
        style: 'multi'
      },
      keys: {
        keys: [13
        /* ENTER */
        , 38
        /* UP */
        , 40]
      },
      initComplete: function initComplete(event) {
        var $selfTable = this;
        $selfTable.api().columns([3]).search(countries[0].ISO2).draw();
        dataTableHelper.setToolbar(contextState.toolbar, contextState.toolbarNames, contextState.tableId);
        select_country = formhelper.setSelectControl('#countries-dropdown', countries, 'ISO2', 'Name');
        select_country.on('change', function () {
          $selfTable.api().columns([3]).search(this.value).draw();
        });
      }
    }
  };
  var dataTableHelper = {
    jsonUrlLang: '/wp-content/plugins/woo-chile-custom/assets/admin/vendors/datatable/i18n/{0}.json',
    getjsonlang: function getjsonlang() {
      return this.jsonUrlLang.replace('{0}', currentLang);
    },
    getDataTable: function getDataTable(dataTable) {
      return commHelper.isString(dataTable) ? $(dataTable) : dataTable;
    },
    setPropertiesInField: function setPropertiesInField(cols, meta) {
      var extraName = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
      return {
        name: " name=\"row-".concat(cols[meta.col]).concat(extraName, "\" "),
        dataColName: " data-colName=\"".concat(cols[meta.col], "\" "),
        rowId: " data-row=\"".concat(meta.row, "\" ")
      };
    },
    getPropertiesInField: function getPropertiesInField($this, dataTable) {
      var $dataTable = this.getDataTable(dataTable);
      var rowId = $this.attr('data-row');
      return {
        name: $this.prop('name'),
        dataColName: $this.attr('data-colName'),
        rowId: rowId,
        data: $dataTable.row(rowId).data()
      };
    },
    appendsHideData: function appendsHideData(value, type, row, meta, cols) {
      var prop = this.setPropertiesInField(cols, meta);
      return "<div ".concat(prop.name, " class='novisible' ").concat(prop.rowId, " ").concat(prop.dataColName, ">").concat(value, "</div>");
    },
    appendswitch: function appendswitch(value, type, row, meta, cols) {
      var cheched = commHelper.getBool(value) ? 'checked' : '';
      var prop = this.setPropertiesInField(cols, meta);
      return "<div class=\"switch\"><label>".concat(jsonTranslate.no, "<input ").concat(prop.name, " type=\"checkbox\" ").concat(cheched, " ").concat(prop.rowId, " ").concat(prop.dataColName, "><span class=\"lever\"></span> ").concat(jsonTranslate.yes, "</label></div>");
    },
    onChangeSwitch: function onChangeSwitch(event) {
      var $self = $(this);
      var $dataTable = dataTableHelper.getDataTable(event.data.dtable);
      var rowInfo = dataTableHelper.getPropertiesInField($self, $dataTable);
      var status = $(this).prop('checked') ? 1 : 0;
      rowInfo.data[rowInfo.dataColName] = status;
      $dataTable.row(rowInfo.rowId).data(rowInfo.data).invalidate();
    },
    appendsCheckboxselection: function appendsCheckboxselection(data, type, full, meta, cols) {
      var prop = this.setPropertiesInField(cols, meta);
      return "<input type=\"checkbox\" name=\"id[]\" class=\"filled-in\" ".concat(prop.rowId, " value=\"0\">");
    },
    appendsButtonEdit: function appendsButtonEdit(data, type, full, meta, cols) {
      var prop = this.setPropertiesInField(cols, meta, 'Edit');
      return "<button ".concat(prop.name, " class=\"btn btn-xs btn-edit\" ").concat(prop.rowId, " ").concat(prop.dataColName, " type=\"button\" >").concat(jsonTranslate.edit, "</button>");
    },
    setToolbar: function setToolbar(toolbar, items, tableId) {
      var translItems = commHelper.transformJsonToArray(jsonTranslate, items);
      var tool = commHelper.replaceStringFromArray(toolbar, translItems);
      var $tableToolbar = $(tableId + '_wrapper');
      $tableToolbar.find('div.toolbar').html(tool);
      $tableToolbar.find('.dataTables_length').appendTo('div.toolbar');
      $tableToolbar.find('.dataTables_length select').formSelect();
    },
    setOnCheckedAll: function setOnCheckedAll(event) {
      //if ($('#select_all:checked').val() === 'on') table.rows().select();
      var $dataTable = dataTableHelper.getDataTable(event.data.dtable);
      if ($(this).is(':checked')) $dataTable.rows().select();else $dataTable.rows().deselect();
    }
  };
  var formhelper = {
    reset: function reset(form) {
      var $form = commHelper.isString(form) ? $(form) : form;
      $form.get(0).reset();
      $form.find('input:checkbox').removeAttr('checked');
      $form.find('input:radio').removeAttr('checked');
      $form.find('select').prop('selectedIndex', 0);
    },
    setSelectControl: function setSelectControl(name, data, keyvalue, keyname) {
      var control = $(name);
      control.empty();
      $.each(data, function (key, entry) {
        control.append($('<option></option>').attr('value', entry[keyvalue]).text(entry[keyname]));
      });
      control.prop('selectedIndex', 0);
      control.formSelect();
      return control;
    },
    RowIdSet: function RowIdSet($form, rowId) {
      this.setFieldValuebyName($form, 'rowId', rowId);
    },
    RowIdGet: function RowIdGet($form) {
      return this.getFieldByName($form, 'rowId');
    },
    updateFormFieldsFromDatatable: function updateFormFieldsFromDatatable(form, rowInfo) {
      var $form = commHelper.isString(form) ? $(form) : form;
      formhelper.reset($form);
      this.RowIdSet($form, rowInfo.rowId);

      for (var key in rowInfo.data) {
        this.setFieldValuebyName($form, key, rowInfo.data[key]);
      }
    },
    updateDatatableFromFormFields: function updateDatatableFromFormFields(form, dataTable) {
      var $form = commHelper.isString(form) ? $(form) : form;
      var $dataTable = commHelper.isString(dataTable) ? $(dataTable) : dataTable;
      var rowId = this.RowIdGet($form).value;
      var currentRow = $dataTable.row(rowId).data();
      console.log(currentRow, rowId);

      for (var key in currentRow) {
        var field = this.getFieldByName($form, key);
        console.log(key, field);
        currentRow[key] = commHelper.isEmpty(field) ? currentRow[key] : commHelper.getValue(field.value, currentRow[key]);
      }

      console.log(currentRow);
      $dataTable.row(rowId).data(currentRow).invalidate(); // Refresh table

      $dataTable.draw(false);
    },
    setFieldValuebyName: function setFieldValuebyName(form, name, value) {
      var multiple = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0;
      var $form = commHelper.isString(form) ? $(form) : form;
      var field = this.getFieldByName($form, name);
      if (commHelper.isEmpty(field)) return;

      switch (field.tag) {
        case 'select':
          if (multiple == 1) {
            value = typeof value === 'string' ? value.split(',') : value;
            field.control.val(value).trigger('change');
          } else {
            field.control.val(value);
          }

          break;

        case 'textarea':
          value = value ? commHelper.decodeHtml(value) : value;
          field.control.val(value);
          break;

        default:
          switch (field.type) {
            case 'checkbox':
              value = commHelper.getBool(value) ? field.control.attr('checked', 'checked') : field.control.removeAttr('checked');
              break;

            case 'radio':
              value = value ? commHelper.decodeHtml(value) : value;
              field.control.filter("[value='".concat(value, "']")).attr('checked', 'checked');
              break;

            default:
              value = value ? commHelper.decodeHtml(value) : value;
              field.control.val(value);
          }

      }
    },
    getFieldByName: function getFieldByName($objForm, name) {
      var $field = $objForm.find("[name='".concat(name, "']"));

      if ($field.length > 0) {
        var type = $field.attr('type');
        var tag = $field[0].tagName.toLowerCase();
        var value = '';

        switch (tag) {
          case 'select':
          case 'textarea':
            value = $field.val();
            value = commHelper.getValue(value);
            break;

          default:
            switch (type) {
              case 'checkbox':
                value = $field.prop('checked') ? 1 : 0;
                break;

              case 'radio':
                value = $field.find(':checked').val();
                value = commHelper.getValue(value);
                break;

              default:
                value = $field.val();
                value = commHelper.getValue(value);
            }

        }

        return {
          control: $field,
          tag: tag,
          type: type,
          value: value
        };
      } else {
        return null;
      }
    }
  };
  var app = {
    // formNames: { action: '#woocfcl-form-field-option', edit: '#woocfcl-form-row-edit', add: '' },
    formAction: $('#woocfcl-form-field-option'),
    formEdit: $('#woocfcl-form-row-edit'),
    formAdd: '',
    updateAction: function updateAction(action) {
      $action_changes.push(action);
      formhelper.setFieldValuebyName(app.formAction, 'fieldsAction', action);
    },
    showSpinner: function showSpinner() {
      loader.fadeIn();
      container.fadeOut();
    },
    hideSpinner: function hideSpinner() {
      loader.fadeOut();
      container.fadeIn();
    }
  };
  $(function () {
    if (config.view === 'states') {
      contextState.datatable.language.url = dataTableHelper.getjsonlang();
      table = $(contextState.tableId).DataTable(contextState.datatable); // set search text on specific column

      table.on('click', '#select_all', {
        dtable: table
      }, dataTableHelper.setOnCheckedAll); // Handle click on "Edit" button

      table.on('change', 'div.switch input[name="row-enabled"]', {
        dtable: table
      }, dataTableHelper.onChangeSwitch); // Handle click on "Edit" button

      table.on('click', 'button[name="row-ButtonEdit"]', function (event) {
        var $self = $(this);
        var rowInfo = dataTableHelper.getPropertiesInField($self, table);
        console.log(rowInfo);
        formhelper.updateFormFieldsFromDatatable($formRowEdit, rowInfo);
        $modalRowEdit.modal('open');
      });
      table.on('row-reordered', function (e, diff, edit) {
        // for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
        // 	$(diff[i].node).addClass("reordered");
        // }
        console.log("reorden", e, diff, edit);
      }); // Handle form submission event

      $formRowEdit.on('submit', function (e) {
        e.preventDefault();
        formhelper.updateDatatableFromFormFields($formRowEdit, table);
        $modalRowEdit.modal('close');
      });
      app.hideSpinner();
    } else {
      app.hideSpinner();
    }

    $('#woocfcl_new_field_form_pp').dialog({
      modal: true,
      width: 600,
      resizable: false,
      autoOpen: false,
      buttons: [{
        text: 'Save',
        click: function click() {
          var form = $('#woocfcl_new_field_form');
          var valid = validate_field_form(form);

          if (valid) {
            prepare_field_form(form);
            form.submit();
          }
        }
      }]
    });
    $('#woocfcl_edit_field_form_pp').dialog({
      modal: true,
      width: 600,
      resizable: false,
      autoOpen: false,
      buttons: [{
        text: 'Save',
        click: function click() {
          var form = $('#woocfcl_edit_field_form');
          var valid = validate_field_form(form);

          if (valid) {
            prepare_field_form(form);
            form.submit();
          }
        }
      }]
    });
    $('select.woocfcl-enhanced-multi-select').select2({
      minimumResultsForSearch: 10,
      allowClear: true,
      placeholder: $(this).data('placeholder')
    }).addClass('enhanced');
    $('#woocfcl_checkout_fields tbody').sortable({
      items: 'tr',
      cursor: 'move',
      axis: 'y',
      handle: 'td.sort',
      scrollSensitivity: 40,
      helper: function helper(e, ui) {
        ui.children().each(function () {
          $(this).width($(this).width());
        });
        ui.css('left', '0');
        return ui;
      }
    });
    $('#woocfcl_checkout_fields tbody').on('sortstart', function (event, ui) {
      ui.item.css('background-color', '#f6f6f6');
    });
    $('#woocfcl_checkout_fields tbody').on('sortstop', function (event, ui) {
      ui.item.removeAttr('style');
      prepare_field_order_indexes();
    });
  });

  function isHtmlIdValid(id) {
    var re = /^[a-z\_]+[a-z0-9\_]*$/;
    return re.test(id.trim());
  }

  function decodeHtml(str) {
    if ($.type(str) === 'string') {
      var map = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
      };
      return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function (m) {
        return map[m];
      });
    }

    return str;
  }

  function get_property_field_value(form, type, name) {
    var value = '';

    switch (type) {
      case 'select':
        value = form.find("select[name=i_".concat(name, "]")).val();
        value = value == null ? '' : value;
        break;

      case 'checkbox':
        value = form.find("input[name=i_".concat(name, "]")).prop('checked');
        value = value ? 1 : 0;
        break;

      case 'textarea':
        value = form.find("textarea[name=i_".concat(name, "]")).val();
        value = value == null ? '' : value;

      default:
        value = form.find("input[name=i_".concat(name, "]")).val();
        value = value == null ? '' : value;
    }

    return value;
  }

  function set_property_field_value(form, type, name, value, multiple) {
    switch (type) {
      case 'select':
        if (multiple == 1) {
          value = typeof value === 'string' ? value.split(',') : value;
          name = "".concat(name, "[]");
          form.find("select[name=\"i_".concat(name, "\"]")).val(value).trigger('change');
        } else {
          form.find("select[name=\"i_".concat(name, "\"]")).val(value);
        }

        break;

      case 'checkbox':
        value = value == 1;
        form.find("input[name=i_".concat(name, "]")).prop('checked', value);
        break;

      case 'textarea':
        value = value ? decodeHtml(value) : value;
        form.find("textarea[name=i_".concat(name, "]")).val(value);
        break;

      default:
        value = value ? decodeHtml(value) : value;
        form.find("input[name=i_".concat(name, "]")).val(value);
    }
  }

  function openNewFieldForm(sname) {
    if (sname == 'billing' || sname == 'shipping' || sname == 'additional') {
      sname = "".concat(sname, "_");
    }

    var form = $('#woocfcl_new_field_form');
    clear_field_form(form);
    form.find('select[name=i_type]').change();
    set_property_field_value(form, 'text', 'name', sname, 0);
    set_property_field_value(form, 'text', 'class', 'form-row-wide', 0);
    $('#woocfcl_new_field_form_pp').dialog('open');
  }

  function openEditFieldForm(elm, rowId) {
    var row = $(elm).closest('tr');
    var form = $('#woocfcl_edit_field_form');
    var props_json = row.find('.f_props').val(); // props_json = decodeHtml(props_json);

    var props = JSON.parse(props_json); // var type = props.type;

    populate_field_form_general(form, props);
    form.find('select[name=i_type]').change();
    populate_field_form(row, form, props);
    $('#woocfcl_edit_field_form_pp').dialog('open');
  }

  function clear_field_form(form) {
    form.find('.err_msgs').html('');
    set_property_field_value(form, 'hidden', 'autocomplete', '', 0);
    set_property_field_value(form, 'hidden', 'priority', '', 0);
    set_property_field_value(form, 'hidden', 'custom', '', 0);
    set_property_field_value(form, 'hidden', 'oname', '', 0);
    set_property_field_value(form, 'hidden', 'otype', '', 0);
    set_property_field_value(form, 'select', 'type', 'text', 0);
    set_property_field_value(form, 'text', 'name', '', 0);
    set_property_field_value(form, 'text', 'label', '', 0);
    set_property_field_value(form, 'text', 'placeholder', '', 0);
    set_property_field_value(form, 'text', 'default', '', 0); // set_property_field_value(form, 'text', 'options', '', 0);

    set_property_field_value(form, 'text', 'class', '', 0);
    set_property_field_value(form, 'select', 'validate', '', 1);
    set_property_field_value(form, 'checkbox', 'required', 1, 0); // set_property_field_value(form, 'checkbox', 'clear', 1, 0);

    set_property_field_value(form, 'checkbox', 'enabled', 1, 0);
    set_property_field_value(form, 'checkbox', 'show_in_email', 1, 0);
    set_property_field_value(form, 'checkbox', 'show_in_order', 1, 0);
    populate_options_list(form, false);
  }

  function populate_field_form_general(form, props) {
    var autocomplete = props.autocomplete ? props.autocomplete : '';
    var priority = props.priority ? props.priority : '';
    var custom = props.custom ? props.custom : '';
    var type = props.type ? props.type : 'text';
    var name = props.name ? props.name : '';
    set_property_field_value(form, 'hidden', 'autocomplete', autocomplete, 0);
    set_property_field_value(form, 'hidden', 'priority', priority, 0);
    set_property_field_value(form, 'hidden', 'custom', custom, 0);
    set_property_field_value(form, 'hidden', 'oname', name, 0);
    set_property_field_value(form, 'hidden', 'otype', type, 0);
    set_property_field_value(form, 'select', 'type', type, 0);
    set_property_field_value(form, 'text', 'name', name, 0);
  }

  function populate_field_form(row, form, props, custom) {
    var custom = props.custom ? props.custom : '';
    var label = props.label ? props.label : '';
    var placeholder = props.placeholder ? props.placeholder : '';
    var default_val = props["default"] ? props["default"] : ''; // var options = props['options'] ? props['options'] : '';

    var cssclass = props["class"] ? props["class"] : '';
    var validate = props.validate ? props.validate : '';
    var required = props.required && (props.required || props.required === 'yes') ? 1 : 0; // var clear = props['clear'] && (props['clear'] || props['clear'] === 'yes') ? 1 : 0;

    var enabled = props.enabled && (props.enabled || props.enabled === 'yes') ? 1 : 0;
    var show_in_email = props.show_in_email && (props.show_in_email || props.show_in_email === 'yes') ? 1 : 0;
    var show_in_order = props.show_in_order && (props.show_in_order || props.show_in_order === 'yes') ? 1 : 0;
    show_in_email = custom == 1 ? show_in_email : true;
    show_in_order = custom == 1 ? show_in_order : true;
    set_property_field_value(form, 'text', 'label', label, 0);
    set_property_field_value(form, 'text', 'placeholder', placeholder, 0);
    set_property_field_value(form, 'text', 'default', default_val, 0); // set_property_field_value(form, 'text', 'options', options, 0);

    set_property_field_value(form, 'text', 'class', cssclass, 0);
    set_property_field_value(form, 'select', 'validate', validate, 1);
    set_property_field_value(form, 'checkbox', 'required', required, 0); // set_property_field_value(form, 'checkbox', 'clear', clear, 0);

    set_property_field_value(form, 'checkbox', 'enabled', enabled, 0);
    set_property_field_value(form, 'checkbox', 'show_in_email', show_in_email, 0);
    set_property_field_value(form, 'checkbox', 'show_in_order', show_in_order, 0);
    var optionsJson = row.find('.f_options').val();
    populate_options_list(form, optionsJson);

    if (custom === 1) {
      form.find('input[name=i_name]').prop('disabled', false);
      form.find('select[name=i_type]').prop('disabled', false);
      form.find('input[name=i_show_in_email]').prop('disabled', false);
      form.find('input[name=i_show_in_order]').prop('disabled', false);
    } else {
      form.find('input[name=i_name]').prop('disabled', true);
      form.find('select[name=i_type]').prop('disabled', true);
      form.find('input[name=i_show_in_email]').prop('disabled', true);
      form.find('input[name=i_show_in_order]').prop('disabled', true);
      form.find('input[name=i_label]').focus();
    }
  }

  function prepare_field_form(form) {
    var options_json = get_options(form);
    set_property_field_value(form, 'hidden', 'options_json', options_json, 0);
  }

  function validate_field_form(form) {
    var err_msgs = '';
    var name = get_property_field_value(form, 'text', 'name');
    var type = get_property_field_value(form, 'select', 'type');
    var otype = get_property_field_value(form, 'select', 'otype');

    if (type == '' && otype != 'country' && otype == 'state') {
      err_msgs = 'Type is required';
    } else if (name == '') {
      err_msgs = 'Name is required';
    } else if (!isHtmlIdValid(name)) {
      err_msgs = MSG_INVALID_NAME;
    }

    if (err_msgs != '') {
      form.find('.err_msgs').html(err_msgs);
      return false;
    }

    return true;
  }

  function fieldTypeChangeListner(elm) {
    var type = $(elm).val();
    var form = $(elm).closest('form');
    showAllFields(form);

    if (type === 'select') {
      form.find('.row-validate').hide();
    } else if (type === 'radio') {
      form.find('.row-validate').hide();
      form.find('.row-placeholder').hide();
    } else {
      form.find('.row-options').hide();
    }
  }

  function showAllFields(form) {
    form.find('.row-options').show();
    form.find('.row-placeholder').show();
    form.find('.row-validate').show();
  }
  /*------------------------------------
   *---- OPTIONS FUNCTIONS - SATRT ------
   *------------------------------------*/


  function get_options(form) {
    var optionsKey = form.find("input[name='i_options_key[]']").map(function () {
      return $(this).val();
    }).get();
    var optionsText = form.find("input[name='i_options_text[]']").map(function () {
      return $(this).val();
    }).get();
    var optionsSize = optionsText.length;
    var optionsArr = [];

    for (var i = 0; i < optionsSize; i++) {
      var optionDetails = {};
      optionDetails.key = optionsKey[i];
      optionDetails.text = optionsText[i];
      optionsArr.push(optionDetails);
    }

    var optionsJson = optionsArr.length > 0 ? JSON.stringify(optionsArr) : '';
    optionsJson = encodeURIComponent(optionsJson);
    return optionsJson;
  }

  function populate_options_list(form, optionsJson) {
    var optionsHtml = '';

    if (optionsJson) {
      try {
        optionsJson = decodeURIComponent(optionsJson);
        var optionsList = $.parseJSON(optionsJson);

        if (optionsList) {
          jQuery.each(optionsList, function () {
            var html = '<tr>';
            html += "<td style=\"width:150px;\"><input type=\"text\" name=\"i_options_key[]\" value=\"".concat(this.key, "\" placeholder=\"Option Value\" style=\"width:140px;\"/></td>");
            html += "<td style=\"width:190px;\"><input type=\"text\" name=\"i_options_text[]\" value=\"".concat(this.text, "\" placeholder=\"Option Text\" style=\"width:180px;\"/></td>");
            html += '<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>';
            html += '<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>';
            html += '<td class="action-cell sort ui-sortable-handle"></td>';
            html += '</tr>';
            optionsHtml += html;
          });
        }
      } catch (err) {
        console.log(err);
      }
    }

    var optionsTable = form.find('.woocfcl-option-list tbody');

    if (optionsHtml) {
      optionsTable.html(optionsHtml);
    } else {
      optionsTable.html(OPTION_ROW_HTML);
    }
  }

  function add_new_option_row(elm) {
    var ptable = $(elm).closest('table');
    var optionsSize = ptable.find('tbody tr').size();

    if (optionsSize > 0) {
      ptable.find('tbody tr:last').after(OPTION_ROW_HTML);
    } else {
      ptable.find('tbody').append(OPTION_ROW_HTML);
    }
  }

  function remove_option_row(elm) {
    var ptable = $(elm).closest('table');
    $(elm).closest('tr').remove();
    var optionsSize = ptable.find('tbody tr').size();

    if (optionsSize == 0) {
      ptable.find('tbody').append(OPTION_ROW_HTML);
    }
  }
  /*------------------------------------
   *---- OPTIONS FUNCTIONS - END --------
   *------------------------------------*/


  function prepare_field_order_indexes() {
    $('#woocfcl_checkout_fields tbody tr').each(function (index, el) {
      $('input.f_order', el).val(parseInt($(el).index('#woocfcl_checkout_fields tbody tr')));
    });
  }

  function selectAllCheckoutFields(elm) {
    var checkAll = $(elm).prop('checked');
    $('#woocfcl_checkout_fields tbody input:checkbox[name=select_field]').prop('checked', checkAll);
  }

  function removeSelectedFields() {
    $('#woocfcl_checkout_fields tbody tr').removeClass('thpladmin-strikeout');
    $('#woocfcl_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
      var row = $(this).closest('tr');

      if (!row.hasClass('woocfcl-strikeout')) {
        row.addClass('woocfcl-strikeout');
      }

      row.find('.f_deleted').val(1);
      row.find('.f_edit_btn').prop('disabled', true);
    });
  }

  function enableDisableSelectedFields(enabled) {
    $('#woocfcl_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
      var row = $(this).closest('tr');
      row.find('.f_enabled').val(enabled);

      if (enabled == 0) {
        if (!row.hasClass('woocfcl-disabled')) {
          row.addClass('woocfcl-disabled');
        }

        row.find('.f_edit_btn').prop('disabled', true);
        row.find('.td_enabled').html('-');
      } else {
        row.removeClass('woocfcl-disabled');
        row.find('.f_edit_btn').prop('disabled', false);
        row.find('.td_enabled').html('<span class="dashicons dashicons-yes"></span>');
      }
    });
  }

  return {
    openNewFieldForm: openNewFieldForm,
    openEditFieldForm: openEditFieldForm,
    selectAllCheckoutFields: selectAllCheckoutFields,
    removeSelectedFields: removeSelectedFields,
    enableDisableSelectedFields: enableDisableSelectedFields,
    fieldTypeChangeListner: fieldTypeChangeListner,
    addNewOptionRow: add_new_option_row,
    removeOptionRow: remove_option_row
  };
}(window.jQuery, window, document);

function woocfclOpenNewFieldForm(tabName) {
  woocfcl_settings.openNewFieldForm(tabName);
}

function woocfclOpenEditFieldForm(elm, rowId) {
  woocfcl_settings.openEditFieldForm(elm, rowId);
}

function woocfclRemoveSelectedFields() {
  woocfcl_settings.removeSelectedFields();
}

function woocfclEnableSelectedFields() {
  woocfcl_settings.enableDisableSelectedFields(1);
}

function woocfclDisableSelectedFields() {
  woocfcl_settings.enableDisableSelectedFields(0);
}

function woocfclFieldTypeChangeListner(elm) {
  woocfcl_settings.fieldTypeChangeListner(elm);
}

function woocfclSelectAllCheckoutFields(elm) {
  woocfcl_settings.selectAllCheckoutFields(elm);
}

function woocfclAddNewOptionRow(elm) {
  woocfcl_settings.addNewOptionRow(elm);
}

function woocfclRemoveOptionRow(elm) {
  woocfcl_settings.removeOptionRow(elm);
} // ("#myid").on('click', {arg1: 'hello', arg2: 'bye'}, myfunction);
// function myfunction(e) {
//     var arg1 = e.data.arg1;
//     var arg2 = e.data.arg2;
//     alert(arg1);
//     alert(arg2);
// }