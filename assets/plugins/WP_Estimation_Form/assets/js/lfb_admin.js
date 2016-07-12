// from php : lfb_data
var lfb_isLinking = false;
var lfb_links = new Array();
var lfb_linkCurrentIndex = -1;
var lfb_canvasTimer;
var lfb_mouseX, lfb_mouseY;
var lfb_linkGradientIndex = 1;
var lfb_itemWinTimer;
var lfb_currentDomElement = false;
var lfb_currentStep = false;
var lfb_currentStepID = 0;
var lfb_lock = false;
var lfb_defaultStep = false;
var lfb_steps = false;
var lfb_params;
var lfb_currentLinkIndex = 0;
var lfb_settings;
var lfb_formfield;
var lfb_currentFormID = 0;
var lfb_actTimer;
var lfb_currentForm = false;
var lfb_currentItemID = 0;

lfb_data = lfb_data[0];

jQuery(document).ready(function () {
    jQuery('#lfb_loader').remove();
    jQuery('#wpcontent').append('<div id="lfb_loader"><div class="lfb_spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>');
    jQuery('#lfb_loader .lfb_spinner').css({
        top: jQuery(window).height() / 2 - jQuery('#wpadminbar').height() / 2
    });
    jQuery(window).resize(function () {
        jQuery('#lfb_loader .lfb_spinner').css({
            top: jQuery(window).height() / 2 - jQuery('#wpadminbar').height() / 2
        });
        jQuery('#lfb_bootstraped,#estimation_popup').css({
          minHeight: jQuery('#wpcontent').height()
        });
    });
    jQuery('#lfb_bootstraped,#estimation_popup').css({
      minHeight: jQuery('#wpcontent').height()
    });
    jQuery('#lfb_stepsContainer').droppable({
        drop: function (event, ui) {
            var $object = jQuery(ui.draggable[0]);
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'lfb_saveStepPosition',
                    stepID: $object.attr('data-stepid'),
                    posX: parseInt($object.css('left')),
                    posY: parseInt($object.css('top'))
                }
            });
        }
    });

    jQuery('.imageBtn').click(function () {
        lfb_formfield = jQuery(this).prev('input');
        tb_show('', 'media-upload.php?TB_iframe=true');

        return false;

    });
    window.old_tb_remove = window.tb_remove;
    window.tb_remove = function () {
        window.old_tb_remove();
        lfb_formfield = null;
    };
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function (html) {
        if (lfb_formfield) {
            fileurl = jQuery('img', html).attr('src');
            jQuery(lfb_formfield).val(fileurl);
            tb_remove();
        } else {
            window.original_send_to_editor(html);
        }
    };
    jQuery('#wpwrap').css({
        height: jQuery('#lfb_bootstraped').height() + 48
    });
    setInterval(function () {
        if (jQuery('#lfb_winStep').css('display') == 'block') {
            jQuery('#wpwrap').css({
                height: jQuery('#lfb_winStep').height() + 48
            });

        } else {
            jQuery('#wpwrap').css({
                height: jQuery('#lfb_bootstraped').height() + 48
            });

        }
    }, 1000);

    lfb_canvasTimer = setInterval(lfb_updateStepCanvas, 30);
    jQuery(document).mousemove(function (e) {
        if (lfb_isLinking) {
            lfb_mouseX = e.pageX - jQuery('#lfb_stepsContainer').offset().left;
            lfb_mouseY = e.pageY - jQuery('#lfb_stepsContainer').offset().top;
        }
    });
    jQuery(window).resize(lfb_updateStepsDesign);
    lfb_itemWinTimer = setInterval(lfb_updateWinItemPosition, 30);
    jQuery('#lfb_actionSelect').change(function () {
        lfb_changeActionBubble(jQuery('#lfb_actionSelect').val());
    });
    jQuery('#lfb_interactionSelect').change(function () {
        lfb_changeInteractionBubble(jQuery('#lfb_interactionSelect').val());
    });

    jQuery('#lfb_interactionBubble,#lfb_actionBubble,#lfb_linkBubble,#lfb_fieldBubble').hover(function (e) {
        jQuery(this).addClass('lfb_hover');
    }, function (e) {
        jQuery(this).removeClass('lfb_hover');
    });
    jQuery('#lfb_interactionBubble,#lfb_actionBubble,#lfb_linkBubble,#lfb_fieldBubble').find('select').focus(function () {
        jQuery(this).addClass('lfb_hover');
    }).blur(function () {
        jQuery(this).removeClass('lfb_hover');
    });
    jQuery('body').click(function () {
        if (!jQuery('#lfb_interactionBubble').is('.lfb_hover')) {
            jQuery('#lfb_interactionBubble').fadeOut();
        }
        if (!jQuery('#lfb_actionBubble').is('.lfb_hover') && !jQuery('#lfb_websiteFrame').is('.lfb_hover') && !jQuery('.lfb_selectElementPanel').is('.lfb_hover')) {
            jQuery('#lfb_actionBubble').fadeOut();
        }
        if (!jQuery('#lfb_linkBubble').is('.lfb_hover')) {
            jQuery('#lfb_linkBubble').fadeOut();
        }
        if (!jQuery('#lfb_fieldBubble').is('.lfb_hover') && jQuery('#lfb_fieldBubble').find('.lfb_hover').length == 0) {
            jQuery('#lfb_fieldBubble').fadeOut();
        }
    });
    if (jQuery('#lfb_winActivation').is('[data-show="true"]') && document.referrer.indexOf('admin.php?page=lfb_menu')<0){
      jQuery('#lfb_winActivation .modal-dialog').hover(function(){
          jQuery(this).addClass('lfb_hover');
      },function(){
          jQuery(this).removeClass('lfb_hover');
      });
      lfb_lock = true;
	  jQuery('#lfb_closeWinActivationBtn').click(function(){
		if (!lfb_lock) {
			jQuery('#lfb_winActivation').modal('hide');
		}
	  });
	  jQuery('#lfb_closeWinActivationBtn .lfb_text').data('num',10).html('Wait 10 seconds');
	  lfb_actTimer = setInterval(function(){
		var num = jQuery('#lfb_closeWinActivationBtn .lfb_text').data('num');
		num--;
		if(num>0){
			jQuery('#lfb_closeWinActivationBtn .lfb_text').data('num',num).html('Wait '+num+' seconds');
		} else {
			jQuery('#lfb_closeWinActivationBtn').removeClass('disabled');
			lfb_lock = false;
			jQuery('#lfb_closeWinActivationBtn .lfb_text').data('num','').html('Close');
		}
	  },1000);
    } else {
		jQuery('#lfb_winActivation').attr('data-show','false');
	}
	jQuery('#lfb_winActivation').on('hide.bs.modal', function (e) {
		 if (lfb_lock && !jQuery('#lfb_winActivation .modal-dialog').is('.lfb_hover')) {
          e.preventDefault();
        }
	});
    jQuery(document).mousedown(function (e) {
        if (e.button == 2 && lfb_isLinking) {
            lfb_isLinking = false;
        }
    });


    jQuery('.form-group').each(function () {
        var self = this;
        if (jQuery(self).find('small').length > 0 && jQuery(self).find('.form-control').length > 0) {
            jQuery(this).find('.form-control').tooltip({
                title: jQuery(self).find('small').html()
            });
        }
    });

    jQuery("#lfb_bootstraped.lfb_bootstraped [data-toggle='switch']").wrap('<div class="switch" data-on-label="'+lfb_data.texts['Yes']+'" data-off-label="'+lfb_data.texts['No']+'" />').parent().bootstrapSwitch({onLabel: lfb_data.texts['Yes'],offLabel: lfb_data.texts['No']});
// lfb_loadSteps();
	jQuery('#lfb_winActivation').modal();
    jQuery('[data-toggle="tooltip"]').tooltip();
    lfb_loadSettings();
    lfb_initFormsBackend();
})
;

function lfb_initFormsBackend() {
    jQuery('#lfb_formFields [name="use_paypal"]').on('change', lfb_formPaypalChange);
    lfb_formPaypalChange();
    jQuery('#lfb_formFields [name="gravityFormID"]').change(lfb_formGravityChange);
    lfb_formGravityChange();
    jQuery('#lfb_formFields [name="save_to_cart"]').on('change', lfb_formWooChange);
    lfb_formWooChange();
    jQuery('#lfb_formFields [name="email_toUser"]').change(lfb_formEmailUserChange);
    jQuery('#lfb_formFields [name="email_toUser"]').on('change', lfb_formEmailUserChange);
    jQuery('#lfb_formFields [name="legalNoticeEnable"]').change(lfb_formLegalNoticeChange);
    jQuery('#lfb_formFields [name="legalNoticeEnable"]').on('change', lfb_formLegalNoticeChange);
    lfb_formLegalNoticeChange();
    lfb_formEmailUserChange();
}

