$(function()
{
	var country_id = 'country';
	var state_id = 'state';

    $('#' + country_id).change(function()
	{
		$('#' + state_id).attr('disabled', 'US' != $('#'  + country_id + ' option:selected').val());

	}).change();

});
