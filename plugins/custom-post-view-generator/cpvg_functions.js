//************************ KNOCKOUTJS **************************/

//used to stored dynamic data that will be changed during the session
var volatile_data = function(data){
	var vol_data = ko.toJS(data);

	for (var key in vol_data){
		this[key] = ko.observable(vol_data[key]);
	}

    this.setMutipleData = function(data) {
		for(var key in data){
			this[key](data[key]);
		}
	}

    this.setMutipleObservableData = function(data) {
		for(var key in data){
			if(typeof(data[key]()) != 'undefined'){
				this[key](data[key]());
			}
		}
	}

    this.setData = function(name,data,type) {
		if(type == 'json'){
			this[name] = ko.toJS(data);
		}else if(type == 'array-append'){
			if(!this[name]){ this[name] = ko.observableArray([]);}

			for(var key in data){
				this[name].push(data[key]);
			}
		}else{
			if(!this[name]){ this[name] = ko.observable([]); }
			this[name](data);
		}
    }
}

//used to store static data that will be sent to the server
var static_data = function(static_data){
	for (var key in static_data){
		this[key] = static_data[key];
	}

    this.setData = function(name,data,type) {
		this[name] = data;
    }
    this.setMutiple = function(static_data) {
		for (var key in static_data){
			this[key] = static_data[key];
		}
    }
}

//fieldtype option - used on the drag-and-drop options
var fieldtype_option = function (name,label){
	this.name = ko.observable(name);
	this.label = ko.observable(label);
	
	this.type = ko.observable('cpvg_text');
	this.hide_empty =  ko.observable(false);
	this.extra_options = { 'checkboxes':ko.observableArray([]) }
	this.temp_data = { }

	this.type.subscribe(function(value){
		//garbage collection - remove options vars
		for(var prop_name in this){
			if(prop_name.slice(0,7) == 'options'){
				delete this[prop_name];
			}
		}
	}.bind(this));

	this.removeOptionCtp = function(){
		viewModel.current_fieldtype_options.remove(this);
	}

	this.getOutputVar = function(index){
		if(typeof(this["options"+(index+1)]) == 'undefined'){
			this["options"+(index+1)] = ko.observable();
		}
		return this["options"+(index+1)];
	}
	
	this.moveOptionCtpUp = function(){
		var item_index = viewModel.current_fieldtype_options.indexOf(this);
		
		if(item_index != 0){
			item_index-=1;
		}
		viewModel.current_fieldtype_options.remove(this);
		viewModel.current_fieldtype_options.splice(item_index,0,this);	
	}
	this.moveOptionCtpDown = function(){
		var item_index = viewModel.current_fieldtype_options.indexOf(this);
		
		if(viewModel.current_fieldtype_options().length != 0){
			item_index+=1;
		}
		viewModel.current_fieldtype_options.remove(this);
		viewModel.current_fieldtype_options.splice(item_index,0,this);	
	}		
}

//used to store parameter data of the list views
var basic_param = function(data){
	this.section = ko.observable();
	this.name = ko.observable();

	this.parameter = ko.observable();
	this.parameter.subscribe(function(value){
		if(typeof(viewModel.getParamData('choices')) != 'undefined'){
			this.select_input_value(false);
		}else{
			this.select_input_value(true);
		}

	}.bind(this));

	this.select_value = ko.observableArray();
	this.input_value = ko.observable('');
	this.select_input_value = ko.observable(false);

	this.select_input_value.subscribe(function(value){
		if(value){
			jQuery('.cvpg-multi-select').attr('disabled','disabled');
		}else{
			jQuery('.cvpg-multi-select').removeAttr('disabled');

			this.input_value('');
		}
	}.bind(this));

	this.value = ko.dependentObservable({
		read: function () {
			if(this.select_input_value() == true || this.select_input_value() == "true"){
				return this.input_value();
			}

			if(this.select_value()[0] == undefined){
				this.select_value().shift();
			}
			return this.select_value();
		},
		write: function (value) {},
		owner: this });

	this.operator = ko.observable();
	this.type = ko.observable('CHAR');

	for (var key in data){
		if(data[key] == 'false'){
			this[key](false);
		}else if(data[key] == 'true'){
			this[key](true);
		}else{
			this[key](data[key]);
		}
	}
}

