// ===========================================================================================
//
// File: jquery.persiaeditor.js
//
// Description: JavaScript for the PersiaEditor.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// How to do this?
//
// http://docs.jquery.com/Plugins/Authoring
//

(function($) {

	$.fn.persiaEditor = function(settings) {
		var options = {
				'foo': 'bar',
				'resize': true,
			};
		if (settings) $.extend(options, settings);
		
		this.each(function() {
			var $$, editor;
			
			// Current item
			$$ = $(this);

			//
			// Build up the editor
			//
			
			// Wrap a div around the textarea, inherit width from textarea
			$$.wrap("<div class='persiaEditorContainer' style='width: " + $$.outerWidth() + "px;' />");
			
			// Set the header with menuitems
			header = $("<div class='persiaEditorHeader' />").insertBefore($$);
			header.append("<a href=''>Header</a> ");
			header.append("<a href=''>Para</a> ");
			
			// Set the footer
			$("<div class='persiaEditorFooter' />").insertAfter($$);
			
		});
     
		return this;
	};

})(jQuery);