function lfb_formLegalNoticeChange() {
    if (jQuery('#lfb_formFields [name="legalNoticeEnable"]').is(':checked') ) {
        jQuery('#lfb_formFields [name="legalNoticeTitle"]').parent().slideDown();
        jQuery('#lfb_formFields [name="legalNoticeContent"]').parent().slideDown();
    } else {
        jQuery('#lfb_formFields [name="legalNoticeTitle"]').parent().slideUp();
        jQuery('#lfb_formFields [name="legalNoticeContent"]').parent().slideUp();
    }
}
function lfb_formPaypalChange() {
    if (jQuery('#lfb_formFields [name="use_paypal"]').is(':checked') ) {
        jQuery('#lfb_formPaypal').slideDown();
    } else {
        jQuery('#lfb_formPaypal').slideUp();
    }
}
function lfb_showShortcodeWin(formID){
  if(!formID){
    formID= lfb_currentFormID;
  }
  jQuery('#lfb_winShortcode').find('span[data-displayid]').html(formID);
  jQuery('#lfb_winShortcode').modal('show');
}
function lfb_formGravityChange() {
    if (jQuery('#lfb_formFields select[name="gravityFormID"]').val() > 0) {
        jQuery('#lfb_formFields [name="save_to_cart"]').val('0');
        jQuery('#lfb_finalStepFields').slideUp();
        jQuery('#lfb_formFields [name="use_paypal"]').val('0');
        jQuery('#lfb_formFields [name="use_paypal"]').closest('.form-group').slideUp();
        jQuery('#lfb_formFields [name="use_paypal"]').closest('.form-group').prev('h4').slideUp();

    } else {
        jQuery('#lfb_finalStepFields').slideDown();
        jQuery('#lfb_formFields [name="use_paypal"]').closest('.form-group').slideDown();
        jQuery('#lfb_formFields [name="use_paypal"]').closest('.form-group').prev('h4').slideDown();
    }
}
function lfb_formEmailUserChange() {
    if (jQuery('#lfb_formFields [name="email_toUser"]').is(':checked') ) {
        jQuery('#lfb_formEmailUser').slideDown();
    } else {
        jQuery('#lfb_formEmailUser').slideUp();
    }
}
function lfb_formWooChange() {
    if (jQuery('#lfb_formFields select[name="gravityFormID"]').val() == 1) {
        jQuery('#lfb_formFields select[name="gravityFormID"]').val('0');
    }
}
function lfb_getStepByID(stepID) {
    var rep = false;
    jQuery.each(lfb_steps, function (i) {
        if (this.id == stepID) {
            rep = this;
        }
    });
    return rep;
}
function lfb_showLoader(){
    jQuery('body').animate({ scrollTop: 0}, 250);
    jQuery('#lfb_loader').fadeIn();
}
function lfb_addStep(step) {
    var title = '';
    var startStep = 0;
    if (!step.content) {
        title = step;
    } else {
        title = step.title;

    }
    var newStep = jQuery('<div class="lfb_stepBloc palette palette-clouds"><div class="lfb_stepBlocWrapper"><h4>' + title + '</h4></div>' +
        '<a href="javascript:" class="lfb_btnEdit" title="' + lfb_data.texts['tip_editStep'] + '"><span class="glyphicon glyphicon-pencil"></span></a>' +
        '<a href="javascript:" class="lfb_btnSup" title="' + lfb_data.texts['tip_delStep'] + '"><span class="glyphicon glyphicon-trash"></span></a>' +
        '<a href="javascript:" class="lfb_btnDup" title="' + lfb_data.texts['tip_duplicateStep'] + '"><span class="glyphicon glyphicon-duplicate"></span></a>' +
        '<a href="javascript:" class="lfb_btnLink" title="' + lfb_data.texts['tip_linkStep'] + '"><span class="glyphicon glyphicon-link"></span></a>' +
        '<a href="javascript:" class="lfb_btnStart" title="' + lfb_data.texts['tip_flagStep'] + '"><span class="glyphicon glyphicon-flag"></span></a></div>');
    if (step.content && step.content.start == 1) {
        newStep.find('.lfb_btnStart').addClass('lfb_selected');
        newStep.addClass('lfb_selected');
    }
    if (step.elementID) {
        newStep.attr('id', step.elementID);

    } else {
        newStep.uniqueId();
    }

    newStep.children('a.lfb_btnEdit').click(function () {
        lfb_openWinStep(jQuery(this).parent().attr('data-stepid'));
    });
    newStep.children('a.lfb_btnLink').click(function () {
        lfb_startLink(jQuery(this).parent().attr('id'));
    });
    newStep.children('a.lfb_btnSup').click(function () {
        lfb_removeStep(jQuery(this).parent().attr('data-stepid'));
    });
    newStep.children('a.lfb_btnDup').click(function () {
        lfb_duplicateStep(jQuery(this).parent().attr('data-stepid'));
    });
    newStep.children('a.lfb_btnStart').click(function () {
        lfb_showLoader();
        jQuery('.lfb_stepBloc[data-stepid]').find('.lfb_btnStart').removeClass('lfb_selected');
        jQuery('.lfb_stepBloc[data-stepid]').find('.lfb_btnStart').closest('.lfb_stepBloc').removeClass('lfb_selected');
        jQuery.each(lfb_steps, function () {
            var step = this;
            if (step.id != jQuery(this).parent().attr('data-stepid') && step.content.start == 1) {
                step.content.start = 0;
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: {
                        action: 'lfb_saveStep',
                        id: step.id,
                        start: 0,
                        formID: lfb_currentFormID,
                        content: JSON.stringify(step.content)
                    }
                });
            }
        });

        jQuery(this).addClass('lfb_selected');
        jQuery(this).closest('.lfb_stepBloc').addClass('lfb_selected');
        var currentStep = lfb_getStepByID(jQuery(this).parent().attr('data-stepid'));
        currentStep.content.start = 1;
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_saveStep',
                id: step.id,
                start: 1,
                formID: lfb_currentFormID,
                content: JSON.stringify(currentStep.content)
            },
            success: function () {
                lfb_loadForm(lfb_currentFormID);
            }
        });
    });


    newStep.draggable({
        containment: "parent",
        handle: ".lfb_stepBlocWrapper"
    });
    newStep.children('.lfb_stepBlocWrapper').click(function () {
        if (lfb_isLinking) {
            lfb_stopLink(newStep);
        }
    });
    var posX = 10, posY = 10;
    if (step.content && step.content.previewPosX) {
        posX = step.content.previewPosX;
        posY = step.content.previewPosY;
    } else {
        posX = jQuery('#lfb_stepsOverflow').scrollLeft() + jQuery('#lfb_stepsOverflow').width() / 2 - 64;
        posY = jQuery('#lfb_stepsOverflow').scrollTop() + jQuery('#lfb_stepsOverflow').height() / 2 - 64;
    }
    newStep.hide();
    jQuery('#lfb_stepsContainer').append(newStep);
    newStep.css({
        left: (posX) + 'px',
        top: posY + 'px'
    });

    newStep.fadeIn();
    setTimeout(lfb_updateStepsDesign, 250);
    // lfb_updateStepsDesign();
    jQuery('.lfb_btnWinClose').parent().click(function () {
        lfb_closeWin(jQuery(this).parents('.lfb_window'));
    });
    if (jQuery('#lfb_stepsContainer .lfb_stepBloc').length == 0) {
        startStep = 1;
    }
    if (step.id) {
        newStep.attr('data-stepid', step.id);
    } else {
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_addStep',
                elementID: newStep.attr('id'),
                formID: lfb_currentFormID,
                previewPosX: posX,
                previewPosY: posY,
                start: startStep
            },
            success: function (step) {
                step = jQuery.parseJSON(step);
                newStep.attr('data-stepid', step.id);
                if (step.start == 1) {
                    newStep.find('.lfb_btnStart').addClass('lfb_selected');
                    newStep.addClass('lfb_selected');
                }
                lfb_steps.push({
                    content: step
                });
            }
        });
    }
}

function lfb_removeStep(stepID) {
    var i = 0;

    jQuery('.lfb_stepBloc[data-stepid="' + stepID + '"]').remove();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_removeStep',
            stepID: stepID
        },
        success: function () {
        }
    });
}
function lfb_updateStepsDesign() {
    jQuery('#wpwrap').css({
        height: jQuery('#lfb_bootstraped').height() + 48
    });
    jQuery('#lfb_stepsCanvas').attr('width', jQuery('#lfb_stepsContainer').outerWidth());
    jQuery('#lfb_stepsCanvas').attr('height', jQuery('#lfb_stepsContainer').outerHeight());
    jQuery('#lfb_stepsCanvas').css({
        width: jQuery('#lfb_stepsContainer').outerWidth(),
        height: jQuery('#lfb_stepsContainer').outerHeight()
    });
    jQuery('.lfb_stepBloc > .lfb_stepBlocWrapper > h4').each(function () {
        jQuery(this).css('margin-top', 0 - jQuery(this).height() / 2);
    });
}

