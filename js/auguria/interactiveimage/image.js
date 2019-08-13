InteractiveImage = Class.create();
InteractiveImage.prototype = {
		
    initialize: function(imgUrl, config){

    	if(config == undefined){
    		alert(Translator.translate('Image path is not defined. Upload the image (choose image path and save product) then define areas (and save again).'));
    		return false;
    	}
    	
    	// by config
    	this.imgUrl = imgUrl;
    	this.imagePath = config.image_path;
    	
    	var areas = {};
    	if(config.areas){var areas = JSON.parse(config.areas);}
    	this.originAreas = areas;
    	this.areas = areas;
    	
    	// base
    	this.popin = null;
    	this.types = {rect: 'rect'}
    	this.formId = 'interactive-image-form';
    	
    	// canvas
    	this.canvasId = 'interactive-image-canvas';
    	this.canvasWidth = null;
    	this.canvasHeight = null;
    	
    	this.stage = null;
    	this.currentArea = null;
    	
    	// control panel
    	this.panelId = 'interactive-image-panel';
    	this.panel = $(this.panelId);
    },

    // display popin for edition (areas definitions)
    display: function(){
    	if(this.imagePath == undefined){
    		alert(Translator.translate('Image path is not defined. Upload the image (choose image path and save product) then define areas (and save again).'));
    		return false;
    	}
    	
    	var _self = this;
    	
    	// informations used for edition interface
    	var params = {
			'image_path': this.imagePath,
			'areas': this.areas
    	}
    	
    	this.initImage(); // get canvas/image dimensions
    	
        new Ajax.Request('/admin/auguria_interactiveImage_image/display', {
            parameters: {
            	'interactive_image': Object.toJSON(params),
            },
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON(true);                                

                if(response.error){
            		alert('error');
                }else{
                	if(response.content){
                		_self.popin = Dialog.info(response.content, {
                			closable: true,
                			resizable: true,
                			draggable: true,
                			className: 'magento',
                			windowClassName: 'popup-window',
                			width: 'auto',
                			height: 'auto',
                			top: 0, // fix top position with largest images (draggable popin)
                			zIndex: 1000,
                			recenterAuto: false,
                			hideEffect: Element.hide,
                			showEffect: Element.show,
                			id: 'interactive-image-popin',
                			destroyOnClose: true
                		});
                	}
                	
                	_self.initCanvas();
                	_self.initAreas();
                }
            }
        });
    },
    
    // init image/canvas dimensions
    initImage: function(){
    	var img  = new Image();
    	img.src = this.imgUrl;
    	this.canvasWidth = img.width;
    	this.canvasHeight = img.height;
    },
    
    // clear areas
    clear: function(){
    	this.stage.clear(); // layers
    	// DOM
    	$$('#' + this.formId + ' fieldset').each(function(elem){
    		elem.remove();
    	});
    	this.areas = this.originAreas;
    },
    
    // close generated popin
    close: function(){
    	if(this.popin != null){
//    		this.areas = this.originAreas;
    		this.popin.close();
    	}
    	
    	return false;
    },
    
    // save areas
    save: function(){
    	var _self = this;
    	
    	new Ajax.Request('/admin/auguria_interactiveImage_image/save', {
            parameters: $(this.formId).serialize(true),
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON(true);                                

                if(response.error){
            		alert('error');
                }else{
                	$('auguria_interactiveimage_image_areas').setValue(response.content);
                	_self.areas = JSON.parse(response.content);
                	alert('saved');
                }
            }
        });
    },

    // init canvas
    initCanvas: function(){
    	_self = this;
    	
    	this.stage = new Kinetic.Stage({
	        container: this.canvasId,
	        width: this.canvasWidth,
	        height: this.canvasHeight
		});
    	
    	document.getElementById(this.canvasId).addEventListener('mousedown', function(){_self.mouseDown(_self)});
    },
    
    mouseDown: function(_self){
    	var mousePos = _self.stage.getMousePosition();
    	
    	if(document.body.style.cursor != 'pointer'){
    		_self.draw = true;
    		_self.initCurrentArea({type: _self.types.rect, x:mousePos.x, y:mousePos.y});
    		
    		var mouseMove = _self.mouseMove(_self);
    		document.getElementById(this.canvasId).addEventListener('mousemove', mouseMove, false);
    		
    		var mouseUp = _self.mouseUp(_self);
    		document.getElementById(this.canvasId).addEventListener('mouseup', mouseUp, false);
    	}
    },
    
    mouseUp: function(_self){
    	var handler = function(event){
    		_self.createArea(_self.currentArea);
        	_self.createPanelTab(_self.currentArea);
        	
//        	var mouseMove = _self.mouseMove(_self);
//        	document.getElementById(_self.canvasId).removeEventListener('mousemove', mouseMove, false);
        	
        	document.getElementById(_self.canvasId).removeEventListener('mouseup', arguments.callee, false);
        	
        	_self.draw = false;
    	}
    	
    	return handler;
    },
    
    mouseMove: function(_self){
    	var handler = function(event){
    		if(!_self.draw){
    			document.getElementById(_self.canvasId).removeEventListener('mousemove', arguments.callee, false);
    		}
    		
			var mousePos = _self.stage.getMousePosition();
			
			// rect
			if(_self.currentArea.type == _self.types.rect){
				_self.currentArea.size.width = mousePos.x - _self.currentArea.size.x;
				_self.currentArea.size.height = mousePos.y - _self.currentArea.size.y;
			}
    	}
    	
    	return handler;
    },
    
    // init existing areas
    initAreas: function(){
    	var _self = this;
    	
    	for (var i in _self.areas) {
			if (_self.areas.hasOwnProperty(i)) {
				_self.createArea(_self.areas[i]);
			}
    	}
    	
    	$('auguria_interactiveimage_image_areas').setValue(Object.toJSON(_self.areas));
    },
    
    initCurrentArea: function(params){
    	if(params.type == this.types.rect){
    		
    		var areaKeys = Object.keys(this.areas);
    		if(areaKeys.length > 0){
    			var id = areaKeys[areaKeys.length - 1];
    			id = parseInt(id) + 1;
    		}else{
    			var id = 0;
    		}
    		
    		this.currentArea = {
    				type: params.type,
    				color:'#00D2FF',
    				size: {
    					x: params.x,
    					y: params.y,
    					width: 0,
    					height: 0
    				},
    				render: 'default',
    				id: id,
    		}
    	}
    },
    
    // create defined area
    createArea: function(area){
    	_self = this;
		var layer = new Kinetic.Layer();
    	
    	if(area.type == this.types.rect){
    		var box = new Kinetic.Rect({
    			x: parseFloat(area.size.x),
    			y: parseFloat(area.size.y),
    			width: parseFloat(area.size.width),
    			height: parseFloat(area.size.height),
    			fill: area.color,
    			stroke: 'black',
    			strokeWidth: 1,
    			opacity: 0.6,
    			draggable: true,
    			dragBoundFunc: function(pos) {
    				// x
    	            var newX = pos.x < 0 ? 0 : pos.x;
    	            var newX = newX > _self.canvasWidth - area.size.width ? _self.canvasWidth - area.size.width : newX;
    	            // y
    	            var newY = pos.y < 0 ? 0 : pos.y;
    	            var newY = newY > _self.canvasHeight - area.size.height ? _self.canvasHeight - area.size.height : newY;
    	            
    	            return {
    	              x: newX,
    	              y: newY
    	            };
				}
    		});
    	}

    	if(!this.areas.hasOwnProperty(area.id)){
    		this.areas[area.id] = area;
    	}
    	
    	this.areas[area.id].layer = layer;
    	this.appendEvents(box, area);
		
		layer.add(box);
		this.stage.add(layer);
    },
    
    // delete existing area
    deleteArea: function(areaId){
    	this.areas[areaId].layer.destroy(); // remove layer
    	delete this.areas[areaId];
    	
    	$('fieldset-' + areaId).remove(); // remove from DOM
    },
    
    // define events
    appendEvents: function(box, area){
    	_self = this;
    	
    	// add cursor styling
    	box.on('mouseover', function() {
    		_self.draw = false;
    		document.body.style.cursor = 'pointer';
    	});
    	box.on('mouseout', function() {
    		document.body.style.cursor = 'default';
    	});
    	box.on('click', function() {
    		_self.draw = false;
    		_self.focusArea(area, box);
    	});
    	box.on('dragend', function() {
    		_self.draw = false;
    		_self.dragArea(area, box);
    	});
    },
    
    focusArea: function(area, box = null){
    	// clean active filedsets
    	$$('#' + this.formId + ' fieldset').each(function(elem){
    		elem.removeClassName('active');
    	});
    	
    	if(box){
    		box.stroke = 'red';
    	}
    	$('fieldset-' + area.id).addClassName('active');
    },
    
    dragArea: function(area, box = null){
    	if(box){
    		$$('#' + this.formId + ' [name="areas[' + area.id + '][size][x]"]').first().value = parseInt(box.attrs.x);
    		$$('#' + this.formId + ' [name="areas[' + area.id + '][size][y]"]').first().value = parseInt(box.attrs.y);
    		
    		area.size.x = parseInt(box.attrs.x);
    		area.size.y = parseInt(box.attrs.y);
    	}
    },
    
    createPanelTab: function(area){
    	_self = this;
    	
    	new Ajax.Request('/admin/auguria_interactiveImage_image/createPanelTab', {
            parameters: {
            	'area': Object.toJSON(area),
            },
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON(true);                                

                if(response.error){
            		alert('error');
                }else{
                	if(response.content){
                		$('interactive-image-form').insert(response.content);
                		_self.focusArea(area);
                	}
                }
            }
        });
    }
    
}