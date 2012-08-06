folder2gallery = function(){};

$(function()
{
	$('#do_folder2gallery').click(function()
	{
		$.post(folder2gallery.url,
			{'folder': folder2gallery_folder.value},
			process_folder2gallery,
			'json'
		);
	});
});

function process_folder2gallery(data)
{
	habari.editor.insertSelection(data);
}