function lfb_repositionLinkPoint(linkIndex) {
    var link = lfb_links[linkIndex];
    var originLeft = (jQuery('#' + link.originID).offset().left - jQuery('#lfb_stepsContainer').offset().left) + jQuery('#' + link.originID).width() / 2;
    var originTop = (jQuery('#' + link.originID).offset().top - jQuery('#lfb_stepsContainer').offset().top) + jQuery('#' + link.originID).height() / 2;
    var destinationLeft = (jQuery('#' + link.destinationID).offset().left - jQuery('#lfb_stepsContainer').offset().left) + jQuery('#' + link.destinationID).width() / 2;
    var destinationTop = (jQuery('#' + link.destinationID).offset().top - jQuery('#lfb_stepsContainer').offset().top) + jQuery('#' + link.destinationID).height() / 2;
    var posX = originLeft + (destinationLeft - originLeft) / 2;
    var posY = originTop + (destinationTop - originTop) / 2;

    jQuery.each(lfb_links, function (i) {
        if (this.originID == link.destinationID && this.destinationID == link.originID && i < linkIndex) {

            posX += 15;
            posY += 15;
        }
    });
    jQuery('.lfb_linkPoint[data-linkindex="' + linkIndex + '"]').css({
        left: posX + 'px',
        top: posY + 'px'
    });
}
function lfb_loadSettings() {
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_loadSettings'
        },
        success: function (settings) {
            settings = jQuery.parseJSON(settings);
            lfb_settings = settings;


            jQuery('#lfb_loader').fadeOut();
        }
    });
}

function lfb_closeSettings() {
    lfb_showLoader();
    document.location.href = document.location.href;
}

function lfb_duplicateStep(stepID){
    lfb_showLoader();
  jQuery.ajax({
    url: ajaxurl,
    type: 'post',
    data: {
        action: 'lfb_duplicateStep',
        stepID: stepID
    },
    success: function (newStepID) {
        lfb_loadForm(lfb_currentFormID);
    }
  });
}

function lfb_updateStepCanvas() {
    lfb_linkGradientIndex++;
    if (lfb_linkGradientIndex >= 30) {
        lfb_linkGradientIndex = 1;
    }
    var ctx = jQuery('#lfb_stepsCanvas').get(0).getContext('2d');
    ctx.clearRect(0, 0, jQuery('#lfb_stepsCanvas').attr('width'), jQuery('#lfb_stepsCanvas').attr('height'));
    jQuery.each(lfb_links, function (index) {
        var link = this;
        if (link.destinationID && jQuery('#' + link.originID).length > 0 && jQuery('#' + link.destinationID).length > 0) {
            var posX = parseInt(jQuery('#' + link.originID).css('left')) + jQuery('#' + link.originID).outerWidth() / 2 + 22;
            var posY = parseInt(jQuery('#' + link.originID).css('top')) + jQuery('#' + link.originID).outerHeight() / 2 + 22;
            var posX2 = parseInt(jQuery('#' + link.destinationID).css('left')) + jQuery('#' + link.destinationID).outerWidth() / 2 + 22;
            var posY2 = parseInt(jQuery('#' + link.destinationID).css('top')) + jQuery('#' + link.destinationID).outerHeight() / 2 + 22;
            var grd = ctx.createLinearGradient(posX, posY, posX2, posY2);

            var chkBack = false;
            var lfb_linkGradientIndexA = lfb_linkGradientIndex / 30;
            var gradPos1 = lfb_linkGradientIndexA;
            var gradPos2 = lfb_linkGradientIndexA + 0.1;
            var gradPos3 = lfb_linkGradientIndexA + 0.2;
            ctx.lineWidth = 4;
            if (gradPos2 > 1) {
                gradPos2 = 0;
                gradPos3 = 0.2;
            }
            if (gradPos3 > 1) {
                gradPos3 = 0;
            }

            grd.addColorStop(gradPos1, "#bdc3c7");
            grd.addColorStop(gradPos2, "#1ABC9C");
            grd.addColorStop(gradPos3, "#bdc3c7");
            ctx.strokeStyle = grd;
            ctx.beginPath();
            ctx.moveTo(posX, posY);
            ctx.lineTo(posX2, posY2);
            ctx.stroke();

            if (jQuery('.lfb_linkPoint[data-linkindex="' + index + '"]').length == 0) {
                var $point = jQuery('<a href="javascript:" data-linkindex="' + index + '" class="lfb_linkPoint"><span class="glyphicon glyphicon-pencil"></span></a>');
                jQuery('#lfb_stepsContainer').append($point);
                $point.click(function () {
                    lfb_openWinLink(jQuery(this));
                });
            }
            lfb_repositionLinkPoint(index);

        } else {
            jQuery('.lfb_linkPoint[data-linkindex="' + index + '"]').remove();
        }
    });
    if (lfb_isLinking) {
        var step = jQuery('#' + lfb_links[lfb_linkCurrentIndex].originID);
        var posX = step.position().left + jQuery('#lfb_stepsOverflow').scrollLeft() + step.outerWidth() / 2;
        var posY = step.position().top + jQuery('#lfb_stepsOverflow').scrollTop() + step.outerHeight() / 2;
        ctx.strokeStyle = "#bdc3c7";
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(posX, posY);
        ctx.lineTo(lfb_mouseX, lfb_mouseY);
        ctx.stroke();
    }
}
function lfb_removeItem(itemID) {
    lfb_showLoader();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_removeItem',
            itemID: itemID
        },
        success: function () {
            lfb_loadForm(lfb_currentFormID);
            lfb_openWinStep(lfb_currentStepID);
        }
    });
}
function lfb_editItem(itemID) {
    lfb_currentItemID = itemID;
    jQuery('#lfb_winItem').find('input,textarea').val('');
    jQuery('#lfb_winItem').find('select option').removeAttr('selected');
    jQuery('#lfb_winItem').find('select option:eq(0)').attr('selected', 'selected');
    jQuery('#lfb_winItem').find('.switch [data-switch="switch"]').bootstrapSwitch('destroy');
    jQuery('#lfb_winItem').find('.switch > div > :not([data-switch="switch"])').remove();
    jQuery('#lfb_winItem').find('.switch [data-switch="switch"]').unwrap().unwrap();
    jQuery('#lfb_winItem').find('#lfb_itemPricesGrid tbody tr').not('.static').remove();
    jQuery('#lfb_winItem').find('#lfb_itemOptionsValues tbody tr').not('.static').remove();
    if (itemID > 0) {
        jQuery.each(lfb_currentStep.items, function () {
            var item = this;
            if (item.id == itemID) {
                jQuery('#lfb_winItem').find('input,select,textarea').each(function () {
                  if(jQuery(this).is('[data-switch="switch"]')){
                      var value = false;
                  //  jQuery(this).attr('checked','checked');

                  eval('if(item.' + jQuery(this).attr('name') + ' == 1){jQuery(this).attr(\'checked\',\'checked\');} else {jQuery(this).attr(\'checked\',false);}');
                    jQuery(this).wrap('<div class="switch" data-on-label="'+lfb_data.texts['Yes']+'" data-off-label="'+lfb_data.texts['No']+'" />').parent().bootstrapSwitch();
                    }else {
                      eval('jQuery(this).val(item.' + jQuery(this).attr('name') + ');');
                    }
                });
                var reducs = item.reducsQt.split('*');
                jQuery.each(reducs, function () {
                    var reduc = this.split('|');
                    if(reduc[0] && reduc[0] > 0){
                    jQuery('#lfb_itemPricesGrid tbody').prepend('<tr><td>' + reduc[0] + '</td><td>' + parseFloat(reduc[1]).toFixed(2) + '</td><td><a href="javascript:" onclick="lfb_del_reduc(this);" class="btn btn-danger  btn-circle "><span class="glyphicon glyphicon-trash"></span></a></td></tr>');
                  }
                });
                var optionsV = item.optionsValues.split('|');
                jQuery.each(optionsV, function () {
                  if(this != ""){
                    jQuery('#lfb_itemOptionsValues #option_new_value').closest('tr').before('<tr><td>' + this + '</td><td><a href="javascript:" onclick="lfb_del_option(this);" class="btn btn-danger  btn-circle "><span class="glyphicon glyphicon-trash"></span></a></td></tr>');
                  }
                });


                jQuery('#lfb_winItem').find('[name="wooProductID"]').val(item.wooProductID);
                if (item.wooProductID > 0 && item.wooVariation > 0) {
                    jQuery('#lfb_winItem').find('[name="wooProductID"]').find('option[value="' + item.wooProductID + '"]').each(function () {
                        if (jQuery(this).attr('data-woovariation') == item.wooVariation) {
                            jQuery(this).attr('selected', 'selected');
                        }
                    });
                }
            }
        });
    } else {
        jQuery('#lfb_winItem').find('input[name="operation"]').val('+');
        jQuery('#lfb_winItem').find('input[name="ordersort"]').val(0);
        jQuery('#lfb_winItem').find('input[name="quantity_max"]').val(5);
        jQuery('#lfb_winItem').find('[name="reduc_enabled"]').prop('checked',false);
        jQuery('#lfb_winItem').find('[name="quantity_enabled"]').prop('checked',false);
        jQuery('#lfb_winItem').find('[name="ischecked"]').prop('checked',false);
        jQuery('#lfb_winItem').find('select[name="type"]').val('picture');
        jQuery('#lfb_winItem').find('[data-switch="switch"]').wrap('<div class="switch" data-on-label="'+lfb_data.texts['Yes']+'" data-off-label="'+lfb_data.texts['No']+'" />').parent().bootstrapSwitch({onLabel: lfb_data.texts['Yes'],offLabel: lfb_data.texts['No']});


    }
    jQuery('#lfb_winItem').find('[name="quantity_enabled"]').on('change', lfb_changeQuantityEnabled);
    lfb_changeQuantityEnabled();
    jQuery('#lfb_winItem').find('[name="reduc_enabled"]').on('change', lfb_changeReducEnabled);
    lfb_changeReducEnabled();
    jQuery('#lfb_winItem').find('[name="quantityUpdated"]').change(lfb_changeQuantity);
    lfb_changeQuantity();
    jQuery('#lfb_winItem').find('[name="wooProductID"]').change(lfb_changeWoo);
    lfb_changeWoo();
    jQuery('#lfb_winItem').find('[name="operation"]').change(lpf_changeOperation);
    lpf_changeOperation();
    jQuery('#lfb_winItem').find('[name="type"]').change(lfb_changeItemType);
    lfb_changeItemType();
    jQuery('#lfb_winItem').fadeIn();
}
var lfb_isWoo = false;

