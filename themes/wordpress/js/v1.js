var x = d3.scale.linear().domain([0, 8]).range([0, 300]);
var o = d3.scale.linear().domain([0, 650]).range([.2, 1]);

var latRange = d3.scale.linear().domain([-90,90]).range([0,600]);
var longRange = d3.scale.linear().domain([-180,180]).range([0,1200]);
var magRange = d3.scale.linear().domain([0.01,8]).range([1,7]);
var magColor = d3.scale.linear().domain([0.01,8]).range(['dark-grey','red']);

var globalData;


//	d3.csv("http://earthquake.usgs.gov/earthquakes/feed/v0.1/summary/1.0_week.csv")

function loadData(){
	d3.csv("/wp-content/themes/wordpress/assets/earthquakes.csv")
			.row(function(d){ return {latitude: +d.Latitude, longitude: +d.Longitude, depth: +d.Depth, magnitude: +d.Magnitude};})
			.get(function(error, rows) {drawData(rows);});
}

function drawData(loadedData) {

	globalData = loadedData;

	var chart = d3.select("#data-viz").append("svg")
		.attr("width", 1200)
		.attr("height", 450)
		.attr("class", "chart");

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



		chart.append("text")
			.attr("x", 765)
            .attr("y", 430)
            .attr("font-size", "14px")
            .attr("font-family", "'Lato', 'HelveticaNeue', 'Helvetica Neue', Helvetica, Arial, sans-serif")
            .style("fill", "#424242")
            .text("Visualization of Earthquake Data from USGS")

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

loadData();
