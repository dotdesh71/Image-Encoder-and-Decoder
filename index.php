<?php
if (isset($_POST['action']) && $_POST['action'] == 'encode') {
  $image = file_get_contents($_FILES['image']['tmp_name']);
  $numbers = unpack('C*', $image);
  echo implode(',', $numbers);
  exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'decode') {
  $numbers = explode(',', $_POST['values']);
  $image = call_user_func_array('pack', array_merge(array('C*'), $numbers));
  echo base64_encode($image);
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Image Encoder and Decoder</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
  <style>
    .container {
      padding-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="title">Image Encoder and Decoder</h1>
    <form action="index.php" method="post" enctype="multipart/form-data">
      <div class="field">
        <div class="file has-name">
          <label class="file-label">
            <input class="file-input" type="file" name="image" id="image">
            <span class="file-cta">
              <span class="file-icon">
                <i class="fas fa-upload"></i>
              </span>
              <span class="file-label">
                Choose a file…
              </span>
            </span>
            <span class="file-name" id="file-name"></span>
          </label>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button class="button is-primary" id="encode-button">Encode</button>
          <button class="button is-primary" id="decode-button">Decode</button>
        </div>
      </div>
    </form>
    <div class="field">
      <div class="control my-3">
      <p class="title is-6">Paste Number To Decode</p>
        <textarea class="textarea" id="numerical-values"></textarea>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <img id="image-preview" width="250px" style="display: none;">
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#image').change(function() {
        $('#file-name').text(this.files[0].name);
      });

      $('#encode-button').click(function(e) {
        e.preventDefault();

        var formData = new FormData();
        formData.append('action', 'encode');
        formData.append('image', $('#image')[0].files[0]);

        $.ajax({
          url: 'index.php',
          type: 'post',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            $('#numerical-values').val(response);
            $('#numerical-values').show();
            $('#decode-button').show();
            $('#encode-button').show();
          }
        });
      });

      $('#decode-button').click(function(e) {
        e.preventDefault();

        var data = {
          action: 'decode',
          values: $('#numerical-values').val()
        };

        $.ajax({
          url: 'index.php',
          type: 'post',
          data: data,
          success: function(response) {
            var image = new Image();
            image.src = 'data:image/jpeg;base64,' + response;
            $('#image-preview').attr('src', image.src);
            $('#image-preview').show();
            $('#numerical-values').hide();
            $('#decode-button').show();
            $('#encode-button').show();
          }
        });
      });
    });
  </script>

</body>
</html>