function lfb_changeWoo() {
    if (jQuery('#lfb_winItem').find('[name="wooProductID"]').val() != '0') {
        if (!lfb_isWoo) {
            jQuery('#lfb_winItem').find('[name="quantity_enabled"]').prop('checked',true);
            jQuery('.quantity_max_tr').show();
        }
        lfb_isWoo = true;
        jQuery('.wooMasked').fadeOut(250);
        jQuery('#lfb_winItem').find('[name="title"]').val(jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('title'));
        if (jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('max')) {
            jQuery('#lfb_winItem').find('[name="quantity_max"]').val(jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('max'));
        }
        if (jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('img')) {
            jQuery('#lfb_winItem').find('[name="image"]').val(jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('img'));
        }
    } else {
        lfb_isWoo = false;
        jQuery('.wooMasked').fadeIn(250);
    }

}
function lpf_changeOperation() {
    if (jQuery('#lfb_winItem').find('[name="operation"]').val() == 'x' || jQuery('#lfb_winItem').find('[name="operation"]').val() == '/') {
        jQuery('#lfb_winItem').find('[name="price"]').parent().find('label:eq(1)').slideDown();
        jQuery('#lfb_winItem').find('[name="price"]').parent().find('label:eq(0)').slideUp();
    } else {
        jQuery('#lfb_winItem').find('[name="price"]').parent().find('label:eq(1)').slideUp();
        jQuery('#lfb_winItem').find('[name="price"]').parent().find('label:eq(0)').slideDown();
    }
    if (jQuery('#lfb_winItem').find('[name="operation"]').val() != '+') {
        jQuery('#lfb_winItem').find('[name="reduc_enabled"]').closest('.form-group').slideUp();
        jQuery('#lfb_winItem').find('[name="reduc_enabled"]').prop('checked',false);
        jQuery('#lfb_itemPricesGrid').slideUp();
        jQuery('#lfb_winItem').find('#lfb_itemPricesGrid tbody tr').not('.static').remove();
    } else if (jQuery('#lfb_winItem').find('[name="quantity_enabled"]').is(':checked')) {
        jQuery('#lfb_winItem').find('[name="reduc_enabled"]').closest('.form-group').slideDown();
    }
}

function lfb_changeItemType() {
    if (jQuery('#lfb_winItem').find('[name="type"]').val() == 'picture' || jQuery('#lfb_winItem').find('[name="type"]').val() == 'qtfield') {
        jQuery('.picOnly').slideDown();
        jQuery('#lfb_winItem').find('[name="quantity_enabled"]').closest('.form-group').slideDown();
        jQuery('#lfb_winItem').find('[name="price"]').closest('.form-group').slideDown();
        jQuery('#lfb_winItem').find('[name="isRequired"]').closest('.form-group').slideUp();
        jQuery('#lfb_winItem').find('#lfb_itemOptionsValuesPanel').slideUp();
        jQuery('#lfb_winItem').find('[name="operation"]').closest('.form-group').slideDown();
        jQuery('#lfb_winItem').find('[name="showPrice"]').closest('.form-group').slideDown();
        jQuery('#lfb_winItem').find('[name="groupitems"]').closest('.form-group').slideDown();
        jQuery('#lfb_winItem').find('[name="quantity_max"]').closest('.form-group').slideDown();
        jQuery('#lfb_winItem').find('[name="reduc_enabled"]').closest('.form-group').slideDown();
        if (jQuery('#lfb_winItem').find('[name="type"]').val() == 'qtfield') {
            jQuery('#lfb_winItem').find('[name="isRequired"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="isSelected"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="groupitems"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="imageTint"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="quantity_enabled"]').prop('checked',true);
            jQuery('#lfb_winItem').find('[name="quantity_enabled"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="image"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="ischecked"]').closest('.form-group').slideUp();
           jQuery('#lfb_winItem').find('[name="urlTarget"]').closest('.form-group').slideUp();

        } else {
          jQuery('#lfb_winItem').find('[name="ischecked"]').closest('.form-group').slideDown();
          jQuery('#lfb_winItem').find('#lfb_itemOptionsValuesPanel').slideUp();
          jQuery('#lfb_winItem').find('[name="wooProductID"]').closest('.form-group').slideDown();

         jQuery('#lfb_winItem').find('[name="urlTarget"]').closest('.form-group').slideDown();
          lfb_changeReducEnabled();
        }
    } else {
        jQuery('#lfb_winItem').find('[name="isRequired"]').closest('.form-group').slideDown();
        jQuery('.picOnly').slideUp();
        jQuery('#lfb_itemPricesGrid').slideUp();
        jQuery('#lfb_winItem').find('input[name="showPrice"]').val(0);

        if (jQuery('#lfb_winItem').find('[name="type"]').val() == 'textfield') {
            jQuery('#lfb_winItem').find('[name="operation"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="price"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="showPrice"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('#lfb_itemOptionsValuesPanel').slideUp();
            jQuery('#lfb_winItem').find('[name="groupitems"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="wooProductID"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="isSelected"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="ischecked"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="quantity_enabled"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('input[name="showPrice"]').val(0);
           jQuery('#lfb_winItem').find('[name="urlTarget"]').closest('.form-group').slideUp();
          }
           else  if (jQuery('#lfb_winItem').find('[name="type"]').val() == 'select') {
           jQuery('#lfb_winItem').find('input[name="showPrice"]').val(0);
               jQuery('#lfb_winItem').find('[name="operation"]').parent().slideUp();
               jQuery('#lfb_winItem').find('[name="price"]').closest('.form-group').slideUp();
               jQuery('#lfb_winItem').find('[name="showPrice"]').closest('.form-group').slideUp();
               jQuery('#lfb_winItem').find('[name="groupitems"]').parent().slideUp();
               jQuery('#lfb_winItem').find('[name="wooProductID"]').parent().slideUp();
               jQuery('#lfb_winItem').find('[name="isSelected"]').closest('.form-group').slideUp();
               jQuery('#lfb_winItem').find('[name="ischecked"]').closest('.form-group').slideUp();
               jQuery('#lfb_winItem').find('[name="quantity_enabled"]').closest('.form-group').slideUp();
               jQuery('#lfb_winItem').find('#lfb_itemOptionsValuesPanel').slideDown();
              jQuery('#lfb_winItem').find('[name="urlTarget"]').closest('.form-group').slideUp();

      //    lfb_itemOptionsValues

        } else  if (jQuery('#lfb_winItem').find('[name="type"]').val() == 'filefield') {
            jQuery('#lfb_winItem').find('[name="operation"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="price"]').parent().slideUp();
            jQuery('#lfb_winItem').find('#lfb_itemOptionsValuesPanel').slideUp();
            jQuery('#lfb_winItem').find('[name="showPrice"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="isSelected"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="groupitems"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="wooProductID"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="ischecked"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="quantity_enabled"]').closest('.form-group').slideUp();
        } else {
          jQuery('#lfb_winItem').find('[name="price"]').closest('.form-group').slideDown();
            jQuery('#lfb_winItem').find('[name="showPrice"]').closest('.form-group').slideDown();
            jQuery('#lfb_winItem').find('[name="operation"]').parent().slideDown();
            jQuery('#lfb_winItem').find('[name="groupitems"]').parent().slideDown();
            jQuery('#lfb_winItem').find('#lfb_itemOptionsValuesPanel').slideUp();
            jQuery('#lfb_winItem').find('[name="wooProductID"]').parent().slideUp();
            jQuery('#lfb_winItem').find('[name="ischecked"]').closest('.form-group').slideDown();
            jQuery('#lfb_winItem').find('[name="quantity_enabled"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="quantity_max"]').closest('.form-group').slideUp();
            jQuery('#lfb_winItem').find('[name="reduc_enabled"]').closest('.form-group').slideUp();
           jQuery('#lfb_winItem').find('[name="urlTarget"]').closest('.form-group').slideDown();
        }
    }
}
function lfb_changeQuantityEnabled() {
    if (jQuery('#lfb_winItem').find('[name="quantity_enabled"]').is(':checked') && jQuery('#lfb_winItem').find('select[name="operation"]').val() == '+') {
        jQuery('#efp_itemQuantity').slideDown();
    } else {
        jQuery('#lfb_winItem').find('[name="reduc_enabled"]').prop('checked',false);
        jQuery('#efp_itemQuantity').slideUp();
    }
}
function lfb_changeReducEnabled() {
    if (jQuery('#lfb_winItem').find('[name="reduc_enabled"]').is(':checked')) {
        jQuery('#lfb_itemPricesGrid').slideDown(250);
    } else {
        jQuery('#lfb_itemPricesGrid').slideUp(250);
    }
}
function lfb_changeQuantity() {
    if (jQuery('#lfb_winItem').find('input[name="quantityUpdated"]').val() < 1) {
        jQuery('#lfb_winItem').find('input[name="quantityUpdated"]').val('3');
    }
}
function lfb_getReducs() {
    var reducsTab = new Array();
    jQuery('#lfb_itemPricesGrid tbody tr').not('.static').each(function () {
        var qt = jQuery(this).find('td:eq(0)').html();
        var price = jQuery(this).find('td:eq(1)').html();
        reducsTab.push(new Array(qt, price));
    });
    reducsTab.sort(function (a, b) {
        return a[0] - b[0];
    });
    return reducsTab;
}
function lfb_getOptions() {
  var optionsTab = new Array();
  jQuery('#lfb_itemOptionsValues tbody tr').not('.static').each(function () {
    optionsTab.push(jQuery(this).find('td:eq(0)').html());
  });
  return optionsTab;
}
function lfb_add_option(){
  var newValue = jQuery('#lfb_itemOptionsValues #option_new_value').val();
  if(newValue != ""){
  jQuery('#lfb_itemOptionsValues #option_new_value').closest('tr').before('<tr><td>'+newValue+'</td><td><a href="javascript:" onclick="lfb_del_option(this);" class="btn btn-danger btn-circle "><span class="glyphicon glyphicon-trash"></span></a></td></tr>');
  jQuery('#lfb_itemOptionsValues #option_new_value').val('');
  }
}
function lfb_del_option(btn) {
    jQuery(btn).parent().parent().remove();
}

function lfb_add_reduc() {
    var qt = parseInt(jQuery('#reduc_new_qt').val());
    var price = parseFloat(jQuery('#reduc_new_price').val());

    if(!isNaN(qt) && qt>0 && !isNaN(price)){

      var reducsTab = lfb_getReducs();
      reducsTab.push(new Array(qt, price));
      reducsTab.sort(function (a, b) {
          return b[0] - a[0];
      });
      jQuery('#lfb_itemPricesGrid tbody tr').not('.static').remove();
      jQuery.each(reducsTab, function () {
          jQuery('#lfb_itemPricesGrid tbody').prepend('<tr><td>' + this[0] + '</td><td>' + parseFloat(this[1]).toFixed(2) + '</td><td><a href="javascript:" onclick="lfb_del_reduc(this);" class="btn btn-danger btn-circle "><span class="glyphicon glyphicon-trash"></span></a></td></tr>');
      });
      jQuery('#reduc_new_qt').val('');
      jQuery('#reduc_new_price').val('');
  }
}
function lfb_del_reduc(btn) {
    jQuery(btn).parent().parent().remove();
}

function lfb_saveItem() {
    var reducs = '';
    var optionsValues = '';
    var wooVariation = 0;

    //
    jQuery('#lfb_winItem').find('input[name="title"]').parent().removeClass('has-error');
    jQuery('#lfb_winItem').find('input[name="image"]').parent().removeClass('has-error');
    jQuery('#lfb_winItem').find('input[name="quantity_max"]').parent().removeClass('has-error');

    if (jQuery('#lfb_winItem').find('input[name="title"]').val() < 1) {
        error = true;
        jQuery('#lfb_winItem').find('input[name="title"]').parent().addClass('has-error');
    }
    if (jQuery('#lfb_winItem').find('input[name="type"]').val() == 'picture' && jQuery('#lfb_winItem').find('input[name="image"]').val().length < 4) {
        error = true;
        jQuery('#lfb_winItem').find('input[name="image"]').parent().addClass('has-error');
    }
    if (jQuery('#lfb_winItem').find('[name="quantity_enabled"]').val() == '1' && jQuery('#lfb_winItem').find('input[name="quantity_max"]').val() == "") {
        error = true;
        jQuery('#lfb_winItem').find('input[name="quantity_max"]').parent().addClass('has-error');
    }
      var optionStab = lfb_getOptions();
      jQuery.each(optionStab, function () {
        optionsValues += this + '|';
      });

    if (jQuery('#lfb_winItem').find('[name="reduc_enabled"]').is(':checked')) {
        var reducsTab = lfb_getReducs();
        jQuery.each(reducsTab, function () {
            reducs += this[0] + '|' + parseFloat(this[1]).toFixed(2) + '*';
        });
        reducs = reducs.substr(0, reducs.length - 1);
    }
    if (jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('woovariation') && jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('woovariation') > 0) {
        wooVariation = jQuery('#lfb_winItem').find('[name="wooProductID"] option:selected').data('woovariation');
    }


    lfb_showLoader();
    jQuery('#lfb_winItem').fadeOut();
    var itemData = {};
    jQuery('#lfb_winItem').find('input,select,textarea').each(function () {
        if (jQuery(this).closest('#lfb_itemPricesGrid').length == 0 && jQuery(this).closest('#lfb_itemOptionsValues').length == 0) {
          if (!jQuery(this).is('[data-switch="switch"]')) {
            eval('itemData.' + jQuery(this).attr('name') + ' = jQuery(this).val();');
          } else {
            var value = 0;
            if (jQuery(this).is(':checked')){
              value = 1;
            }
              eval('itemData.' + jQuery(this).attr('name') + ' = value;');
        }
        }
    });
    itemData.action = 'lfb_saveItem';
    itemData.formID = lfb_currentFormID;
    itemData.stepID = lfb_currentStepID;
    itemData.id = lfb_currentItemID;
    itemData.wooVariation = wooVariation;
    itemData.reducsQt = reducs;
    itemData.optionsValues = optionsValues;

    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: itemData,
        success: function (itemID) {
            lfb_loadForm(lfb_currentFormID);
            lfb_openWinStep(lfb_currentStepID);
        }
    });
}

