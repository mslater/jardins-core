<?php if(isset($_POST["smPreview"])): ?>
<br><br>
<div>
	<?php echo html_entity_decode($vendor->aboutus);?>
</div>
<?php else: ?>
<script src="/static/ckeditor/ckeditor.js"></script>

<h1 class="vendor_header">About Us</h1>

<form method="post">
    <div class="form-group">
        <label for="email">Please input your information using the below text editor</label>
        <textarea class="ckeditor" id="about_us" name="aboutus"><?php echo isset($_POST["aboutus"]) ? $_POST["aboutus"] : $vendor->aboutus;?></textarea>
    </div>
    <button type="submit" name="smSubmit" class="btn btn-primary">Submit</button>
    <button type="submit" name="smPreview" class="btn btn-primary">Preview</button>
</form>


            <script>
            CKEDITOR.replace( 'about_us', {
	toolbar: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor','preview' ] }
            ]
            });
            </script>
<?php endif; ?>