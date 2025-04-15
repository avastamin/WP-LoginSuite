jQuery(document).ready(function ($) {
  var mediaUploader;

  $("#upload_logo_button").click(function (e) {
    e.preventDefault();

    // If the media uploader already exists, open it
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    // Create the media uploader
    mediaUploader = wp.media({
      title: "Choose Logo",
      button: {
        text: "Choose Logo",
      },
      multiple: false,
    });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#logo_url").val(attachment.url);
      $("#logo_preview").html(
        '<img src="' +
          attachment.url +
          '" style="max-width: 100%; height: auto;">'
      );
    });

    // Open the media uploader
    mediaUploader.open();
  });
});
