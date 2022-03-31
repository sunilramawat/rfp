@extends('Admin.mainlayout')
@section('content')


<section class="content-header">
    <h4> 
    	<b> Chips </b>
    </h4>
    <ol class="breadcrumb">
        <li><a href="{{URL('admin/chip/view')}}"><i class="fa fa-users"></i> Chips</a></li>
        <li class="active">Chip Detail</li>
    </ol>
    <br>
    <div style="margin-left:10px;">
    <h4><b>Red  Wrap Crispa Details</b></h4>

</section>

<section class="content">
  	<div >
	    <div class="box-body">
	    	<div class="row">
		    	<div class="col-lg-6">
			        <div class="card">
			            <div class="card-body">	
			               <!--  <table id="example2" class="table table-bordered table-striped dataTable"> -->
			                <table class="table table-bordered table-striped dataTable">
			               	   	<thead>
				            	    <tr>
				                	    <th>#</th>
				                    	<th>Date Time</th> 
				                    	<th>Count</th> 
				                	</tr>
			                  	</thead>
			                  	<tbody>
			                  		@foreach($Chips as $no => $chip)
			                  		<tr>
			                  			<td>{{$no+1}}</td>
		                  				<td>{{$chip->data_date_time }}</td>
			                  			<td>{{$chip->cycle_count  }}</td>

			                  		</tr>
			                  		@endforeach()	
			                  	</tbody>
			                </table>
		              	</div>
					</div>
				</div>
				<div class="col-lg-6">
					<lable>Chips Data Graph</lable>
					<div style="margin-top: 20px;">
						<div id="chartContainer1" style="height: 370px;"></div>
					</div>
					<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

				</div>		
			</div>	
		</div>
	</div>
</section>  

@stop

<script>
window.onload = function () {
var chart1 = new CanvasJS.Chart("chartContainer1", {
	animationEnabled: true,	
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	
	axisY: {
 		includeZero: true

	},

	axisX:{
	},
	data: [{
		type: "column", 
		indexLabelFontColor: "#5A5757",
      	indexLabelFontSize: 16,
		indexLabelPlacement: "outside",
		dataPoints: [
			{ x: 10, y: 71 },
			{ x: 20, y: 55 },
			{ x: 30, y: 50 },
			{ x: 40, y: 65 },
			{ x: 50, y: 92 },
			{ x: 60, y: 68 },
			{ x: 70, y: 38 },
			{ x: 80, y: 71 },
			{ x: 90, y: 54 },
			{ x: 100, y: 60 },
			{ x: 110, y: 36 },
			{ x: 120, y: 49 },
			{ x: 130, y: 21}
		]
	}]

});
chart1.render();
}


</script>