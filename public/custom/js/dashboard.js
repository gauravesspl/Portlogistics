
$(document).ready(function () {
	checkFilterNotification();
	$(".expand-btn").click(function () {
		$(".dashboard-filter-content").toggle('slow');
	});
	$('.multiselect').multiselect(
		{
			includeSelectAllOption: true,
			selectAllText: 'Select all'
		}
	);

	var dateNow = new Date();
	$('#plotDate').datetimepicker({
		format: 'L',
		defaultDate: dateNow
	});
	//On change event dashboard data load
	$("#plotDate").on("change.datetimepicker", ({ date, oldDate }) => {
		getDashboardDetails();
	})

	//Data fetching by changing Date, Vessel, Customer, Cargo Filters
	$('#cmbVessel,#cmbCustomer,#cmbCargo').on('change', function (e) {
		getDashboardDetails();
	});
	function getDashboardDetails() {
		let data = {
			'date': $("#plotDate").val(),
			'vessel': $("#cmbVessel").val(),
			'customer': $("#cmbCustomer").val(),
			'cargo': $("#cmbCargo").val(),
			"_token": token
		}
		checkFilterNotification();
		$("#sectionLoader").show();
		$.ajax({
			url: APP_URL + "/dashboard",
			type: 'post',
			data: data,
			success: function (response) {
				console.log(response.result);

				getChartsDataReady(response.result.ca, 'grapharea-challansdep');
				getChartsDataReady(response.result.ntu, 'grapharea-nooftrucks');
				getChartsDataReady(response.result.ttc, 'grapharea-tripscompleted');

				let keys = Object.keys(response.result.ttc);
				let rows = '';
				keys.forEach((val) => {

					rows += `<tr>
               				<td>Shift-`+ val + `</td>
               				<td>`+ response.result.ntu[val] + `</td>
               				<td>`+ response.result.ttc[val] + `</td>
           				</tr>`

				});
				$('#btp').html(rows);
				$("#sectionLoader").hide();
			}
		});
	}
	getDashboardDetails();
});

function toggleFilterArea() {
	$('.filterArea').slideToggle(500, 'swing');
}
$('.filterBtn').click(function () {
	toggleFilterArea();
});

function getChartsDataReady(data, area) {
	console.log(data);
	let glabel = ['Shift-A', 'Shift-B', 'Shift-C'];
	let gdata = Object.values(data);

	createChart(area, glabel, gdata, 'doughnut');
	$('.' + area + '-A').html(data.A);
	$('.' + area + '-B').html(data.B);
	$('.' + area + '-C').html(data.C);



}






function createChart(location, label, data, type) {

	let graphData = {
		labels: label,
		datasets: [{
			data: data,

			backgroundColor: [
				'rgba(255, 206, 86, 1)',
				'rgba(54, 162, 235, 1)',
				'rgba(255,99,132,1)',
			],
			borderColor: [
				'rgba(255, 206, 86, 1)',
				'rgba(54, 162, 235, 1)',
				'rgba(255,99,132,1)',
			],
			borderWidth: 1
		}]
	};
	var cd = document.getElementById(location);
	var myDoughnutChart = new Chart(cd, {
		type: type,
		data: graphData,
		options: {
			responsive: true,
			legend: {
				display: false
			},
			tooltips: {
				enabled: true
			}
		},
	});
}

function checkFilterNotification(){
	    var selectionCount =parseInt($("#plotDate").val()?1:0)+
		parseInt($("#cmbVessel").val().length>0?1:0)+
		parseInt($("#cmbCustomer").val().length>0?1:0)+
		parseInt($("#cmbCargo").val().length>0?1:0);

		if(selectionCount>0){
			$('.filter-notification').html(selectionCount);
			$('.filter-notification').show();
			let msg = (selectionCount>1)?"filters applied.":" filter applied.";
			$('.filter-notification').attr('title', selectionCount+ msg);
		}
		else{
			$('.filter-notification').hide();
			$('.filter-notification').html(0);
		}
}



