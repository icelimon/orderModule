<?php
include_once 'includes/header.php';

?>
<title><?php echo isset($_SESSION['emp_name']) ? $_SESSION['emp_name']: "Oracle";?></title>
</head>
<body>
	<?php
	include_once 'connect.php';
	include_once 'includes/navbar.php';
	if(!isset($_SESSION['loggedin'])){
		header("Location: signin.php");
	}
	?>

<div class="container-fluid">
		
<!-- begin #content -->
<div id="content" class="content">

	<!-- begin data-insertion message -->

	<?php if (isset($_SESSION['succ_msg'])){ ?>
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="alert alert-success" role="alert"><?php echo $_SESSION['succ_msg']; ?></div>
		</div>
	</div>

	<?php 
	unset($_SESSION['unsucc_msg']);
	unset($_SESSION['succ_msg']);
	}else if(isset($_SESSION['succ_msg'])){ ?>
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="alert alert-danger" role="alert"><?php echo $_SESSION['unsucc_msg']; ?></div>
		</div>
	</div>
	<?php 
	unset($_SESSION['succ_msg']);
	unset($_SESSION['unsucc_msg']); } ?>
	<!-- end data-insertion message -->

	<form action="crudmodel.php" method="post" class="form-horizontal" enctype ="multipart/form-data" data-parsley-validate=true >
	<div class="row">
		<div class="col-md-12">
			<!-- begin panel -->
	        <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Basic information</h4>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="border-right: 1px solid #ccc;">
                    	<div class="note note-success">
							<h4>Instructions</h4>
							<ul>
								<li>Check and recheck before creating the sale. The sale once made can not be altered.</li>
								<li>You can add product either by selecting product category.</li>
								<li>You can enter discount percentage/amount and also can edit.</li>
								<li>You can enter sale amount, quantity and also can edit.</li>

							</ul>
						</div>
                    </div>
                    <div class="col-md-6">
                    	
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3">
                                Date
                            </label>
                            <div class="col-md-9 col-sm-9">
                                <input class="form-control" type="text" name="create_date" data-parsley-required="true"
                                    value="<?php echo date('d-M-y');?>" readonly />
                            </div>
                        </div>

                        <div class="form-group">
							<label class="control-label col-md-3 col-sm-3">
								Dealer
							</label>
							<div class="col-md-9 col-sm-9">

<?php

	$emp_id         = $_SESSION['emp_id'];
    $sql = "SELECT DEALER_ID, DEALER_NAME FROM sndms.DEALER_MST WHERE EXECUTIVE_ID = :emp_id";
    $query = oci_parse($conn, $sql);
    oci_bind_by_name($query, ':emp_id', $emp_id);
    $exe=oci_execute($query);
    $dealer_id 		= 0;
    $dealer_name 	= '';
?>
      <select id="sub-dealer-cng" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="dealer_id">
      	<option value="0" selected>Select dealer</option>
<?php     while ($row = oci_fetch_array($query, OCI_RETURN_NULLS+OCI_ASSOC)) {
        echo '<option value="'.$row['DEALER_ID'].'">'.$row['DEALER_NAME'].'</option>';
}?>
      </select>
<?php oci_free_statement($query); ?>
							</div>

						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3">
								Sub Dealer
							</label>
							<div class="col-md-9 col-sm-9">
								
								<div id="sub_dealer_entry_holder"></div>
	      							
							</div>
							
						</div>

						

						<input id="customer-name-selected" type="hidden" name="nothing" value="0">

						
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3">
                            	Delevery date
                            </label>
                            <div class="col-md-9 col-sm-9">
                                <input type="text" class="form-control datepicker" id="datepicker-autoClose2" name="delivery_date" placeholder="Select date" />
                            </div>
                        </div>
                    
					</div>
                </div>
            </div>
	        <!-- end panel -->
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			
	        <!-- begin panel -->
	        <div class="panel panel-warning" id="barnd_product">
                <div class="panel-heading">
                    <h4 class="panel-title">select_product</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
						<div class="col-md-12 col-sm-12">