//Creates knockoutjs view model to be used of the template
var viewModel = {
	//MODEL VARS
	view_type: 'post',
	siteurl: '',
	touchscreen:ko.observable(false),

	//USED IN BOTH POST AND LIST VIEW
	available_fieldtypes: { 'types':[] , 'options':{} },
	available_template_files: [],

	available_post_types: [],
	available_all_fields: [],
		
	available_fields: {},
	available_custom_fields: {},
	available_extra_data: {},

	selected_post_type: ko.observable(),

	selected_field_section: ko.observable(),
	selected_post_template: ko.observable(),

	current_fieldtype_options: ko.observableArray([]),
	
	temp_data: {},
	acf_enabled: true,
	//selected_field_custom_text: new fieldtype_option("section.fieldname","label"),

	//LIST VIEWS
	available_listviews_names: ko.observableArray([]),
	selected_list: { 'id' : ko.observable('') , 'name' : ko.observable(''), 'original_name' : '', 'template':ko.observable(), 'index': ko.observable(-1) },

	available_param_names: [{'value':'filter','text':'Filters' } , { 'value':'order','text':'Order' },
							{'value':'pagination','text':'Pagination' }, {'value':'usersorting','text':'User Sorting' }],
	available_param_data: { 'filter':'', 'order':'','pagination':'','usersorting':'' },

	selected_param: { 'type': ko.observable('filter'), 'index': ko.observable(0), 'item':'', 'visible_form':ko.observable(false) },

	current_params: {
		'order': ko.observableArray([]),
		'filter': ko.observableArray([]),
		'pagination': ko.observableArray([]),
		'usersorting': ko.observableArray([])
	},
	post_page_name: ko.observable('Insert name for page/post'),
	/*SERVER/ACTION FUNCTIONS*/
	//Parses several types of data and store on the viewModel
    setData: function(name,data,type) {
		var existing_type = typeof(this[name]);

		if(existing_type == 'undefined'){ //not set
			switch (type){
				case 'array':
				case 'assocArray':
				case 'arrayObservables': this[name] = ko.observableArray([]); break;
				case 'observable': this[name] =  ko.observable(); break;
				case 'observableArray': this[name](data); break;
			}
		}
		switch (type){
			case 'observable':
			case 'observableArray': this[name](data); break;
			case 'assocArray':
				/*for(var key in data){
					this[name][this[name].length] = { 'key':key, 'value':data[key] };
				}*/
				this[name] = convert_assoc_array(data);
			break;
			case 'arrayObservables':
				for(var key in data){
					this[name].push( { 'value': ko.observable(data[key])} );
				}
			break;
			case 'json':
			case 'array':
			default: this[name] = data; break;
		}
		
		//additional processing for certain vars
		if(name == 'available_extra_data'){
			for(var field_section_name in this['available_extra_data']){
				if(typeof(this['available_extra_data'][field_section_name]['form_fields']) != 'undefined'){
					for(var extra_field_name in this['available_extra_data'][field_section_name]['form_fields']){					
						if(typeof(this['available_extra_data'][field_section_name]['form_fields'][extra_field_name]['options']) != 'undefined'){
							this['available_extra_data'][field_section_name]['form_fields'][extra_field_name]['options'] = convert_assoc_array(this['available_extra_data'][field_section_name]['form_fields'][extra_field_name]['options'] );
						}
					}
				}	
			}
		}else if(name == 'available_fields'){
			var sections = this.getAvailableSections();

			for(var section_key in sections){
				if(sections[section_key] != 'misc'){
					for(var field_key in this.available_fields[sections[section_key]]){
						this.available_all_fields[this.available_all_fields.length] = { 'key':sections[section_key]+"."+field_key, 'value':sections[section_key].toUpperCase()+": "+this.available_fields[sections[section_key]][field_key] };
					}
				}
			}
			for(var section_key in sections){
				for(var field_key in this.available_custom_fields[sections[section_key]]){
					this.available_all_fields[this.available_all_fields.length] = { 'key':sections[section_key]+"."+this.available_custom_fields[sections[section_key]][field_key] ,'value':sections[section_key].toUpperCase()+": "+this.available_custom_fields[sections[section_key]][field_key] };
				}
			}			
		}
    },
	//Parses all field types and available options - cpvg_text,...,etc
	setAvailableFieldTypes: function(data) {
		for(var key in data){
			this.available_fieldtypes.types[this.available_fieldtypes.types.length] = {'key':key,'value':data[key].label};

			if(data[key].options){
				this.available_fieldtypes[key] = [];
				
				for(var opt_index in data[key].options){
					this.available_fieldtypes[key][opt_index] = [];

					for(var opt_key in data[key].options[opt_index]){
						this.available_fieldtypes[key][opt_index][this.available_fieldtypes[key][opt_index].length] = {'key':opt_key,'value':data[key].options[opt_index][opt_key]};
					}
				}
			}
		}
    },
    //Parses all fields and sections displayed rigth side of the forms - custom fields, post fields,..,etc
 	getAvailableSections: function() {
		if(typeof(this.selected_post_type()) == 'undefined'){
			return this.available_fields.field_sections;
		}else{
			if(jQuery.inArray(this.selected_post_type(),this.available_fields.field_sections) > -1){
				if(this.acf_enabled == "true"){
					return jQuery.merge(jQuery.merge([],this.available_fields.field_sections), ['acf']);
				}
				return this.available_fields.field_sections;
			}else{
				return jQuery.merge(jQuery.merge([],this.available_fields.field_sections), [this.selected_post_type()]);
			}
		}
	},	
	/*FIELD TYPE OPTIONS*/
	//Adds a new field type - call the field is droped in the left side of the form
	addFieldtypeOption: function(name,label,ft_option){
		if(!(viewModel.getDatafieldExtraData(name,'label'))){
			label = undefined;
		}	
		if(typeof(ft_option) == 'undefined'){
			var new_fieldtype_option = new fieldtype_option(name,label);
		}else{
			var new_fieldtype_option = ft_option;
		}
		   
		if(typeof(viewModel.available_extra_data[name]) != 'undefined'){
			if(typeof(viewModel.available_extra_data[name]['form_fields']) != 'undefined'){
				for(var f_name in viewModel.available_extra_data[name]['form_fields']){
					
					if(typeof(ft_option) == 'undefined'){
						new_fieldtype_option['extra_options'][f_name] = new ko.observable();
					}
					
					if(typeof(viewModel.available_extra_data[name]['form_fields'][f_name]['append_field_type']) != 'undefined'){
						new_fieldtype_option['temp_data'][f_name] = new ko.observable();
						viewModel.temp_data[f_name] = new fieldtype_option("section.fieldname","label");					
					}
				}			
			}
		}						
		this.current_fieldtype_options.push(new_fieldtype_option);
	},
	displayFieldtypeOptionWindow: function(section_fieldname,extra_data_field,field_to_append){
		section_fieldname_splitted = section_fieldname.split('.');
		
		viewModel.temp_data[extra_data_field].name(section_fieldname);
		viewModel.temp_data[extra_data_field].label(section_fieldname_splitted[1]);
		
		jQuery("#cpvg-fieldtype-modal").dialog({
			dialogClass : 'wp-dialog',
			resizable: true,
			width: 'auto' ,
			height: 'auto',
			modal: true,
			buttons: {
				'Append Field': function() {
					jQuery(this).dialog('close');

					var output = " [["+section_fieldname;
					output+=";"+viewModel.temp_data[extra_data_field].type();

					for(var prop_name in viewModel.temp_data[extra_data_field]){
						if(prop_name.slice(0,7) == 'options'){
							output+=";"+viewModel.temp_data[extra_data_field][prop_name]();
						}
					}
					output+="]]";
					if(field_to_append() == undefined){
						field_to_append("");
					}
					field_to_append(field_to_append()+output);
					viewModel.temp_data[extra_data_field].type("cpvg_text");
				},
				Cancel: function() {
					jQuery(this).dialog('close');
					viewModel.temp_data[extra_data_field].type("cpvg_text");
				}
			}
		});
	},
	//Cosmetic function - displays a formated name on rigth upper corner of a field type in the form of each field type
	formartFieldtypeOptionName: function(name){
		var name_parts = name.split(".");

		if(name_parts.length == 2){
			var field_name = name_parts[1];

			if(typeof(viewModel.available_fields[name_parts[0]]) != 'undefined' && typeof(viewModel.available_fields[name_parts[0]][name_parts[1]]) != 'undefined'){
				field_name = viewModel.available_fields[name_parts[0]][name_parts[1]];
			}else if(typeof(viewModel.available_custom_fields[name_parts[0]]) != 'undefined' && typeof(viewModel.available_custom_fields[name_parts[0]][name_parts[1]]) != 'undefined'){
				field_name = viewModel.available_custom_fields[name_parts[0]][name_parts[1]];
			}

			return name_parts[0].charAt(0).toUpperCase() + name_parts[0].slice(1) + ' - ' +  field_name;
		}
		return name;
	},

	/*ACTION BUTTONS*/
	//Send data to server - called when creating, updating and deleting views
	sendData: function(action){
		var temp_data = new static_data({ 'action': action, 'post_type': this.selected_post_type() ,
										   'view_type': this.view_type, 'post_type': this.selected_post_type() });

		if(this.view_type == 'post'){
			temp_data.setData("view_value",this.selected_post_type());
		}else if(this.view_type == 'list'){
			temp_data.setMutiple({ "view_value":this.selected_list.original_name,
								   "new_view_value":this.selected_list.name() });
		}

		if(action != 'delete_layout'){
			temp_data.setMutiple({ "template":this.selected_post_template(),
								   "fields":this.current_fieldtype_options() });

			if(this.view_type == 'list'){
				temp_data.setData("param",{});
				for(var key in this.current_params){
					temp_data.param[key] = this.current_params[key];
				}
				temp_data.setData("template",this.selected_list.template());
			}else if(this.view_type == 'post'){
				temp_data.setData("template",this.selected_post_template());
			}
		}

		var send_data = this.removeUnecessarySendData(ko.toJS(temp_data));

		jQuery.post(this.siteurl+"/wp-admin/admin-ajax.php",send_data,
		   function(response){
			   if(action === "save_layout" || action === "delete_layout"){
					jQuery('.action-message').html(response).show(1000).delay(2000).hide(1000);

					if(action == "delete_layout" && typeof(this.current_fieldtype_options) != 'undefined'){
						this.current_fieldtype_options.removeAll();
					}
			   }
			   if(action == "generate_preview"){
				   jQuery('#cpvg-posttype-preview-content').html(response);
			   }
		   }
		);
	},
	//Remove unecessary/temp vars
	removeUnecessarySendData: function(send_data){
		delete send_data.setData;
		delete send_data.setMutiple;
				
		for(var field_key in send_data["fields"]){
			for(var prop_key in send_data["fields"][field_key]){
				if(typeof(send_data["fields"][field_key][prop_key]) == 'function'){
					delete send_data["fields"][field_key][prop_key];
				}
				if(typeof(send_data["fields"][field_key]['temp_data']) != 'undefined'){
					delete send_data["fields"][field_key]['temp_data'];
				}								
			}
			
			var var_name = send_data["fields"][field_key]['name'];
			if(typeof(viewModel.available_extra_data[var_name]) != 'undefined' && 
			   typeof(viewModel.available_extra_data[var_name]['options']) != 'undefined'){
				if(viewModel.available_extra_data[var_name]['options'] == false){					
					for(var prop_name in send_data["fields"][field_key]){
						if(prop_name.slice(0,7) == 'options'){
							delete send_data["fields"][field_key][prop_name];
						}
					}		
				}
				if(viewModel.available_extra_data[var_name]['type'] == false){
					delete send_data["fields"][field_key]['type'];
				}					
				if(viewModel.available_extra_data[var_name]['label'] == false){
					delete send_data["fields"][field_key]['label'];
				}	
			}					
		}	
		return send_data;
	},	
	//Get data from server - updated data when selecting a post type or clicking a list view on the forms
	getServerData: function(){
		var view_value = false;

		if(this.view_type == 'post'){
			view_value = this.selected_post_type();
		}else if(this.view_type == 'list'){
			view_value = this.selected_list.name(); //TO BE REVIEWED
		}

		if(view_value){
			var temp_data = new static_data({ 'action': 'get_' + this.view_type + '_view_data',
											  'view_value':view_value,
											  'view_type': this.view_type });

			jQuery.post(this.siteurl+"/wp-admin/admin-ajax.php",ko.toJS(temp_data),
						 function(response){
							if(response != 0){
								viewModel.parseServerData(jQuery.parseJSON(response.slice(0,response.length-1)));
							}
						}
			);
		}
	},
	//Parses data received from server and updates all objects
    parseServerData: function(config_data) {
		var opt, available_options = [];
		this.current_fieldtype_options.removeAll();

		for(var param_name in this.current_params){
			this.current_params[param_name].removeAll();
		}

		if(this.view_type == 'list' && typeof(config_data.post_type) != 'undefined' ){
			this.selected_post_type(config_data.post_type);
			this.selected_list.template	(config_data.template_file);

			if(typeof(config_data['param']) != 'undefined'){
				var param = '';
				for(var param_name in config_data['param']){
					for(var param_key in config_data['param'][param_name]){
						if(param_name == 'filter'){
							param = new basic_param(config_data['param'][param_name][param_key]);
						}else if(param_name == 'order'){
							param = new basic_param(config_data['param'][param_name][param_key]);
						}else if(param_name == 'pagination'){
							param = new basic_param(config_data['param'][param_name][param_key]);
						}else if(param_name == 'usersorting'){
							param = new basic_param(config_data['param'][param_name][param_key]);
						}else{							
							//TODO LATER WHEN NEW OPTIONS ARE ADDED
						}
						this.current_params[param_name].push(param);
					}
				}
			}
		}
		if(this.view_type == 'post'){
			this.selected_post_template(config_data.template_file);
		}

		for(var opt_key in config_data.fields){
			if(config_data.fields[opt_key]['type'] == "content-editor"){
				config_data.fields[opt_key]['type'] = "cpvg_text";
			}

			opt = new fieldtype_option(config_data.fields[opt_key]['name'],config_data.fields[opt_key]['label']);
			opt.type(config_data.fields[opt_key]['type']);
			if(config_data.fields[opt_key]['hide_empty'] == 'true'){
				opt.hide_empty(true);
			}
			
			for(var var_key in config_data.fields[opt_key]){
				
				if(var_key.slice(0,7) == 'options'){
					opt[var_key] = ko.observable(config_data.fields[opt_key][var_key]);
				}else if(var_key == 'extra_options'){	
					for(var eo_key in config_data.fields[opt_key][var_key]){
						opt[var_key][eo_key] = new ko.observable(config_data.fields[opt_key][var_key][eo_key]);
						
					}
				}
			}
			
			
			this.addFieldtypeOption(config_data.fields[opt_key]['name'],config_data.fields[opt_key]['label'],opt);
		}
	},
	getDatafieldExtraData: function(section_field_name,field_name){	
		if(typeof(viewModel.available_extra_data[section_field_name]) != 'undefined'){
			return viewModel.available_extra_data[section_field_name][field_name];
		}
		
		if(field_name == 'label' || field_name == 'type' || field_name == 'options' || field_name == 'hide_empty'){
			return true;
		}
		
		return false;
	},
	
	/*LIST VIEWS*/
	//Performs several list view actions
	listViewAction: function(action,index,item,new_item){
		switch (action){
			case 'add':
				var new_item = { 'value' : ko.observable('list'+new Date().getTime()) };
				this.available_list_views.push(new_item);
				this.current_fieldtype_options.removeAll();
				this.listViewAction('select',this.available_list_views().length-1,new_item.value(),true);
				this.listViewAction('save');
			break;
			case 'select':
				//update selected_list
				this.selected_list.name(item);
				this.selected_list.original_name = item;
				this.selected_list.index(index);

				//reset select_param
				this.selected_param.type('filter');
				this.selected_param.index(0);
				this.selected_param.item = '';
				this.selected_param.visible_form(false);

				if(typeof(new_item)=='undefined'){
					this.getServerData();
				}
			break;
			case 'delete':
				this.listViewAction('select',index,this.available_list_views()[index].value());
				this.sendData('delete_layout');
				this.selected_list.name(undefined);
				this.selected_list.template(undefined);
				this.selected_post_type(undefined);
				this.available_list_views.remove(this.available_list_views()[index]);

				//reset selected_list
				this.selected_list.name('');
				this.selected_list.original_name = '';
				this.selected_list.index(-1);

				//reset fieldtype_options
				this.current_fieldtype_options.removeAll();
			break;
			case 'save':
				this.sendData('save_layout');
				this.selected_list.original_name = this.selected_list.name();
				if(this.selected_list.index() > -1){
					this.available_list_views()[this.selected_list.index()].value(this.selected_list.name());
				}
			break;
		}
	},
	//Parses the parameter data from a list view received from the server
	parseParamConfig: function(var_name,data) {
		var paramData = ko.toJS(data);
		var sdata = { 'sections':[], 'fields':[], 'choices':[], 'mutiple_choices':[], 'messages':{}, 'operators':{}, 'types':{} };

		for(var section_key in paramData){
			sdata['sections'][sdata['sections'].length] = section_key;

			for(var var_key in paramData[section_key]){
				if(var_key == 'message'){
					sdata['messages'][section_key] = paramData[section_key][var_key];
				}else if(var_key == 'mutiple_choices'){
					sdata['mutiple_choices'] = jQuery.merge(sdata['mutiple_choices'], paramData[section_key][var_key]);
				}else if(var_key == 'operator' || var_key == 'type'){
					var values = paramData[section_key][var_key];
					sdata[var_key+'s'][section_key] = [];
					for(var operator_key in values){
						sdata[var_key+'s'][section_key][sdata[var_key+'s'][section_key].length] = {'key':operator_key, 'value':values[operator_key]};
					}
				}else{
					for(var value_key in paramData[section_key][var_key]){
						if(typeof(paramData[section_key][var_key][value_key]) == 'object'){ // CHOICES
							
							if(typeof(sdata['choices'][section_key]) == 'undefined'){ 

								sdata['choices'][section_key] = { };
							}
							if(typeof(sdata['choices'][section_key][value_key]) == 'undefined'){
								sdata['choices'][section_key][value_key] = [];
							}
							for(var obj_key in paramData[section_key][var_key][value_key]){
								sdata['choices'][section_key][value_key][sdata['choices'][section_key][value_key].length] = { 'key': obj_key,'value': paramData[section_key][var_key][value_key][obj_key] };
							}
						}else{ //FIELDS
							if(typeof(sdata['fields'][section_key]) == 'undefined'){
								sdata['fields'][section_key] = [];
							}
							sdata['fields'][section_key][sdata['fields'][section_key].length] = { 'key': value_key,'value': paramData[section_key][var_key][value_key] };
						}
					}
				}
			}
		}
		this.available_param_data[var_name] = sdata;
	},
	//Returns parameter data - used to fill/refresh the list view form data
	getParamData: function(field_name,observable){
		var curr_type = this.selected_param.type();
		var curr_index = this.selected_param.index();

		switch(field_name){
		case 'current_records':
		  return this.current_params[curr_type];
		case 'sections':
			if(typeof(this.available_param_data[curr_type]['sections']) != 'undefined' ){
				return this.available_param_data[curr_type]['sections'];
			}
			break;
		case 'fields':
		    var curr_section = this.getParamData('section');

			if (typeof(this.available_param_data[curr_type]['fields']) != 'undefined' &&
				typeof(curr_section) != 'undefined' &&
				typeof(this.available_param_data[curr_type]['fields'][curr_section()]) != 'undefined' ){
					return this.available_param_data[curr_type][field_name][curr_section()];
			}
			break;
		case 'types':
		case 'operators':
		case 'messages':
			var curr_section = this.getParamData('section');

			if (typeof(this.available_param_data[curr_type][field_name]) != 'undefined' &&
				typeof(curr_section) != 'undefined' &&
				typeof(this.available_param_data[curr_type][field_name][curr_section()])){

				return this.available_param_data[curr_type][field_name][curr_section()];
			}
			return [];
			break;
		case 'choices':
			var curr_section = this.getParamData('section');
			var curr_param = this.getParamData('parameter');


			if (typeof(this.available_param_data[curr_type]['choices']) != 'undefined' &&
				typeof(curr_section) != 'undefined' &&
				typeof(curr_param) != 'undefined' &&
				typeof(this.available_param_data[curr_type]['choices'][curr_section()]) != 'undefined' ){

					if(typeof(this.current_params[curr_type]()[curr_index])!='undefined'){
						if(jQuery.inArray(curr_param(),this.available_param_data[curr_type]['mutiple_choices']) > -1){
							jQuery('.cvpg-multi-select').attr('multiple','');
						}else{
							jQuery('.cvpg-multi-select').removeAttr('multiple');
						}
					}
					return this.available_param_data[curr_type]['choices'][curr_section()][curr_param()];
			}
			break;
		default:
			if(typeof(this.current_params[curr_type]()[curr_index])!='undefined'){
				if(observable){
					return this.current_params[curr_type]()[curr_index][field_name]();
				}
				return this.current_params[curr_type]()[curr_index][field_name];
			}
			break;
		}
		return undefined;
	},
	//Performs several parameters actions in the list view form
	runParamAction: function(action,item){
		if(action == 'select'){
			this.selected_param.index(this.current_params[this.selected_param.type()].indexOf(item));
			this.selected_param.item = item;
			this.selected_param.visible_form(true);
			this.getParamData('choices');
		}else if(action == 'remove'){
			this.selected_param.index(undefined);
			this.current_params[this.selected_param.type()].remove(item);
			this.selected_param.visible_form(false);
		}else{
			//add + select
			var new_item = '';
			if(this.selected_param.type() == 'filter' || this.selected_param.type() == 'order' || this.selected_param.type() == 'pagination' || this.selected_param.type() == 'usersorting'  ){
				new_item = new basic_param({ 'name': this.selected_param.type() + new Date().getTime()});
			}else{
				//new_item = new basic_param({ 'name': this.selected_param.type() + new Date().getTime()});
			}
			this.current_params[this.selected_param.type()].push(new_item);
			this.runParamAction('select',new_item);
		}
	},
	createPostPage: function(object_type) {
		this.sendData('save');
		var temp_data = { 'action': 'create_postpage', 'name': this.post_page_name(),
						  'object_type':object_type, 'list_view_name': this.selected_list.name() };

		jQuery.post(this.siteurl+"/wp-admin/admin-ajax.php",ko.toJS(temp_data),
		   function(response){
			   jQuery('.action-message').html(response.slice(0,response.length-1)).show(1000).delay(2000).hide(1000);
		   }
		);
	},
	/* MISC */
	debugServerData: function(data) {
		//console.log();
	}
}

