<style type="text/css">
	.popup-left{
		float: left;
		width: 0px;
	}

	.popup-right{
		float: right;
		width: 100%;
	}

	.box-infor{
		margin-top: 3px;
	}

	.box-row{
		margin-top: 1px;
	}

	.box-item{
		padding: 0px;
	}

	label{
		margin-bottom: 2px;
	}

	input[type="text"]{
		height: 28px;
	}

	.padding-left-15{
  		padding-left: 15px;
  	}

  	.panel-title{
  		text-align: center;
  	}

  	.panel-content{
  		font-size: 24px;
  		text-align: center;
  		color: red;
  		padding: 15px 0px;
  	}
</style>

<div class="container-fluid">
	<div class="col-sm-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">THÔNG TIN TÀI KHOẢN</h3>
			</div>
			<div class="panel-body">
				<div class="col-sm-6 box-item">
					<div class="col-sm-12">
						<div class="form-group">
						    <label class="control-label col-sm-4" for="pwd">Tên đầy đủ </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Tên đầy đủ" value="<?php echo $this->_agent_fname . ' ' . $this->_agent_lname;?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4" for="pwd">Email </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Email" value="<?php echo '';?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4" for="pwd">Số điện thoại </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Số điện thoại" value="<?php echo $this->_mobile;?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4" for="pwd">Số Ext </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Số Ext" value="<?php echo $this->_ext;?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4" for="pwd">Ngày tạo </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Ngày tạo" value="<?php echo $date_created;?>" readonly>
						    </div>
					  	</div>
				  	</div>
				</div>

				<div class="col-sm-6 box-item">
					<div class="col-sm-12">
						<div class="form-group">
						    <label class="control-label col-sm-4 padding-left-15" for="pwd">Quyền </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" name="uniqid" class="form-control" placeholder="Quyền" value="<?php echo $this->_role;?>" readonly>
						    </div>
					  	</div>
						<div class="form-group">
						    <label class="control-label col-sm-4 padding-left-15" for="pwd">Tỉnh/Thành phố </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" name="uniqid" class="form-control" placeholder="Tỉnh/Thành phố" value="<?php echo $city_name;?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4 padding-left-15" for="pwd">Trung tâm </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Trung tâm" value="<?php echo $center_name;?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4 padding-left-15" for="pwd">Phòng ban </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Phòng ban" value="<?php echo $department_name;?>" readonly>
						    </div>
					  	</div>
					  	<div class="form-group">
						    <label class="control-label col-sm-4 padding-left-15" for="pwd">Nhóm </label>
						    <div class="col-sm-8 box-row">
						      	<input type="text" class="form-control" placeholder="Nhóm" value="<?php echo $group_name;?>" readonly>
						    </div>
					  	</div>
				  	</div>
				</div>
			</div>
		</div>
	</div>
</div>