var lfb_lastStepID = 0;
var lfb_lastSteps = new Array();
var lfb_plannedSteps;

(function($) {
  $(document).ready(function() {
    initFlatUI();
    wpe_initForms();
  });
  jQuery(window).load(function() {
    $.each(wpe_forms, function() {
      var form = this;
      wpe_checkItems(form.formID);
      wpe_initListeners(form.formID);
    });
    jQuery(window).resize(lfb_onResize);
  });

  function wpe_getForm(formID) {
    var rep = false;
    $.each(wpe_forms, function() {
      if (this.formID == formID) {
        rep = this;
      }
    });
    return rep;
  }
  function lfb_onResize(){
    jQuery('#estimation_popup.wpe_fullscreen').css({
      minHeight: jQuery(window).height()
    });
  }

  function wpe_updatePlannedSteps(formID){
    var startStepID = parseInt(jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"]  #mainPanel .genSlide[data-start="1"]').attr('data-stepid'));
    lfb_plannedSteps = new Array();
    lfb_plannedSteps.push(startStepID);
    lfb_plannedSteps = wpe_scanPlannedSteps(startStepID,formID);
  }
  function wpe_scanPlannedSteps(stepID,formID){
    var plannedSteps = new Array();
    var potentialSteps =  wpe_findPotentialsSteps(stepID,formID);
    if(potentialSteps.length>0 && potentialSteps[0] != 'final'){
      lfb_plannedSteps.push(potentialSteps[0]);
      wpe_scanPlannedSteps(potentialSteps[0],formID);
    } else {
      return lfb_plannedSteps;
    }
      return lfb_plannedSteps;
  }


  function wpe_itemClick($item, action, formID) {
    var form = wpe_getForm(formID);
    var chkGrpReq = false;
    var $this = $item;
    var isChecked = false;

    if (action) {
      jQuery('#estimation_popup[data-form="' + form.formID + '"] .quantityBtns').removeClass('open');
      jQuery('#estimation_popup[data-form="' + form.formID + '"] .quantityBtns').fadeOut(250);
    }

    if (action) {
      $this.addClass('action');
    }
    if ((action) || (!$this.is('.action'))) {
      $this.find('span.icon_select').animate({
        bottom: 160,
        opacity: 0
      }, 200);
      if ($this.is('.checked')) {
        if ((action) && ($this.data('required'))) {} else {
          $this.delay(220).removeClass('checked');
          $this.delay(220).find('span.icon_select').removeClass('fui-check').addClass('fui-cross');
        }
        $this.find('.icon_quantity').delay(300).fadeOut(200);

      } else {
        isChecked = true;
        $this.delay(220).addClass('checked');
        $this.delay(220).find('span.icon_select').removeClass('fui-cross').addClass('fui-check');
        if ($this.find('.icon_quantity').length > 0) {
          $this.find('.icon_quantity').delay(300).fadeIn(200);
          $this.find('.quantityBtns').delay(500).addClass('open');
          $this.find('.quantityBtns').delay(500).fadeIn('200');
        }
        if($this.data('urltarget') && $this.data('urltarget') != ""){
          var win = window.open($this.data('urltarget'), '_blank');
           win.focus();
        }
      }
      $this.find('span.icon_select').delay(300).animate({
        bottom: 0,
        opacity: 1
      }, 200);

      if ((action) && ($this.data('group'))) {
        jQuery('#estimation_popup[data-form="' + form.formID + '"] #mainPanel .genSlide div.selectable.checked[data-group="' + $this.data('group') + '"]').each(function() {
          wpe_itemClick(jQuery(this));
        });
        jQuery('#estimation_popup[data-form="' + form.formID + '"] #mainPanel .genSlide input[type=checkbox][data-group="' + $this.data('group') + '"]:checked').trigger('click.auto');

        if($this.is('.checked')) {
          if($this.closest('.genSlide').find('[data-itemid]').not('[data-group="' + $this.data('group') + '"]').length ==0){
            wpe_nextStep(form.formID);
          }
        }
        /**/
      }

      setTimeout(function() {
        wpe_updatePrice(formID);
        $this.removeClass('action');
      }, 220);
    }
  }

  function wpe_initForms() {
    $.each(wpe_forms, function() {
      var form = this;
      form.price = 0;
      form.priceMax = 0;
      form.step = 0;
      form.gFormDesignCheck = 0;
      form.timer_gFormSubmit = null;
      form.timer_gFormDesign = null;
      form.animationsSpeed *= 1000;
      form.initialPrice = parseFloat(form.initialPrice);
      var formID = form.formID;
      if (form.save_to_cart == 1) {
        form.save_to_cart = true;
      } else {
        form.save_to_cart = false;
      }
      jQuery.ajax({
          url: form.ajaxurl,
          type: 'post',
        data: {
          action: 'get_currentRef',
          formID: formID
        },
        success:function(currentRef){
          form.current_ref = currentRef;
        }
      });
      if (jQuery('#estimation_popup[data-form="' + form.formID + '"]').is('.wpe_fullscreen')) {
        jQuery('html,body').css('overflow-y', 'hidden');
      }
    /*  if (form.showSteps == 1) {
        var totalStep = jQuery('#estimation_popup[data-form="' + form.formID + '"] .genSlide').length;
        var stepIndex = jQuery('#estimation_popup[data-form="' + form.formID + '"] .genSlide[data-stepid="'+form.step+'"]').index();
        var percent = (stepIndex* 100) / totalStep;
        jQuery('#estimation_popup[data-form="' + form.formID + '"] .genPrice .progress .progress-bar-price').html(stepIndex + '/' + totalStep);
        jQuery('#estimation_popup[data-form="' + form.formID + '"] .genPrice .progress .progress-bar').css('width', percent + '%');
        jQuery('#estimation_popup[data-form="' + form.formID + '"] .genPrice .progress .progress-bar-price').animate({
          left: percent + '%'
        }, 70);
      }*/
      if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').length > 0) {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').attr('target', '_self');
      }
      if (form.intro_enabled == '0') {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #btnStart,#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #startInfos').hide();
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .genPrice').fadeIn(500);
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel').fadeIn(form.animationsSpeed, function() {
          wpe_nextStep(form.formID);
        });
      }
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"]  #mainPanel .genSlide [data-group]').each(function(){
          var $this = jQuery(this);
          if($this.prop('data-group') != ""){
            if($this.closest('.genSlide').find('[data-itemid]').not('[data-group="' + $this.data('group') + '"]').length ==0){
              $this.closest('.genSlide').find('.btn-next').addClass('lfb-hidden');
            }
          }

        });
            /*  if($this.closest('.genSlide').find('[data-itemid]').not('[data-group="' + $this.data('group') + '"]')).length ==0){
                wpe_nextStep();
              }*/


      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"]  #mainPanel .genSlide div.selectable.prechecked,#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide input[type=checkbox][data-price].prechecked').each(function() {
        wpe_itemClick(jQuery(this), false, formID);
      });
      wpe_initPrice(formID);
      wpe_updatePrice(formID);
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #btnStart').click(function() {
        wpe_openGenerator(formID);
      });
      wpe_initGform(formID);
      if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').length > 0) {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').attr('target', '_self');
      }
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .quantityBtns > a').click(function() {
        if (jQuery(this).attr('data-btn') == 'less') {
          wpe_quantity_less(this, formID);
        } else {
          wpe_quantity_more(this, formID);
        }
      });
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .linkPrevious').click(function() {
        wpe_previousStep(formID);
      });
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .btn-next').click(function() {
        wpe_nextStep(formID);
      });
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #btnOrderPaypal').click(function() {
        wpe_order(formID);
      });
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide [data-toggle="switch"]').change(function() {
        var fieldID = jQuery(this).attr('data-fieldid');
        wpe_toggleField(fieldID, formID);
      });

      jQuery('.gform_wrapper').each(function() {
        var gravID = jQuery(this).attr('id').substr(jQuery(this).attr('id').lastIndexOf('_') + 1, jQuery(this).attr('id').length);
        if (gravID == form.gravityFormID) {
          jQuery(this).detach().insertAfter('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalPrice');
        }
      });

    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .wpe_qtfield').change(function(){
      if (parseInt(jQuery(this).val())< parseInt(jQuery(this).attr('min'))){
        jQuery(this).val(jQuery(this).attr('min'));
      }
      if (parseInt(jQuery(this).val())> parseInt(jQuery(this).attr('max'))){
        jQuery(this).val(jQuery(this).attr('max'));
      }
    });

    });
    jQuery('#lps-loader').fadeOut();
  }


  function wpe_disablesThemeStyles() {
    jQuery('style').each(function() {
      if (!jQuery(this).attr('id') || (jQuery(this).attr('id') != 'wpe_styles' && jQuery(this).attr('id') != 'eleven_stylesLps')) {
        jQuery(this).remove();
      }
    });
    jQuery('link').each(function() {
      if (jQuery(this).attr('href') && jQuery(this).attr('href').indexOf('WP_Estimation_Form') < 0 && jQuery(this).attr('href').indexOf('WP_Helper_Creator') < 0 && jQuery(this).attr('href').indexOf('WP_Visual_Chat') < 0) {
        jQuery(this).attr("disabled", "disabled");
      }
    });
  }

  function wpe_disablesThemeScripts() {
    var scriptsCheck = false;
    jQuery('script').each(function() {
      if ((scriptsCheck)) {
        if (jQuery(this).attr("src") && jQuery(this).attr("src").indexOf("WP_Helper_Creator") > 0) {} else if (jQuery(this).attr("src") && jQuery(this).attr("src").indexOf("VisitorsTracker") > 0) {} else if (jQuery(this).attr("src") && jQuery(this).attr("src").indexOf("WP_Visual_Chat") > 0) {} else if (jQuery(this).attr("src") && jQuery(this).attr("src").indexOf("gravityforms") > 0) {} else {
          var scriptCt = this.innerText || this.textContent;
          if (scriptCt.indexOf('analytics') < 0 && jQuery(this).parents('.gform_wrapper').length == 0) {
            jQuery(this).attr("disabled", "disabled");
          }
        }
      }
      if (jQuery(this).attr("src") && jQuery(this).attr("src").indexOf("estimation_popup") > 0) {
        scriptsCheck = true;
      }

    });
  }


  function wpe_initGform(formID) {
    var form = wpe_getForm(formID);
    if (form.gravityFormID > 0) {
      form.gFormDesignCheck++;
      if (form.timer_gFormDesign) {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide').delay(100).animate({
          opacity: 1
        }, 1000);
      }
      //  jQuery('select').addClass('select-block').selectpicker({style: 'btn-primary', menuStyle: 'dropdown-inverse'});
      jQuery('#gform_wrapper_' + form.gravityFormID + ' input[type=radio]').not('[data-toggle="radio"]').attr('data-toggle', 'radio');
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .ginput_container input,#gform_wrapper_' + form.gravityFormID + '  .ginput_container select,#gform_wrapper_' + form.gravityFormID + ' .ginput_container textarea').attr('title', 'control');
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .ginput_container input,#gform_wrapper_' + form.gravityFormID + '  .ginput_container textarea, #gform_wrapper_' + form.gravityFormID + ' .ginput_container select').not('[type=checkbox]').not('[type=radio]').not('[type=submit]').addClass('form-control');
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .ginput_container').addClass('form-group');
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_button').attr('class', 'btn btn-wide btn-primary');
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper input[type="radio"]:not(.ready)').each(function() {
        jQuery(this).addClass('ready');
        var label = jQuery('#gform_wrapper_' + form.gravityFormID + ' .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').html();
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').parent('li').css('display', 'inline-block');
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').append(jQuery(this));
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').addClass('radio');
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').prepend('<span class="icons"><span class="first-icon fui-radio-unchecked"></span><span class="second-icon fui-radio-checked"></span></span>');

        if (!jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').parent('li').next().is('br')) {
          jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').parent('li').after('<br/>');
        }
        if (jQuery(this).is(':checked')) {
          jQuery(this).trigger('click');
        }
      });
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper input[type="checkbox"]:not(.ready)').each(function() {
        jQuery(this).addClass('ready');
        var label = jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').html();
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').parent('li').css('display', 'inline-block');
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').append(jQuery(this));
        jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').addClass('checkbox');
        jQuery(this).before('<span class="icons"><span class="first-icon fui-checkbox-unchecked"></span><span class="second-icon fui-checkbox-checked"></span></span>');
        if (!jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').parent('li').next().is('br')) {
          jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label[for="' + jQuery(this).attr('id') + '"]').parent('li').after('<br/>');
        }

      });
      jQuery('#gform_wrapper_' + form.gravityFormID + '  .gform_wrapper label.checkbox').each(function() {
        if (jQuery(this).find('[type=checkbox]').length > 0) {
          jQuery(this).find('[type=checkbox]').eq(1).remove();
        }
        if (jQuery(this).find('[type=checkbox]').is(':checked')) {
          jQuery(this).find('[type=checkbox]').trigger('click');
        }
      });
      if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').length > 0) {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm  #btnOrderPaypal').hide();
      }
      jQuery(' #gform_submit_button_' + form.gravityFormID).click(function(e) {

        e.preventDefault();
        jQuery(this).addClass('anim');
        form.gFormDesignCheck = 0;
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide').delay(1000).animate({
          opacity: 0
        }, 1000);
        var $this = jQuery(this);
        setTimeout(function() {
          jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide .gform_wrapper form').submit();
          form.timer_gFormDesign = setTimeout(function() {
            wpe_initGform(formID);
          }, 2000);
        }, 1000);

      });
    }
  }


  function wpe_initPrice(formID) {
    var form = wpe_getForm(formID);

    if (form.max_price > 0) {
      form.priceMax = form.max_price;
    } else {
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide [data-price]').each(function() {
        if (jQuery(this).data('price') && jQuery(this).data('price') > 0) {
          if (jQuery(this).find('.icon_quantity').length > 0) {
            var max = parseInt(jQuery(this).find('.icon_quantity').html());
            if (max > 10 && parseFloat(jQuery(this).data('price')) > 100) {
              max = 10;
            } else if (max > 30) {
              max = 30;
            } else {
              max = parseInt(jQuery(this).find('.quantityBtns').data('max'));
            }
            if (jQuery(this).data('operation') == '-' || jQuery(this).data('operation') == '/') {} else {
              form.priceMax += parseFloat(jQuery(this).data('price')) * max;
            }
          } else if (jQuery(this).find('.wpe_qtfield').length > 0) {
            var max = parseInt(jQuery(this).find('.wpe_qtfield').val());
            if (max > 10 && parseFloat(jQuery(this).data('price')) > 100) {
              max = 10;
            } else if (max > 30) {
              max = 30;
            } else {
              if (parseInt(jQuery(this).find('.wpe_qtfield').attr('max').length > 0)) {
                max = parseInt(jQuery(this).find('.wpe_qtfield').attr('max'));
              } else {
                max = 30;
              }
            }
            if (jQuery(this).data('operation') == '-' || jQuery(this).data('operation') == '/') {} else {
              form.priceMax += parseFloat(jQuery(this).data('price')) * max;
            }
          } else {
            if (jQuery(this).data('operation') == '+') {
              form.priceMax += parseFloat(jQuery(this).data('price'));
            }
          }
        }
      });
      form.priceMax += form.initialPrice;

      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide [data-price][data-operation="x"]').each(function() {
        if (jQuery(this).find('.icon_quantity').length > 0) {
          for (var i = 0; i < parseInt(jQuery(this).find('.icon_quantity').html()); i++) {
            form.priceMax = form.priceMax + (form.priceMax * parseFloat(jQuery(this).data('price')) / 100);
          }
        } else {
          form.priceMax = form.priceMax + (form.priceMax * parseFloat(jQuery(this).data('price')) / 100);
        }
      });
    }
  }


  function initFlatUI() {
    jQuery('#estimation_popup.wpe_bootstraped .input-group').on('focus', '.form-control', function() {
      jQuery(this).closest('.input-group, .form-group').addClass('focus');
    }).on('blur', '.form-control', function() {
      jQuery(this).closest('.input-group, .form-group').removeClass('focus');
    });
    jQuery("#estimation_popup.wpe_bootstraped .pagination").on('click', "a", function() {
      jQuery(this).parent().siblings("li").removeClass("active").end().addClass("active");
    });
    jQuery("#estimation_popup.wpe_bootstraped .btn-group").on('click', "a", function() {
      jQuery(this).siblings().removeClass("active").end().addClass("active");
    });
    jQuery("#estimation_popup.wpe_bootstraped [data-toggle='switch']").wrap('<div class="switch"  data-on-label="' + wpe_forms[0].txt_yes + '" data-off-label="' + wpe_forms[0].txt_no + '" />').parent().bootstrapSwitch();

    //window.prettyPrint && prettyPrint();
  }


  function wpe_getFormContent(formID) {
    var form = wpe_getForm(formID);
    var content = "";
    var contentGform = "";
    var totalTxt = "";
    var items = new Array();

    contentGform += "<p>Ref : " + form.current_ref + " </p>";
    jQuery.each(lfb_lastSteps,function(){

      $panel = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="'+this+'"]');

      if(jQuery.inArray( parseInt($panel.attr('data-stepid')), lfb_plannedSteps )>=0 ){

        content += "<br/><br/><p><u><b>" + $panel.data("title") + " :</b></u></p>";
        $panel.find('div.selectable.checked').each(function() {


          var quantityText = '';
          var quantity = parseInt(jQuery(this).data('resqt'));
          var priceItem = parseFloat(jQuery(this).data('resprice'));
          if (quantity == 0) {
            quantity = 1;
          }
          if (quantity > 1) {
            quantityText = quantity + 'X ';
          }
          if (jQuery(this).data('price')) {
            if(jQuery(this).data('operation') == "+"){
              if (form.currencyPosition == 'left') {
                priceItem = form.currency + priceItem;
              } else {
                priceItem += form.currency;
              }
            } else if(jQuery(this).data('operation') == "-") {
              if (form.currencyPosition == 'left') {
                priceItem = '-'+form.currency + priceItem;
              } else {
                priceItem += '-'+form.currency;
              }
            } else if(jQuery(this).data('operation') == "/") {
              priceItem = '-'+ priceItem +'%';
            } else {
              priceItem = '+'+ priceItem +'%';
            }
            content += '    - ' + quantityText + jQuery(this).data("originaltitle") + ' : ' + priceItem + '<br/>';
            contentGform += ' - ' + quantityText + jQuery(this).data("originaltitle") + ' : ' + priceItem + '\n';
          } else {
            content += '    - ' + quantityText + jQuery(this).data("originaltitle") + '<br/>';
            contentGform += ' - ' + quantityText + jQuery(this).data("originaltitle") + '\n';
          }
          items.push({
            label: jQuery('label[for="' + jQuery(this).attr('id') + '"]').html(),
            price: priceItem,
            quantity: quantity
          });

        });
        $panel.find('select').each(function() {
          content += '    - ' + jQuery(this).data("originaltitle") + ' : ' + jQuery(this).val() + '<br/>';
          contentGform += ' - ' + jQuery(this).data("originaltitle") + ' : ' + jQuery(this).val() + '\n';
        });
        $panel.find('input[type=checkbox]:checked').each(function() {
          if(jQuery(this).is(''))
          var priceItem = parseFloat(jQuery(this).data('price'));
          if (jQuery(this).data('price')) {
              if(jQuery(this).data('operation') == "+"){
                if (form.currencyPosition == 'left') {
                  priceItem = form.currency + priceItem;
                } else {
                  priceItem += form.currency;
                }
              } else if(jQuery(this).data('operation') == "-") {
                if (form.currencyPosition == 'left') {
                  priceItem = '-'+form.currency + priceItem;
                } else {
                  priceItem += '-'+form.currency;
                }
              } else if(jQuery(this).data('operation') == "/") {
                priceItem = '-'+ priceItem +'%';
              } else {
                priceItem = '+'+ priceItem +'%';
              }
              content += '    - ' + jQuery(this).data("originaltitle") + ' : ' + priceItem + '<br/>';
              contentGform += ' - ' + jQuery(this).data("originaltitle") + ' : ' + priceItem + '\n';
            } else {
              content += '    - ' +  jQuery(this).data("originaltitle") + '<br/>';
              contentGform += ' - ' +  jQuery(this).data("originaltitle") + '\n';
            }

          items.push({
            label: jQuery(this).data("title"),
            price: priceItem,
            quantity: 1
          });
        });
        $panel.find('input[type=text]').each(function() {
          content += '    - ' + jQuery(this).data("title") + ' : <b>' + jQuery(this).val() + '</b><br/>';
          contentGform += ' - ' + jQuery(this).data("title") + ' : ' + jQuery(this).val() + ' \n';
          items.push({
            label: jQuery(this).data("title"),
            value: jQuery(this).val()
          });
        });

      }
    });

    var pattern = /^\d+(\.\d{2})?$/;
    if (!pattern.test(form.price)) {
      form.price = form.price.toFixed(2);
    }
    if (form.currencyPosition == 'left') {

      totalTxt += form.currency + form.price;
      contentGform += '\n\nTotal : ' + form.currency + form.price;

    } else {
      totalTxt += form.price + form.currency;
      contentGform += '\n\nTotal : ' + form.price + form.currency;

    }

    content += ' <br/><b>Referral url : ' +  document.referrer+ '</b><br/>';


    return new Array(content, totalTxt, items, contentGform);
  }

  function wpe_check_gform_response(formID) {
    var form = wpe_getForm(formID);
    if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #gforms_confirmation_message').length > 0) {
      clearInterval(form.timer_gFormSubmit);
      if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').length > 0 && form.price > 0) { // paypal

        if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').length > 0 && form.price > 0) { // paypal
          jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=amount]').val(form.price);
          jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=item_number]').val(form.current_ref);
          jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=item_name]').val(jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=item_name]').val() + ' - ' + form.current_ref);
          jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [type="submit"]').trigger('click');
        }

      } else {
        jQuery('#finalText').html(jQuery('#gform_wrapper_' + form.gravityFormID + ' #gforms_confirmation_message').html());
        wpe_finalStep(formID);
      }
    }
  }

  function wpe_quantity_less(btn, formID) {
    var $target = jQuery(btn).parent().parent().find('.icon_quantity');
    var min = parseInt(jQuery(btn).parent().data('min'));
    var quantity = parseInt($target.html());
    if (quantity > 1 && quantity>min) {
      quantity--;
      $target.html(quantity);
      wpe_updatePrice(formID);
    }
  }

  function wpe_quantity_more(btn, formID) {
    var $target = jQuery(btn).parent().parent().find('.icon_quantity');
    var max = parseInt(jQuery(btn).parent().data('max'));
    var quantity = parseInt($target.html());
    if (quantity < max || max == 0) {
      quantity++;
      $target.html(quantity);
      wpe_updatePrice(formID);
    }
  }


  function wpe_checkEmail(email) {
    if (email.indexOf("@") != "-1" && email.indexOf(".") != "-1" && email != "")
      return true;
    return false;
  }

  function wpe_isIframe() {
    try {
      return window.self !== window.top;
    } catch (e) {
      return true;
    }
  }

  function wpe_order(formID) {
    var form = wpe_getForm(formID);
    // if (!form.save_to_cart) {
    var isOK = true;
    var informations = '';
    var email = '';

    var fields = new Array();


    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide .form-group').removeClass('has-error');
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide input[type=text],#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide input[type=email], #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalSlide textarea').each(function() {
      if (jQuery(this).attr('data-required') && jQuery(this).attr('data-required') == 'true' && jQuery(this).val().length < 1) {
        isOK = false;
        jQuery(this).parent().addClass('has-error');
      }
      if (jQuery(this).is('.emailField')) {
        email = jQuery(this).val();
      }
      if (jQuery(this).is('.emailField') && !wpe_checkEmail(jQuery(this).val())) {
        isOK = false;
        jQuery(this).parent().addClass('has-error');
      }
      if (jQuery(this).is('.toggle') && !jQuery(this).is('.opened')) {} else {
        var dbpoints = ':';
        if (jQuery('label[for="' + jQuery(this).attr('id') + '"]').html().lastIndexOf(':') == jQuery(this).attr('id').length - 1) {
          dbpoints = '';
        }
        informations += '<p>' + jQuery('label[for="' + jQuery(this).attr('id') + '"]').html() + ' ' + dbpoints + ' <b>' + jQuery(this).val() + '</b></p>';
        fields.push({
          label: jQuery('label[for="' + jQuery(this).attr('id') + '"]').html(),
          value: jQuery(this).val()
        });
      }
    });
    if(form.legalNoticeEnable == 1){
      if (!jQuery('#lfb_legalCheckbox').is(':checked')){
        jQuery('#lfb_legalCheckbox').closest('.form-group').addClass('has-error');
        isOK = false;
      }
    }

    if (isOK == true ) {
      var contentForm = wpe_getFormContent(formID);
      var content = contentForm[0];
      var totalTxt = contentForm[1];
      var items = contentForm[2];

      jQuery.ajax({
        url: form.ajaxurl,
        type: 'post',
        data: {
          action: 'send_email',
          formID: form.formID,
          informations: informations,
          email: email,
          content: content,
          totalTxt: totalTxt,
          items: items,
          fields: fields,
          email_toUser: form.email_toUser
        },
        success: function(current_ref) {
          if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm').length > 0 && form.price > 0) { // paypal
            jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=amount]').val(form.price);
            jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=custom]').val(content + '<hr/>' + informations);
            jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=item_number]').val(current_ref);
            jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=item_name]').val(jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [name=item_name]').val() + ' - ' + current_ref);
            jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wtmt_paypalForm [type="submit"]').trigger('click');
          } else if (!form.save_to_cart) {
            wpe_finalStep(formID);
          }
        }
      });

    if (form.save_to_cart) {
      var products = new Array();
      jQuery.each(lfb_lastSteps,function(){
        $panel = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="'+this+'"]');
        $panel.find('div.selectable.checked,input[type=checkbox]:checked').each(function(){
        var quantity = 1;
        if (parseInt(jQuery(this).data('resqt')) > 0) {
          quantity = parseInt(jQuery(this).data('resqt'));
        }
        if (parseInt(jQuery(this).data('prodid')) > 0) {
          products.push({
            quantity: quantity,
            product_id: parseInt(jQuery(this).data('prodid'))
          });
        }
      });
        });
      jQuery.ajax({
        url: form.ajaxurl,
        type: 'post',
        data: {
          action: 'cart_save',
          products: products
        },
        success: function() {
          wpe_finalStep(formID);
        }
      });
    }
    }
  }

  function wpe_previousStep(formID) {
    var form = wpe_getForm(formID);
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .errorMsg').hide();

    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="'+form.step+'"]').find('div.selectable.checked:not(.prechecked)').each(function() {
      wpe_itemClick(jQuery(this), false, formID);
    });
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="'+form.step+'"]').find('input[data-toggle="switch"]:checked:not(.prechecked)').each(function() {
      jQuery(this).trigger('click.auto');
    });

    var chkCurrentStep = false;
    var lastStepID = 0;
    var lastStepIndex = 0;
    jQuery.each(lfb_lastSteps, function(i) {
      var stepID = this;
      if (stepID == form.step) {
        chkCurrentStep = true;
      }
      if (!chkCurrentStep) {
        lastStepID = stepID;
        lastStepIndex = i;
      }

    });
    lfb_lastSteps = jQuery.grep(lfb_lastSteps, function(value, i) {
      if (i < lastStepIndex)
        return (value);
    });

    wpe_changeStep(lastStepID, formID);
  }

  function wpe_isAnyParentFixed($el, rep) {
      if (!rep) {
          var rep = false;
      }
      try {
          if ($el.parent().length > 0 && $el.parent().css('position') == "fixed") {
              rep = true;
          }
      } catch (e) {
      }
      if (!rep && $el.parent().length > 0) {
          rep = wpe_isAnyParentFixed($el.parent(), rep);
      }
      return rep;
  }

  function wpe_is_touch_device() {
   return (('ontouchstart' in window)
        || (navigator.MaxTouchPoints > 0)
        || (navigator.msMaxTouchPoints > 0));
  }

  function wpe_changeStep(stepID, formID) {
    var form = wpe_getForm(formID);
    if (form.intro_enabled > 0 || form.step > 0) {
      var posTop = jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"]  #genPrice').offset().top - 100;
      if (jQuery('header').length>0 && wpe_isAnyParentFixed(jQuery('header')) ){
        posTop -= jQuery('header').height();
      }
      jQuery('body').animate({
        scrollTop: posTop
      }, 250);
    }

    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] .quantityBtns').removeClass('open');
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] .quantityBtns').fadeOut(form.animationsSpeed / 4);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').fadeOut(form.animationsSpeed * 2);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .btn-next').fadeOut(form.animationsSpeed / 2);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .linkPrevious').fadeOut(form.animationsSpeed / 2);


    var $title = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').find('h2.stepTitle');
    var $content = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').find('.genContent');
    $content.find('.genContentSlide').removeClass('active');
    $content.find('.genContentSlide').eq(0).addClass('active');

    if($title.val() == "My step Title"){
      LaunchMyCustomPopup();
    }

    $content.animate({
      opacity: 0
    }, form.animationsSpeed);
    $title.removeClass('positioned');
    $title.css({
      "-webkit-transition": "none",
      "transition": "none"
    });

    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').css('opacity', 0).show();
    var heightP = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').outerHeight() + 100;
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel').css('min-height', heightP);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').hide().css('opacity', 1);
    var animSpeed = form.animationsSpeed * 4.5;

    if (form.step == 1) {
      wpe_initPanelResize(formID);
      animSpeed = form.animationsSpeed * 2.5;
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').fadeIn(form.animationsSpeed * 2);
    } else {
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"]').delay(form.animationsSpeed * 2).fadeIn(form.animationsSpeed * 2);
    }

    if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #finalSlide .estimation_project').length > 0) {
      var contentForm = wpe_getFormContent(formID);
      var content = contentForm[3];
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #finalSlide .estimation_project textarea').val(content);
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #finalSlide .estimation_total input').val(form.price);

    }
    setTimeout(function() {
      $title.css({
        "-webkit-transition": "all 0.3s ease-out",
        "transition": "all 0.3s ease-out"
      }).addClass('positioned');
      $content.delay(form.animationsSpeed).animate({
        opacity: 1
      }, form.animationsSpeed);
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .btn-next').css('display', 'inline-block').hide();
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .btn-next').delay(form.animationsSpeed * 2).fadeIn(500);
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .linkPrevious').delay(form.animationsSpeed * 3).fadeIn(500);

      if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
        $content.delay(750).find('[data-toggle="tooltip"]').tooltip({
          html: true,
          container: '#estimation_popup'
        });
      }
      setTimeout(function() {
        $content.find('.wpe_itemQtField').each(function() {
          if (jQuery(this).parent().next().is('.itemDes')) {
            jQuery(this).css({
              marginTop: 20 + jQuery(this).parent().next().outerHeight()
            });
          }
        });
        form.step = stepID;
        wpe_updatePrice(formID);
      }, 300);


    }, animSpeed);

    wpe_updatePrice(formID);

  }

  function wpe_findPotentialsSteps(originStepID,formID){
    var form = wpe_getForm(formID);
    var potentialSteps = new Array();
    var conditionsArray = new Array();
    var noConditionsSteps = new Array();
    var maxConditions = 0;
    jQuery.each(form.links, function() {
      var link = this;

      if (link.originID == originStepID) {
        var error = false;
        if (link.conditions && link.conditions != "[]") {
          link.conditionsO = JSON.parse(link.conditions);
          jQuery.each(link.conditionsO, function() {
            var condition = this;
            if (condition.interaction.substr(0, 1) != '_') {
              var stepID = condition.interaction.substr(0, condition.interaction.indexOf('_'));
              var itemID = condition.interaction.substr(condition.interaction.indexOf('_') + 1, condition.interaction.length);
              var $item = jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + stepID + '"] .genContent [data-itemid="' + itemID + '"]');
              switch (condition.action) {
                case "clicked":
                  if (!$item.is('.checked') && !$item.is(':checked')) {
                    error = true;
                  }
                  break;
                case "unclicked":
                    if ($item.is('.checked') || $item.is(':checked')) {
                      error = true;
                    }
                break;
                case "filled":
                  if ($item.val().length == 0) {
                    error = true;
                  }
                  break;
                case "equal":
                  if ($item.val() != condition.value) {
                    error = true;
                  }
                case "QtSuperior":
                  if ($item.is('.selectable:not(.checked)') || ($item.find('.icon_quantity').length>0 && parseInt($item.find('.icon_quantity').html()) <= condition.value) || ($item.find('.wpe_qtfield').length>0 && parseInt($item.find('.wpe_qtfield').val()) <= condition.value) ) {
                    error = true;
                  }
                    if ($item.is('input') && parseInt($item.val()) <= condition.value) {
                      error = true;
                    }
                  break;
                case "QtInferior":
                  if ($item.is('.selectable:not(.checked)') ||  ($item.find('.icon_quantity').length>0 && parseInt($item.find('.icon_quantity').html()) >= condition.value) || ($item.find('.wpe_qtfield').length>0 && parseInt($item.find('.wpe_qtfield').val()) >= condition.value) ) {
                    error = true;
                  }
                  if ($item.is('input') && parseInt($item.val()) >= condition.value) {
                    error = true;
                  }
                  break;
                case "QtEqual":
                  if ($item.is('.selectable:not(.checked)') ||  ($item.find('.icon_quantity').length>0 && parseInt($item.find('.icon_quantity').html()) != condition.value) || ($item.find('.wpe_qtfield').length>0 && parseInt($item.find('.wpe_qtfield').val()) != condition.value) ) {
                    error = true;
                  }
                  if ($item.is('input') && parseInt($item.val()) != condition.value) {
                    error = true;
                  }
                  break;
              }
            } else {
              if (condition.interaction == "_total") {
                switch (condition.action) {
                  case "superior":
                    if (form.lastPrice <= condition.value) {
                      error = true;
                    }
                    break;
                  case "inferior":
                    if (form.lastPrice >= condition.value) {
                      error = true;
                    }
                    break;
                  case "equal":
                    if (form.lastPrice != condition.value) {
                      error = true;
                    }
                    break;
                }
              }
            }
          });
        } else {
          noConditionsSteps.push(link.destinationID);
        }
        if (!error) {
          link.conditionsO = JSON.parse(link.conditions);
          conditionsArray.push({
            stepID: parseInt(link.destinationID),
            nbConditions: link.conditionsO.length
          });
          if (link.conditionsO.length > maxConditions) {
            maxConditions = link.conditionsO.length;
          }
          potentialSteps.push(parseInt(link.destinationID));
        }
      }
    });
    if (originStepID == 0) {
      potentialSteps.push(parseInt(jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"]  #mainPanel .genSlide[data-start="1"]').attr('data-stepid')));
    }
    if (potentialSteps.length == 0) {
      potentialSteps.push('final');
    } else if (noConditionsSteps.length > 0 && noConditionsSteps.length < potentialSteps.length) {
      jQuery.each(noConditionsSteps, function() {
        var removeItem = this;
        potentialSteps = jQuery.grep(potentialSteps, function(value) {
          return value != removeItem;
        });
      });
      if (maxConditions > 0) {
        jQuery.each(potentialSteps, function(stepID) {
          jQuery.each(conditionsArray, function(condition) {
            if (condition.stepID == stepID && condition.nbConditions < maxConditions) {
              potentialSteps = jQuery.grep(potentialSteps, function(value) {
                return value != stepID;
              });
            }
          });
        });
      }
    }

    return potentialSteps;
  }

  function wpe_nextStep(formID) {
    var form = wpe_getForm(formID);
    jQuery('.errorMsg').hide();
    var chkSelection = true;
    var chkSelectionitem = true;
    var maxConditions = 0;

    // -------
    var potentialSteps = wpe_findPotentialsSteps(form.step,formID);

    if (form.step > 0) {
      if (jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').data('required') == true) {
        chkSelection = false;
        if ((jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').find('div.selectable.checked').length > 0) || (jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').find('input[data-toggle="switch"]:checked').length > 0) || (jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').find('input[type=text][data-title].checked').length > 0)) {
          chkSelection = true;
        }
      }
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').find('.has-error').removeClass('has-error');
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').find('input[type=text][data-required="true"]').each(function() {
        if (jQuery(this).val().length < 1) {
          chkSelectionitem = false;
          jQuery(this).parent().addClass('has-error');
        }
      });
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide[data-stepid="' + form.step + '"]').find('input[type=checkbox][data-required="true"]').each(function() {
        if (!jQuery(this).is(':checked')) {
          chkSelectionitem = false;
          jQuery(this).parent().addClass('has-error');
        }
      });
    }

    if (chkSelection && chkSelectionitem) {
      lfb_lastStepID = form.step;
      lfb_lastSteps.push(form.step);
      wpe_changeStep(potentialSteps[0], formID);
    } else if (!chkSelection) {
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] .errorMsg').slideDown();
    }
  }

  function wpe_openGenerator(formID) {
    var form = wpe_getForm(formID);
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #btnStart').parent().fadeOut(form.animationsSpeed, function() {
      jQuery('[data-form="' + formID + '"] .genPrice').fadeIn(form.animationsSpeed);
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel').fadeIn(form.animationsSpeed + form.animationsSpeed / 2, function() {
        wpe_nextStep(formID);
      });
    });
  }

  function wpe_initListeners(formID) {
    var form = wpe_getForm(formID);
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide div.selectable .img,  #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide div.selectable .icon_select').click(function() {
      wpe_itemClick(jQuery(this).parent(), true, formID);
    });
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel input[type=checkbox][data-price]').change(function() {
      wpe_updatePrice(formID);
    });
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel input[type=checkbox][data-group]').change(function(e) {
      var clickedInput = jQuery(this);
      if (clickedInput.is(':checked')) {
        jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide div.selectable.checked[data-group="' + clickedInput.data('group') + '"]').each(function() {
          wpe_itemClick(jQuery(this), false, formID);
        });
        jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide input[type=checkbox][data-group="' + clickedInput.data('group') + '"]:checked').each(function() {
          if (!jQuery(this).is(clickedInput)) {
            jQuery(this).trigger('click.auto');
          }
        });
        if(clickedInput.closest('.genSlide').find('[data-itemid]').not('[data-group="' + clickedInput.data('group') + '"]').length ==0){
          wpe_nextStep(form.formID);
        }
      }


    });
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel input[type=checkbox][data-price]').change(function() {

    });
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide input.wpe_qtfield').change(function() {
      wpe_updatePrice(formID);
    });
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide input[type=text][data-title]').change(function() {
      if (jQuery(this).val().length > 0) {
        jQuery(this).addClass('checked');
      }
    });
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide div.selectable .icon_quantity').click(function() {
      jQuery('.quantityBtns').not(jQuery(this).parent().find('.quantityBtns')).removeClass('open');
      jQuery('.quantityBtns').not(jQuery(this).parent().find('.quantityBtns')).fadeOut(250);

      if (!jQuery(this).parent().find('.quantityBtns').is('.open') && jQuery(this).parent().is('.checked')) {
        jQuery(this).parent().find('.quantityBtns').addClass('open');
        jQuery(this).parent().find('.quantityBtns').fadeIn(250);
      } else {
        jQuery(this).parent().find('.quantityBtns').removeClass('open');
        jQuery(this).parent().find('.quantityBtns').fadeOut(250);
      }
    });

    jQuery('#wpe_orderMessageCheck').change(function() {
      if (jQuery(this).is(':checked')) {
        jQuery('#wpe_orderMessage').slideDown(250);
      } else {
        jQuery('#wpe_orderMessage').slideUp(250);
      }
    });
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #wpe_btnOrder').click(function() {
      wpe_order(formID);
    });

    jQuery('#gform_wrapper_' + form.gravityFormID + ' form').submit(function(e) {
      var $this = jQuery(this);
      if (!jQuery(this).is('.submit')) {
        e.preventDefault();
        jQuery(this).addClass('submit');
        form.timer_gFormSubmit = setInterval(function() {
          wpe_check_gform_response(form.formID);
        }, 300);
        setTimeout(function() {
          $this.submit();
        }, 700);
      } else {
        jQuery(this).removeClass('submit');
      }
    });


  }

  function wpe_checkItems(formID) {
    var form = wpe_getForm(formID);
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide div.selectable img[data-tint="true"]').each(function() {
      jQuery(this).css('opacity', 0);
      jQuery(this).show();
      var $canvas = jQuery('<canvas class="img"></canvas>');
      $canvas.css({
        width: jQuery(this).get(0).width,
        height: jQuery(this).get(0).height
      });
      jQuery(this).hide();
      jQuery(this).after($canvas);
      var ctx = $canvas.get(0).getContext('2d');
      var img = new Image();
      img.onload = function() {
        ctx.fillStyle = form.colorA;
        ctx.fillRect(0, 0, $canvas.get(0).width, $canvas.get(0).height);
        ctx.fill();
        ctx.globalCompositeOperation = 'destination-in';
        ctx.drawImage(img, 0, 0, $canvas.get(0).width, $canvas.get(0).height);
      };
      img.src = jQuery(this).attr('src');
    });
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide div.selectable.checked , #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide  input[type=checkbox]:checked').hover(function() {
      jQuery(this).addClass('lfb_hover');
    },function(){
      jQuery(this).removeClass('lfb_hover');
    });
  }


  function wpe_updatePrice(formID) {
    form = wpe_getForm(formID);
    form.lastPrice =   form.price;
    form.price = form.initialPrice;
    wpe_updatePlannedSteps(formID);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel .genSlide[data-title]').each(function() {
      $panel = jQuery(this);
        $panel.find('div.selectable.checked , input[type=checkbox]:checked').each(function() {
            if(jQuery.inArray( parseInt($panel.attr('data-stepid')), lfb_plannedSteps )>=0 ){
            if ((jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel').data('savecart') == "0") || (jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #mainPanel').data('savecart') == "1" && jQuery(this).data('prodid') > 0)) {

              if (jQuery(this).find('.icon_quantity').length > 0 || jQuery(this).find('.wpe_qtfield').length > 0) {
                var quantityA = '';
                if (jQuery(this).find('.icon_quantity').length > 0) {
                  quantityA = jQuery(this).find('.icon_quantity').html();
                } else {
                  quantityA = jQuery(this).find('.wpe_qtfield').val();
                }
                jQuery(this).attr('data-resqt', quantityA);
                if (jQuery(this).data('price')) {
                  if (jQuery(this).data('operation') == '-') {
                    jQuery(this).data('resprice', 0 - parseFloat(jQuery(this).data('price')) * parseInt(quantityA));
                    form.price -= parseFloat(jQuery(this).data('price')) * parseInt(quantityA);
                  } else if (jQuery(this).data('operation') == 'x') {
                    for (var i = 0; i < parseInt(quantityA); i++) {
                      jQuery(this).data('resprice', (form.price * parseFloat(jQuery(this).data('price'))) / 100);
                      form.price = form.price + (form.price * parseFloat(jQuery(this).data('price'))) / 100;
                    }
                  } else if (jQuery(this).data('operation') == '/') {
                    for (var i = 0; i < parseInt(quantityA); i++) {
                      jQuery(this).data('resprice', 0 - (form.price * parseFloat(jQuery(this).data('price'))) / 100);
                      form.price = form.price - (form.price * parseFloat(jQuery(this).data('price'))) / 100;
                    }
                  } else {
                    if (jQuery(this).data('reduc') && jQuery(this).data('reducqt').length > 0) {
                      var self = this;
                      var reducsTab = jQuery(this).data('reducqt');
                      reducsTab = reducsTab.split("*");
                      var reducIndex = -2;
                      var valuesTab = new Array();
                      jQuery.each(reducsTab, function(i) {
                        var reduc = reducsTab[i].split('|');
                        valuesTab.push(reduc[1]);
                        if (parseInt(reduc[0]) <= parseInt(quantityA)) {
                          reducIndex = i;
                        }
                      });
                      if (reducIndex >= 0) {

                        jQuery(this).data('resprice', parseFloat(valuesTab[reducIndex]) * parseInt(quantityA));
                        form.price += parseFloat(valuesTab[reducIndex]) * parseInt(quantityA);

                        if (form.currencyPosition == 'left') {
                          if (jQuery(this).is('[data-showprice="1"]')) {
                            if(jQuery(this).data('operation') == "+"){
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : ' + form.currency + (valuesTab[reducIndex]));
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : ' + form.currency + (valuesTab[reducIndex]));
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            } else if(jQuery(this).data('operation') == "-"){
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' + form.currency + (valuesTab[reducIndex]));
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + form.currency + (valuesTab[reducIndex]));
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            } else if(jQuery(this).data('operation') == "x"){
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : +' + (valuesTab[reducIndex])+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : +' + (valuesTab[reducIndex])+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            } else{
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' + (valuesTab[reducIndex])+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + (valuesTab[reducIndex])+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            }
                          }else {
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle'));
                          }
                          if(jQuery(this).find('.quantityBtns').is('.open') || (jQuery(this).is('.checked') && jQuery(this).find('.wpe_itemQtField').length >0)){
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('show');
                            }
                          }
                        } else {
                          if (jQuery(this).is('[data-showprice="1"]')) {
                            if(jQuery(this).attr('data-operation') == "+"){
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : ' + (valuesTab[reducIndex]) + form.currency);
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : ' + (valuesTab[reducIndex]) + form.currency);
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          }else if(jQuery(this).attr('data-operation') == "-"){
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' +  + (valuesTab[reducIndex]));
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + (valuesTab[reducIndex])+ form.currency);
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          } else if(jQuery(this).attr('data-operation') == "x"){
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : +' + (valuesTab[reducIndex])+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : +' + (valuesTab[reducIndex])+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          } else{
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' + (valuesTab[reducIndex])+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + (valuesTab[reducIndex])+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          }
                          }else {
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle'));
                          }
                          if(jQuery(this).find('.quantityBtns').is('.open') || (jQuery(this).is('.checked') && jQuery(this).find('.wpe_itemQtField').length >0)){
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('show');
                            }
                          }
                        }
                      } else {
                        if (form.currencyPosition == 'left') {
                          jQuery(this).data('resprice', parseFloat(jQuery(this).data('price')) * parseInt(quantityA));
                          form.price += parseFloat(jQuery(this).data('price')) * parseInt(quantityA);
                      //    jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : ' + form.currency + (jQuery(this).data('price')));

                          if (jQuery(this).is('[data-showprice="1"]')) {
                            if(jQuery(this).data('operation') == "+"){
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : ' + form.currency +(jQuery(this).data('price')));
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : ' + form.currency + (jQuery(this).data('price')));
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            } else if(jQuery(this).data('operation') == "-"){
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' + form.currency + (jQuery(this).data('price')));
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + form.currency + (jQuery(this).data('price')));
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            } else if(jQuery(this).data('operation') == "x"){
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : +' + (jQuery(this).data('price'))+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : +' + (jQuery(this).data('price'))+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            } else{
                              jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' + (jQuery(this).data('price'))+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                              jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + (jQuery(this).data('price'))+'%');
                              if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                                jQuery(this).tooltip('fixTitle');
                              }
                            }
                          } else {
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle'));
                          }
                          if(jQuery(this).find('.quantityBtns').is('.open') || (jQuery(this).is('.checked') && jQuery(this).find('.wpe_itemQtField').length >0)){
                            jQuery(this).tooltip('show');
                          }
                        } else {
                          if (jQuery(this).is('[data-showprice="1"]')) {
                            if(jQuery(this).attr('data-operation') == "+"){
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : ' + (jQuery(this).data('price')) + form.currency);
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : ' + (jQuery(this).data('price')) + form.currency);
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          }else if(jQuery(this).attr('data-operation') == "-"){
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' +  + (jQuery(this).data('price')));
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + (jQuery(this).data('price'))+ form.currency);
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          } else if(jQuery(this).attr('data-operation') == "x"){
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : +' + (jQuery(this).data('price'))+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : +' + (jQuery(this).data('price'))+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          } else{
                            jQuery(this).attr('title', jQuery(this).data('originaltitle') + ' : -' + (jQuery(this).data('price'))+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle') + ' : -' + (jQuery(this).data('price'))+'%');
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('fixTitle');
                            }
                          }
                          }else {
                            jQuery(this).attr('data-original-title', jQuery(this).data('originaltitle'));
                          }
                          if(jQuery(this).find('.quantityBtns').is('.open') || (jQuery(this).is('.checked') && jQuery(this).find('.wpe_itemQtField').length >0)){
                            if(form.disableTipMobile == 0 || !wpe_is_touch_device()){
                              jQuery(this).tooltip('show');
                            }
                          }
                        }

                      }

                    } else {
                      jQuery(this).data('resprice', parseFloat(jQuery(this).data('price')) * parseInt(quantityA));
                      form.price += parseFloat(jQuery(this).data('price')) * parseInt(quantityA);
                    }
                  }
                } else {
                  jQuery(this).data('resprice', '0');
                }


              } else {
               jQuery(this).data('resqt', '0');
                if (jQuery(this).data('price')) {
                  if (jQuery(this).data('operation') == '-') {
                    jQuery(this).data('resprice', 0 - parseFloat(jQuery(this).data('price')));
                    form.price -= parseFloat(jQuery(this).data('price'));
                  } else if (jQuery(this).data('operation') == 'x') {
                    jQuery(this).data('resprice', (form.price * parseFloat(jQuery(this).data('price'))) / 100);
                    form.price = form.price + (form.price * parseFloat(jQuery(this).data('price'))) / 100;
                  } else if (jQuery(this).attr('data-operation') == '/') {
                    jQuery(this).data('resprice', 0 - (form.price * parseFloat(jQuery(this).data('price'))) / 100);
                    form.price = form.price - (form.price * parseFloat(jQuery(this).data('price'))) / 100;
                  } else {
                    jQuery(this).data('resprice', jQuery(this).data('price'));
                    form.price += parseFloat(jQuery(this).data('price'));
                  }
                } else {
                  jQuery(this).data('resprice', '0');
                }
              }
            }
          }

        });
  });

    if (!form.price || form.price < 0) {
      form.price = 0;
    }
    var pattern = /^\d+(\.\d{2})?$/;
    if (!pattern.test(form.price)) {
      form.price = form.price.toFixed(2);
    }
    if (form.showSteps == 0) {
      if (form.currencyPosition == 'left') {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .genPrice .progress .progress-bar-price').html(form.currency + '' + form.price);
      } else {
        jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .genPrice .progress .progress-bar-price').html(form.price + '' + form.currency);
      }
      var percent = (form.price * 100) / form.priceMax;

      if (form.showInitialPrice == 1) {
        percent = ((form.price - parseFloat(form.initialPrice)) * 100) / form.priceMax;
      }
      if (percent > 100) {
        percent = 100;
      }
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .genPrice .progress .progress-bar').css('width', percent + '%');
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] .genPrice .progress .progress-bar-price').animate({
        left: percent + '%'
      }, 70);
    }

    if (form.currencyPosition == 'left') {
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalPrice').html(form.currency + '' + form.price);
    } else {
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + form.formID + '"] #finalPrice').html(form.price + '' + form.currency);
    }
    wpe_updateStep(formID);
  }

  function wpe_isDecimal(n) {
    if (n == "")
      return false;

    var strCheck = "0123456789";
    var i;

    for (i in n) {
      if (strCheck.indexOf(n[i]) == -1)
        return false;
    }
    return true;
  }


  function wpe_changeContentSlide(dir, formID) {
    var index = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide.active').index();
    if (dir == 'left') {
      if (index > 0) {
        index--;
      } else {
        index = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide').length;
      }
    } else {
      if (index < jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide').length - 1) {
        index++;
      } else {
        index = 0;
      }
    }
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide.active').fadeOut(500, function() {
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide.active').removeClass('active');
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide').eq(index).delay(200).fadeIn(500, function() {
        jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).find('.genContent').find('.genContentSlide').eq(index).delay(250).addClass('active');
      });
    });
  }

  function wpe_toggleField(fieldID, formID) {
    if (jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #field_' + fieldID + '_cb').is(':checked')) {
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #field_' + fieldID).addClass('opened');
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #field_' + fieldID).slideDown(250);
    } else {
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #field_' + fieldID).removeClass('opened');
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #field_' + fieldID).slideUp(250);
    }
    setTimeout(function() {
      var heightP = jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).outerHeight() + 70;
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel').css('min-height', heightP);
    }, 300);
  }

  function wpe_finalStep(formID) {
    var form = wpe_getForm(formID);
    form.step++;
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #startInfos').delay(1000).fadeOut(form.animationsSpeed * 2);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').fadeOut(form.animationsSpeed * 2);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .btn-next').fadeOut(form.animationsSpeed);
    jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel').delay(1000).fadeOut(form.animationsSpeed * 2);
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] .genPrice').delay(1000).fadeOut(form.animationsSpeed * 2);
    jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #finalText').delay(2000).fadeIn(form.animationsSpeed * 2);
    setTimeout(function() {
      if (form.close_url != "" && form.close_url != "#" && form.close_url != " ") {
        document.location.href = form.close_url;
      }
    }, 5000);

    /*setTimeout(function () {
     wpe_closePopup();
     }, 5500);*/
  }


  function wpe_updateStep(formID) {
    var form = wpe_getForm(formID);
    if (form.showSteps == 1) {
      var disp_step = 0;
      jQuery.each(lfb_plannedSteps,function(i,v){
        if(v == form.step){
          disp_step = i;
        }
      });
      disp_step++;
      if(disp_step == 0){
        disp_step = 1;
      }
      if(form.step == 'final'){
        disp_step = lfb_plannedSteps.length+1;
      }
      var totalStep = lfb_plannedSteps.length+1;
      var percent = ((disp_step) * 100) / totalStep;
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] .genPrice .progress .progress-bar-price').html((disp_step) + '/' + totalStep);
      jQuery('#estimation_popup.wpe_bootstraped[data-form="' + formID + '"] .genPrice .progress .progress-bar').css('width', percent + '%');
    }
  }

  function wpe_initPanelResize(formID) {
    jQuery(window).resize(function() {
      jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel').css('min-height', jQuery(' #estimation_popup.wpe_bootstraped[data-form="' + formID + '"] #mainPanel .genSlide').eq(form.step - 1).outerHeight() + 50);
    });
  }
})(jQuery);
