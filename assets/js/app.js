'use strict';

const $ = require('jquery');
require('font-awesome/css/font-awesome.css');
require('bootstrap/dist/js/bootstrap.js');
require('bootstrap/dist/css/bootstrap.css');
require('../css/app.css');

$("a[data-method]").click(function(e) {
  var $form = $('<form/>').hide();
  $form.attr({
    'action' : $(this).attr('href'),
    'method': $(this).data('method')
  })
  $form.append($('<input/>',{
    type:'hidden',
    name:'_method'
  }).val($(this).data('method')));
  $(this).parent().append($form);
  $form.submit();
  return false;
});
