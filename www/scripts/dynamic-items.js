/*
     Used for form with dynamic elements
*/
$(function(){
    $('.__ADD_ITEM_LINK__').click(function(){
        var elemName = $(this).attr('data-name');
        var itemsCount = $(this).attr('data-itemsCount');

        var elementDl = $('fieldset[id="fieldset-' + elemName + '"]').children("dl:first");
        var new_item = $("#" + elemName + "-__TEMPLATE__").tmpl({__TEMPLATE: itemsCount});
        new_item.appendTo(elementDl);
        $(this).attr('data-itemsCount', itemsCount + 1);
        return false;
    });

    $('.__DELETE_ITEM_LINK__').live('click', function(){
        var name = $(this).attr('data-name');
        var element = $(this).parents("dl").children('dt[id="' + name + '-label"], dd[id="' + name + '-element"]');
        element.remove();
        return false;
    });
});