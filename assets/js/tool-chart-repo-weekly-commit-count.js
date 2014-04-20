/* global d3 */
(function(d3){

	var data = chartData['all'];

	var width = 680,
			height = 240,
			barWidth = width / data.length;

	var y = d3.scale.linear()
		.range([height, 0])
		.domain([0, d3.max(data)]);

	var chart = d3.select("#weekly-commit-count")
		.attr("width", width)
		.attr("height", height);

	var bar = chart.selectAll("g").data(data)
		.enter()
			.append("g")
			.attr("transform", function(d, i) { return "translate(" + i * barWidth + ",0)"; });

	bar.append("rect")
		.attr("y", y)
		.attr("height", function(d) { return height - y(d); })
		.attr("width", barWidth - 1);

	bar.append("text")
		.attr("x", barWidth / 2)
		.attr("y", function(d) { return y(d) + 3; })
		.attr("dy", ".75em")
		.text(function(d) { return d; });

}(d3));