<?php
    $sql = "SELECT ITEM_ID, ITEM_NAME, ITEM_RATE FROM sndms.SALES_ITEM_INFO";
    $query = oci_parse($conn, $sql);
    //oci_bind_by_name($query, ':emp_id', $emp_id);
    $exe=oci_execute($query);
    $item_id 	= 0;
    $item_name 	= '';
    $item_rate  = 0;
?>
<select id="select-products" class="form-control selectpicker" data-live-search="true" onchange="add_product(this.value)">
	<option value="0" selected="true">Select product</option>
<?php     while ($row = oci_fetch_array($query, OCI_RETURN_NULLS+OCI_ASSOC)) {
        echo '<option value="'.$row['ITEM_ID'].'">'.$row['ITEM_NAME'].'</option>';
}?>
</select>

<?php oci_free_statement($query); ?>

						</div>
					</div>
				</div>
            </div>
	        <!-- end panel -->

		</div>
		
		<div class="col-md-9">
        	<!-- begin panel -->
	        <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">sale_added</h4>
                </div>
                <div class="panel-body">
	                <div id="data-table" class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>name</th>
									<th class="small-th">quantity</th>
									<th>unit price</th>
									<th class="small-th">Discount</th>
									<th class="small-th">Discount %</th>
									<th>total</th>
									<th><i class="fa fa-trash"></i></th>
								</tr>
							</thead>
							<tbody id="invoice_entry_holder">
							</tbody>
						</table>
					</div>
				</div>
            </div>
	        <!-- end panel -->
	        <div class="col-md-5"></div>
	        <div class="col-md-7" style="padding:15px 0px">
		        <!-- begin panel -->
	            <div class="panel panel-default" data-sortable-id="ui-widget-8">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
	                    	payment
	                    </h4>
	                    
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td>grand_total</td>
										<td>
											<input type="text" class="form-control text-right" id="grand_total" value="" name="grand_total">
										</td>
									</tr>
									<tr>
										<td>total quantity</td>
										<td>
											<input type="text" class="form-control text-right" id="total_qty" value="" name="total_qty" readonly>
										</td>
									</tr>

								</tbody>
							</table>
							<div class="form-group col-md-10">
								<button type="submit" class="btn btn-success" name="do_sale">create_new_sale</button>
							</div>
						</div>
	                </div>
	            </div>
	            <!-- end panel -->
	        </div>
        </div>

	</div>
	</form>
</div>


</div>

	<?php
	include_once 'includes/footer.php';
	?>