function lfb_checkLicense(){
  var error = false;
  var $field = jQuery('#lfb_winActivation input[name="purchaseCode"]');
  if($field.val().length<9){
    $field.parent().addClass('has-error');
  } else  {
      lfb_showLoader();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {action:'lfb_checkLicense',code:$field.val()},
        success: function (rep) {
            jQuery('#lfb_loader').fadeOut();
            if(rep.length>0){
              $field.parent().addClass('has-error');
            } else {
              jQuery('#lfb_winActivation').modal('hide');
            }
        }
    });
  }
}

function lfb_duplicateForm(formID){
  lfb_showLoader();
  jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {action:'lfb_duplicateForm',formID:formID},
      success: function (rep) {
        document.location.href = document.location.href;
      }
    });
}
function lfb_duplicateItem(itemID){
  lfb_showLoader();
  jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {action:'lfb_duplicateItem',itemID:itemID},
      success: function (rep) {
        lfb_openWinStep(lfb_currentStepID);
      }
    });
}

function lfb_startPreview() {

}
function lfb_openWinStep(stepID) {
    lfb_currentStepID = stepID;
        lfb_showLoader();

    jQuery('#lfb_itemsTable tbody').html('');
    if (lfb_currentStepID == 0) {
        jQuery('#lfb_itemsList').hide();
    } else {
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_loadStep',
                stepID: stepID
            },
            success: function (rep) {
                rep = jQuery.parseJSON(rep);
                step = rep.step;
                lfb_currentStep = rep;

                jQuery('#lfb_stepTabGeneral').find('input,select,textarea').each(function () {
                    eval('jQuery(this).val(step.' + jQuery(this).attr('name') + ');');
                });
                jQuery.each(rep.items, function () {
                    var item = this;
                    var $tr = jQuery('<tr data-itemid="' + item.id + '"></tr>');
                    $tr.append('<td><a href="javascript:"  onclick="lfb_editItem(' + item.id + ');">' + item.title + '</a></td>');
                    $tr.append('<td>' + item.groupitems + '</td>');
                    $tr.append('<td><a href="javascript:" onclick="lfb_editItem(' + item.id + ');" class="btn btn-primary btn-circle"><span class="glyphicon glyphicon-pencil"></span></a>'+
                        '<a href="javascript:" onclick="lfb_duplicateItem(' + item.id + ');" class="btn btn-default btn-circle"><span class="glyphicon glyphicon-duplicate"></span></a>'+
                        '<a href="javascript:" onclick="lfb_removeItem(' + item.id + ');" class="btn btn-danger btn-circle"><span class="glyphicon glyphicon-trash"></span></a></td>');
                    jQuery('#lfb_itemsTable tbody').append($tr);
                });
                jQuery('#lfb_itemsList').show();

                jQuery('#lfb_btns').html('');
                jQuery('#lfb_winStep').show();
                jQuery('#lfb_stepsContainer').slideUp();
                jQuery('#lfb_loader').fadeOut();

                jQuery('#wpwrap').css({
                    height: jQuery('#lfb_winStep').height() + 48
                });

            }
        });
    }

}


function lfb_saveStep() {
    lfb_showLoader();
    var stepData = {};
    jQuery('#lfb_stepTabGeneral').find('input,select,textarea').each(function () {
        eval('stepData.' + jQuery(this).attr('name') + ' = jQuery(this).val();');
    });
    stepData.action = 'lfb_saveStep';
    stepData.formID = lfb_currentFormID;
    stepData.id = lfb_currentStepID;
    jQuery('.lfb_stepBloc[data-stepid="' + lfb_currentStepID + '"] h4').html(stepData.title);
    lfb_updateStepsDesign();

    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: stepData,
        success: function (stepID) {
            lfb_openWinStep(stepID);
        }
    });
}

