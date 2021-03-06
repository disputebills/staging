
if(typeof gperk == 'undefined')
    var gperk = {};
    
gperk.confirmActionUrl = function(event, message, url) {
    event.preventDefault();
    
    var elem = jQuery(event.target);
    
    if(!url)
        url = elem.prop('href');
    
    if(confirm(message)) {
        location.href = url;
    }
    
}
    
/**
* Add a tab to the form editor
* 
*/
gperk.addTab = function(elem, id, label) {
    
    var tabClass = id == '#gws_form_tab' ? 'gwp_form_tab' : 'gwp_field_tab';
    
    // destory tabs already initialized
    elem.tabs( 'destroy' );
    
    // add new tab
    elem.find( 'ul' ).eq(0).append( '<li style="width:100px; padding:0px;" class="' + tabClass + '"> \
        <a href="' + id + '">' + label + '</a> \
        </li>' )
        
    // add new tab content
    elem.append( jQuery( id ) );
    
    // re-init tabs
    elem.tabs({
        beforeActivate: function(event, ui) {
            switch( jQuery( ui.newPanel ).prop( 'id' ) ) {
            case 'gws_form_tab':
                jQuery(document).trigger( 'gwsFormTabSelected', [ form ] );
                break;
            case 'gws_field_tab':
                jQuery(document).trigger( 'gwsFieldTabSelected', [ field ] );
                break;
            };
        }
    });
    
}

gperk.togglePerksTab = function() {

    var fieldTab = jQuery( '.ui-tabs-nav li.gwp_field_tab' );

    fieldTab.hide();

    if( gperk.fieldHasSettings() ) {
        fieldTab.show();
    }

};

gperk.fieldHasSettings = function() {
    
    var hasSetting = false;
    
    jQuery('#gws_field_tab').find('li.field_setting').each(function(){
        var patt = /(display[: ]+none)/;
        if(!patt.test(jQuery(this).attr('style')))
            hasSetting = true;
    });
    
    return hasSetting;
}

gperk.toggleSection = function(elem, selector) {
    var elem = jQuery(elem);
    
    if(elem.prop('checked')) {
        elem.parents('.gwp-field').addClass('open');
        jQuery(selector).gwpSlide('down', '.perk-settings');    
    } else {
        elem.parents('.gwp-field').removeClass('open');
        jQuery(selector).gwpSlide('up', '.perk-settings');
    }
    
}

gperk.isSingleProduct = function(field) {
    singleFieldTypes = gperk.applyFilter('gwSingleFieldTypes', ['singleproduct', 'hiddenproduct', 'calculation']);
    return jQuery.inArray(field.inputType, singleFieldTypes) != -1;
}

gperk.getFieldLabel = function(field, inputId) {
    
    if(gperk.isUndefined(inputId))
        inputId = false;
    
    var label = field.label;
    var input = gperk.getInput(field, inputId);
    
    if(field.type == 'checkbox' && input != false) {
        return input.label;
    } else if(input != false) {
        return input.label;
    } else {
        return label;
    }
            
}

gperk.getInput = function(field, inputId) {
    
    if(gperk.isUndefined(field['inputs']) && jQuery.isArray(field['inputs'])) {
        for(i in field['inputs']) {
            var input = field['inputs'][i];
            if(input.id == inputId)
                return input;
        }
    }
    
    return false;
}

gperk.toggleSettings = function(id, toggleSettingsId, isChecked) {
            
    var elem = jQuery('#' + id);
    var settingsElem = jQuery('#' + toggleSettingsId);
    
    // if "isChecked" is passed, check the checkbox
    if(!gperk.is(isChecked, 'undefined')) {
        elem.prop('checked', isChecked);
    } else {
        var isChecked = elem.is(':checked');
    }
    
    if(isChecked) {
        settingsElem.gfSlide('down');
    } else {
        settingsElem.gfSlide('up');
    }
    
    SetFieldProperty(id, isChecked);
    
}

gperk.setInputProperty = function(inputId, property, value) {
    
    var field = GetSelectedField();
    
    for(i in field.inputs) {
        if(field.inputs[i].id == inputId)
            field.inputs[i][property] = value;
    }
    
}

/**
* Set a form property on current form.
* 
* This function should only be used on the Gravity Forms form editor page where the "form" object is a global
* variable and available for modification. Changes made to the form object on this page will be saved
* when the user clicks "Update Form".
* 
* @type Object
*/
gperk.setFormProperty = function(property, value) {
    form[property] = value;
}

/**
* GWPerks version of the gfSlide jQuery plugin, used to show/hide/slideup/slidedown depending on whether
* the settings are being init or already displayed
* 
* @param isVisibleSelector Defaults to '#field_settings'; pass "false" to force "hide()"
* 
*/

jQuery.fn.gwpSlide = function(direction, isVisibleSelector) {
    
    if(typeof isVisibleSelector == undefined)
        isVisibleSelector = '#field_settings';
        
    var isVisible = isVisibleSelector === false || isVisibleSelector === true ? isVisibleSelector : jQuery(isVisibleSelector).is(':visible');

    if(direction == 'up') {
        if(!isVisible) {
            this.hide();
        } else {
            this.slideUp();
        }
    } else {
        if(!isVisible) {
            this.show();
        } else {
            this.slideDown();
        }
    }

    return this;
};