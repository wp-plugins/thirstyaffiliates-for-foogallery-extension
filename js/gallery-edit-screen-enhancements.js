jQuery(document).ready(function() {

    // Enable JS flag for ThirstyAffiliates
    thirstyJSEnable = true;

    // Add dummy replycontent field for link insertion later
    jQuery('.foogallery-attachments-list').append('<textarea style="position: absolute; left: -99999px;" class="thirstyaffiliates_temp_url_holder" id="replycontent">blah</textarea>');

    // Add the Aff button to the attachments
    jQuery(".foogallery-attachments-list li.attachment .attachment-preview").each(function() {
        // Add aff button
        var currentHtml = jQuery(this).html();
        var attachmentId = jQuery(this).parent().data('attachment-id');
        jQuery(this).html(currentHtml + "<a class=\"afflink\" href=\"#\" title=\"Add Affiliate Link\"><img src=\"" + thirstyAffiliates_For_FooGallery_URL + "assets/icon-aff.png\" /></a>");

        // Add aff link url holder
        currentHtml = jQuery(this).html();
        jQuery(this).html(currentHtml + "<br /><input class=\"afflink_url\" placeholder=\"Affiliate Link URL\" type=\"text\" id=\"" + attachmentId + "_aff_link\" name=\"thirstyaff[" + attachmentId + "]\" value=\"\">");
    });

    // Add click handler to the aff buttons
    jQuery('.afflink').click(function() {
        var attachmentId = jQuery(this).parent().parent().data('attachment-id');

        // This such a hacky way and I think we should find a better way at some point, but it works for now
        jQuery('.thirstyaffiliates_temp_url_holder').text('temp').select();
        jQuery('.thirstyaffiliates_temp_url_holder').unbind(); // unbind any previous change monitors
        jQuery('.thirstyaffiliates_temp_url_holder').on('change keyup paste', function() {
            var linkVal = jQuery(this).val();
            console.log(linkVal);
            var linkValURL = linkVal.match(/http[^""]*/);
            jQuery('input[id="' + attachmentId + '_aff_link"]').val(linkValURL);
        });
        tb_show("Add an Affiliate Link", thirstyAjaxLink + '?action=thirstyGetThickboxContent&height=640&width=640&TB_iframe=true');

    });

    // Get the existing value for each aff link text box
    jQuery('.foogallery-attachments-list li.attachment').each(function() {
        var attachmentId = jQuery(this).data('attachment-id');
        var galleryId = jQuery('#post_ID').val();

        jQuery.post(
			ajaxurl,
			{
				action: 'foogalleryThirstyGetLinkDetails',
				attachmentID: attachmentId,
                galleryID: galleryId
			},
			function(result) {
                console.log(result);
                jQuery('input[id="' + attachmentId + '_aff_link"]').val(result);
            }
		);

    });

});