function lfb_closeWin(win) {
    win.fadeOut();
    jQuery('#lfb_stepsContainer').slideDown();

    setTimeout(function () {
        lfb_updateStepsDesign();
    }, 250);
}

function lfb_startLink(stepID) {
    lfb_isLinking = true;
    lfb_linkCurrentIndex = lfb_links.length;
    lfb_links.push({
        originID: stepID,
        destinationID: null
    });

}

function lfb_stopLink(newStep) {
    lfb_isLinking = false;
    var chkLink = false;
    jQuery.each(lfb_links, function () {
        if (this.originID == lfb_links[lfb_linkCurrentIndex].originID && this.destinationID == newStep.attr('id')) {
            chkLink = this;
        }
    });
    if (!chkLink) {
        lfb_showLoader();
        lfb_links[lfb_linkCurrentIndex].destinationID = newStep.attr('id');
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_newLink',
                formID: lfb_currentFormID,
                originStepID: jQuery('#' + lfb_links[lfb_linkCurrentIndex].originID).attr('data-stepid'),
                destinationStepID: jQuery('#' + lfb_links[lfb_linkCurrentIndex].destinationID).attr('data-stepid')
            },
            success: function (linkID) {
                lfb_links[lfb_linkCurrentIndex].id = linkID;
                lfb_loadForm(lfb_currentFormID);
            }
        });
    } else {
        jQuery.grep(lfb_links, function (value) {
            return value != chkLink;
        });
    }
}

function lfb_itemsCheckRows(item) {
    var clear = jQuery(item).parent().children('.clearfix');
    clear.detach();
    jQuery(item).parent().append(clear);
}


function lfb_getUniqueTime() {
    var time = new Date().getTime();
    while (time == new Date().getTime());
    return new Date().getTime();
}

function lfb_changeInteractionBubble(action) {
    jQuery('#lfb_interactionBubble').data('type', action);
    jQuery('#lfb_interactionBubble #lfb_interactionContent > div').slideUp();
    if (action != "") {
        jQuery('#lfb_interactionBubble #lfb_interactionContent > [data-type="' + action + '"]').slideDown();
    }
    if (action == 'select') {
        var nbSel = jQuery('#lfb_interactionContent > [data-type="' + action + '"]').find('.form-group:not(.default)').length;

        if (nbSel == 0 || jQuery('#lfb_interactionContent > [data-type="' + action + '"]').find('.form-group:not(.default):last-child').find('input').val() == '') {
            lfb_interactionAddSelect(action);
        }
    }
}

function lfb_interactionAddSelect(action) {
    var nbSel = jQuery('#lfb_interactionContent > [data-type="' + action + '"]').find('.form-group').length;
    var $field = jQuery('<div class="form-group"><label>' + lfb_data.txt_option + '</label><input type="text" placeholder="' + lfb_data.txt_option + '" class="form-control" name="s_' + nbSel + '_value"></div>');
    $field.find('input').keyup(function () {
        if (jQuery(this).val() == '') {
            if (jQuery(this).closest('.form-group:not(.default)').index() > 0) {
                jQuery(this).closest('.form-group:not(.default)').remove();
            }
        } else {
            if (jQuery(this).closest('.form-group:not(.default)').next('.form-group:not(.default)').length == 0) {
                lfb_interactionAddSelect(action)
            }
        }
    });
    jQuery('#lfb_interactionContent > [data-type="' + action + '"]').append($field);
    return $field;
}

function lfb_openWinLink($item) {
    lfb_currentLinkIndex = $item.attr('data-linkindex');
    jQuery('#lfb_winLink').attr('data-linkindex', $item.attr('data-linkindex'));
    jQuery('.lfb_conditionItem').remove();
    var stepID = jQuery('#' + lfb_links[$item.attr('data-linkindex')].originID).attr('data-stepid');
    var step = lfb_getStepByID(stepID);
    var destID = jQuery('#' + lfb_links[$item.attr('data-linkindex')].destinationID).attr('data-stepid');
    var destination = lfb_getStepByID(destID);

    jQuery('#lfb_linkInteractions').show();
    jQuery('#lfb_linkOriginTitle').html(step.title);
    jQuery('#lfb_linkDestinationTitle').html(destination.title);

    jQuery.each(lfb_links[lfb_currentLinkIndex].conditions, function () {
        lfb_addLinkInteraction(this);
    });
    jQuery('#lfb_winLink').fadeIn(250);

    setTimeout(lfb_updateStepsDesign, 255);
    setTimeout(function () {
        jQuery('#wpwrap').css({
            height: jQuery('#lfb_bootstraped').height() + 48
        });
    }, 300);

}

function lfb_addLinkInteraction(data) {
    var $item = jQuery('<tr class="lfb_conditionItem"></tr>');
    var $select = jQuery('<select class="lfb_conditionSelect form-control"></select>');
    jQuery.each(lfb_steps, function () {
        var step = this;
        jQuery.each(step.items, function () {
            var item = this;
            var itemID = step.id + '_' + item.id;
            $select.append('<option value="' + itemID + '" data-type="' + item.type + '">' + step.title + ' : " ' + item.title + ' "</option>');
        });
    });
    $select.append('<option value="_total" data-static="1" data-type="totalPrice" data-variable="pricefield">' + lfb_data.texts['totalPrice'] + '</option>');
    var $operator = jQuery('<select class="lfb_conditionoperatorSelect form-control"></select>');
    $select.change(function () {
        var stepID = $select.val().substr(0, $select.val().indexOf('_'));
        var itemID = $select.val().substr($select.val().indexOf('_') + 1, $select.val().length);
        var item = false;
        jQuery.each(lfb_steps, function () {
            var step = this;
            if (step.id == stepID) {
                jQuery.each(step.items, function () {
                    if (this.id == itemID) {
                        item = this;
                    }
                });
            }
        });
        var operator = jQuery(this).parent().parent().find('.lfb_conditionoperatorSelect');
        operator.find('option').remove();
        if ($select.find('option:selected').is('[data-static]')) {
            var options = lfb_conditionGetOperators({
                type: $select.find('option:selected').attr('data-type')
            }, $select);
        } else {
            var options = lfb_conditionGetOperators(item, $select);
        }
        jQuery.each(options, function () {
            operator.append('<option value="' + this.value + '"  data-variable="' + this.hasVariable + '">' + this.text + '</option>');
        });
        $operator.change();
    });
    if (data) {
        $select.val(data.interaction);
    }
    $select.change();
    if ($select.find('option:selected').is('[data-static]')) {
        var options = lfb_conditionGetOperators({
            type: $select.find('option:selected').attr('data-type')
        }, $select);
    } else {
        var stepID = $select.val().substr(0, $select.val().indexOf('_'));
        var itemID = $select.val().substr($select.val().indexOf('_') + 1, $select.val().length);
        var item = false;
        jQuery.each(lfb_steps, function () {
            var step = this;
            if (step.id == stepID) {
                jQuery.each(step.items, function () {
                    if (this.id == itemID) {
                        item = this;
                    }
                });
            }
        });
        var options = lfb_conditionGetOperators(item, $select);
    }
    jQuery.each(options, function () {
        $operator.append('<option value="' + this.value + '" data-variable="' + this.hasVariable + '">' + this.text + '</option>');
    });

    $operator.change(function () {
        lfb_linksUpdateFields(jQuery(this));
    });
    var $col1 = jQuery('<td></td>');
    $col1.append($select);
    $item.append($col1);
    var $col2 = jQuery('<td></td>');
    $col2.append($operator);
    $item.append($col2);
    $item.append('<td></td><td><a href="javascript:" class="lfb_conditionDelBtn" onclick="lfb_conditionRemove(this);"><span class="glyphicon glyphicon-remove"></span></a> </td>');
    if (data) {
        $operator.val(data.action);
        $operator.change();
        if (data.value) {
            $operator.closest('.lfb_conditionItem').find('.lfb_conditionValue').val(data.value);
        }
        setTimeout(function () {
            lfb_linksUpdateFields($operator, data);
            if (data.value) {
                $operator.closest('.lfb_conditionItem').find('.lfb_conditionValue').val(data.value);
            }
        }, 500);
    }
    jQuery('#lfb_conditionsTable tbody').append($item);
}

