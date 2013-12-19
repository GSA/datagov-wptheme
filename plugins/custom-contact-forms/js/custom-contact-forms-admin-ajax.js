
/** {{{ http://code.activestate.com/recipes/414334/ (r1) */
// This is Javascript, not PHP!

function is_int(value){ 
  if((parseFloat(value) == parseInt(value)) && !isNaN(value)){
      return true;
  } else { 
      return false;
  } 
}

function js_array_to_php_array (a)
{
    var a_php = "";
    var total = 0;
    for (var key in a)
    {
		if (is_int(key)) {
			++ total;
			a_php = a_php + "s:" +
					String(key).length + ":\"" + String(key) + "\";s:" +
					String(a[key]).length + ":\"" + String(a[key]) + "\";";
		}
	}
    a_php = "a:" + total + ":{" + a_php + "}";
    return a_php;
}

function print_r(x, max, sep, l) {

	l = l || 0;
	max = max || 10;
	sep = sep || ' ';

	if (l > max) {
		return "[WARNING: Too much recursion]\n";
	}

	var
		i,
		r = '',
		t = typeof x,
		tab = '';

	if (x === null) {
		r += "(null)\n";
	} else if (t == 'object') {

		l++;

		for (i = 0; i < l; i++) {
			tab += sep;
		}

		if (x && x.length) {
			t = 'array';
		}

		r += '(' + t + ") :\n";

		for (i in x) {
			try {
				r += tab + '<br />[' + i + '] : ' + print_r(x[i], max, sep, (l + 1));
			} catch(e) {
				return "[ERROR: " + e + "]\n";
			}
		}

	} else {

		if (t == 'string') {
			if (x == '') {
				x = '(empty)';
			}
		}

		r += '(' + t + ') ' + x + "\n";

	}

	return r;

};

function getFormFieldValue(field_name, formData) {
	for(var i = 0; i < formData.length; i++) {
		if (formData[i].name == field_name) {
			return formData[i].value;
		}
	}
	return false;
};

function deleteCleanUp(this_object, form_dom) {
	//alert(print_r(this_object));
	//alert(".row-" + this_object.object_type + "-" + this_object.object_id);
	form_dom.find(".row-" + this_object.object_type + "-" + this_object.object_id).hide().remove();
	if (this_object.object_type == "style") {
		/* delete occurences of this option within style dropdowns. */
		var style_inputs = $j(".form_style_input");
		style_inputs.each(function() {
			this_option = $j(this).find("option[value=" + this_object.object_id + "]");
			if (this_option.attr("selected") == "selected")
				$j(this).find("option[value=0]").attr("selected", "selected");
			this_option.remove();
		});
	} else if (this_object.object_type == "field" || this_object.object_type == "field_option") {
		if (this_object.object_type == "field") {
			var fields_options_input = $j("select.field-dropdown");
			var fields_options_list = $j("ul.field-list");
		} else {
			var fields_options_input = $j("select.field-option-dropdown");
			var fields_options_list = $j("ul.field-option-list");
		}
		fields_options_input.each(function () {
			var this_obj = $j(this);
			var this_option = this_obj.find("option[value=" + this_object.object_id + "]");
			if (this_option.length >=1 && this_obj.find("option").length <= 1) {
				$j("<option>")
					.attr("value", "-1")
					.text(ccfLang.no_fields)
					.prependTo(this_obj);
			}
			this_option.remove();
		});
		fields_options_list.each(function () {
			var this_obj = $j(this);
			var this_option = this_obj.find(".field"+this_object.object_id);
			this_option.remove();
		});
		if (this_object.object_type == "field")
			fields_options_input = $j("select.attach-field option[value=" + this_object.object_id + "]");
		else
			fields_options_input = $j("select.attach-field-option option[value=" + this_object.object_id + "]");
			
		fields_options_input.each(function () {
			$j(this).remove();
		});
	}
};

