<?php echo isset($error) && $error != "" ? '<div style="margin-top: 10px;" class="alert alert-danger">'.$error.'</div>' : "";?>
<?php echo isset($info) && $info != "" ? '<div style="margin-top: 10px;" class="alert alert-success">'.$info.'</div>' : "";?>
<?php echo isset($warn) && $warn != "" ? '<div style="margin-top: 10px;" class="alert alert-warning">'.$warn.'</div>' : "";?>

<div class="row">
    <div class="col-md-8">
        <?php include "static/static_html/vendor_register.html"; ?>
 
        <form id="vendor_register" role="form" method="post" enctype="multipart/form-data">
            <p><span style="color:#244da3"><span style="font-size:16px"><strong>Company Information</strong></span></span><br />
This information will be publicly displayed in Jardins Sans Secret website.&nbsp;</p>
            
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shopname">Company *</label>
                        <input type="text" required name="shopname" value="<?php echo isset($_POST["shopname"]) ? $_POST["shopname"] : "";?>" class="form-control" id="shopname" placeholder="Enter Company">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="website">Website *</label>
                        <input type="text" required name="website" value="<?php echo isset($_POST["website"]) ? $_POST["website"] : "";?>" class="form-control" id="website" placeholder="Enter website">
                    </div>
                </div>
            </div>
            
            
            <div class="form-group">
                <label for="shopname">Address *</label>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" required name="street" value="<?php echo isset($_POST["street"]) ? $_POST["street"] : "";?>" class="form-control" id="shopname" placeholder="Street">
                    </div>
                    <div class="col-md-4">
                        <input type="text" required name="city" value="<?php echo isset($_POST["city"]) ? $_POST["city"] : "";?>" class="form-control" id="shopname" placeholder="City">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="zip" value="<?php echo isset($_POST["zip"]) ? $_POST["zip"] : "";?>" class="form-control" id="shopname" placeholder="Zip code, Post Code">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" required id="country" name="country">
                    <option value="">Choose country</option><option value="AF">Afghanistan</option> <option value="AL">Albania</option> <option value="DZ">Algeria</option> <option value="AS">American Samoa</option> <option value="AD">Andorra</option> <option value="AG">Angola</option> <option value="AI">Anguilla</option> <option value="AG">Antigua &amp; Barbuda</option> <option value="AR">Argentina</option> <option value="AA">Armenia</option> <option value="AW">Aruba</option> <option value="AU">Australia</option> <option value="AT">Austria</option> <option value="AZ">Azerbaijan</option> <option value="BS">Bahamas</option> <option value="BH">Bahrain</option> <option value="BD">Bangladesh</option> <option value="BB">Barbados</option> <option value="BY">Belarus</option> <option value="BE">Belgium</option> <option value="BZ">Belize</option> <option value="BJ">Benin</option> <option value="BM">Bermuda</option> <option value="BT">Bhutan</option> <option value="BO">Bolivia</option> <option value="BL">Bonaire</option> <option value="BA">Bosnia &amp; Herzegovina</option> <option value="BW">Botswana</option> <option value="BR">Brazil</option> <option value="BC">British Indian Ocean Ter</option> <option value="BN">Brunei</option> <option value="BG">Bulgaria</option> <option value="BF">Burkina Faso</option> <option value="BI">Burundi</option> <option value="KH">Cambodia</option> <option value="CM">Cameroon</option> <option value="CA">Canada</option> <option value="IC">Canary Islands</option> <option value="CV">Cape Verde</option> <option value="KY">Cayman Islands</option> <option value="CF">Central African Republic</option> <option value="TD">Chad</option> <option value="CD">Channel Islands</option> <option value="CL">Chile</option> <option value="CN">China</option> <option value="CI">Christmas Island</option> <option value="CS">Cocos Island</option> <option value="CO">Colombia</option> <option value="CC">Comoros</option> <option value="CG">Congo</option> <option value="CK">Cook Islands</option> <option value="CR">Costa Rica</option> <option value="CT">Cote D'Ivoire</option> <option value="HR">Croatia</option> <option value="CU">Cuba</option> <option value="CB">Curacao</option> <option value="CY">Cyprus</option> <option value="CZ">Czech Republic</option> <option value="DK">Denmark</option> <option value="DJ">Djibouti</option> <option value="DM">Dominica</option> <option value="DO">Dominican Republic</option> <option value="TM">East Timor</option> <option value="EC">Ecuador</option> <option value="EG">Egypt</option> <option value="SV">El Salvador</option> <option value="GQ">Equatorial Guinea</option> <option value="ER">Eritrea</option> <option value="EE">Estonia</option> <option value="ET">Ethiopia</option> <option value="FA">Falkland Islands</option> <option value="FO">Faroe Islands</option> <option value="FJ">Fiji</option> <option value="FI">Finland</option> <option value="FR">France</option> <option value="GF">French Guiana</option> <option value="PF">French Polynesia</option> <option value="FS">French Southern Ter</option> <option value="GA">Gabon</option> <option value="GM">Gambia</option> <option value="GE">Georgia</option> <option value="DE">Germany</option> <option value="GH">Ghana</option> <option value="GI">Gibraltar</option> <option value="GR">Greece</option> <option value="GL">Greenland</option> <option value="GD">Grenada</option> <option value="GP">Guadeloupe</option> <option value="GU">Guam</option> <option value="GT">Guatemala</option> <option value="GN">Guinea</option> <option value="GY">Guyana</option> <option value="HT">Haiti</option> <option value="HW">Hawaii</option> <option value="HN">Honduras</option> <option value="HK">Hong Kong</option> <option value="HU">Hungary</option> <option value="IS">Iceland</option> <option value="IN">India</option> <option value="ID">Indonesia</option> <option value="IA">Iran</option> <option value="IQ">Iraq</option> <option value="IR">Ireland</option> <option value="IM">Isle of Man</option> <option value="IL">Israel</option> <option value="IT">Italy</option> <option value="JM">Jamaica</option> <option value="JP">Japan</option> <option value="JO">Jordan</option> <option value="KZ">Kazakhstan</option> <option value="KE">Kenya</option> <option value="KI">Kiribati</option> <option value="NK">Korea North</option> <option value="KS">Korea South</option> <option value="KW">Kuwait</option> <option value="KG">Kyrgyzstan</option> <option value="LA">Laos</option> <option value="LV">Latvia</option> <option value="LB">Lebanon</option> <option value="LS">Lesotho</option> <option value="LR">Liberia</option> <option value="LY">Libya</option> <option value="LI">Liechtenstein</option> <option value="LT">Lithuania</option> <option value="LU">Luxembourg</option> <option value="MO">Macau</option> <option value="MK">Macedonia</option> <option value="MG">Madagascar</option> <option value="MY">Malaysia</option> <option value="MW">Malawi</option> <option value="MV">Maldives</option> <option value="ML">Mali</option> <option value="MT">Malta</option> <option value="MH">Marshall Islands</option> <option value="MQ">Martinique</option> <option value="MR">Mauritania</option> <option value="MU">Mauritius</option> <option value="ME">Mayotte</option> <option value="MX">Mexico</option> <option value="MI">Midway Islands</option> <option value="MD">Moldova</option> <option value="MC">Monaco</option> <option value="MN">Mongolia</option> <option value="MS">Montserrat</option> <option value="MA">Morocco</option> <option value="MZ">Mozambique</option> <option value="MM">Myanmar</option> <option value="NA">Nambia</option> <option value="NU">Nauru</option> <option value="NP">Nepal</option> <option value="AN">Netherland Antilles</option> <option value="NL">Netherlands (Holland, Europe)</option> <option value="NV">Nevis</option> <option value="NC">New Caledonia</option> <option value="NZ">New Zealand</option> <option value="NI">Nicaragua</option> <option value="NE">Niger</option> <option value="NG">Nigeria</option> <option value="NW">Niue</option> <option value="NF">Norfolk Island</option> <option value="NO">Norway</option> <option value="OM">Oman</option> <option value="PK">Pakistan</option> <option value="PW">Palau Island</option> <option value="PS">Palestine</option> <option value="PA">Panama</option> <option value="PG">Papua New Guinea</option> <option value="PY">Paraguay</option> <option value="PE">Peru</option> <option value="PH">Philippines</option> <option value="PO">Pitcairn Island</option> <option value="PL">Poland</option> <option value="PT">Portugal</option> <option value="PR">Puerto Rico</option> <option value="QA">Qatar</option> <option value="ME">Republic of Montenegro</option> <option value="RS">Republic of Serbia</option> <option value="RE">Reunion</option> <option value="RO">Romania</option> <option value="RU">Russia</option> <option value="RW">Rwanda</option> <option value="NT">St Barthelemy</option> <option value="EU">St Eustatius</option> <option value="HE">St Helena</option> <option value="KN">St Kitts-Nevis</option> <option value="LC">St Lucia</option> <option value="MB">St Maarten</option> <option value="PM">St Pierre &amp; Miquelon</option> <option value="VC">St Vincent &amp; Grenadines</option> <option value="SP">Saipan</option> <option value="SO">Samoa</option> <option value="AS">Samoa American</option> <option value="SM">San Marino</option> <option value="ST">Sao Tome &amp; Principe</option> <option value="SA">Saudi Arabia</option> <option value="SN">Senegal</option> <option value="RS">Serbia</option> <option value="SC">Seychelles</option> <option value="SL">Sierra Leone</option> <option value="SG">Singapore</option> <option value="SK">Slovakia</option> <option value="SI">Slovenia</option> <option value="SB">Solomon Islands</option> <option value="OI">Somalia</option> <option value="ZA">South Africa</option> <option value="ES">Spain</option> <option value="LK">Sri Lanka</option> <option value="SD">Sudan</option> <option value="SR">Suriname</option> <option value="SZ">Swaziland</option> <option value="SE">Sweden</option> <option value="CH">Switzerland</option> <option value="SY">Syria</option> <option value="TA">Tahiti</option> <option value="TW">Taiwan</option> <option value="TJ">Tajikistan</option> <option value="TZ">Tanzania</option> <option value="TH">Thailand</option> <option value="TG">Togo</option> <option value="TK">Tokelau</option> <option value="TO">Tonga</option> <option value="TT">Trinidad &amp; Tobago</option> <option value="TN">Tunisia</option> <option value="TR">Turkey</option> <option value="TU">Turkmenistan</option> <option value="TC">Turks &amp; Caicos Is</option> <option value="TV">Tuvalu</option> <option value="UG">Uganda</option> <option value="UA">Ukraine</option> <option value="AE">United Arab Emirates</option> <option value="UK">United Kingdom</option> <option value="US">United States of America</option> <option value="UY">Uruguay</option> <option value="UZ">Uzbekistan</option> <option value="VU">Vanuatu</option> <option value="VS">Vatican City State</option> <option value="VE">Venezuela</option> <option value="VN">Vietnam</option> <option value="VB">Virgin Islands (Brit)</option> <option value="VA">Virgin Islands (USA)</option> <option value="WK">Wake Island</option> <option value="WF">Wallis &amp; Futana Is</option> <option value="YE">Yemen</option> <option value="ZR">Zaire</option> <option value="ZM">Zambia</option> <option value="ZW">Zimbabwe</option>
                  </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" id="state" name="state"><option value="">-- Please Select --</option> <option value="AL">Alabama</option> <option value="AK">Alaska</option> <option value="AZ">Arizona</option> <option value="AR">Arkansas</option> <option value="CA">California</option> <option value="CO">Colorado</option> <option value="CT">Connecticut</option> <option value="DE">Delaware</option> <option value="DC">District of Columbia</option> <option value="FL">Florida</option> <option value="GA">Georgia</option> <option value="HI">Hawaii</option> <option value="ID">Idaho</option> <option value="IL">Illinois</option> <option value="IN">Indiana</option> <option value="IA">Iowa</option> <option value="KS">Kansas</option> <option value="KY">Kentucky</option> <option value="LA">Louisiana</option> <option value="ME">Maine</option> <option value="MD">Maryland</option> <option value="MA">Massachusetts</option> <option value="MI">Michigan</option> <option value="MN">Minnesota</option> <option value="MS">Mississippi</option> <option value="MO">Missouri</option> <option value="MT">Montana</option> <option value="NE">Nebraska</option> <option value="NV">Nevada</option> <option value="NH">New Hampshire</option> <option value="NJ">New Jersey</option> <option value="NM">New Mexico</option> <option value="NY">New York</option> <option value="NC">North Carolina</option> <option value="ND">North Dakota</option> <option value="OH">Ohio</option> <option value="OK">Oklahoma</option> <option value="OR">Oregon</option> <option value="PA">Pennsylvania</option> <option value="RI">Rhode Island</option> <option value="SC">South Carolina</option> <option value="SD">South Dakota</option> <option value="TN">Tennessee</option> <option value="TX">Texas</option> <option value="UT">Utah</option> <option value="VT">Vermont</option> <option value="VA">Virginia</option> <option value="WA">Washington</option> <option value="WV">West Virginia</option> <option value="WI">Wisconsin</option> <option value="WY">Wyoming</option> <option value="0"> </option> <option value="0">-- US Territories ---</option> <option value="PR">Puerto Rico</option> <option value="VI">US Virgin Islands</option> <option value="0"> </option> <option value="0">-- Canada --</option> <option value="AB">Alberta</option> <option value="NT">Northwest Territories</option> <option value="BC">British Columbia</option> <option value="ON">Ontario</option> <option value="MB">Manitoba</option> <option value="PE">Prince Edward Island</option> <option value="NB">New Brunswick</option> <option value="NU">Nunavut Territory</option> <option value="QC">Quebec</option> <option value="NL">Newfoundland</option> <option value="SK">Saskatchewan</option> <option value="NS">Nova Scotia</option> <option value="YT">Yukon Territory</option></select>
                    </div>
                </div>
            </div>
            
            <p><span style="color:#244da3"><span style="font-size:16px"><strong>Private Information</strong></span></span><br />
