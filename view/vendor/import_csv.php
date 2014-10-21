<h1 class="vendor_header">Upload your product file using the below Template</h1>
<?php
if (isset($res["success"]))
{
    echo '<div class="alert alert-success">'.$res["success"].'</div>';
}
?>
<a class="btn btn-info" href="/static/vendor_data_template.csv">Download Template</a>

<?php include "static/static_html/vendor_home.html"; ?>

<form method="post" enctype="multipart/form-data">
    <div class="form-group">
    <input type="file" id="csvFile" name="csvFile">
    <p class="help-block">Data file should be in CSV extension</p>
    <button type="submit" name="smCSVFile" class="btn btn-primary">Submit</button>
  </div>
</form>

<h3>History <small>(Last 50 imports)</small>:</h3>
<table class="table">
        <thead>
          <tr>
            <th>Upload Date</th>
            <th>Status</th>
            <th># Updates</th>
            <th># Inserts</th>
            <th># Deletes</th>
            <th># Products</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($r = mysql_fetch_object($data)){
              echo '<tr>
            <td>'.$r->created_date.'</td>
            <td>'.$r->import_status.'</td>
            <td>'.$r->no_update.'</td>
            <td>'.$r->no_insert.'</td>
            <td>'.$r->no_delete.'</td>
            <td>'.$r->imported_count.'</td>
          </tr>';
          }?>
        </tbody>
      </table>