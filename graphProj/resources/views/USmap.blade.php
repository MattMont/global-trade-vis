<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>D3: Loading GeoJSON data and generating SVG paths</title>
		<script type="text/javascript" src="../d3/d3.v3.js"></script>
		<script src="//d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<script src="js/us-states.json"></script>
		<style type="text/css">
			/* No style rules here yet */
		</style>
	</head>
	<body>
		<script type="text/javascript">
			//Width and height
			var w = 500;
			var h = 300;
			//Define default path generator
			//Define map projection
			var projection = d3.geo.albersUsa()
						.translate([w/2, h/2])
						.scale([500]);
//Define path generator
			var path = d3.geo.path()
				 .projection(projection);

			//Create SVG element
			var svg = d3.select("body")
						.append("svg")
						.attr("width", w)
						.attr("height", h);

						//Define quantize scale to sort data values into buckets of color
			var color = d3.scale.quantize()
							.range(["rgb(237,248,233)","rgb(186,228,179)","rgb(116,196,118)","rgb(49,163,84)","rgb(0,109,44)"]);
								//Colors taken from colorbrewer.js, included in the D3 download

			//Load in agriculture data
			d3.csv("csv/us-ag-productivity-2004.csv", function(data) {
			//Set input domain for color scale
				color.domain([
					d3.min(data, function(d) { return d.value; }),
					d3.max(data, function(d) { return d.value; })
				]);
			//Load in GeoJSON data
			d3.json("js/us-states.json", function(json) {

				//Merge the ag. data and GeoJSON
					//Loop through once for each ag. data value
					for (var i = 0; i < data.length; i++) {

						//Grab state name
						var dataState = data[i].state;

						//Grab data value, and convert from string to float
						var dataValue = parseFloat(data[i].value);

						//Find the corresponding state inside the GeoJSON
						for (var j = 0; j < json.features.length; j++) {

							var jsonState = json.features[j].properties.name;

							if (dataState == jsonState) {

								//Copy the data value into the JSON
								json.features[j].properties.value = dataValue;

								//Stop looking through the JSON
								break;

							}
						}
					}

				//Bind data and create one path per GeoJSON feature
				//Bind data and create one path per GeoJSON feature
					svg.selectAll("path")
					   .data(json.features)
					   .enter()
					   .append("path")
					   .attr("d", path)
					   .style("fill", function(d) {
					   		//Get data value
					   		var value = d.properties.value;

					   		if (value) {
					   			//If value exists…
						   		return color(value);
					   		} else {
					   			//If value is undefined…
						   		return "#ccc";
					   		}
					   });
					//Load in cities data
					d3.csv("csv/us-cities.csv", function(data) {

						svg.selectAll("circle")
						   .data(data)
						   .enter()
						   .append("circle")
						   .attr("cx", function(d) {
							   return projection([d.lon, d.lat])[0];
						   })
						   .attr("cy", function(d) {
							   return projection([d.lon, d.lat])[1];
						   })
						   .attr("r", function(d) {
								return Math.sqrt(parseInt(d.population) * 0.00004);
						   })
						   .style("fill", "yellow")
						   .style("opacity", 0.75);

					});

				});

			});

		</script>
	</body>
</html>