<script type="text/javascript">

    total_number = 0;
    total_quantity = 0;

    //Add new product
    function add_product(product_id) {
        total_number++;
        if(product_id > 0){
	        $.ajax({
	           url:"crudmodel.php",
	           type: "post",
	           //dataType: 'json',
	           data: {function_name: "get_selected_product", product_id: product_id, total_number: total_number},
	            success: function(response)
	            {
	                jQuery('#invoice_entry_holder').append(response);
	                calculate_grand_total();
	                calculate_change_amount(); 
	                //alert("success");
	            },
	            error: function(xhr) {
	            	console.log('Request Status: ' + xhr.status + '\n Status Text: ' + xhr.statusText + ' \n' + xhr.responseText);
				}
	        });
	    }
    }

    //Handle dealer change to sub-dealer
    $(document).on('change','#sub-dealer-cng',function(){
    	var dealer_id = this.value;
    	$('#sub_dealer_entry_holder').html('');
        if(dealer_id>0){
        $.ajax({
           url:"crudmodel.php",
           type: "post",
           data: {function_name: "get_sub_dealer", dealer_id: dealer_id},
            success: function(response)
            {
                jQuery('#sub_dealer_entry_holder').append(response); 
                jQuery('#sub_dealer_select').addClass('form-control selectpicker');
                //$('#sub_dealer_select').prop('data-live-search', true);
                $('#sub_dealer_select').attr('data-live-search', true);
                //alert("success");
            },
            error: function(xhr) {
            	console.log('Request Status: ' + xhr.status + '\n Status Text: ' + xhr.statusText + ' \n' + xhr.responseText);
			}
        });
    	}
    });

	//Take action to remove a product
	function remove_row(entry_number) {
        //alert (total_number);
        $('#entry_row_'+entry_number).remove();

        for (var i = entry_number ; i < total_number ; i++)
        {
            $("#single_entry_total_"            + (i + 1) ).attr("id" , "single_entry_total_" + i);

            $("#serial_number_"                 + (i + 1) ).attr("id" , "serial_number_" + i);
            $("#serial_number_"                 + (i ) ).html(i);

            $("#single_entry_quantity_"         + (i + 1) ).attr("id" , "single_entry_quantity_" + i);
            $("#single_entry_quantity_"         + (i ) ).attr({onkeyup: "calculate_single_entry_total(" + i + ")" , onclick: "calculate_single_entry_total(" + i + ")"});

            $("#single_entry_selling_price_"    + (i + 1) ).attr("id" , "single_entry_selling_price_" + i);
            $("#single_entry_selling_price_"    + (i ) ).attr({onkeyup: "calculate_single_entry_total(" + i + ")" , onclick: "calculate_single_entry_total(" + i + ")"});
            
            $("#delete_button_"                 + (i + 1) ).attr("id" , "delete_button_" + i);
            $("#delete_button_"                 + (i ) ).attr("onclick" , "remove_row(" + i + ")");

            $("#entry_row_"                     + (i + 1) ).attr("id" , "entry_row_" + i);

            console.log(i);
        }

        total_number--;
        // on deletion each single entry, update the grand total area also
        calculate_grand_total();
        calculate_change_amount();
    }

    //Calculate single entry action here
    function calculate_single_entry_total(entry_number) {

        quantity        = $("#single_entry_quantity_"+entry_number).val();
        selling_price   = $("#single_entry_selling_price_"+entry_number).val();
        discount_percentage = $("#discount_percentage_"+entry_number).val();
        discount 		= $("#discount_"+entry_number).val();

        single_entry_total = quantity * selling_price;
        single_entry_total = single_entry_total - (single_entry_total * (discount_percentage / 100));
        single_entry_total = single_entry_total - (quantity * discount);
        $("#single_entry_total_"+entry_number).html( single_entry_total );

        if(discount > 0){
        	$("#discount_percentage_"+entry_number).attr("disabled", true);
        }else{
        	$("#discount_"+entry_number).prop("disabled", false);
        }
        if(discount_percentage > 0){
        	$("#discount_"+entry_number).attr("disabled", true);
        }else{
        	$("#discount_percentage_"+entry_number).prop("disabled", false);
        }
        // on change each single entry, update the grand total area also
        calculate_grand_total();
        calculate_change_amount();
        console.log('quantity : '+quantity);
    }

    // calculate the grand total area
    function calculate_grand_total() {

        // calculating subtotal
        sub_total = 0;
        total_quantity = 0;
        for (var i = 1 ; i <= total_number ; i++)
        {
            sub_total   +=   Number( $("#single_entry_total_"+ i).html() );
            total_quantity += Number( $("#single_entry_quantity_"+ i).val() );
            
        }
        $("#sub_total").attr("value" , sub_total);
        $("#total_qty").attr("value" , total_quantity);
        // calculating grand total
        /*discount_percentage    =   Number( $("#discount_percentage").val() );
        vat_percentage         =   Number( $("#vat_percentage").val() );
		*/
		//discount_percentage		= 10;
		vat_percentage 			= 0;
        //sub_total              =   sub_total - (sub_total * (discount_percentage / 100));
        grand_total            =   sub_total + (sub_total * (vat_percentage / 100));
		
        grand_total            =    grand_total.toFixed(2);
        $("#grand_total").attr("value" , grand_total);
        calculate_change_amount();
    }

	// calculate the amount change area
    function calculate_change_amount() {
		get_grand_total    =	Number( $("#grand_total").val() );
		get_payment_amount =	Number( $("#payment").val() );

		if (get_payment_amount > get_grand_total) {

			change_amount      =	get_payment_amount - get_grand_total;
			change_amount      =	change_amount.toFixed(2);
			$("#change_amount").attr("value" , change_amount);
			get_change_amount  =	Number( $("#change_amount").val() );
			net_payable		   =	get_payment_amount - get_change_amount;
			net_payable		   =	net_payable.toFixed(2);
		}
    }


</script>