//Each time selected_post_type is change, all data is refreshed/updated
viewModel.selected_post_type.subscribe(function() {
	this.current_fieldtype_options.removeAll();

    if(this.view_type == 'post'){ this.getServerData(); }

	//COSMETIC
	this.selected_post_template(this.available_template_files[0]['key']);
	jQuery('#cpvg-posttype-preview-content').html('');
}, viewModel);

//Each time a parameter type is selected the selected parameter index is set to 0
viewModel.selected_param.type.subscribe(function() {
	this.selected_param.index(undefined);
	this.selected_param.visible_form(false);
}, viewModel);

viewModel.touchscreen.subscribe(function() {
	if(this.touchscreen()){
		jQuery("#cpvg-fieldlist").droppable("destroy");	
		jQuery(".cpvg-field-draggable").draggable("destroy").click(function() {
			viewModel.addFieldtypeOption(jQuery(this).attr('id'),jQuery(this).text());
		});
	}else{
		jQuery(".cpvg-field-draggable").unbind("click");
		cvpg_set_draggable_droppable();
	}
}, viewModel);

//Enables sorting with observableArray - Source: http://www.knockmeout.net/2011/05/dragging-dropping-and-sorting-with.html
ko.bindingHandlers.sortableList = {
    init: function(element, valueAccessor) {
        var list = valueAccessor();
        jQuery(element).sortable({
            update: function(event, ui) {
                //retrieve our actual data item
                var item = ui.item.tmplItem().data;
                //figure out its new position
                var position = ko.utils.arrayIndexOf(ui.item.parent().children(), ui.item[0]);
                //remove the item and add it back in the right spot
                if (position >= 0) {
                    list.remove(item);
                    list.splice(position, 0, item);
                }
            }
        });
    }
};

//************************ INIT FUNCTION **************************/
jQuery(document).ready(function(){
		ko.applyBindings(viewModel);

		cvpg_set_draggable_droppable();
		
		var deviceAgent = navigator.userAgent.toLowerCase();
		var agentID = deviceAgent.match(/(iphone|ipod|ipad|android)/);
		if (agentID) {
			viewModel.touchscreen(true);	 
		}else{
			viewModel.touchscreen(false);
		}		
});

function cvpg_set_draggable_droppable(){
		jQuery(".cpvg-field-draggable").draggable({
			appendTo: "body",
			helper: "clone"
		});

		jQuery("#cpvg-fieldlist").droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drop: function( event, ui ){
				viewModel.addFieldtypeOption(ui.draggable.attr('id'),ui.draggable.text());
			}
		});
}

function convert_assoc_array(data){
	var output = [];
	for(var key in data){
		output[output.length] = { 'key':key, 'value':data[key] };
	}	
	return output;
}