This information will be publicly displayed in Jardins Sans Secret website.&nbsp;</p>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="firstname">First name (*)</label>
                        <input type="text" required name="firstname" value="<?php echo isset($_POST["firstname"]) ? $_POST["firstname"] : "";?>" class="form-control" id="firstname" placeholder="Enter First name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lastname">Last name (*)</label>
                        <input type="text" required name="lastname" value="<?php echo isset($_POST["lastname"]) ? $_POST["lastname"] : "";?>" class="form-control" id="lastname" placeholder="Enter Last name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email">Email address (*)</label>
                        <input type="email" required name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : "";?>" class="form-control" id="email" placeholder="Enter email">
                    </div>
                </div>
            </div>
            
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                <label for="password">Password (*)</label>
                <input type="password" required name="password" class="form-control" id="password" placeholder="Password">
            </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                <label for="password2">Re-ender password (*)</label>
                <input type="password" required name="password2" class="form-control" id="password2" placeholder="Re-ender password">
            </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                <label for="phonenumber">Phone number</label>
                <input type="text" name="phonenumber" value="<?php echo isset($_POST["phonenumber"]) ? $_POST["phonenumber"] : "";?>" class="form-control" id="phonenumber" placeholder="Enter phone number">
            </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="logo">Upload your logo</label>
                        <input type="file" name="logo" id="logo">
                        <p class="help-block">Upload your logo to our server.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="securitycode">Security code (*)</label>
                        <img style="border: 1px solid #000; margin-right: 15px" src="/static/securimage/securimage_show.php?sid=<?php echo md5(uniqid()) ?>" alt="CAPTCHA Image"/>                
                        <input type="text" required name="securitycode" class="form-control" id="securitycode" placeholder="Enter security code">
                    </div>
                </div>
            </div>                
                             
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="agreed" id="agreed" value="1" required> (*) I agree to our <a target="_blank" href="/cms/2/Terms-Conditions.html">Terms & Conditions</a>
                </label>
            </div>
            <button type="submit" name="smVendorRegister" class="btn btn-primary">Register</button>
        </form>
    </div>
    <div class="col-md-4">
        <div id="login_form">
            <h2><span style="font-size:20px"><span style="color:#244da3"><strong>Merchant Login</strong></span></span></h2>
        
            <form id="login_form" role="form" method="post">
                <div class="form-group">
                    <label for="liemail">Email address</label>
                    <input type="email" required name="email" <?php echo isset($_POST["email"]) ? $_POST["email"] : "";?> class="form-control" id="liemail" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="lipassword">Password</label>
                    <input type="password" required name="password" class="form-control" id="lipassword" placeholder="Password">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="rememberme" value="1"> Remember me
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6" style="margin-top: 10px;text-align: right;">
                        <a href="javascript:void()" onClick="return ShowRecoverPassword();">Lost password</a>
                    </div>
                </div>
                <button type="submit" name="smVendorLogin" class="btn btn-primary">Login</button>
            </form>
        </div>
        <div id="recovery_password_form" style="display:none;">
            <h2><span style="font-size:20px"><span style="color:#244da3"><strong>Password Recovery</strong></span></span></h2>
        
            <form id="password_form" role="form" method="post">
                <div class="form-group">
                    <label for="liemail">Email address</label>
                    <input type="email" required name="email" <?php echo isset($_POST["email"]) ? $_POST["email"] : "";?> class="form-control" id="liemail" placeholder="Enter email">
                </div>
                <div class="col-md-12" style="text-align: right;">
                    <a href="javascript:void()" onClick="return ShowLogin();">Login</a>
                </div>
                <button type="submit" name="smVendorRecovery" class="btn btn-primary">Send new password</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#country").val("<?php echo isset($_POST["country"]) ? $_POST["country"] : "";?>");
    $("#state").val("<?php echo isset($_POST["state"]) ? $_POST["state"] : "";?>");
    function ShowRecoverPassword()
    {
        $("#login_form").hide();
        $("#recovery_password_form").show();
        return false;
    }
    function ShowLogin()
    {
        $("#recovery_password_form").hide();
        $("#login_form").show();
        return false;
    }
    
    
    $('#vendor_register #website').tooltipster({ 
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false,    // allow multiple tips to be open at a time
        position: "right"  // display the tips to the right of the element
    });
    $('#vendor_register #securitycode').tooltipster({ 
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false,    // allow multiple tips to be open at a time
        position: "right"  // display the tips to the right of the element
    });
    $('#vendor_register #agreed').tooltipster({ 
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false,    // allow multiple tips to be open at a time
        position: "left"  // display the tips to the right of the element
    });
    $('#vendor_register #state').tooltipster({ 
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false,    // allow multiple tips to be open at a time
        position: "right"  // display the tips to the right of the element
    });
    $('#vendor_register *').tooltipster({ 
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false    // allow multiple tips to be open at a time
    });
    
    $("#country").change(function(){
    	if ($(this).val() == "US" || $(this).val() == "CA")
    	{
    		$("#state").attr("required","required");
    	}
    	else
    	{
    		$("#state").removeAttr("required");
    		$("#state").tooltipster('hide');
    	}
    });
    
    $("#vendor_register").validate({
        errorPlacement: function (error, element) {
            $(element).tooltipster('update', $(error).text());
            $(element).tooltipster('show');
        },
        success: function (label, element) {
            $(element).tooltipster('hide');
        }
    });
    
    SetValidationForm("#vendor_register","top");
    SetValidationForm("#login_form","right");
    SetValidationForm("#password_form","right");
</script>