<?php



if(isset($_POST['function_name'])){
	if($_POST["function_name"] == "get_selected_product" ){
		$id = $_POST['product_id'];
		$total_number = $_POST['total_number'];
		return get_selected_product($id, $total_number);
	}
	if($_POST["function_name"] == "get_sub_dealer" ){
		$id = $_POST['dealer_id'];
		get_sub_dealer($id);
	}
}




//INSERT SALE DATA TO DB 
//dd-mm-yyyy
if(isset($_POST['do_sale'])){
	session_start();
	include 'connect.php';
	$exe_dtl 	= false;
	$exe_mst 	= false;
	$emp_id    	= $_SESSION['emp_id'];
	$branch_id  = $_SESSION['branch_id'];
	//$data2['timestamp']   = strtotime(date("d-M-y"));
	$create_date        = $_POST['create_date'];
	$dealer_id          = $_POST['dealer_id'];
	$sub_dealer_id 		= $_POST['sub_dealer_id'];
	$delivery_date      = $_POST['delivery_date'];

	$to_day = date('d-M-Y',strtotime($create_date));
	$d_date = date('d-M-Y',strtotime($delivery_date));

	$today 		= date('d/m/Y');
	$today_po_format = date('dmY');
	$sql = 'SELECT * FROM SCHEMA.DEALER_POST_MST WHERE DEALER_PO_NO = ( select max(DEALER_PO_NO) from SCHEMA.DEALER_POST_MST where DEALER_PO_DATE=:today_po )';
	$st_fts = oci_parse($conn, $sql);
	oci_bind_by_name($st_fts, ":today_po", $today);

	oci_execute($st_fts);
	while ($row = oci_fetch_array($st_fts, OCI_RETURN_NULLS+OCI_ASSOC)){
		$dealer_po_no 	= $row['DEALER_PO_NO'];
		$dealer_po_date = $row['DEALER_PO_DATE'];
	}
	oci_free_statement($st_fts);


	//SET: DEALER_PO_NO

	if(isset($dealer_po_date)){
		if($today == $dealer_po_date){
			$sub_po 		= substr($dealer_po_no, 14);
			$sub_po 		=(int)$sub_po+1;
			$dealer_po_no 	= $dealer_id.$today_po_format.$sub_po;
		}else{
			$dealer_po_no 	= $dealer_id.$today_po_format.'1';
		}
	}else{
		$dealer_po_no 	= $dealer_id.$today_po_format.'1';
	}


	//INSERT DATA INTO DEALER_POST_MST TABLE
	$sql='INSERT INTO SCHEMA.DEALER_POST_MST(DEALER_PO_NO, BRANCH_ID, DEALER_ID, SUB_DEALER_ID, DEALER_PO_DATE, PO_DELIVERY_DATE)'.
	'VALUES(:dealer_po_id, :branch_id, :dealer_id, :sub_dealer_id, :created_date, :delivery_date)'.'RETURNING DEALER_PO_NO INTO :dealer_po_no';

	$compiled = oci_parse($conn, $sql);
	oci_bind_by_name($compiled, ':dealer_po_id', $dealer_po_no);
	oci_bind_by_name($compiled, ':branch_id', $branch_id);
	oci_bind_by_name($compiled, ':dealer_id', $dealer_id);
	oci_bind_by_name($compiled, ':sub_dealer_id', $sub_dealer_id);
	oci_bind_by_name($compiled, ':created_date', $to_day);
	oci_bind_by_name($compiled, ':delivery_date', $d_date);

	oci_bind_by_name($compiled, ":dealer_po_no", $dealer_po_rtn_id, 18);
	$exe_mst = oci_execute($compiled);
	oci_free_statement($compiled);

	//SELECT LAST INSERTED PO_DTL_SL ID FROM DEALER_PO_DTL TABLE
	$st_cnt = oci_parse($conn, "SELECT count(*) c FROM SCHEMA.DEALER_PO_DTL");
	oci_execute($st_cnt);
	oci_fetch_all($st_cnt, $res);
	$rows = $res['C'][0];
	oci_free_statement($st_cnt);

	//GET USER INSERTED DATA 
	$product_ids                 = $_POST['product_id'];
	$discount_pcts               = $_POST['discount_pct'];
	$item_qnties               	 = $_POST['item_qnty'];
	$selling_prices              = $_POST['selling_price'];
	
	$number_of_entries           = sizeof($product_ids);
	/*
	//CALCULATE TOTAL PRICE FOR EACH ITEM
	single_entry_total = quantity * selling_price;
	single_entry_total = single_entry_total - (single_entry_total * (discount_percentage / 100));
	*/
	
	//EXECUTE ALL PRODUCT OPERATION
	for ($i = 0; $i < $number_of_entries; $i++) {
		if ($product_ids[$i] != "" && $item_qnties[$i] != "" && $selling_prices[$i] != "") {
			$item_id 		= $product_ids[$i];
			$discount_pct 	= $discount_pcts[$i];
			$item_qnty 		= $item_qnties[$i];
			$rows++;
			//INSERT DATA INTO DEALER_PO_DTL TABLE
			$sql='INSERT INTO SCHEMA.DEALER_PO_DTL(PO_DLT_SL, DEALER_PO_NO, ITEM_ID, DISCOUNT_PCT, ITEM_QNTY, CREATE_EMP_ID, CREATE_DATE)'.'VALUES(:po_detail_sl, :dealer_po_no, :item_id, :discount_pct, :item_qnty, :emp_id, :create_date)';

			$compiled = oci_parse($conn, $sql);
			oci_bind_by_name($compiled, ':po_detail_sl', $rows);
			oci_bind_by_name($compiled, ':dealer_po_no', $dealer_po_rtn_id);
			oci_bind_by_name($compiled, ':item_id', $item_id);
			oci_bind_by_name($compiled, ':discount_pct', $discount_pct);
			oci_bind_by_name($compiled, ':item_qnty', $item_qnty);
			oci_bind_by_name($compiled, ':emp_id', $emp_id);
			oci_bind_by_name($compiled, ':create_date', $to_day);
			$exe_dtl = oci_execute($compiled);
			oci_free_statement($compiled);

			//return $invoice_id;
		}//END IF CONDITION
	}//END FOR LOOP

	if($exe_mst && $exe_dtl){
		$_SESSION['succ_msg'] = 'Data Successfully Inserted!';
	}else{
		$_SESSION['unsucc_msg'] = 'Opps!<br>Data failed to Insert!';
	}
	header("Location: profile.php");
}//END DO_SALE IF CONDITION





