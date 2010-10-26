<html>
<head>
	<title>CI Uploadify Example</title>
	
	<!-- Include Uploadify CSS and JS in the head section here -->

	<script type="text/javascript" language="javascript">
    $(document).ready(function(){
        
        $('#uploadfile').click(function(){
            var obj = [];
            $('#name').each(function(v)
            {
                obj[this.name] = this.value;
            });
            $('#description').each(function(v)
            {
                obj[this.name] = this.value;
            });
            $('#upload').uploadifySettings('scriptData', obj);
            $('#upload').uploadifyUpload();
        });
        
		// Your paths to the uploadify.swf and cancelImg may need to be modified for your set up
         $("#upload").uploadify({
            uploader: '<?php echo site_url('js/uploadify/uploadify.swf'); ?>',
            script: '<?php echo site_url('upload/do_upload'); ?>',
            cancelImg: '<?php echo site_url('js/uploadify/cancel.png'); ?>',
            folder: 'files/uploads',
            scriptData: {'name': $("input#name").val(), 'description': $("input#description").val()},
            scriptAccess: 'always',
            multi: false,
            'onError' : function (a, b, c, d) {
                 if (d.status == 404)
                        alert('Could not find upload script.');
                 else if (d.type === "HTTP")
                        alert('error '+d.type+": "+d.status);
                 else if (d.type ==="File Size")
                        alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
                 else
                        alert('error '+d.type+": "+d.text);
            },
            'onComplete'   : function (event, queueID, fileObj, response, data) {
                //Post response back to controller
                $.post('<?php echo site_url('upload/uploadify'); ?>',{message: response},function(info){
                        $("#target").append(info);  //Add response returned by controller
                });
            }
        });
        
    });
	</script>
</head>

<body>
<h1>CodeIgniter Uploadify Example</h1>
<br />

<div id="target">

</div>

<?php echo $this->session->flashdata('message'); ?>

<p class="red"><b>Note</b>: Do not navigate away from the page until the upload successful message is shown.</p>

<?php echo form_open_multipart('upload/do_upload');?>

<p>
  <label for="Filedata"><b>Choose a File:</b></label><br/>
  <?php echo form_upload(array('name' => 'Filedata', 'id' => 'upload'));?>
</p>

<br />

<p>
  <label for="name"><b>File Name:</b></label><br />
  <input name="name" id="name" style="width:320px;"/>
</p>

<p>
  <label for="description"><b>File Description:</b></label><br />
  <textarea name="description" id="description" rows="3" cols="50"></textarea>
</p>

<br />

<p>
  <a href="#" id="uploadfile" class="btn-big-grey"><span>Upload File</span></a>
</p>


<?php echo form_close();?>

</body>
</html>