function lfb_linksUpdateFields($operatorSelect, data) {

    $operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').parent().remove();
    if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionoperatorSelect option:selected').attr('data-variable') == "textfield") {
        if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').length == 0) {
            $operatorSelect.closest('.lfb_conditionItem').children('td:eq(2)').html('<div><input type="text" placeholder="http://..." class="lfb_conditionValue form-control" /> </div>');
        }
    }

    if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionoperatorSelect option:selected').attr('data-variable') == "numberfield") {
        if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').length == 0) {
            $operatorSelect.closest('.lfb_conditionItem').children('td:eq(2)').html('<div><input type="number" class="lfb_conditionValue form-control" /> </div>');
        }
    }
    if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionoperatorSelect option:selected').attr('data-variable') == "pricefield") {
        if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').length == 0) {
            $operatorSelect.closest('.lfb_conditionItem').children('td:eq(2)').html('<div><input type="number" step="any" class="lfb_conditionValue form-control" /> </div>');
        }
    }
    if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionoperatorSelect option:selected').attr('data-variable') == "select") {
      var optionsSelect = '';
      var $select = $operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionSelect');
      var stepID = $select.val().substr(0, $select.val().indexOf('_'));
      var itemID = $select.val().substr($select.val().indexOf('_') + 1, $select.val().length);

      var optionsString = '';
      jQuery.each(lfb_currentForm.steps,function(){
          if(this.id == stepID){
          jQuery.each(this.items,function(){
            if(this.id == itemID){
            optionsString = this.optionsValues;
            }
          });
          }
      });
      var optionsArray = optionsString.split('|');
      jQuery.each(optionsArray,function(){
        if(this.length >0){
          optionsString += '<option value="'+this+'">'+this+'</option>';
        }
      });

      if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').length == 0) {
          $operatorSelect.closest('.lfb_conditionItem').children('td:eq(2)').html('<div><select class="lfb_conditionValue form-control">'+optionsString+'</select> </div>');
      }
    }

    if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionoperatorSelect option:selected').attr('data-variable') == "datefield") {
        if ($operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').length == 0) {
            $operatorSelect.closest('.lfb_conditionItem').children('td:eq(2)').html('<div><input type="text" step="any" class="lfb_conditionValue form-control"/> </div>');
            $operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }
    }
    if (data && data.value) {
        $operatorSelect.closest('.lfb_conditionItem').find('.lfb_conditionValue').val(data.value);
    }
}

function lfb_conditionRemove(btn) {
    var $btn = jQuery(btn);
    $btn.closest('.lfb_conditionItem').remove();
}

function lfb_linkSave() {
    lfb_links[lfb_currentLinkIndex].conditions = new Array();
    jQuery('.lfb_conditionItem').each(function () {
        lfb_links[lfb_currentLinkIndex].conditions.push({
            interaction: jQuery(this).find('.lfb_conditionSelect').val(),
            action: jQuery(this).find('.lfb_conditionoperatorSelect').val(),
            value: jQuery(this).find('.lfb_conditionValue').val()
        });
    });
    var cloneLinks = lfb_links.slice();
    jQuery.each(cloneLinks, function () {
        this.originID = jQuery('#' + this.originID).attr('data-stepid');
        this.destinationID = jQuery('#' + this.destinationID).attr('data-stepid');
    });
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_saveLinks',
            formID: lfb_currentFormID,
            links: JSON.stringify(cloneLinks)
        },
        success: function () {
            lfb_closeWin(jQuery('#lfb_winLink'));
            lfb_loadForm(lfb_currentFormID);
        }
    });

}

function lfb_linkDel() {
    lfb_links.splice(jQuery.inArray(lfb_links[lfb_currentLinkIndex], lfb_links), 1);
    var cloneLinks = lfb_links.slice();
    jQuery.each(cloneLinks, function () {
        this.originID = jQuery('#' + this.originID).attr('data-stepid');
        this.destinationID = jQuery('#' + this.destinationID).attr('data-stepid');
    });
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_saveLinks',
            formID: lfb_currentFormID,
            links: JSON.stringify(cloneLinks)
        },
        success: function () {
            lfb_closeWin(jQuery('#lfb_winLink'));
            lfb_loadForm(lfb_currentFormID);
        }
    });
}

function lfb_conditionGetOperators(item, $select) {
    var options = new Array();
    switch (item.type) {
        case "totalPrice":
            options.push({
                value: 'superior',
                text: lfb_data.texts['isSuperior'],
                hasVariable: 'pricefield'
            });
            options.push({
                value: 'inferior',
                text: lfb_data.texts['isInferior'],
                hasVariable: 'pricefield'
            });
            options.push({
                value: 'equal',
                text: lfb_data.texts['isEqual'],
                hasVariable: 'pricefield'
            });
            break;
        case "picture":
            options.push({
                value: 'clicked',
                text: lfb_data.texts['isSelected']
            });
            options.push({
                value: 'unclicked',
                text: lfb_data.texts['isUnselected']
            });
            if (item.quantity_enabled == "1") {
                options.push({
                    value: 'QtSuperior',
                    text: lfb_data.texts['isQuantitySuperior'],
                    hasVariable: 'numberfield'
                });
                options.push({
                    value: 'QtInferior',
                    text: lfb_data.texts['isQuantityInferior'],
                    hasVariable: 'numberfield'
                });
                options.push({
                    value: 'QtEqual',
                    text: lfb_data.texts['isQuantityEqual'],
                    hasVariable: 'numberfield'
                });
            }
            break;
        case "textfield":
            options.push({
                value: 'filled',
                text: lfb_data.texts['isFilled']
            });
            break;
        case "select":
            options.push({
                value: 'equal',
                text: lfb_data.texts['isEqual'],
                hasVariable: 'select'
            });
            break;
        case "filefield":
            options.push({
                value: 'filled',
                text: lfb_data.texts['isFilled']
            });
            break;
        case "checkbox":
            options.push({
                value: 'clicked',
                text: lfb_data.texts['isSelected']
            });
            options.push({
                value: 'unclicked',
                text: lfb_data.texts['isUnselected']
            });
            break;
        case "datefield":
            options.push({
                value: 'filled',
                text: lfb_data.txt_filled
            });
            options.push({
                value: 'superior',
                text: lfb_data.txt_superiorTo
            });
            options.push({
                value: 'inferior',
                text: lfb_data.txt_inferiorTo
            });
            options.push({
                value: 'equal',
                text: lfb_data.txt_equalTo
            });
            break;
        case "date":
            options.push({
                value: 'superior',
                text: lfb_data.txt_superiorTo
            });
            options.push({
                value: 'inferior',
                text: lfb_data.txt_inferiorTo
            });
            options.push({
                value: 'equal',
                text: lfb_data.txt_equalTo
            });
            break;
    }
    return options;
}


function lfb_updateWinItemPosition() {
    if (jQuery('#lfb_winStep').css('display') != 'none') {
        var $item = jQuery('#' + jQuery('#lfb_itemWindow').attr('data-item'));
        if ($item.length > 0) {
            jQuery('#lfb_itemWindow').css({
                top: $item.offset().top - jQuery('#lfb_bootstraped.lfb_bootstraped').offset().top + $item.outerHeight() + 12,
                left: $item.offset().left - jQuery('#lfb_bootstraped.lfb_bootstraped').offset().left
            });
        } else {
            jQuery('#lfb_itemWindow').fadeOut();
        }
    } else {
        jQuery('#lfb_itemWindow').fadeOut();
    }
}

function lfb_checkEmail(emailToTest) {
    if (emailToTest.indexOf("@") != "-1" && emailToTest.indexOf(".") != "-1" && emailToTest != "")
        return true;
    return false;
}


function lfb_existInDefaultStep(itemID) {
    var rep = false;
    jQuery.each(lfb_defaultStep.interactions, function () {
        var interaction = this;
        if (interaction.itemID == itemID) {
            rep = true;
        }
    });
    return rep;
}

function lfb_removeAllSteps() {
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_removeAllSteps',
            formID: lfb_currentFormID
        },
        success: function () {
            lfb_loadForm(lfb_currentFormID);
        }
    });
}

