$(function()
{

    $('a', '#logout').click(function()
    {
        return confirm('Are you sure you want to log out?');
    });

});