$j.preloadImages(ccfAjax.plugin_dir + "/images/wpspin_light.gif"); // preload loading image
$j(document).ready(function() {
	
	//initPagination();
	$j('.ccf-edit-ajax').attr("action", ccfAjax.url);
	
	var loading_img = null;
	var form_dom = null;
	$j('.ccf-edit-ajax').ajaxForm({
		data: { action: 'ccf-ajax', nonce: ccfLang.nonce },
		beforeSubmit: function(formData, jqForm, options)  {
			var action_type = getFormFieldValue('object_bulk_action', formData);
			//var bulk_apply_button = getFormFieldValue('object_bulk_action', formData);
			var attach_button = getFormFieldValue('buttons', formData);
			var detach_button = getFormFieldValue('object_bulk_action', formData);
			if (action_type == 0) return false;
			bulk_button = jqForm.find("input[name=object_bulk_apply]");
			form_dom = jqForm;
			loading_img = jqForm.find(".loading-img").fadeIn();
			return true;
		},
		success : function(responseText) {
			if (responseText.objects) {
				for (var i = 0; i < responseText.objects.length; i++) {
					if (responseText.object_bulk_action == 'delete') {
						deleteCleanUp(responseText.objects[i], form_dom);
					} else if (responseText.object_bulk_action == 'edit') {
						/* TODO: update field and field option slug dropdowns */
						if (responseText.objects[i].object_type == "field" || responseText.objects[i].object_type == "field_option") {
							
						}
					}
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		debug = fx.initDebugWindow();
					$j("<div></div>").html(textStatus + " " + errorThrown).appendTo(debug);
			//alert(textStatus + " " + errorThrown);
		},
		complete: function() {
			//j("test").html(data).appendTo(debug);
			loading_img.fadeOut();
		}
	});

	$j(".single-save").click(function () {
		var single_save = $j(this);
		var object_type = single_save.parent().find(".object-type").attr("value");
		single_save.parentsUntil("form").parent().ajaxSubmit({
			data: { action: 'ccf-ajax', nonce: ccfLang.nonce },
			url: ccfAjax.url,
			complete: function() {
				loading_img.fadeOut();
			},
			beforeSubmit: function(formData, jqForm, options)  {
				var object_id = single_save.parent().find(".object-id").val();
				loading_img = single_save.parent().find(".loading-img-inner-"+ object_type +"-"+object_id).fadeIn();
				var last_index = 0;
				$j.each(formData, function(i, obj) {
					if (obj != undefined && obj.name.indexOf("[object_do]") != -1) {
						formData.splice(i, 1);		
					}
					last_index = i;
				});
				var new_obj = new Object();
				new_obj.name = "objects[" + object_id + "][object_do]";
				new_obj.value = 1;
				var new_obj2 = new Object();
				new_obj2.name = "object_bulk_action";
				new_obj2.value = "edit";
				var new_obj3 = new Object();
				new_obj3.name = "object_bulk_apply";
				new_obj3.value = "1";
				formData[last_index + 1] = new_obj;
				formData[last_index + 2] = new_obj2;
				formData[last_index + 3] = new_obj3;
				form_dom = jqForm;
				return true;
			}				
		});
		return false;
	});
	
	$j(".single-delete").click(function () {
            if (confirm(ccfLang.delete_confirm)) {
		var single_delete = $j(this);
		var object_type = single_delete.parent().find(".object-type").attr("value");
		single_delete.parentsUntil("form").parent().ajaxSubmit({
			data: { action: 'ccf-ajax', nonce: ccfLang.nonce },
			url: ccfAjax.url,
			success : function(responseText) {
				if (responseText.objects) {
					for (var i = 0; i < responseText.objects.length; i++) {
						deleteCleanUp(responseText.objects[i], form_dom);
					}
				}
			},
			complete: function() {
				
			},
			beforeSubmit: function(formData, jqForm, options)  {
				var object_id = single_delete.parent().find(".object-id").val();
				loading_img = single_delete.parent().find(".loading-img-inner-"+ object_type +"-"+object_id).fadeIn();
				var last_index = 0;
				$j.each(formData, function(i, obj) {
					if (obj != undefined && obj.name.indexOf("[object_do]") != -1) {
						formData.splice(i, 1);		
					}
					last_index = i;
				});
				var new_obj = new Object();
				new_obj.name = "objects[" + object_id + "][object_do]";
				new_obj.value = 1;
				var new_obj2 = new Object();
				new_obj2.name = "object_bulk_action";
				new_obj2.value = "delete";
				var new_obj3 = new Object();
				new_obj3.name = "object_bulk_apply";
				new_obj3.value = "1";
				formData[last_index + 1] = new_obj;
				formData[last_index + 2] = new_obj2;
				formData[last_index + 3] = new_obj3;
				form_dom = jqForm;
				return true;
			}				
		});
            }
	    return false;
	});
	
	$j(".ccfsort").find("span").click(function() { $j(this).parent().hide().remove(); });
	
	$j(".attach-button").live("click", function() {
		var object_type = $j(this).parentsUntil("table").find(".object-type").attr("value");
		var attach_object_field = $j(this).parent().find(".attach-object");
		var object_id = attach_object_field.attr("class").split(' ')[0].replace(/[^0-9]*([0-9]*)/, "$1");
		var attached_list = $j(this).parentsUntil('td').find(".attached ul");
		var attach_object_id = attach_object_field.attr("value");
		var attach_object_text = attach_object_field.find("option[value=" + attach_object_id + "]:eq(0)").first().text();
		var already_attached = false;
		
		attached_list.find("li").each(function() {
			var classes = $j(this).attr("class").split(' ');
			$j.each(classes, function(index, cls) {
				if (cls == "field" + attach_object_id) {
					already_attached = true;
				}
			});
		});
		
		if (!already_attached) {
			var new_li = $j("<li>").html(attach_object_text).addClass("field" + attach_object_id).addClass("ui-state-default").appendTo(attached_list);
			var new_span = $j("<span>").html("&times;").prependTo(new_li);
			new_span.click(function() { $j(this).parent().hide().remove(); });
		}
	});
	
	$j(".attached-update-button").live("click", function() {
		var object_type = $j(this).parentsUntil("table").find(".object-type").attr("value");
		var attached_list = $j(this).parent().parent().find(".attached ul");
		var fields_array = new Array();
		var i = 0;
		attached_list.find("li").each(function() {
			fields_array[i] = $j(this).attr("class").replace(/^[^0-9]*?field([0-9]+?)[^0-9]*?$/i, "$1");
			i++;
		});
		var object_id = attached_list.attr("class").replace(/^[^0-9]*?onObject([0-9]+?)[^0-9]*?$/i, "$1");
		$j.ajax({
			type: "POST",
			url: ccfAjax.url,
			data: "nonce=" + ccfLang.nonce + "&action=ccf-ajax&attached_save=1&attached_array=" + js_array_to_php_array(fields_array) + "&object_id=" + object_id + "&object_type=" + object_type,
			success: function(data) {
				
			},
			beforeSend: function()  {
				loading_img = $j(".loading-img-field-config-" + object_type + "-" + object_id).fadeIn();	
			},
			complete: function() { loading_img.fadeOut(); }
		});
	});
		
});