<?php
	/*if(isset($_GET)){
		$str="";
		$i_str=0;
		foreach($_GET as $key=>$value){
			if($i_str==0){
				$str.="/?";
			}
			$str.="".$key."=".$value."&";
			$i_str++;
		}
	}*/
	//print_r($this->columns);

	$action="";
	$requestURI=urldecode($_SERVER['REQUEST_URI']);
	$requestURI = explode('/', $requestURI);
	if(isset($requestURI[2])){
		$action="/".$requestURI[2];
	}
	if(strrpos($action, "?")!==false){
		$action="";
	}
	$str=$action."/?".http_build_query(array_merge($_GET,$_POST));
	$temp_arr=array();
	foreach($this->columns as $key=>$value){
		if(isset($value["searchfull"])&&$value["searchfull"]==1){
			$temp_arr[]=array(
				'key'=>$key,
				'as'=>$value["as"],
			);
		}
	}
	//print_r($temp_arr);
?>
<?php
	if(!empty($temp_arr)){
		?>
        <div class="d-flex justify-content-center mb-2">
            <button id="search_button" onclick="event.preventDefault();$('.dataTables_filter').show();$('#search_button').hide();" class="btn btn-info">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
        </div>
		<div style="display:none;margin-bottom:10px;width: 100%" class="dataTables_filter" id="DataTables_Table_0_filter">
            <div class="d-flex flex-row">
                <?php foreach($temp_arr as $key=>$value){ ?>
                    <div class="input-group m-3 flex-wrap">
                        <span class="input-group-text" id="<?php echo $value["key"]; ?>"><?php echo $value["as"]; ?></span>
                        <input aria-describedby="basic-addon3" type="text" name="<?php echo $value["key"]; ?>" class="form-control ajax_search_<?php echo $this->page_type; ?>">
                    </div>
                <?php }	?>
            </div>
            <div class="d-flex justify-content-center mb-2">
                <button onclick="event.preventDefault();$('.dataTables_filter').hide();$('#search_button').show();" class="btn btn-warning">Cerrar Búsqueda</button>
            </div>
		</div>
		<?php
	}else{
?>
<div class="dataTables_filter" id="DataTables_Table_0_filter" style="width: 100%">
	<label>Buscar: <input type="text" name="search" class="ajax_search_<?php echo $this->page_type; ?>"></label>
</div>
<?php
	}
?>
<script>
	$(function(){
		$(".ajax_search_<?php echo $this->page_type; ?>").keyup($.debounce(250,ajax_send_<?php echo $this->page_type; ?>));
	});
	function ajax_send_<?php echo $this->page_type; ?>(){
		var page=0;
		var args=<?php echo json_encode($this->args); ?>;
		//var str=$(".ajax_search_<?php echo $this->page_type; ?>").val();
		var sort=JSON.stringify(sorting_<?php echo $this->page_type; ?>);
		var str2={};
		$(".ajax_search_<?php echo $this->page_type; ?>").map(function(){
			str2[$(this).attr("name")]=$(this).val();
		});
		var str=JSON.stringify(str2);
		$.post('<?php echo $html->link($this->page_name).$str; ?>', {action:"search",ajax:1,type:'<?php echo $this->page_type; ?>',
		page:page,args:args,str:str,sort:sort,table:"<?php echo $this->page_type; ?>"},
		function(data) {
			//$('#block_elements').unblock();
			if(typeof data.success!='undefined'){
				if (typeof construct_table == 'function') {
					construct_table(data);
					//alert("construir");
				}
				//construct_pagination(data);
			}
			if(typeof data.error!="undefined"){
				alert(data.error);
			}else if(typeof data.jumpTo!='undefined'){
				changePage(data.jumpTo);
				//window.location=data.jumpTo;
			}else if(typeof data.eval!='undefined'){
				eval(data.eval);
			}
		}, "json");
	}
</script>
<script src="/js/debounce.js"></script>