// DECLARATION: GET SELECTED PRODUCTS TO BE SOLD
function get_selected_product($product_id, $total_number)
{
	include 'connect.php';
	$sql = "SELECT ITEM_ID, ITEM_NAME, DEALER_RATE FROM SCHEMA.SALES_ITEM_INFO WHERE ITEM_ID = :product_id";
	$query = oci_parse($conn, $sql);
	oci_bind_by_name($query, ':product_id', $product_id);
	$exe=oci_execute($query);

	while ($row = oci_fetch_array($query, OCI_RETURN_NULLS+OCI_ASSOC)) {
		echo '<tr id="entry_row_' . $total_number . '">
		<td type="hidden" id="serial_' . $total_number . '">' . $total_number . '</td>
		<td>'.$row['ITEM_NAME'].'
		<input type="hidden" name="product_id[]" value="'.$row['ITEM_ID'].'" 
		id="single_entry_product_id_' . $total_number . '">
		</td>
		<td>
		<input type="number" class="small-th" name="item_qnty[]" value="1" min="1"
		id="single_entry_quantity_' . $total_number . '"
		onkeyup="calculate_single_entry_total(' . $total_number . ')"
		onclick="calculate_single_entry_total(' . $total_number . ')">
		</td>
		<td>
		<input type="text" data-parsley-type="number" name="selling_price[]" value="'.$row['DEALER_RATE'].'" min="0"
		id="single_entry_selling_price_' . $total_number . '"
		onkeyup="calculate_single_entry_total(' . $total_number . ')"
		onclick="calculate_single_entry_total(' . $total_number . ')">
		</td>

		<td>
		<input type="number" class="small-th" name="discount" 
		id="discount_' . $total_number . '" value="0" placeholder=" " onkeyup="calculate_single_entry_total(' . $total_number . ')"
		data-parsley-required="true"onclick="calculate_single_entry_total(' . $total_number . ')">
		</td>

		<td>
		<input type="number" class="small-th" name="discount_pct[]" 
		id="discount_percentage_' . $total_number . '" value="0" placeholder=" " onkeyup="calculate_single_entry_total(' . $total_number . ')"
		data-parsley-required="true"onclick="calculate_single_entry_total(' . $total_number . ')">
		</td>

		<td id="single_entry_total_' . $total_number . '">'.$row['DEALER_RATE'].'</td>
		<td>
		<i class="fa fa-trash" onclick="remove_row(' . $total_number . ')"
		id="delete_button_' . $total_number . '" style="cursor: pointer;"></i>
		</td>
		</tr>';
	}
	oci_free_statement($query);
}

// DECLARATION: GET SELECTED PRODUCTS TO BE SOLD
function get_sub_dealer($dealer_id)
{

	include 'connect.php';

	if (!$conn) {
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

// Prepare the statement
	$stid = oci_parse($conn, 'SELECT * FROM SCHEMA.SUB_DEALER_INFO WHERE DEALER_ID = :dealer_id ');
	if (!$stid) {
		$e = oci_error($conn);
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}
	oci_bind_by_name($stid, ':dealer_id', $dealer_id);
// Perform the logic of the query
	$r = oci_execute($stid);
	if (!$r) {
		$e = oci_error($stid);
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

// Fetch the results of the query
	echo '<select id="sub_dealer_select" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="sub_dealer_id">
	<option value="0" selected>Select sub-dealer</option>';
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		echo '<option value="'.$row['SUB_DEALER_ID'].'">'.$row['SUB_DEALER_NAME'].'</option>';
	}
	echo '</select>';
	oci_free_statement($stid);

	oci_close($conn);


}