function lfb_addForm() {
    lfb_showLoader();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_addForm'
        },
        success: function (formID) {
            lfb_loadForm(formID);
        }
    });
}
function lfb_removeForm(formID) {
    lfb_showLoader();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_removeForm',
            formID: formID
        },
        success: function () {
            lfb_closeSettings();
        }
    });

}
function lfb_saveForm() {
    lfb_showLoader();
    var formData = {};
    jQuery('#lfb_formFields').find('input,select,textarea').each(function () {
        if (jQuery(this).closest('#lfb_fieldBubble').length == 0){
          if (!jQuery(this).is('[data-switch="switch"]')) {
            eval('formData.' + jQuery(this).attr('name') + ' = jQuery(this).val();');
          } else {
            var value = 0;
            if (jQuery(this).is(':checked')){
              value = 1;
            }
              eval('formData.' + jQuery(this).attr('name') + ' = value;');
        }
      }
    });

    if(tinyMCE.get('email_adminContent')){
      formData.email_adminContent = tinyMCE.get('email_adminContent').getContent();
    }
    if(tinyMCE.get('email_userContent')){
      formData.email_userContent = tinyMCE.get('email_userContent').getContent();
    }

    formData.action = 'lfb_saveForm';
    formData.formID = lfb_currentFormID;
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: formData,
        success: function () {
            jQuery('#lfb_loader').fadeOut();
        }
    });
}
function lfb_editField(fieldID) {
    jQuery('#lfb_fieldBubble').find('input,textarea').val('');
    jQuery('#lfb_fieldBubble').find('select option').removeAttr('selected');
    jQuery('#lfb_fieldBubble').find('select option:eq(0)').attr('selected', 'selected');
    if (fieldID > 0) {
        jQuery.each(lfb_currentForm.fields, function () {
            var field = this;
            if (field.id == fieldID) {
                jQuery('#lfb_fieldBubble').find('input,select,textarea').each(function () {
                    eval('jQuery(this).val(field.' + jQuery(this).attr('name') + ');');
                });
            }
        });
        jQuery('#lfb_fieldBubble').css({
            left: jQuery('#lfb_finalStepFields tr[data-fieldid="' + fieldID + '"] td:eq(0) a').offset().left,
            top: jQuery('#lfb_finalStepFields tr[data-fieldid="' + fieldID + '"] td:eq(0) a').offset().top
        });
    } else {
        jQuery('#lfb_fieldBubble').find('input[name="id"]').val(0);
        jQuery('#lfb_fieldBubble').css({
            left: jQuery('#lfb_addFieldBtn').offset().left,
            top: jQuery('#lfb_addFieldBtn').offset().top + 18
        });
    }
    jQuery('#lfb_fieldBubble').fadeIn();
    jQuery('#lfb_fieldBubble').addClass('lfb_hover');
    setTimeout(function () {
        jQuery('#lfb_fieldBubble').removeClass('lfb_hover');
    }, 50);

}
function lfb_saveField() {
    lfb_showLoader();
    jQuery('#lfb_fieldBubble').fadeOut();
    var fieldData = {};
    jQuery('#lfb_fieldBubble').find('input,select,textarea').each(function () {
        eval('fieldData.' + jQuery(this).attr('name') + ' = jQuery(this).val();');
    });
    fieldData.action = 'lfb_saveField';
    fieldData.formID = lfb_currentFormID;
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: fieldData,
        success: function () {
            lfb_loadFields();
        }
    });
}
function lfb_loadFields() {
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_loadFields',
            formID: lfb_currentFormID
        },
        success: function (fields) {
            jQuery('#lfb_finalStepFields table tbody').html('');
            if(fields != "[]"){
            fields = JSON.parse(fields);
            lfb_currentForm.fields = fields;
            jQuery.each(fields, function () {
                var field = this;
                var $tr = jQuery('<tr data-fieldid="' + field.id + '"></tr>');
                $tr.append('<td><a href="javascript:" onclick="lfb_editField(' + field.id + ');">' + field.label + '</a></td>');
                $tr.append('<td>' + field.ordersort + '</td>');
                $tr.append('<td>' + field.typefield + '</td>');
                $tr.append('<td>' +
                    '<a href="javascript:" onclick="lfb_editField(' + field.id + ');" class="btn btn-primary btn-circle"><span class="glyphicon glyphicon-pencil"></span></a>' +
                    '<a href="javascript:" onclick="lfb_removeField(' + field.id + ');" class="btn btn-danger btn-circle"><span class="glyphicon glyphicon-trash"></span></a>' +
                    '</td>');
                jQuery('#lfb_finalStepFields table tbody').append($tr);
               jQuery('#lfb_loader').fadeOut();

            });
          }
        }
    });
}
function lfb_removeField(fieldID) {
    jQuery('#lfb_finalStepFields table tr[data-fieldid="' + fieldID + '"]').slideUp();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_removeField',
            fieldID: fieldID
        }
    });
}
function lfb_loadForm(formID) {
    lfb_currentFormID = formID;
    lfb_showLoader();
    jQuery('#lfb_stepsContainer .lfb_stepBloc,.lfb_loadSteps,.lfb_linkPoint').remove();
    jQuery('#lfb_formFields').find('.switch [data-switch="switch"]').bootstrapSwitch('destroy');
    jQuery('#lfb_formFields').find('.switch > div > :not([data-switch="switch"])').remove();
    jQuery('#lfb_formFields').find('.switch [data-switch="switch"]').unwrap().unwrap();
    jQuery('#lfb_formFields').find('#lfb_itemPricesGrid tbody tr').not('.static').remove();
    lfb_loadFields();
    jQuery('#lfb_btnPreview').attr('href',lfb_data.websiteUrl+'?lfb_action=preview&form='+formID);
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_loadForm',
            formID: formID
        },
        success: function (rep) {

            rep = JSON.parse(rep);
            lfb_currentForm = rep;
            lfb_params = rep.params;
            lfb_steps = rep.steps;
            jQuery('#lfb_formFields').find('input,select,textarea').each(function () {
                if(jQuery(this).is('[data-switch="switch"]')){
                    var value = false;
                    eval('if(rep.form.' + jQuery(this).attr('name') + ' == 1){jQuery(this).attr(\'checked\',\'checked\');} else {jQuery(this).attr(\'checked\',false);}');
                    jQuery(this).wrap('<div class="switch" data-on-label="'+lfb_data.texts['Yes']+'" data-off-label="'+lfb_data.texts['No']+'" />').parent().bootstrapSwitch({onLabel: lfb_data.texts['Yes'],offLabel: lfb_data.texts['No']});
                  }else {
                    eval('jQuery(this).val(rep.form.' + jQuery(this).attr('name') + ');');
                  }
            });
            lfb_initFormsBackend();
            jQuery('#lfb_tabEmail').show();
            tinymce.init({selector:'#email_adminContent'});
            tinymce.activeEditor.setContent(rep.form.email_adminContent);


            jQuery('#lfb_formEmailUser').show();
            tinymce.init({selector:'#email_userContent'});
            tinymce.activeEditor.setContent(rep.form.email_userContent);

              if (!jQuery('#lfb_formFields [name="email_toUser"]').is(':checked') ) {
                  jQuery('#lfb_formEmailUser').hide();
              }
              jQuery('#lfb_tabEmail').attr('style','');
              jQuery('#lfb_tabEmail').prop('style','');

      			jQuery('.colorpick').each(function () {
      			    var $this = jQuery(this);
      			    jQuery(this).colpick({
      			        color: $this.val().substr(1, 7),
      			        onChange: function (hsb, hex, rgb, el, bySetColor) {
      			            jQuery(el).val('#' + hex);
      			        }
      			    });
      			});

            jQuery('#lfb_stepsContainer').css({
                height: lfb_params.previewHeight + 'px'
            });
            jQuery.each(rep.steps, function (index) {
                var step = this;
                step.content = JSON.parse(step.content);
                lfb_addStep(step);
            });
            jQuery.each(rep.links, function (index) {
                var link = this;
                link.originID = jQuery('.lfb_stepBloc[data-stepid="' + link.originID + '"]').attr('id');
                link.destinationID = jQuery('.lfb_stepBloc[data-stepid="' + link.destinationID + '"]').attr('id');
                link.conditions = JSON.parse(link.conditions);
                lfb_links[index] = link;
            });

            tinymce.execCommand('mceRemoveEditor', true, 'email_adminContent');
            tinymce.execCommand('mceRemoveEditor', true, 'email_userContent');
            jQuery("#wp-email_adminContent-wrap").appendTo("#email_adminContent_editor");
            jQuery("#wp-email_userContent-wrap").appendTo("#email_userContent_editor");
            tinymce.execCommand('mceAddEditor', true, 'email_adminContent');
            tinymce.execCommand('mceAddEditor', true, 'email_userContent');
      			tinyMCE.get('email_adminContent').setContent(rep.form.email_adminContent);


            jQuery('#lfb_panelPreview').show();
            jQuery('#lfb_panelFormsList').hide();
            jQuery('#lfb_panelLogs').hide();
            jQuery('#lfb_panelSettings').hide();
           jQuery('#lfb_loader').delay(1000).fadeOut();

          lfb_updateStepsDesign();
            setTimeout(function () {
                lfb_updateStepsDesign();
            }, 250);
        }
    });
}
function lfb_loadLogs(formID) {
  lfb_showLoader();
  jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
          action: 'lfb_loadLogs',
          formID: formID
      },
      success: function(rep){
        jQuery('#lfb_logsTable tbody').html(rep);
        jQuery('#lfb_panelPreview').hide();
        jQuery('#lfb_panelFormsList').hide();
        jQuery('#lfb_panelLogs').show();
        jQuery('#lfb_logsTable tbody [data-toggle="tooltip"]').tooltip();
       jQuery('#lfb_loader').fadeOut();
      }
    });
}
function lfb_loadLog(logID){
  jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
          action: 'lfb_loadLog',
          logID: logID
      },
      success: function(rep){
        jQuery('#lfb_winLog').find('.modal-body').html(rep);
        jQuery('#lfb_winLog').modal('show');
      }
    });
}
function lfb_removeLog(logID){
  lfb_showLoader();
  jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
          action: 'lfb_removeLog',
          logID: logID
      },
      success: function(){
        lfb_loadLogs();
      }
    });
}
function lfb_exportForms(){
    lfb_showLoader();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'lfb_exportForms'
        },
        success: function(rep){
            jQuery('#lfb_loader').fadeOut();
            if (rep == '1'){
                jQuery('#lfb_winExport').modal('show');
            } else {
                alert(lfb_data.texts['errorExport']);
            }
        }
    });

}
function lfb_importForms(){
    lfb_showLoader();
    jQuery('#lfb_winImport').modal('hide');
    var formData = new FormData(jQuery('#lfb_winImportForm')[0]);

    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        xhr: function () {
            var myXhr = jQuery.ajaxSettings.xhr();
            return myXhr;
        },
        success: function (rep) {
            if(rep != '1'){
                jQuery('#lfb_loader').fadeOut();
                alert(lfb_data.texts['errorImport']);
            } else {
                document.location.href = document.location.href;
            }
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
}
