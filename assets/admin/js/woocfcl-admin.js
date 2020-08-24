
const commHelper = {
	// encriptacion
	isInternetExplorer() { return navigator.userAgent.toLowerCase().indexOf('msie') >= 0; },
	isUndefined(o) { return typeof o === 'undefined'; },
	isNumber(o) { return !Number.isNaN(o); },
	isString(o) { return typeof o === 'string'; },
	isDate(o) { return o && toString.call(o) === '[object Date]' && !Number.isNaN(o); },
	isDateString(o) { const dateWrapper = new Date(o); return !Number.isNaN(dateWrapper.getDate()); },
	isFunction(o) { return typeof o === 'function'; },
	isJSON(o) { return Object.prototype.toString.call(o) === '[object Object]'; },
	// jquery required
	isArray(o) { return $.isArray(o); },
	isEmpty(o) { return typeof o === 'undefined' || o === null || o === ''; },
	isEmptyArray(o) { let $isArray = this.isArray(o); if ($isArray === true) return o.length === 0; return $isArray; },
	isEmptyLookup(o) { return o === -1 || o === 0 || o === '-1' || o === '0' || this.isEmpty(o); },
	isPhonenumberCl(o) { const phoneno = /^(\+?56)?(\s?)(0?9)(\s?)[9876543]\d{7}$/; if (this.isEmpty(o)) return false; return !!o.match(phoneno); },
	isEmail(o) { const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; return re.test(String(o).toLowerCase()); },
	isStringValid(o, minlength, maxlength) { const str = this.getString(o); return !(str.length < minlength || str.length > maxlength); },
	getString(o, defaultvalue) { defaultvalue = this.isEmpty(defaultvalue) ? '' : defaultvalue; return this.isEmpty(o) ? defaultvalue : o; },
	getBool(o) { return this.isEmpty(o) ? false : (!!(o === 'on' || o === 'true' || o === true)); },
	getDate(o) {
		let $o = this.isEmpty(o) ? null : o;
		if ($o !== null) {	$o = this.isString($o) ? (this.isDateString($o) ? new Date($o) : null) : $o;$o = $o !== null && this.isDate($o) ? $o : null;}
		return $o;
	},
	getInteger(o, value) { if (this.isEmpty(o)) return value; return this.getNumber(o, value, 0); },
	getNumber(o, value, decimals) {
		const $value = this.isEmpty(value) ? 0 : value;
		const $decimals = this.isEmpty(decimals) ? 2 : decimals;
		let $o = this.isEmpty(o) ? $value : o.toString().replace(/\$|,/g, '');
		$o = Number.isNaN($o) ? $value : $o;
		$o = Number(parseInt($o, 10).toFixed($decimals));
		return $o;
	},
	getNumberFromString(o) {
		if (typeof o === 'string') {
			const match = o.match(/[0-9,.]+/g);// commas to delimit thousands need to be removed
			if (match !== null) { o = match[0].replace(/,/g, ''); o = parseFloat(o); }
		}
		return o;
	},
	getDateStringfromISO(o) {
		const date = !this.isEmpty(o) ? new Date(o) : '';
		if (date === '') return date;
		return `${this.appendLeadingZeroes(date.getDate())}-${this.appendLeadingZeroes(date.getMonth() + 1)}-${date.getFullYear()}`;
	},
	getDatefromISO(o) { return !this.isEmpty(o) ? new Date(o) : null; },
	setDateToISO(date, strhour) {
		strhour = this.isEmpty(strhour) ? 'T12:00:00Z' : strhour;
		return `${date.getFullYear()}-${this.appendLeadingZeroes(date.getMonth() + 1)}-${this.appendLeadingZeroes(date.getDate())}${strhour}`;
	},
	setDateToISOFilter(date) {
		const self = this;
		const strhour = {
			ini: self.setDateToISO(date, 'T00:00:00Z'),
			end: self.setDateToISO(date, 'T23:59:59Z'),
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
	getMoneyformat(n, c, d, t) {
		c = Number.isNaN(c = Math.abs(c)) ? 2 : c;
		d = d === undefined ? '.' : d;
		t = t === undefined ? ',' : t;
		const s = n < 0 ? '-' : '';
		const i = `${parseInt(n = Math.abs(+n || 0).toFixed(c), 10)}`;
		var j = (j = i.length) > 3 ? j % 3 : 0;
		return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, `$1${t}`) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
	},
	getUrlParameter(name) {
		name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
		const regex = new RegExp(`[\\?&]${name}=([^&#]*)`);
		const results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	},
	urlformat(url, param) {
		const paramlg = langsupport === true ? `?lg=${APP_PAGE.lang}` : '';
		let munion = url.indexOf('?') >= 0 ? '&' : '?';
		param = this.isEmpty(param) ? '' : munion + param;
		return url + param;
	},
	redirect(url, param) {
		const self = this;
		window.location.replace(self.urlformat(url, param));
	},
	redirectTime(url, param) {
		const self = this;
		if (!this.isEmpty(url)) setTimeout(() => { window.location.replace(self.urlformat(url, param)); }, 500);
	},
	redirectNewWindow(url) {
		const win = window.open(url, '_blank').focus();
	},
	appendLeadingZeroes(n) { return n <= 9 ? `0${n}` : n; },
	findInArray(o, value, key, subkey) {
		const self = this;
		if (self.isEmpty(o) || !self.isArray(o)) return null;
		if (self.isEmpty(value)) return null;

		const resultArray = $.grep(o, function (item, i) {
			if (self.isEmpty(subkey)) return item[key] === value;

			if (this.isEmpty(item[key][subkey])) return null;
			return item[key][subkey] === value;
		}, false);

		return resultArray.length > 0 ? resultArray : null;
	},
	getValueFromObject(obj, arrkey) {
		try {
			if (this.isEmpty(obj)) return null;

			let keys = null;
			let objx = obj;
			$.each(arrkey, (ix, itemkey) => {
				keys = Object.keys(objx);
				// console.log("objx", objx, keys);
				if (keys.indexOf(itemkey) !== -1) {
					objx = objx[itemkey];
				} else return null;
			});

			return objx;
		} catch (ex) {
			return '';
		}
	},
	getjsonfromUrl(url) {
		const d = jQuery.ajax({
			type: 'GET',
			url,
			cache: false,
			async: false,
		}).responseText;
		return JSON.parse(d);
	},

};

const woocfcl_settings = (function ($, window, document, undefined) {
	const countries = (typeof woocfcl_countries !== 'undefined') ? woocfcl_countries : null;
	const states = (typeof woocfcl_states !== 'undefined') ? woocfcl_states : null;
	const options = (typeof woocfcl_options !== 'undefined') ? woocfcl_options : null;
	const config = (typeof woocfcl_config !== 'undefined') ? woocfcl_config : null;
	let currentRowId = 0;

	const currentLang = config.lang.shortlang;
	const jsonTranslate = commHelper.getjsonfromUrl(`/wp-content/plugins/woo-chile-custom/assets/admin/js/i18n/${currentLang}.json`);
	// dom object;
	const container = $('.container');
	const loader = $('#loader');
	const modal = $('#EditFieldModal').modal();
	let table; let select_pagination; let
		select_country;
	const MSG_INVALID_NAME = 'NAME/ID must begin with a lowercase letter ([a-z]) and may be followed by any number of lowercase letters, digits ([0-9]) and underscores ("_")';
	let OPTION_ROW_HTML = '<tr>';
	OPTION_ROW_HTML += '<td style="width:150px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:140px;"/></td>';
	OPTION_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>';
	OPTION_ROW_HTML += '<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>';
	OPTION_ROW_HTML += '<td class="action-cell"><a href="javascript:void(0)" onclick="woocfclRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>';
	OPTION_ROW_HTML += '<td class="action-cell sort ui-sortable-handle"></td>';
	OPTION_ROW_HTML += '</tr>';

	const contextState = {
		toolbar: '<div class="input-field col s4"><select id="countries-dropdown" name="countries"></select><label>{0}</label></div>',
		datatable: {

			dom: '<"toolbar"> lfrtip',
			data: options,
			language: { url: '' },
			deferLoading: true,
			columns: [
				{ data: 'RowOrder', className: 'reorder', render(data, type, full, meta) { return dataTableHelper.appendsHideData(data, type, full, meta); } },
				{
					targets: 1,
					searchable: false,
					orderable: false,
					className: 'select-checkbox',
					checkboxes: { selectRow: true },
					render(data, type, full, meta) { return dataTableHelper.appendsCheckboxselection(data, type, full, meta); },
				},
				{ data: 'ID' },
				{ data: 'country', visible: false },
				{ data: 'Name' },
				{ data: 'AdditionalCode' },
				{ data: 'NumberCode' },
				{ data: 'enabled', render(data, type, full, meta) { return dataTableHelper.appendswitch(data, type, full, meta); } },
				{
					data: null,
					visible: true,
					targets: 8,
					searchable: false,
					orderable: false,
					className: 'text-center',
					render(data, type, full, meta) { return dataTableHelper.appendsButtonEdit(data, type, full, meta); },
				},
			],
			columnDefs: [{ orderable: false, targets: [0, 1, 2, 3, 4, 5, 6, 7, 8] }],
			rowReorder: { dataSrc: 'RowOrder' },
			select: { style: 'multi' },
			keys: { keys: [13 /* ENTER */, 38 /* UP */, 40] },
			initComplete() {
				this.api().columns([3]).search(countries[0].ISO2).draw();
				dataTableHelper.setToolbar(contextState.toolbar, [jsonTranslate.country]);
				select_country = formhelper.setSelectControl('#countries-dropdown', countries, 'ISO2', 'Name');
				select_country.on('change', function () {
					table.columns([3]).search(this.value).draw();
				});
			},

		},
	};
	const dataTableHelper = {
		jsonUrlLang: '/wp-content/plugins/woo-chile-custom/assets/admin/vendors/datatable/i18n/{0}.json',
		getjsonlang() {
			
			return this.jsonUrlLang.replace('{0}', currentLang);
		},
		appendsHideData(data, type, full, meta) {
			const rowId=this.setDataRow(meta);
			return `<div class='novisible' ${rowId}>${data}</div>`;
		},
		appendswitch(data, type, row, meta) {
			const cheched = Number(data) === 1 ? 'checked' : '';
			const rowId=this.setDataRow(meta);
			// console.log(jsonTranslate);
			return `<div class="switch"><label>${jsonTranslate.no}<input type="checkbox" ${cheched}  ${rowId}  class="switch"><span class="lever"></span> ${jsonTranslate.yes}</label></div>`;	
		},
		onChangeSwitch(event) {
			//if ($('#select_all:checked').val() === 'on') table.rows().select();
			if ($(this).is(':checked')) table.rows().select();
			else table.rows().deselect();
		},
		appendsCheckboxselection(data, type, full, meta) {
			const rowId=this.setDataRow(meta);
			return `<input type="checkbox" name="id[]" class="filled-in" ${rowId} value="0">`;
		},
		appendsButtonEdit(data, type, full, meta) {
			const rowId=this.setDataRow(meta);
			return `<button class="btn btn-xs btn-edit" ${rowId} type="button" >${jsonTranslate.edit}</button>`;
		},
		setToolbar(toolbar, items) {
			let tool = toolbar;
			let i = 0;
			items.forEach((item) => {
				tool = tool.replace(`{${i}}`, item);
				i += 1;
			});

			$('div.toolbar').html(tool);
			$('.dataTables_length').appendTo('div.toolbar');
			$('.dataTables_length select').formSelect();
		},
		setDataRow(meta) {
			return  ` data-row="${meta.row}" `;
		},
		getDataRow(self) {
			currentRowId = $(self).attr('data-row');
			return table.row(currentRowId).data();
		},
		setOnCheckedAll(event) {
			//if ($('#select_all:checked').val() === 'on') table.rows().select();
			if ($(this).is(':checked')) table.rows().select();
			else table.rows().deselect();
		}


	};
	const formhelper = {
		setSelectControl(name, data, keyvalue, keyname) {
			const control = $(name);
			control.empty();
			$.each(data, (key, entry) => {
				control.append($('<option></option>').attr('value', entry[keyvalue]).text(entry[keyname]));
			});
			control.prop('selectedIndex', 0);
			control.formSelect();
			return control;
		},
		set_property_field_value(form, type, name, value, multiple) {
			switch (type) {
				case 'select':
					if (multiple == 1) {
						value = typeof (value) === 'string' ? value.split(',') : value;
						name = `${name}[]`;
						form.find(`select[name="i_${name}"]`).val(value).trigger('change');
					} else {
						form.find(`select[name="i_${name}"]`).val(value);
					}
					break;

				case 'checkbox':
					value = value == 1;
					form.find(`input[name=i_${name}]`).prop('checked', value);
					break;

				case 'textarea':
					value = value ? decodeHtml(value) : value;
					form.find(`textarea[name=i_${name}]`).val(value);
					break;

				default:
					value = value ? decodeHtml(value) : value;
					form.find(`input[name=i_${name}]`).val(value);
			}
		},
	};

	const app = {

		showSpinner() {
			loader.fadeIn();
			container.fadeOut();
		},
		hideSpinner() {
			loader.fadeOut();
			container.fadeIn();
		},
	};
	$(function () {
		if (config.view === 'states') {
			contextState.datatable.language.url = dataTableHelper.getjsonlang();
			table = $('#options-datatable').DataTable(contextState.datatable);
			// set search text on specific column
			table.on('click', '#select_all', dataTableHelper.setOnCheckedAll);
			// Handle click on "Edit" button
			table.on('change', '.switch input[type=checkbox]', function () {
				const data = dataTableHelper.getDataRow(this);
				const status = $(this).prop('checked') ? 1 : 0;
				data.enabled = status;
				table.row(rowId).data(data).invalidate();
			});
			// Handle click on "Edit" button
			table.on('click', '.btn-edit', function () {
				// Reset form
				currentRowId = $(this).attr('data-row');
				const data = table.row(currentRowId).data();

				$('#form-state-edit').get(0).reset();
				$('#form-state-edit input:checkbox').removeAttr('checked');
				$("#form-state-edit .modal-body input[name='RowId']").val(currentRowId);

				for (const key in data) {
					const inp = $(`#form-state-edit .modal-body input[name='${key}']`);
					if (inp.length > 0) { if (inp.attr('type') === 'checkbox' && Number(data[key]) === 1) inp.attr('checked', 'checked'); else inp.val(data[key]); }
				}
				modal.modal('open');
			});

			// Handle form submission event
			$('#form-state-edit').on('submit', function (e) {
				e.preventDefault();
				// Update table data
				const data = table.row(currentRowId).data();

				for (const key in data) {
					console.log(key);
					const inp = $(`#form-state-edit .modal-body input[name='${key}']`);
					if (inp.length > 0) { if (inp.attr('type') === 'checkbox') data[key] = $(this).prop('checked') ? 1 : 0; else data[key] = inp.val(); }
				}
				console.log(data);
				table.row(row).data(data).invalidate();

				// Refresh table
				table.draw(false);
				modal.modal('close');
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
				click() {
					const form = $('#woocfcl_new_field_form');
					const valid = validate_field_form(form);
					if (valid) {
						prepare_field_form(form);
						form.submit();
					}
				},
			}],
		});

		$('#woocfcl_edit_field_form_pp').dialog({
			modal: true,
			width: 600,
			resizable: false,
			autoOpen: false,
			buttons: [{
				text: 'Save',
				click() {
					const form = $('#woocfcl_edit_field_form');
					const valid = validate_field_form(form);
					if (valid) {
						prepare_field_form(form);
						form.submit();
					}
				},
			}],
		});

		$('select.woocfcl-enhanced-multi-select').select2({
			minimumResultsForSearch: 10,
			allowClear: true,
			placeholder: $(this).data('placeholder'),
		}).addClass('enhanced');

		$('#woocfcl_checkout_fields tbody').sortable({
			items: 'tr',
			cursor: 'move',
			axis: 'y',
			handle: 'td.sort',
			scrollSensitivity: 40,
			helper(e, ui) {
				ui.children().each(function () {
					$(this).width($(this).width());
				});
				ui.css('left', '0');
				return ui;
			},
		});

		$('#woocfcl_checkout_fields tbody').on('sortstart', (event, ui) => {
			ui.item.css('background-color', '#f6f6f6');
		});
		$('#woocfcl_checkout_fields tbody').on('sortstop', (event, ui) => {
			ui.item.removeAttr('style');
			prepare_field_order_indexes();
		});
	});

	function isHtmlIdValid(id) {
		const re = /^[a-z\_]+[a-z0-9\_]*$/;
		return re.test(id.trim());
	}

	function decodeHtml(str) {
		if ($.type(str) === 'string') {
			const map = {
				'&amp;': '&',
				'&lt;': '<',
				'&gt;': '>',
				'&quot;': '"',
				'&#039;': "'",
			};
			return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, (m) => map[m]);
		}
		return str;
	}

	function get_property_field_value(form, type, name) {
		let value = '';

		switch (type) {
			case 'select':
				value = form.find(`select[name=i_${name}]`).val();
				value = value == null ? '' : value;
				break;

			case 'checkbox':
				value = form.find(`input[name=i_${name}]`).prop('checked');
				value = value ? 1 : 0;
				break;

			case 'textarea':
				value = form.find(`textarea[name=i_${name}]`).val();
				value = value == null ? '' : value;

			default:
				value = form.find(`input[name=i_${name}]`).val();
				value = value == null ? '' : value;
		}

		return value;
	}

	function set_property_field_value(form, type, name, value, multiple) {
		switch (type) {
			case 'select':
				if (multiple == 1) {
					value = typeof (value) === 'string' ? value.split(',') : value;
					name = `${name}[]`;
					form.find(`select[name="i_${name}"]`).val(value).trigger('change');
				} else {
					form.find(`select[name="i_${name}"]`).val(value);
				}
				break;

			case 'checkbox':
				value = value == 1;
				form.find(`input[name=i_${name}]`).prop('checked', value);
				break;

			case 'textarea':
				value = value ? decodeHtml(value) : value;
				form.find(`textarea[name=i_${name}]`).val(value);
				break;

			default:
				value = value ? decodeHtml(value) : value;
				form.find(`input[name=i_${name}]`).val(value);
		}
	}

	function openNewFieldForm(sname) {
		if (sname == 'billing' || sname == 'shipping' || sname == 'additional') {
			sname = `${sname}_`;
		}

		const form = $('#woocfcl_new_field_form');
		clear_field_form(form);
		form.find('select[name=i_type]').change();

		set_property_field_value(form, 'text', 'name', sname, 0);
		set_property_field_value(form, 'text', 'class', 'form-row-wide', 0);

		$('#woocfcl_new_field_form_pp').dialog('open');
	}

	function openEditFieldForm(elm, rowId) {
		const row = $(elm).closest('tr');
		const form = $('#woocfcl_edit_field_form');

		const props_json = row.find('.f_props').val();
		// props_json = decodeHtml(props_json);
		const props = JSON.parse(props_json);
		// var type = props.type;

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
		set_property_field_value(form, 'text', 'default', '', 0);
		// set_property_field_value(form, 'text', 'options', '', 0);
		set_property_field_value(form, 'text', 'class', '', 0);
		set_property_field_value(form, 'select', 'validate', '', 1);

		set_property_field_value(form, 'checkbox', 'required', 1, 0);
		// set_property_field_value(form, 'checkbox', 'clear', 1, 0);
		set_property_field_value(form, 'checkbox', 'enabled', 1, 0);
		set_property_field_value(form, 'checkbox', 'show_in_email', 1, 0);
		set_property_field_value(form, 'checkbox', 'show_in_order', 1, 0);

		populate_options_list(form, false);
	}

	function populate_field_form_general(form, props) {
		const autocomplete = props.autocomplete ? props.autocomplete : '';
		const priority = props.priority ? props.priority : '';
		const custom = props.custom ? props.custom : '';

		const type = props.type ? props.type : 'text';
		const name = props.name ? props.name : '';

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

		const label = props.label ? props.label : '';
		const placeholder = props.placeholder ? props.placeholder : '';
		const default_val = props.default ? props.default : '';
		// var options = props['options'] ? props['options'] : '';
		const cssclass = props.class ? props.class : '';
		const validate = props.validate ? props.validate : '';

		const required = props.required && (props.required || props.required === 'yes') ? 1 : 0;
		// var clear = props['clear'] && (props['clear'] || props['clear'] === 'yes') ? 1 : 0;
		const enabled = props.enabled && (props.enabled || props.enabled === 'yes') ? 1 : 0;
		let show_in_email = props.show_in_email && (props.show_in_email || props.show_in_email === 'yes') ? 1 : 0;
		let show_in_order = props.show_in_order && (props.show_in_order || props.show_in_order === 'yes') ? 1 : 0;

		show_in_email = custom == 1 ? show_in_email : true;
		show_in_order = custom == 1 ? show_in_order : true;

		set_property_field_value(form, 'text', 'label', label, 0);
		set_property_field_value(form, 'text', 'placeholder', placeholder, 0);
		set_property_field_value(form, 'text', 'default', default_val, 0);
		// set_property_field_value(form, 'text', 'options', options, 0);
		set_property_field_value(form, 'text', 'class', cssclass, 0);
		set_property_field_value(form, 'select', 'validate', validate, 1);

		set_property_field_value(form, 'checkbox', 'required', required, 0);
		// set_property_field_value(form, 'checkbox', 'clear', clear, 0);
		set_property_field_value(form, 'checkbox', 'enabled', enabled, 0);
		set_property_field_value(form, 'checkbox', 'show_in_email', show_in_email, 0);
		set_property_field_value(form, 'checkbox', 'show_in_order', show_in_order, 0);

		const optionsJson = row.find('.f_options').val();
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
		const options_json = get_options(form);
		set_property_field_value(form, 'hidden', 'options_json', options_json, 0);
	}

	function validate_field_form(form) {
		let err_msgs = '';
		const name = get_property_field_value(form, 'text', 'name');
		const type = get_property_field_value(form, 'select', 'type');
		const otype = get_property_field_value(form, 'select', 'otype');

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
		const type = $(elm).val();
		const form = $(elm).closest('form');

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
		const optionsKey = form.find("input[name='i_options_key[]']").map(function () { return $(this).val(); }).get();
		const optionsText = form.find("input[name='i_options_text[]']").map(function () { return $(this).val(); }).get();

		const optionsSize = optionsText.length;
		const optionsArr = [];

		for (let i = 0; i < optionsSize; i++) {
			const optionDetails = {};
			optionDetails.key = optionsKey[i];
			optionDetails.text = optionsText[i];

			optionsArr.push(optionDetails);
		}

		let optionsJson = optionsArr.length > 0 ? JSON.stringify(optionsArr) : '';
		optionsJson = encodeURIComponent(optionsJson);
		return optionsJson;
	}

	function populate_options_list(form, optionsJson) {
		let optionsHtml = '';

		if (optionsJson) {
			try {
				optionsJson = decodeURIComponent(optionsJson);
				const optionsList = $.parseJSON(optionsJson);
				if (optionsList) {
					jQuery.each(optionsList, function () {
						let html = '<tr>';
						html += `<td style="width:150px;"><input type="text" name="i_options_key[]" value="${this.key}" placeholder="Option Value" style="width:140px;"/></td>`;
						html += `<td style="width:190px;"><input type="text" name="i_options_text[]" value="${this.text}" placeholder="Option Text" style="width:180px;"/></td>`;
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

		const optionsTable = form.find('.woocfcl-option-list tbody');
		if (optionsHtml) {
			optionsTable.html(optionsHtml);
		} else {
			optionsTable.html(OPTION_ROW_HTML);
		}
	}

	function add_new_option_row(elm) {
		const ptable = $(elm).closest('table');
		const optionsSize = ptable.find('tbody tr').size();

		if (optionsSize > 0) {
			ptable.find('tbody tr:last').after(OPTION_ROW_HTML);
		} else {
			ptable.find('tbody').append(OPTION_ROW_HTML);
		}
	}

	function remove_option_row(elm) {
		const ptable = $(elm).closest('table');
		$(elm).closest('tr').remove();
		const optionsSize = ptable.find('tbody tr').size();

		if (optionsSize == 0) {
			ptable.find('tbody').append(OPTION_ROW_HTML);
		}
	}
	/*------------------------------------
		*---- OPTIONS FUNCTIONS - END --------
		*------------------------------------*/

	function prepare_field_order_indexes() {
		$('#woocfcl_checkout_fields tbody tr').each((index, el) => {
			$('input.f_order', el).val(parseInt($(el).index('#woocfcl_checkout_fields tbody tr')));
		});
	}

	function selectAllCheckoutFields(elm) {
		const checkAll = $(elm).prop('checked');
		$('#woocfcl_checkout_fields tbody input:checkbox[name=select_field]').prop('checked', checkAll);
	}

	function removeSelectedFields() {
		$('#woocfcl_checkout_fields tbody tr').removeClass('thpladmin-strikeout');
		$('#woocfcl_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			const row = $(this).closest('tr');

			if (!row.hasClass('woocfcl-strikeout')) {
				row.addClass('woocfcl-strikeout');
			}

			row.find('.f_deleted').val(1);
			row.find('.f_edit_btn').prop('disabled', true);
		});
	}

	function enableDisableSelectedFields(enabled) {
		$('#woocfcl_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			const row = $(this).closest('tr');
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
		openNewFieldForm,
		openEditFieldForm,
		selectAllCheckoutFields,
		removeSelectedFields,
		enableDisableSelectedFields,
		fieldTypeChangeListner,
		addNewOptionRow: add_new_option_row,
		removeOptionRow: remove_option_row,
	};
}(window.jQuery, window, document));

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
}
