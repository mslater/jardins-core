<h2>Login</h2>
<form method="post">
Username: <input type="text" name="username" /><br>
Password: <input type="password" name="password" /><br>
<input type="submit" name="smLogin" /><br> 
<input type="hidden" name="redirect" value="<?php echo $_GET["view"];?>" />
</form>