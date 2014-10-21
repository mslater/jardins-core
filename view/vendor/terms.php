<?php if(isset($_POST["smPreview"])): ?>
<br><br>
<div>
	<?php echo html_entity_decode($vendor->terms);?>
</div>
<?php else: ?>
	<script src="/static/ckeditor/ckeditor.js"></script>

<h1 class="vendor_header">Terms &amp; Conditions</h1>
 
<form method="post">
    <div class="form-group">
        <label for="terms">Please input your information using the below text editor</label>
        <textarea class="ckeditor" id="terms" name="terms"><?php echo isset($_POST["terms"]) ? $_POST["terms"] : $vendor->terms;?></textarea>
    </div>
    <button type="submit" name="smSubmit" class="btn btn-primary">Submit</button>
    <button type="submit" name="smPreview" class="btn btn-primary">Preview</button>
</form>


            <script>
            CKEDITOR.replace( 'terms', {
	toolbar: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
            ]
            });
            </script>
<?php endif; ?>