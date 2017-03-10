<?php 
echo $this->Html->css('bootstrap-multiselect');
echo $this->Html->script('bootstrap-multiselect');
?>
<script type="text/javascript">
$(document).ready(function() {
        var date = new Date();
	$('#specialization').multiselect({
		includeSelectAllOption: true	
	});
	$(".dob").datepicker({
            format: "<?php echo $this->Gym->getSettings('date_format'); ?>",
            endDate: new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0)
        });
        
	var box_height = $(".box").height();
	var box_height = box_height + 500 ;
	$(".content-wrapper").css("height",box_height+"px");
});

function validate_multiselect()
{		
	var specialization = $("#specialization").val();
	if(specialization == null)
	{
		alert("Select Specialization.");
		return false;
	}else{
		return true;
	}	 		
}
</script>
<section class="content">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			<section class="content-header">			
			  <h1>		
				<i class="fa fa-user"></i>
				<?php echo $title;?>
				<small><?php echo __("Staff Member");?></small>
			  </h1>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("StaffMembers","StaffList");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Staff List");?></a>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">			
			<?php				
			echo $this->Form->create("addgroup",["type"=>"file","class"=>"validateForm form-horizontal","role"=>"form","onsubmit"=>"return validate_multiselect()"]);
			echo "<fieldset><legend>". __('Personal Information')."</legend>";
			
                        echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("First Name").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"first_name","class"=>"form-control validate[required]","value"=>(($edit)?$data['first_name']:'')]);
			echo "</div>";	
			echo "</div>";	
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Middle Name").'</label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"middle_name","class"=>"form-control","value"=>(($edit)?$data['middle_name']:'')]);
			echo "</div>";	
			echo "</div>";	
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Last Name").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"last_name","class"=>"form-control validate[required]","value"=>(($edit)?$data['last_name']:'')]);
			echo "</div>";	
			echo "</div>";	
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Gender").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6 checkbox">';
			$radio = [
						['value' => 'male', 'text' => __('Male')],
						['value' => 'female', 'text' => __('Female')]
					];
			echo $this->Form->radio("gender",$radio,['default'=>($edit)?$data["gender"]:'male']);			
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Date of birth").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"birth_date","class"=>"form-control dob validate[required]","value"=>(($edit)?$data['birth_date']->format('d-m-Y'):'')]);
			echo "</div>";	
			echo "</div>";	
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Assign Role").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';			
			echo @$this->Form->select("role",$roles,["default"=>$data['role'],"empty"=>__("Select Role"),"class"=>"form-control validate[required] roles_list"]);
			echo "</div>";	
			echo '<div class="col-md-2">';
			echo "<a href='javascript:void(0)' class='add-role btn btn-flat btn-success' data-url='{$this->Gym->createurl("GymAjax","addRole")}'>".__("Add/Remove")."</a>";
			echo "</div>";	
			echo "</div>";
                        
                        if($session['role_id'] == 1){
                            echo "<div class='form-group'>";	
                            echo '<label class="control-label col-md-2" for="licensee">'. __("Associate Licensee").'<span class="text-danger"> *</span></label>';
                            echo '<div class="col-md-6">';			
                            echo @$this->Form->select("associated_licensee",$licensees,["default"=>$data['associated_licensee'],"empty"=>__("Select Licensee"),"class"=>"form-control validate[required]"]);
                            echo "</div>";	
                            echo '<div class="col-md-2">';
                            echo "<a href='{$this->request->base}/Licensee/addLicensee' class='btn btn-flat btn-primary'>".__("Add")."</a>";
                            echo "</div>";	
                            echo "</div>";
                        }else{
                            echo $this->Form->hidden("",["label"=>false,"name"=>"associated_licensee","class"=>"form-control validate[required]","value"=>( ($edit) ?  $data['associated_licensee'] : $session['id'])]);
                        }
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Specialization").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-8">';			
			echo @$this->Form->select("s_specialization",$specialization,["default"=>json_decode($data['s_specialization']),"multiple"=>"multiple","class"=>"form-control validate[required] specialization_list","id"=>"specialization"]);
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='add-spec btn btn-flat btn-success' data-url='{$this->request->base}/GymAjax/AddSpecialization'>".__("Add/Remove")."</a>";
			echo "</div>";
			echo "</div>";				
			echo "</fieldset>";
			
			echo "<fieldset><legend>". __('Contact Information')."</legend>";
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Home Town Address").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"address","class"=>"form-control validate[required]","value"=>(($edit)?$data['address']:'')]);
			echo "</div>";	
			echo "</div>";	
                        
                        echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("State").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"state","class"=>"form-control validate[required]","value"=>(($edit)?$data['state']:'')]);
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("City").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"city","class"=>"form-control validate[required]","value"=>(($edit)?$data['city']:'')]);
			echo "</div>";	
			echo "</div>";
                        
                        echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Zip code").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"zipcode","class"=>"form-control validate[required]","value"=>(($edit)?$data['zipcode']:'')]);
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Mobile Number").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo '<div class="input-group">';
			echo '<div class="input-group-addon">+'.$this->Gym->getCountryCode($this->Gym->getSettings("country")).'</div>';
			echo $this->Form->input("",["label"=>false,"name"=>"mobile","class"=>"form-control validate[required]","value"=>(($edit)?$data['mobile']:'')]);
			echo "</div>";	
			echo "</div>";	
			echo "</div>";	
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Phone").'</label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"phone","class"=>"form-control","value"=>(($edit)?$data['phone']:'')]);
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Email").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"email","class"=>"form-control validate[required,custom[email]]","value"=>(($edit)?$data['email']:'')]);
			echo "</div>";	
			echo "</div>";			
			echo "</fieldset>";
			
			echo "<fieldset><legend>". __('Login Information')."</legend>";
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Username").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"username","class"=>"form-control validate[required]","value"=>(($edit)?$data['username']:''),"readonly"=> (($edit)?true:false)]);
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Password").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->password("",["label"=>false,"name"=>"password","class"=>"form-control validate[required]","value"=>(($edit)?$data['password']:'')]);
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Display Image").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-4">';
			echo $this->Form->file("image",["class"=>"form-control"]);
			$image = ($edit && !empty($data['image'])) ? $data['image'] : "profile-placeholder.png";
			echo "<br><img width='100' src='{$this->request->webroot}webroot/upload/{$image}'>";
			echo "</div>";	
			echo "</div>";			
			echo "</fieldset>";
			
			echo $this->Form->button(__("Save Staff"),['class'=>"btn btn-flat btn-primary","name"=>"add_group"]);
			echo $this->Form->end();
			?>				
		</div>	
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
	<br>
</section>