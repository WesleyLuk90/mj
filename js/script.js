google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(function(){
	var graphs = $('.running-average-graph');
	for (var i = 0; i < graphs.length; i++) {
		makeGraph(graphs[i]);
	};

	function makeGraph(div){
		var jQueryDiv = $(div);
		var rawData = jQueryDiv.data('games');
		var data = [];
		data.push(["Score", "Average"]);
		for (var i = 0; i < rawData.length; i++) {
			data.push([rawData[i].score / 1000 + "k", Math.round(rawData[i].average)]);
		};

		var options = {
			title: "Average Score vs Time"
		};

		var gdata = google.visualization.arrayToDataTable(data);
		var chart = new google.visualization.LineChart(div)
		chart.draw(gdata, options);
	};
});
