$(function () {
  $("#image-cover-modal").click(function () {
    $(this).removeClass("model-shown");
  });

  $("#image-modal img").click(function () {
    $("#image-cover-modal").addClass("model-shown");
    $("#image-cover-image").attr("src", $(this).attr("src"));

  });
});