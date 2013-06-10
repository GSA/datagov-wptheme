var viz = d3.select("#viz"),
	width = 1000,
	height = 200,
	xRange = d3.scale.linear().range([0,1200]).domain([0,400]),
	yRange = d3.scale.linear().range([0,200]).domain([0,400]);


function loadData(){
	d3.csv("http://54.225.112.145/wp-content/themes/datagov-may20h/assets/earthquakes.csv")
			.row(function(d){ return {depth: +d.Depth, magnitude: +d.Magnitude};})
			.get(function(error, rows) {console.log(rows); drawData(rows);});
}

function drawData(loadedData) {

	var chart = d3.select("#data-viz").append("svg")
		.attr("class", "chart")
		.attr("width", 1200)
		.attr("height", 450);

	var x = d3.scale.linear().domain([0, 8]).range([0, 500]);
	var o = d3.scale.linear().domain([0, 650]).range([.2, 1]);

	chart.selectAll("rect")
		.data(loadedData)
		.enter().append("rect")
		.attr("x", function (d,i) {return i * 25;} )
		.attr("y", function (d,i) {return 445 - x(loadedData[i].magnitude);})
		.attr("width", 20)
		.attr("height", function (d,i) {return x(loadedData[i].magnitude);})
		.style("fill", "white")
		.style("fill-opacity", function (d,i) {return o(loadedData[i].depth);});

}

function update() {
	var circles = viz.selectAll("circle")
		.data(randomData(), function (d) { return d.id;});

	circles.enter()
		.insert("svg:circle")
			.attr("cx", function (d) {return xRange(d.value1);})
			.attr("cy", function (d) {return yRange(d.value2);})
			.style("fill-opacity", 0.6)
			.style("fill", "red");

	circles.transition().duration(1000)
		.ease("exp-in-out")
		.attr("cx", function (d) {return xRange(d.value1);})
		.attr("cy", function (d) {return yRange(d.value2);})
		.attr("r",  function (d) {return d.value3;});

	circles.exit ()
		.transition().duration(1000)
			.ease("exp-in-out")
			.attr("r", 0)
				.remove();

	setTimeout (update, 2000);
}

function randomData () {
	var n = randomRange (1, 20),
		dataset = [];

	for (var i = 0; i < n; i++) {
		var data = {
			id: i,
			value1: randomRange (0, 400),
			value2: randomRange (0, 400),
			value3: randomRange (3, 20)
		}
		dataset.push (data);
	}

	return dataset;
}

function randomRange (min, max) {
	return Math.round (Math.random() * (max - min) + min); 
}

loadData();
update();