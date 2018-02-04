//var box = $('.additionalInfo');
//
$(document).on('click', '.mainInfo', function() { 
	var box = $(this);
	box = box['context'];
	box = box['parentNode'];
	box = $(box).find("div.additionalInfo");
  if (box.hasClass('appearAnimation')) {
  	//Есть элемент управления
    box.removeClass('appearAnimation');
    box.addClass('disappearAnimation');
    setTimeout(function(){
	    box.removeClass('enableElement');
	}, 500);

  } else {
  	//Нет элемента управления
    	if(box.hasClass('disappearAnimation')){
    		box.removeClass('disappearAnimation');
    	}
    	box.addClass('enableElement');
     	box.addClass('appearAnimation');
    }
});