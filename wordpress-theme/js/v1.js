var x = d3.scale.linear().domain([0, 8]).range([0, 300]);
var o = d3.scale.linear().domain([0, 650]).range([.2, 1]);

var latRange = d3.scale.linear().domain([-90,90]).range([0,600]);
var longRange = d3.scale.linear().domain([-180,180]).range([0,1200]);
var magRange = d3.scale.linear().domain([0.01,8]).range([1,7]);
var magColor = d3.scale.linear().domain([0.01,8]).range(['dark-grey','red']);

var globalData;


//	d3.csv("http://earthquake.usgs.gov/earthquakes/feed/v0.1/summary/1.0_week.csv")

function loadData(){
	d3.csv("earthquakes.csv")
			.row(function(d){ return {latitude: +d.Latitude, longitude: +d.Longitude, depth: +d.Depth, magnitude: +d.Magnitude};})
			.get(function(error, rows) {drawData(rows);});
}

function drawData(loadedData) {

	globalData = loadedData;

	var chart = d3.select("#data-viz").append("svg")
		.attr("width", 1200)
		.attr("height", 450)
		.attr("class", "chart");

	/* chart.selectAll("circle")
		.data(loadedData)
		.enter().insert("svg:circle")
			.attr("cx", function (d,i) {return longRange(d.longitude);})
			.attr("cy", function (d,i) {return latRange(-1 * d.latitude);})
			.attr("r", function (d,i) {return magRange(d.magnitude);})
			.style("fill-opacity", 0.6)
			.style("fill", function (d,i) { return magColor(d.magnitude)})
			.on("mouseover", function (d,i) { 
				d3.select(this)
					.transition()
					.delay(0)
					.duration(300)
					.attr("r", function (d,i) {return 5 * magRange(d.magnitude);});})
			.on("mouseout", function (d,i){
				d3.select(this)
				.transition()
				.delay(100)
				.duration(200)
				.attr("r", function (d,i) {return magRange(d.magnitude);} );
			}); */

		chart.selectAll("rect")
		.data(loadedData)
		.enter().append("rect")
			.attr("x", function (d,i) {return i * 5;} )
			.attr("y", function (d,i) {return 250 - x(loadedData[i].magnitude);})
			.attr("width", 2)
			.attr("height", function (d,i) {return 2 * x(loadedData[i].magnitude);})
			.style("fill", "white")
			.style("fill-opacity", function (d,i) {return o(loadedData[i].depth);})
		;

		updateBars()
}

function updateBars(){

	d3.select("#data-viz").selectAll("rect")
		.data(globalData)
		.transition()
			.duration(1000)
			.attr("y", function (d,i) {return 250 - x(globalData[i].magnitude + Math.random());})
			.attr("height", function (d,i) {return (2 * (x(globalData[i].magnitude + Math.random())));})
		;

	console.log("Updating Bars");

	setTimeout (updateBars, 800);
}

function scaleCircle(){
	d3.select(this)
		.transition()
		.delay(0)
		.duration(300)
		.attr("r", 20);
}

function shrinkCircle(d,i){
	d3.select(this)
		.transition()
		.delay(0)
		.duration(300)
		.attr("r", 10);
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
